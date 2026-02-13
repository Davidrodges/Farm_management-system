<?php
// c:/Apache24/htdocs/farm_system/records/view.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
include __DIR__ . '/../includes/header.php';

if (!isset($_GET['id'])) {
    die("ID missing");
}

$id = $_GET['id'];

// Helper to fetch one row
function getRow($pdo, $table, $weekId) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE week_id = ?");
    $stmt->execute([$weekId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch Week
$stmt = $pdo->prepare("SELECT * FROM weeks WHERE id = ?");
$stmt->execute([$id]);
$week = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$week) {
    die("Record not found");
}

// Fetch all sections
$flock = getRow($pdo, 'flock_status', $id);
$eggs = getRow($pdo, 'egg_production', $id);
$feed = getRow($pdo, 'feed_records', $id);
$water = getRow($pdo, 'water_records', $id);
$health = getRow($pdo, 'health_records', $id);
$bio = getRow($pdo, 'biosecurity', $id);
$labour = getRow($pdo, 'labour_records', $id);
$expenses = getRow($pdo, 'expenses', $id);
$sales = getRow($pdo, 'egg_sales', $id);
$inventory = getRow($pdo, 'inventory', $id);
$assets = getRow($pdo, 'assets_equipment', $id);
$notes = getRow($pdo, 'manager_notes', $id);
$perf = getRow($pdo, 'performance_summary', $id);

?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;" class="no-print">
        <h2>Week <?php echo $week['week_number']; ?> Record (<?php echo $week['start_date']; ?> to <?php echo $week['end_date']; ?>)</h2>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn-primary" style="background: #e67e22; text-decoration: none; margin-right: 10px;">✏️ Edit Record</a>
            <button onclick="window.print()" class="btn-primary" style="background: var(--dark-green); margin-right: 10px;">🖨️ Print Report</button>
            <a href="index.php" class="btn-primary" style="background: #95a5a6; text-decoration: none;">&larr; Back to List</a>
        </div>
    </div>

    <!-- 12. PERFORMANCE SUMMARY (Top priority to see) -->
    <div class="card" style="border-top: 4px solid var(--earth-brown);">
        <h3>12. Performance Summary 📊</h3>
        <div class="dashboard-grid">
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['production_rate'], 1); ?>%</span>
                <span class="stat-label">Production</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['mortality_rate'], 2); ?>%</span>
                <span class="stat-label">Mortality</span>
            </div>
             <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['net_profit'], 2); ?></span>
                <span class="stat-label">Net Profit</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['eggs_per_bird'], 2); ?></span>
                <span class="stat-label">Eggs/Bird</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['feed_per_bird'], 2); ?></span>
                <span class="stat-label">Feed/Bird (Bags)</span>
            </div>
             <div class="stat-card">
                <span class="stat-value"><?php echo number_format($perf['cost_production_crate'], 2); ?></span>
                <span class="stat-label">Cost / Crate</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- 1. FLOCK -->
        <div class="card">
            <h4>1. Flock Status</h4>
            <p><strong>Opening:</strong> <?php echo $flock['opening_birds']; ?></p>
            <p><strong>Closing:</strong> <?php echo $flock['closing_birds']; ?></p>
            <p><strong>Deaths:</strong> <?php echo $flock['dead_birds']; ?> (<?php echo $flock['cause_of_death']; ?>)</p>
            <p><strong>Added:</strong> <?php echo $flock['added_birds']; ?> | <strong>Sold:</strong> <?php echo $flock['sold_culls']; ?></p>
        </div>

        <!-- 2. EGGS -->
        <div class="card">
            <h4>2. Egg Production</h4>
            <p><strong>Total Collected:</strong> <?php echo $eggs['total_collected']; ?></p>
            <p><strong>Broken/Cracked:</strong> <?php echo $eggs['cracked_broken']; ?></p>
            <p><strong>Saleable:</strong> <?php echo $eggs['saleable_eggs']; ?></p>
            <p><strong>Crates Packed:</strong> <?php echo $eggs['crates_produced']; ?></p>
        </div>

        <!-- 3. FEED -->
        <div class="card">
            <h4>3. Feed Record</h4>
            <p><strong>Type:</strong> <?php echo $feed['feed_type']; ?></p>
            <p><strong>Used (Bags):</strong> <?php echo $feed['used_bags']; ?></p>
            <p><strong>Purchased:</strong> <?php echo $feed['purchased']; ?> (Cost: <?php echo $feed['cost_purchased']; ?>)</p>
            <p><strong>Stock End:</strong> <?php echo $feed['stock_end']; ?></p>
        </div>
        
        <!-- 9. SALES -->
        <div class="card">
            <h4>9. Egg Sales</h4>
            <p><strong>Total Value:</strong> <?php echo number_format($sales['total_sales_value'], 2); ?></p>
            <p><strong>Cash Received:</strong> <?php echo number_format($sales['cash_received'], 2); ?></p>
            <p><strong>Outstanding:</strong> <?php echo number_format($sales['outstanding_balance'], 2); ?></p>
            <p><strong>Crates Sold:</strong> <?php echo $sales['total_crates_sold']; ?></p>
        </div>
    </div>

    <!-- 8. EXPENSES DETAIL -->
    <div class="card">
        <h4>8. Weekly Expenses Breakdown</h4>
        <div class="dashboard-grid">
            <p>Feed: <?php echo $expenses['feed_cost']; ?></p>
            <p>Meds: <?php echo $expenses['medicine_cost']; ?></p>
            <p>Fuel: <?php echo $expenses['fuel_cost']; ?></p>
            <p>Labour: <?php echo $expenses['labour_cost']; ?></p>
            <p>Repairs: <?php echo $expenses['repairs_cost']; ?></p>
            <p>Misc: <?php echo $expenses['misc_cost']; ?></p>
            <p><strong>Total: <?php echo $expenses['total_expenses']; ?></strong></p>
        </div>
    </div>

    <!-- 13. NOTES -->
    <div class="card">
        <h4>13. Manager Notes</h4>
        <p><strong>Challenges:</strong> <?php echo $notes['challenges']; ?></p>
        <p><strong>Action Plan:</strong> <?php echo $notes['action_plan']; ?></p>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
