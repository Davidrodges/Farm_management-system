<?php
// c:/Apache24/htdocs/farm_system/records/create.php
require_once '../includes/auth_check.php';
include '../includes/header.php';
?>

<div class="card">
    <h2>Create New Weekly Record</h2>
    <form action="save.php" method="POST">
        
        <!-- Section 1: Week Info (The core link) -->
        <div class="record-section">
            <div class="section-header">Week Information</div>
            <div class="section-body">
                <div class="dashboard-grid">
                    <div class="form-group">
                        <label>Week Number</label>
                        <input type="number" name="week_number" required min="1" max="53" placeholder="e.g., 24">
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" required>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // We will include the sections here to keep this file manageable
        // Section 1: Flock
        include 'partials/01_flock.php';
        
        // Section 2: Egg Production
        include 'partials/02_eggs.php';

        // Section 3: Feed
        include 'partials/03_feed.php';

        // Section 4: Water
        include 'partials/04_water.php';

        // Section 5: Health
        include 'partials/05_health.php';

        // Section 6: Biosecurity
        include 'partials/06_biosecurity.php';

        // Section 7: Labour
        include 'partials/07_labour.php';

        // Section 8: Expenses
        include 'partials/08_expenses.php';

        // Section 9: Egg Sales
        include 'partials/09_sales.php';

        // Section 10: Inventory
        include 'partials/10_inventory.php';

        // Section 11: Assets
        include 'partials/11_assets.php';

        // Section 12: Manager Notes (Performance is calculated, so we don't input it directly usually, 
        // but user requested "Manager Notes" as #13. Performance #12 is calculated.)
        include 'partials/13_notes.php';
        ?>

        <div style="margin-top: 2rem; text-align: right;">
            <button type="submit" class="btn-primary">Save Weekly Record</button>
        </div>

    </form>
</div>

<?php include '../includes/footer.php'; ?>
