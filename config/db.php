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
// Mapper function to unify Railway and Standard environment variables
function mapRailwayVariables() {
    $map = [
        'MYSQLHOST'     => 'DB_HOST',
        'MYSQLDATABASE' => 'DB_NAME',
        'MYSQLUSER'     => 'DB_USER',
        'MYSQLPASSWORD' => 'DB_PASSWORD',
        'MYSQLPORT'     => 'DB_PORT'
    ];
    foreach ($map as $railwayVar => $standardVar) {
        // Check all sources
        $val = getenv($railwayVar) ?: ($_ENV[$railwayVar] ?? ($_SERVER[$railwayVar] ?? null));
        
        if ($val && !(getenv($standardVar) ?: ($_ENV[$standardVar] ?? ($_SERVER[$standardVar] ?? null)))) {
            putenv("$standardVar=$val");
            $_ENV[$standardVar] = $val;
            $_SERVER[$standardVar] = $val;
        }
    }
}
mapRailwayVariables();

// Helper to get variable from any source
function get_db_var($name) {
    return getenv($name) ?: ($_ENV[$name] ?? ($_SERVER[$name] ?? null));
}

// Unify connection variables
$dbHost = get_db_var('DB_HOST') ?: get_db_var('MYSQLHOST');
$dbPort = get_db_var('DB_PORT') ?: get_db_var('MYSQLPORT') ?: '3306';
$dbName = get_db_var('DB_NAME') ?: get_db_var('MYSQLDATABASE');
$dbUser = get_db_var('DB_USER') ?: get_db_var('MYSQLUSER');
$dbPass = get_db_var('DB_PASSWORD') ?: get_db_var('DB_PASS') ?: get_db_var('MYSQLPASSWORD');

// Alternative: Parse MYSQL_URL if available
$mysqlUrl = get_db_var('MYSQL_URL');
if ($mysqlUrl && ($url = parse_url($mysqlUrl))) {
    $dbHost = $url['host'] ?? $dbHost;
    $dbPort = $url['port'] ?? $dbPort;
    $dbName = ltrim($url['path'] ?? '', '/') ?: $dbName;
    $dbUser = $url['user'] ?? $dbUser;
    $dbPass = $url['pass'] ?? $dbPass;
}

$is_railway = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false) || getenv('RAILWAY_ENVIRONMENT');

if ($dbHost && $dbName) {
    // Railway / Production Environment
    if (!in_array('mysql', PDO::getAvailableDrivers())) {
        die("Critical Error: PDO MySQL driver not found. Please ensure your composer.json is present to install drivers on Railway.");
    }

    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("<b>Database Connection Failed:</b> " . $e->getMessage() . "<br><br>
             Please check your Railway Variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD).");
    }
} elseif ($is_railway) {
    // On Railway but variables are missing
    die("<b>Error: Missing Database Configuration on Railway.</b><br><br>
         Please add these Variable references in your Railway Dashboard:<br>
         <code>DB_HOST</code> = \${MYSQLHOST}<br>
         <code>DB_NAME</code> = \${MYSQLDATABASE}<br>
         <code>DB_USER</code> = \${MYSQLUSER}<br>
         <code>DB_PASSWORD</code> = \${MYSQLPASSWORD}<br>");
} elseif (!$is_localhost) {
    // Fallback to Hardcoded InfinityFree Settings (Legacy)
    if (!in_array('mysql', PDO::getAvailableDrivers())) {
        die("Environment Error: MySQL PDO driver not found.");
    }

    $host = "sql107.infinityfree.com";
    $dbname = "if0_41076298_farmsystem";
    $username = "if0_41076298";
    $password = "1234";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("InfinityFree Connection failed: " . $e->getMessage());
    }
} else {
    // Local Development Settings (SQLite)
    $dbPath = __DIR__ . '/../farm.db';
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("PRAGMA foreign_keys = ON;");
    } catch(PDOException $e) {
        die("Local Connection failed: " . $e->getMessage());
    }
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
