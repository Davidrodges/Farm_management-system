<?php
// c:/Apache24/htdocs/farm_system/records/export.php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=farm_records_export_' . date('Y-m-d') . '.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Query to get EVERYTHING. 
// We explicitly select columns to avoid 'id' collisions if we want to be neat, 
// but for a quick raw dump, select * with aliases is easier, or just let PHP PDO handle it (duplicate keys might be lost in FETCH_ASSOC).
// Best approach: List explicit important columns or ALL columns from tables.
// Let's do a massive LEFT JOIN.

$sql = "
SELECT 
    w.week_number, w.start_date, w.end_date,
    -- Performance (Calculated)
    p.production_rate as 'prod_rate_pct',
    p.mortality_rate as 'mortality_pct',
    p.net_profit,
    p.total_sales,
    p.total_expenses,
    -- Flock
    f.opening_birds, f.closing_birds, f.dead_birds, f.added_birds, f.sold_culls,
    -- Eggs
    e.total_collected, e.saleable_eggs, e.crates_produced,
    -- Feed
    fr.feed_type, fr.used_bags, fr.purchased as 'feed_purchased',
    -- Sales
    s.total_crates_sold, s.cash_received, s.outstanding_balance,
    -- Expenses Categories
    ex.feed_cost, ex.medicine_cost, ex.labour_cost, ex.fuel_cost,
    -- Labour
    l.num_workers, l.wages_paid,
    -- Notes
    n.challenges, n.action_plan
    
    -- (We can add more specific columns if needed, but this covers the key metrics)
FROM weeks w
LEFT JOIN performance_summary p ON w.id = p.week_id
LEFT JOIN flock_status f ON w.id = f.week_id
LEFT JOIN egg_production e ON w.id = e.week_id
LEFT JOIN feed_records fr ON w.id = fr.week_id
LEFT JOIN egg_sales s ON w.id = s.week_id
LEFT JOIN expenses ex ON w.id = ex.week_id
LEFT JOIN labour_records l ON w.id = l.week_id
LEFT JOIN manager_notes n ON w.id = n.week_id
ORDER BY w.week_number DESC
";

$stmt = $pdo->query($sql);

// Output the column headings
// We can't use fetch(PDO::FETCH_ASSOC) keys readily if result is empty, 
// so we fetch the first row, output keys, then output row, then loop rest.
$firstRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($firstRow) {
    fputcsv($output, array_keys($firstRow));
    fputcsv($output, $firstRow);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} else {
    // Empty CSV with headers if possible, or just message
    fputcsv($output, ['No records found']);
}

fclose($output);
exit;
