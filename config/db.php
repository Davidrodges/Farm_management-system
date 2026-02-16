<?php
// config/db.php

// 1. SSL/HTTPS Detection (Proxy-aware for Railway)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Session Security Configuration
$secure_session = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
ini_set('session.cookie_httponly', 1);
if ($secure_session) {
    ini_set('session.cookie_secure', 1);
}
ini_set('session.cookie_samesite', 'Lax');

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'secure' => $secure_session,
    'httponly' => true,
    'samesite' => 'Lax'
]);

$is_localhost = (
    php_sapi_name() === 'cli' || 
    (isset($_SERVER['SERVER_NAME']) && in_array(strtolower($_SERVER['SERVER_NAME']), ['localhost', '127.0.0.1', '::1'])) ||
    (isset($_SERVER['HTTP_HOST']) && (
        stripos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
        stripos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|127\.|169\.254\.)/', $_SERVER['HTTP_HOST']) ||
        strpos($_SERVER['HTTP_HOST'], '.') === false 
    )) ||
    (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'])) ||
    (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'local')
);

// 2. Dynamic Base URL detection for portability
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('\\', '/', dirname($script_name));
    
    // Normalize base_dir: remove /records or /includes if we are in them
    $base_dir = preg_replace('/(\/records|\/includes)$/', '', $base_dir);
    $base_dir = rtrim($base_dir, '/');
    
    define('BASE_URL', $protocol . "://" . $host . $base_dir);
} else {
    define('BASE_URL', getenv('BASE_URL') ?: '');
}

// 3. Database Connection Logic

// Helper function to get environment variables from any source
// Priority: getenv() -> $_ENV -> $_SERVER
function get_env_var($key, $default = null) {
    $val = getenv($key);
    if ($val === false || $val === null) {
        if (isset($_ENV[$key])) {
            $val = $_ENV[$key];
        } elseif (isset($_SERVER[$key])) {
            $val = $_SERVER[$key];
        }
    }
    
    // Resolve ${VAR} references (Railway specific handling)
    if ($val && is_string($val) && preg_match('/^\$\{(.+)\}$/', $val, $matches)) {
        return get_env_var($matches[1], $default);
    }

    return ($val !== false && $val !== null) ? $val : $default;
}

// Explicit mapping for Railway variables
$dbHost = get_env_var('DB_HOST', get_env_var('MYSQLHOST'));
$dbName = get_env_var('DB_NAME', get_env_var('MYSQLDATABASE'));
$dbUser = get_env_var('DB_USER', get_env_var('MYSQLUSER'));
$dbPass = get_env_var('DB_PASSWORD', get_env_var('MYSQLPASSWORD'));
$dbPort = get_env_var('DB_PORT', get_env_var('MYSQLPORT', 3306));

// Determine environment
$is_railway = (get_env_var('RAILWAY_ENVIRONMENT') || get_env_var('MYSQLHOST') || strpos($_SERVER['HTTP_HOST'] ?? '', 'railway.app') !== false);

