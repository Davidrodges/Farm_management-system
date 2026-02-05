<?php
// simulation_test.php
// This script simulates a full POST request to save.php to verify the logic.

require_once 'config/db.php';

echo "Building simulated POST data...\n";

$_POST = [
    'week_number' => 888,
    'start_date' => '2026-02-01',
    'end_date' => '2026-02-07',
    
    // 01_flock
    'age_weeks' => 20,
    'opening_birds' => 1000,
    'added_birds' => 0,
    'sold_culls' => 0,
    'dead_birds' => 2,
    'cause_of_death' => 'Heat',
    'isolated_birds' => 5,
    'closing_birds' => 998,
    
    // 02_eggs
    'total_collected' => 7000,
    'cracked_broken' => 10,
    'small_eggs' => 5,
    'fed_to_dogs' => 5,
    'consumed_home' => 5,
    'discarded' => 0,
    'crates_produced' => 233.3,
    
    // 03_feed
    'feed_type' => 'Layers Mash',
    'stock_start' => 50,
    'purchased' => 20,
    'cost_purchased' => 4000,
    'used_bags' => 15,
    'stock_end' => 55,
    
    // 04_water
    'water_source' => 'Borehole',
    'treatments_given' => 'Chlorine',
    'cost_treatments' => 50,
    'water_repairs_notes' => 'None',
    
    // 05_health
    'vaccinations' => 'Gumboro',
    'diseases_observed' => 'None',
    'symptoms' => 'None',
    'drugs_given' => 'Vitamins',
    'treatment_duration' => '3 days',
    'withdrawal_period' => 'None',
    'vet_visits' => 'Dr. Smith',
    'vet_recommendations' => 'Keep it cool',
    'deaths_linked_illness' => 0,
    
    // 06_biosecurity
    'disinfectant_used' => 'Virkon',
    'houses_cleaned' => 1,
    'footbath_maintained' => 1,
    'rodent_control' => 1,
    'protective_gear_used' => 1,
    'visitors_recorded' => 'None',
    
    // 07_labour
    'num_workers' => 2,
    'days_worked_per_worker' => 7,
    'wages_paid' => 200,
    'casual_labour_cost' => 0,
    'meals_cost' => 50,
    'labour_notes' => 'Good performance',
    
    // 08_expenses
    'feed_cost' => 1000,
    'medicine_cost' => 100,
    'fuel_cost' => 50,
    'repairs_cost' => 0,
    'electricity_cost' => 30,
    'water_cost' => 10,
    'labour_cost' => 250,
    'transport_cost' => 20,
    'supplies_cost' => 10,
    'misc_cost' => 5,
    
    // 09_sales
    'grade1_sold' => 200,
    'grade2_sold' => 30,
    'grade3_sold' => 0,
    'grade4_sold' => 0,
    'price_per_crate' => 10,
    'cash_received' => 2300,
    'credit_sales' => 0,
    'start_balance' => 0,
    'transport_cost_sales' => 50,
    
    // 10_inventory
    'feed_store_bags' => 55,
    'reorder_needed' => 0,
    'drug_store_low' => 'None',
    'expired_drugs_removed' => 0,
    'egg_trays_balance' => 100,
    'disinfectants_remaining' => 10,
    'supplies_restock' => 'None',
    
    // 11_assets
    'repair_needed' => 'None',
    'repairs_done' => 'None',
    'fuel_used_genset' => 5,
    'vehicle_condition' => 'Good',
    
    // 13_notes
    'challenges' => 'High heat',
    'concerns' => 'None',
    'supplier_issues' => 'None',
    'market_changes' => 'Stable',
    'action_plan' => 'Increase vent'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Cleaning up any old test records for week 888...\n";
$pdo->exec("DELETE FROM weeks WHERE week_number = 888");

echo "Attempting to include save.php logic from a simulation perspective...\n";
// We don't want to actually header() or die() in CLI so we can catch errors
ob_start();

// Mocking some functions that might be in global scope if save.php was called via web
function header_mock($url) {
    echo "REDIRECTED TO: $url\n";
}

// We'll read save.php and eval it or just include it.
// Better to just run it and see if it dies.
try {
    // We need to bypass auth check if we are in CLI
    session_start();
    $_SESSION['user_id'] = 1;
    
    // Include the actual logic
    // We need to be in the records directory or ensure save.php handles its own includes
    include __DIR__ . '/records/save.php';
    
} catch (Exception $e) {
    echo "CATCHED EXCEPTION: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
echo "OUTPUT: \n" . $output . "\n";

// Verify database
echo "Verifying database entries...\n";
$week = $pdo->query("SELECT * FROM weeks WHERE week_number = 888")->fetch();
if ($week) {
    echo "SUCCESS: Week 888 created (ID: {$week['id']})\n";
    
    $perf = $pdo->query("SELECT * FROM performance_summary WHERE week_id = {$week['id']}")->fetch();
    if ($perf) {
        echo "SUCCESS: Performance Summary created.\n";
        echo "Net Profit: " . $perf['net_profit'] . "\n";
    } else {
        echo "FAIL: Performance Summary NOT found.\n";
    }
} else {
    echo "FAIL: Week record NOT created.\n";
}

// History Check Simulation
echo "Simulating History History View Query...\n";
$stmt = $pdo->query("
    SELECT w.id, w.week_number, w.start_date, w.end_date, 
           f.closing_birds, p.production_rate, p.mortality_rate, p.net_profit
    FROM weeks w
    LEFT JOIN flock_status f ON w.id = f.week_id
    LEFT JOIN performance_summary p ON w.id = p.week_id
    WHERE w.week_number = 888
");
$weeks_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($weeks_history) > 0) {
    echo "SUCCESS: History view query works.\n";
    print_r($weeks_history[0]);
} else {
    echo "FAIL: History view query failed to find data.\n";
}
