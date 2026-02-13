<?php
// c:/Apache24/htdocs/farm_system/records/update.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

if (!isset($_POST['id'])) {
    die('ID Missing');
}

$weekId = $_POST['id'];

try {
    $pdo->beginTransaction();

    // 1. Update Week
    $stmt = $pdo->prepare("UPDATE weeks SET week_number = ?, start_date = ?, end_date = ? WHERE id = ?");
    $stmt->execute([
        $_POST['week_number'],
        $_POST['start_date'],
        $_POST['end_date'],
        $weekId
    ]);

    // Helper for optional numbers
    function val($key, $default = 0) {
        return !empty($_POST[$key]) ? $_POST[$key] : $default;
    }
    // Helper for text
    function txt($key) {
        return !empty($_POST[$key]) ? $_POST[$key] : null;
    }

    // 2. Flock
    // Using UPDATE or INSERT ... ON DUPLICATE KEY UPDATE would be better if rows might not exist, 
    // but assuming Create created all rows. To be safe, we could use UPSERT logic or just UPDATE if we trust integrity.
    // For simplicity and matching Create logic, we assume rows exist via week_id foreign key or we use DELETE/INSERT? 
    // DELETE/INSERT changes IDs which might be bad.
    // Let's use UPDATE. IF rows are missing (e.g. added later feature), this fails. 
    // But create.php inserts all rows. So UPDATE is fine.
    
    $stmt = $pdo->prepare("UPDATE flock_status SET age_weeks = ?, opening_birds = ?, added_birds = ?, sold_culls = ?, dead_birds = ?, cause_of_death = ?, isolated_birds = ?, closing_birds = ? WHERE week_id = ?");
    $stmt->execute([
        val('age_weeks'), val('opening_birds'), val('added_birds'), val('sold_culls'), val('dead_birds'), txt('cause_of_death'), val('isolated_birds'), val('closing_birds'), $weekId
    ]);

    // 3. Eggs
    $saleable = val('total_collected') - (val('cracked_broken') + val('fed_to_dogs') + val('consumed_home') + val('discarded'));
    $stmt = $pdo->prepare("UPDATE egg_production SET total_collected = ?, crates_produced = ?, cracked_broken = ?, small_eggs = ?, fed_to_dogs = ?, consumed_home = ?, discarded = ?, saleable_eggs = ? WHERE week_id = ?");
    $stmt->execute([
        val('total_collected'), val('crates_produced'), val('cracked_broken'), val('small_eggs'), val('fed_to_dogs'), val('consumed_home'), val('discarded'), $saleable, $weekId
    ]);

    // 4. Feed
    // Total available update?
    $totalAvailable = val('stock_start') + val('purchased');
    $stmt = $pdo->prepare("UPDATE feed_records SET feed_type = ?, stock_start = ?, purchased = ?, total_available = ?, used_bags = ?, stock_end = ?, cost_purchased = ? WHERE week_id = ?");
    $stmt->execute([
        txt('feed_type'), val('stock_start'), val('purchased'), $totalAvailable, val('used_bags'), val('stock_end'), val('cost_purchased'), $weekId
    ]);

    // 5. Water
    $stmt = $pdo->prepare("UPDATE water_records SET water_source = ?, treatments_given = ?, cost_treatments = ?, repairs_done = ? WHERE week_id = ?");
    $stmt->execute([
        txt('water_source'), txt('treatments_given'), val('cost_treatments'), txt('water_repairs_notes'), $weekId
    ]);

    // 6. Health
    $stmt = $pdo->prepare("UPDATE health_records SET vaccinations = ?, diseases_observed = ?, symptoms = ?, drugs_given = ?, treatment_duration = ?, withdrawal_period = ?, vet_visits = ?, vet_recommendations = ?, deaths_linked_illness = ? WHERE week_id = ?");
    $stmt->execute([
        txt('vaccinations'), txt('diseases_observed'), txt('symptoms'), txt('drugs_given'), txt('treatment_duration'), txt('withdrawal_period'), txt('vet_visits'), txt('vet_recommendations'), val('deaths_linked_illness'), $weekId
    ]);

    // 7. Biosecurity
    $stmt = $pdo->prepare("UPDATE biosecurity SET disinfectant_used = ?, houses_cleaned = ?, footbath_maintained = ?, rodent_control = ?, visitors_recorded = ?, protective_gear_used = ? WHERE week_id = ?");
    $stmt->execute([
        txt('disinfectant_used'), val('houses_cleaned'), val('footbath_maintained'), val('rodent_control'), txt('visitors_recorded'), val('protective_gear_used'), $weekId
    ]);

    // 8. Labour
    $stmt = $pdo->prepare("UPDATE labour_records SET num_workers = ?, days_worked_per_worker = ?, wages_paid = ?, casual_labour_cost = ?, meals_cost = ?, notes = ? WHERE week_id = ?");
    $stmt->execute([
        val('num_workers'), val('days_worked_per_worker'), val('wages_paid'), val('casual_labour_cost'), val('meals_cost'), txt('labour_notes'), $weekId
    ]);

    // 9. Expenses (Calculated Total)
    $totalExpenses = val('feed_cost') + val('medicine_cost') + val('fuel_cost') + val('repairs_cost') + val('electricity_cost') + val('water_cost') + val('labour_cost') + val('transport_cost') + val('supplies_cost') + val('misc_cost');
    $stmt = $pdo->prepare("UPDATE expenses SET feed_cost = ?, medicine_cost = ?, fuel_cost = ?, repairs_cost = ?, electricity_cost = ?, water_cost = ?, labour_cost = ?, transport_cost = ?, supplies_cost = ?, misc_cost = ?, total_expenses = ? WHERE week_id = ?");
    $stmt->execute([
        val('feed_cost'), val('medicine_cost'), val('fuel_cost'), val('repairs_cost'), val('electricity_cost'), val('water_cost'), val('labour_cost'), val('transport_cost'), val('supplies_cost'), val('misc_cost'), $totalExpenses, $weekId
    ]);

    // 10. Sales
    $totalCrates = val('grade1_sold') + val('grade2_sold') + val('grade3_sold') + val('grade4_sold');
    $totalSalesValue = $totalCrates * val('price_per_crate');
    $outstanding = (val('start_balance') + $totalSalesValue) - val('cash_received');
    $stmt = $pdo->prepare("UPDATE egg_sales SET grade1_sold = ?, grade2_sold = ?, grade3_sold = ?, grade4_sold = ?, price_per_crate = ?, start_balance = ?, total_crates_sold = ?, total_sales_value = ?, cash_received = ?, credit_sales = ?, outstanding_balance = ?, transport_cost_sales = ? WHERE week_id = ?");
    $stmt->execute([
        val('grade1_sold'), val('grade2_sold'), val('grade3_sold'), val('grade4_sold'), val('price_per_crate'), val('start_balance'), $totalCrates, $totalSalesValue, val('cash_received'), val('credit_sales'), $outstanding, val('transport_cost_sales'), $weekId
    ]);

    // 11. Inventory
    $stmt = $pdo->prepare("UPDATE inventory SET feed_store_bags = ?, reorder_needed = ?, drug_store_low = ?, expired_drugs_removed = ?, egg_trays_balance = ?, disinfectants_remaining = ?, supplies_restock = ? WHERE week_id = ?");
    $stmt->execute([
        val('feed_store_bags'), val('reorder_needed'), txt('drug_store_low'), val('expired_drugs_removed'), val('egg_trays_balance'), val('disinfectants_remaining'), txt('supplies_restock'), $weekId
    ]);

    // 12. Assets
    $stmt = $pdo->prepare("UPDATE assets_equipment SET repair_needed = ?, repairs_done = ?, fuel_used_genset = ?, vehicle_condition = ? WHERE week_id = ?");
    $stmt->execute([
        txt('repair_needed'), txt('repairs_done'), val('fuel_used_genset'), txt('vehicle_condition'), $weekId
    ]);

    // 13. Notes
    $stmt = $pdo->prepare("UPDATE manager_notes SET challenges = ?, concerns = ?, supplier_issues = ?, market_changes = ?, action_plan = ? WHERE week_id = ?");
    $stmt->execute([
        txt('challenges'), txt('concerns'), txt('supplier_issues'), txt('market_changes'), txt('action_plan'), $weekId
    ]);

    // 14. Performance Calculations
    $opening = val('opening_birds');
    $closing = val('closing_birds');
    $avgBirds = ($opening > 0 && $closing > 0) ? ($opening + $closing) / 2 : $opening;
    
    $mortality = ($opening > 0) ? (val('dead_birds') / $opening) * 100 : 0;
    
    $totalEggs = val('total_collected');
    $productionPct = ($avgBirds > 0) ? ($totalEggs / $avgBirds / 7) * 100 : 0;
    
    $eggsPerBird = ($avgBirds > 0) ? $totalEggs / $avgBirds : 0;
    
    $feedUsed = val('used_bags');
    $feedPerBird = ($avgBirds > 0) ? $feedUsed / $avgBirds : 0;
    
    $feedCost = val('feed_cost');
    $feedCostPerEgg = ($totalEggs > 0) ? $feedCost / $totalEggs : 0;

    $cratesProduced = val('crates_produced');
    $costPerCrate = ($cratesProduced > 0) ? $totalExpenses / $cratesProduced : 0;

    $grossProfit = $totalSalesValue - $totalExpenses;
    $netProfit = $grossProfit;

    $stmt = $pdo->prepare("UPDATE performance_summary SET mortality_rate = ?, production_rate = ?, eggs_per_bird = ?, feed_per_bird = ?, feed_cost_per_egg = ?, cost_production_crate = ?, total_sales = ?, total_expenses = ?, gross_profit = ?, net_profit = ? WHERE week_id = ?");
    $stmt->execute([
        $mortality, $productionPct, $eggsPerBird, $feedPerBird, $feedCostPerEgg, $costPerCrate, $totalSalesValue, $totalExpenses, $grossProfit, $netProfit, $weekId
    ]);

    $pdo->commit();
    header("Location: ../index.php?status=updated");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error updating record: " . $e->getMessage());
}