// Connection Logic
try {
    if ($dbHost) {
        // MySQL / MariaDB Connection (Railway or Local MySQL)
        if (!in_array('mysql', PDO::getAvailableDrivers())) {
             throw new Exception("PDO MySQL driver is missing. Please ensure 'ext-pdo_mysql' is installed/enabled.");
        }

        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    } else {
        // Fallback to Local SQLite
        $dbPath = __DIR__ . '/../farm.db';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec("PRAGMA foreign_keys = ON;");
    }

} catch (PDOException $e) {
    // Hide password in error message
    $safeMsg = str_replace([$dbPass, 'pass='], ['***', 'pass=***'], $e->getMessage());
    
    // Display a friendly error page
    die("
        <div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; border: 1px solid #ccc; border-radius: 8px; background: #fff9f9;'>
            <h2 style='color: #d32f2f;'>Database Connection Failed</h2>
            <p>Could not connect to the database. Please check your configuration.</p>
            <p><strong>Error:</strong> " . htmlspecialchars($safeMsg) . "</p>
            " . ($is_railway ? "
            <hr>
            <h3>Railway Debugging Tips:</h3>
            <ul>
                <li>Ensure <code>MYSQLHOST</code>, <code>MYSQLDATABASE</code>, etc. are set in Variables.</li>
                <li>If using 'Reference Variables' (e.g. \${MYSQLHOST}), ensure they are resolving.</li>
                <li>Check <a href='" . (defined('BASE_URL') ? BASE_URL : '') . "/test_db_detailed.php'>test_db_detailed.php</a> for more info.</li>
            </ul>
            " : "") . "
        </div>
    ");
} catch (Exception $e) {
    die("Configuration Error: " . $e->getMessage());
}

// Check if tables exist, if not create them (Simple Migration System)
function createTables($pdo) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS weeks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_number INTEGER NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(week_number, start_date)
        )",
        "CREATE TABLE IF NOT EXISTS flock_status (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            age_weeks INTEGER,
            opening_birds INTEGER,
            added_birds INTEGER DEFAULT 0,
            sold_culls INTEGER DEFAULT 0,
            dead_birds INTEGER DEFAULT 0,
            cause_of_death TEXT,
            isolated_birds INTEGER DEFAULT 0,
            closing_birds INTEGER,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS egg_production (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            total_collected INTEGER,
            crates_produced REAL,
            cracked_broken INTEGER DEFAULT 0,
            small_eggs INTEGER DEFAULT 0,
            fed_to_dogs INTEGER DEFAULT 0,
            consumed_home INTEGER DEFAULT 0,
            discarded INTEGER DEFAULT 0,
            saleable_eggs INTEGER,
            eggs_per_bird_day REAL,
            production_percent REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS feed_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            feed_type TEXT,
            stock_start REAL,
            purchased REAL,
            total_available REAL,
            used_bags REAL,
            stock_end REAL,
            cost_purchased REAL,
            avg_feed_per_bird REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS water_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            water_source TEXT,
            treatments_given TEXT,
            cost_treatments REAL,
            repairs_done TEXT,
            notes TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS health_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            vaccinations TEXT,
            diseases_observed TEXT,
            symptoms TEXT,
            drugs_given TEXT,
            treatment_duration TEXT,
            withdrawal_period TEXT,
            vet_visits TEXT,
            vet_recommendations TEXT,
            deaths_linked_illness INTEGER DEFAULT 0,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS biosecurity (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            disinfectant_used TEXT,
            houses_cleaned INTEGER DEFAULT 0,
            footbath_maintained INTEGER DEFAULT 0,
            rodent_control INTEGER DEFAULT 0,
            visitors_recorded TEXT,
            protective_gear_used INTEGER DEFAULT 0,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS labour_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            num_workers INTEGER,
            days_worked_per_worker REAL,
            wages_paid REAL,
            casual_labour_cost REAL,
            meals_cost REAL,
            notes TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            feed_cost REAL DEFAULT 0,
            medicine_cost REAL DEFAULT 0,
            fuel_cost REAL DEFAULT 0,
            repairs_cost REAL DEFAULT 0,
            electricity_cost REAL DEFAULT 0,
            water_cost REAL DEFAULT 0,
            labour_cost REAL DEFAULT 0,
            transport_cost REAL DEFAULT 0,
            supplies_cost REAL DEFAULT 0,
            misc_cost REAL DEFAULT 0,
            total_expenses REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS egg_sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            grade1_sold REAL DEFAULT 0,
            grade2_sold REAL DEFAULT 0,
            grade3_sold REAL DEFAULT 0,
            grade4_sold REAL DEFAULT 0,
            price_per_crate REAL DEFAULT 0,
            start_balance REAL DEFAULT 0,
            total_crates_sold REAL,
            total_sales_value REAL,
            cash_received REAL,
            credit_sales REAL,
            outstanding_balance REAL,
            transport_cost_sales REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS inventory (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            feed_store_bags REAL,
            reorder_needed INTEGER DEFAULT 0,
            drug_store_low TEXT,
            expired_drugs_removed INTEGER DEFAULT 0,
            egg_trays_balance REAL,
            disinfectants_remaining REAL,
            supplies_restock TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS assets_equipment (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            repair_needed TEXT,
            repairs_done TEXT,
            fuel_used_genset REAL,
            vehicle_condition TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS performance_summary (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            mortality_rate REAL,
            production_rate REAL,
            eggs_per_bird REAL,
            feed_per_bird REAL,
            feed_cost_per_egg REAL,
            cost_production_crate REAL,
            total_sales REAL,
            total_expenses REAL,
            gross_profit REAL,
            net_profit REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS manager_notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            challenges TEXT,
            concerns TEXT,
            supplier_issues TEXT,
            market_changes TEXT,
            action_plan TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE,
            password TEXT NOT NULL,
            reset_token TEXT,
            reset_expires DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }
}

// Auto-run schema check ONLY on Localhost (SQLite)
if ($is_localhost) {
    createTables($pdo);
}
