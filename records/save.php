<?php
// c:/Apache24/htdocs/farm_system/records/save.php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

try {
    $pdo->beginTransaction();

    // 1. Insert Week
    $stmt = $pdo->prepare("INSERT INTO weeks (week_number, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['week_number'],
        $_POST['start_date'],
        $_POST['end_date']
    ]);
    $weekId = $pdo->lastInsertId();

    // Helper for optional numbers
    function val($key, $default = 0) {
        return !empty($_POST[$key]) ? $_POST[$key] : $default;
    }
    // Helper for text
    function txt($key) {
        return !empty($_POST[$key]) ? $_POST[$key] : null;
    }

    // 2. Flock
    $stmt = $pdo->prepare("INSERT INTO flock_status (week_id, age_weeks, opening_birds, added_birds, sold_culls, dead_birds, cause_of_death, isolated_birds, closing_birds) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        val('age_weeks'),
        val('opening_birds'),
        val('added_birds'),
        val('sold_culls'),
        val('dead_birds'),
        txt('cause_of_death'),
        val('isolated_birds'),
        val('closing_birds')
    ]);

    // 3. Eggs
    $stmt = $pdo->prepare("INSERT INTO egg_production (week_id, total_collected, crates_produced, cracked_broken, small_eggs, fed_to_dogs, consumed_home, discarded, saleable_eggs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // simple calc for saleable if not provided: collected - (broken + fed + consumed + discarded)
    $saleable = val('total_collected') - (val('cracked_broken') + val('fed_to_dogs') + val('consumed_home') + val('discarded'));
    $stmt->execute([
        $weekId,
        val('total_collected'),
        val('crates_produced'),
        val('cracked_broken'),
        val('small_eggs'),
        val('fed_to_dogs'),
        val('consumed_home'),
        val('discarded'),
        $saleable
    ]);

    // 4. Feed
    $stmt = $pdo->prepare("INSERT INTO feed_records (week_id, feed_type, stock_start, purchased, total_available, used_bags, stock_end, cost_purchased, avg_feed_per_bird) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('feed_type'),
        val('stock_start'),
        val('purchased'),
        val('stock_start') + val('purchased'), // Total Available
        val('used_bags'),
        val('stock_end'),
        val('cost_purchased'),
        0 // Calculated later
    ]);

    // 5. Water
    $stmt = $pdo->prepare("INSERT INTO water_records (week_id, water_source, treatments_given, cost_treatments, repairs_done, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('water_source'),
        txt('treatments_given'),
        val('cost_treatments'),
        txt('repairs_done'), // field name mistmatch in partial? check Partial 4: name="water_repairs_notes"
        txt('water_repairs_notes')
    ]);

    // 6. Health
    $stmt = $pdo->prepare("INSERT INTO health_records (week_id, vaccinations, diseases_observed, symptoms, drugs_given, treatment_duration, withdrawal_period, vet_visits, vet_recommendations, deaths_linked_illness) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('vaccinations'),
        txt('diseases_observed'),
        txt('symptoms'),
        txt('drugs_given'),
        txt('treatment_duration'),
        txt('withdrawal_period'),
        txt('vet_visits'),
        txt('vet_recommendations'),
        val('deaths_linked_illness')
    ]);

    // 7. Biosecurity
    $stmt = $pdo->prepare("INSERT INTO biosecurity (week_id, disinfectant_used, houses_cleaned, footbath_maintained, rodent_control, visitors_recorded, protective_gear_used) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('disinfectant_used'),
        val('houses_cleaned'),
        val('footbath_maintained'),
        val('rodent_control'),
        txt('visitors_recorded'),
        val('protective_gear_used')
    ]);

    // 8. Labour
    $stmt = $pdo->prepare("INSERT INTO labour_records (week_id, num_workers, days_worked_per_worker, wages_paid, casual_labour_cost, meals_cost, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        val('num_workers'),
        val('days_worked_per_worker'),
        val('wages_paid'),
        val('casual_labour_cost'),
        val('meals_cost'),
        txt('labour_notes')
    ]);

    // 9. Expenses (Calculated Total)
    $stmt = $pdo->prepare("INSERT INTO expenses (week_id, feed_cost, medicine_cost, fuel_cost, repairs_cost, electricity_cost, water_cost, labour_cost, transport_cost, supplies_cost, misc_cost, total_expenses) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $totalExpenses = val('feed_cost') + val('medicine_cost') + val('fuel_cost') + val('repairs_cost') + val('electricity_cost') + val('water_cost') + val('labour_cost') + val('transport_cost') + val('supplies_cost') + val('misc_cost');
    $stmt->execute([
        $weekId,
        val('feed_cost'),
        val('medicine_cost'),
        val('fuel_cost'),
        val('repairs_cost'),
        val('electricity_cost'),
        val('water_cost'),
        val('labour_cost'),
        val('transport_cost'),
        val('supplies_cost'),
        val('misc_cost'),
        $totalExpenses
    ]);

    // 10. Sales
    $stmt = $pdo->prepare("INSERT INTO egg_sales (week_id, grade1_sold, grade2_sold, grade3_sold, grade4_sold, price_per_crate, start_balance, total_crates_sold, total_sales_value, cash_received, credit_sales, outstanding_balance, transport_cost_sales) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $totalCrates = val('grade1_sold') + val('grade2_sold') + val('grade3_sold') + val('grade4_sold');
    // Assuming simple price per crate average or total? User input price per crate.
    // Total Value roughly = total crates * price. Or user inputs exact sales value? User didn't have 'total value' input, so we calc.
    $totalSalesValue = $totalCrates * val('price_per_crate');
    $outstanding = (val('start_balance') + $totalSalesValue) - val('cash_received'); // Simplified logic

    $stmt->execute([
        $weekId,
        val('grade1_sold'),
        val('grade2_sold'),
        val('grade3_sold'),
        val('grade4_sold'),
        val('price_per_crate'),
        val('start_balance'),
        $totalCrates,
        $totalSalesValue,
        val('cash_received'),
        val('credit_sales'), // Or explicit field
        $outstanding,
        val('transport_cost_sales')
    ]);

    // 11. Inventory
    $stmt = $pdo->prepare("INSERT INTO inventory (week_id, feed_store_bags, reorder_needed, drug_store_low, expired_drugs_removed, egg_trays_balance, disinfectants_remaining, supplies_restock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        val('feed_store_bags'),
        val('reorder_needed'),
        txt('drug_store_low'),
        val('expired_drugs_removed'),
        val('egg_trays_balance'),
        val('disinfectants_remaining'),
        txt('supplies_restock')
    ]);

    // 12. Assets
    $stmt = $pdo->prepare("INSERT INTO assets_equipment (week_id, repair_needed, repairs_done, fuel_used_genset, vehicle_condition) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('repair_needed'),
        txt('repairs_done'),
        val('fuel_used_genset'),
        txt('vehicle_condition')
    ]);

    // 13. Notes
    $stmt = $pdo->prepare("INSERT INTO manager_notes (week_id, challenges, concerns, supplier_issues, market_changes, action_plan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        txt('challenges'),
        txt('concerns'),
        txt('supplier_issues'),
        txt('market_changes'),
        txt('action_plan')
    ]);

    // 14. Performance Calculations
    // Mortality % = (Deaths / Opening Birds) * 100
    // Production % = (Total Eggs / Average Live Birds / 7) * 100
    // Eggs per Bird = Total Eggs / Average Live Birds
    // Feed per Bird = Total Feed Used / Average Live Birds
    // Feed Cost per Egg = Total Feed Cost / Total Eggs
    // Cost of Production per Crate = (Total Expenses / Total Crates Produced)
    
    $opening = val('opening_birds');
    $closing = val('closing_birds');
    $avgBirds = ($opening > 0 && $closing > 0) ? ($opening + $closing) / 2 : $opening; // fallback
    
    $mortality = ($opening > 0) ? (val('dead_birds') / $opening) * 100 : 0;
    
    $totalEggs = val('total_collected');
    $productionPct = ($avgBirds > 0) ? ($totalEggs / $avgBirds / 7) * 100 : 0;
    
    $eggsPerBird = ($avgBirds > 0) ? $totalEggs / $avgBirds : 0;
    
    $feedUsed = val('used_bags'); // Assuming bags... user might want kg calc but assuming unitless ratio is okay for now or needs conversion 50kg?
    // User didn't specify kg/bag. Let's assume bags.
    $feedPerBird = ($avgBirds > 0) ? $feedUsed / $avgBirds : 0; 

    // Feed cost per egg. Using 'feed_cost' from expenses (or feed record purchase cost which is deceptive if stock used). 
    // Best to use cost of feed consumed? We don't have cost per bag in usage well defined, only 'Purchased'. 
    // Let's use Expense Feed Cost.
    $feedCost = val('feed_cost');
    $feedCostPerEgg = ($totalEggs > 0) ? $feedCost / $totalEggs : 0;

    $cratesProduced = val('crates_produced'); // Or $totalCrates sold? User has production crates vs sales crates. Use production.
    $costPerCrate = ($cratesProduced > 0) ? $totalExpenses / $cratesProduced : 0;

    $grossProfit = $totalSalesValue - $totalExpenses; // Simplistic
    $netProfit = $grossProfit;

    $stmt = $pdo->prepare("INSERT INTO performance_summary (week_id, mortality_rate, production_rate, eggs_per_bird, feed_per_bird, feed_cost_per_egg, cost_production_crate, total_sales, total_expenses, gross_profit, net_profit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $weekId,
        $mortality,
        $productionPct,
        $eggsPerBird,
        $feedPerBird,
        $feedCostPerEgg,
        $costPerCrate,
        $totalSalesValue,
        $totalExpenses,
        $grossProfit,
        $netProfit
    ]);

    $pdo->commit();
    header("Location: index.php?status=created");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error saving record: " . $e->getMessage());
}
