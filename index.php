<?php
require_once 'config/db.php';
require_once 'includes/auth_check.php';
include 'includes/header.php';
?>

<div class="card">
    <h2>Welcome to the Farm Management System</h2>
    <p>Manage your weekly farm records efficiently.</p>
    
    <div class="dashboard-grid" style="margin-top: 2rem;">
        <div class="card stat-card">
            <span class="stat-value">Start</span>
            <span class="stat-label">New Week Record</span>
            <a href="records/create.php" class="btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">Create Record</a>
        </div>
        
        <div class="card stat-card">
            <span class="stat-value">View</span>
            <span class="stat-label">Past Records</span>
            <a href="records/index.php" class="btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">View History</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
