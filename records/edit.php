<?php
// c:/Apache24/htdocs/farm_system/records/edit.php
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
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result : []; // Return empty array if not found to avoid null errors
}

// Fetch Week Info
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

?>

<div class="card">
    <h2>Edit Weekly Record: Week <?php echo htmlspecialchars($week['week_number']); ?></h2>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <!-- Section 1: Week Info (The core link) -->
        <div class="record-section">
            <div class="section-header">Week Information</div>
            <div class="section-body">
                <div class="dashboard-grid">
                    <div class="form-group">
                        <label>Week Number</label>
                        <input type="number" name="week_number" required min="1" max="53" value="<?php echo htmlspecialchars($week['week_number']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" required value="<?php echo htmlspecialchars($week['start_date']); ?>">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" required value="<?php echo htmlspecialchars($week['end_date']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // Partials with pre-filled data
        include 'partials/01_flock.php';
        include 'partials/02_eggs.php';
        include 'partials/03_feed.php';
        include 'partials/04_water.php';
        include 'partials/05_health.php';
        include 'partials/06_biosecurity.php';
        include 'partials/07_labour.php';
        include 'partials/08_expenses.php';
        include 'partials/09_sales.php';
        include 'partials/10_inventory.php';
        include 'partials/11_assets.php';
        include 'partials/13_notes.php';
        ?>

        <div style="margin-top: 2rem; text-align: right;">
            <a href="view.php?id=<?php echo $id; ?>" class="btn-primary" style="background: #95a5a6; text-decoration: none; margin-right: 10px;">Cancel</a>
            <button type="submit" class="btn-primary">Update Weekly Record</button>
        </div>

    </form>
</div>

<?php include '../includes/footer.php'; ?>
