<?php
// c:/Apache24/htdocs/farm_system/config/db.php

$dbPath = __DIR__ . '/../farm.db';

try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:' . $dbPath);
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Enable foreign keys
    $pdo->exec("PRAGMA foreign_keys = ON;");

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if tables exist, if not create them (Simple Migration System)
function createTables($pdo) {
    $queries = [
        // 1. Weeks Table (The core record)
        "CREATE TABLE IF NOT EXISTS weeks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_number INTEGER NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(week_number, start_date)
        )",
        
        // 1. Weekly Flock Status
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

        // 2. Weekly Egg Production
        "CREATE TABLE IF NOT EXISTS egg_production (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            total_collected INTEGER,
            crates_produced REAL, -- Can be decimal
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

        // 3. Weekly Feed Record
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

        // 4. Weekly Water Record
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

        // 5. Weekly Health Record
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

        // 6. Weekly Biosecurity
        "CREATE TABLE IF NOT EXISTS biosecurity (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            disinfectant_used TEXT,
            houses_cleaned INTEGER DEFAULT 0, -- 1=Yes, 0=No
            footbath_maintained INTEGER DEFAULT 0,
            rodent_control INTEGER DEFAULT 0,
            visitors_recorded TEXT,
            protective_gear_used INTEGER DEFAULT 0,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",

        // 7. Weekly Labour Record
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

        // 8. Weekly Expenses
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

        // 9. Weekly Egg Sales
        "CREATE TABLE IF NOT EXISTS egg_sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            grade1_sold REAL DEFAULT 0,
            grade2_sold REAL DEFAULT 0,
            grade3_sold REAL DEFAULT 0,
            grade4_sold REAL DEFAULT 0,
            price_per_crate REAL DEFAULT 0,
            start_balance REAL DEFAULT 0, -- Outstanding balances from previous
            total_crates_sold REAL,
            total_sales_value REAL,
            cash_received REAL,
            credit_sales REAL,
            outstanding_balance REAL,
            transport_cost_sales REAL,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",

        // 10. Weekly Inventory Check
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

        // 11. Weekly Asset & Equipment
        "CREATE TABLE IF NOT EXISTS assets_equipment (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            week_id INTEGER NOT NULL,
            repair_needed TEXT,
            repairs_done TEXT,
            fuel_used_genset REAL,
            vehicle_condition TEXT,
            FOREIGN KEY(week_id) REFERENCES weeks(id) ON DELETE CASCADE
        )",

        // 12. Weekly Performance (Calculated fields stored for history or computed on fly)
        // We will compute these mostly on the fly, but storing snapshots is good
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

        // 13. Weekly Manager Notes
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

        // 14. Users Table (Authentication)
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

// Auto-run schema check
createTables($pdo);
