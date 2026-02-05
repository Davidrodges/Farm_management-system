<?php
// c:/Apache24/htdocs/farm_system/records/index.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT w.id, w.week_number, w.start_date, w.end_date, 
           f.closing_birds, p.production_rate, p.mortality_rate, p.net_profit
    FROM weeks w
    LEFT JOIN flock_status f ON w.id = f.week_id
    LEFT JOIN performance_summary p ON w.id = p.week_id
    ORDER BY w.week_number DESC
");
$weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Weekly Records History</h2>
        <a href="create.php" class="btn-primary" style="text-decoration: none;">+ New Record</a>
        <a href="export.php" class="btn-primary" style="text-decoration: none; background-color: var(--earth-brown); margin-left: 10px;">⬇ Export CSV</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'created'): ?>
        <p style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0;">Record saved successfully!</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Week #</th>
                <th>Date Range</th>
                <th>Closing Flock</th>
                <th>Production %</th>
                <th>Mortality %</th>
                <th>Net Profit</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weeks as $week): ?>
            <tr>
                <td><?php echo htmlspecialchars($week['week_number']); ?></td>
                <td><?php echo date('M d', strtotime($week['start_date'])); ?> - <?php echo date('M d', strtotime($week['end_date'])); ?></td>
                <td><?php echo number_format($week['closing_birds']); ?></td>
                <td><?php echo number_format($week['production_rate'], 1); ?>%</td>
                <td><?php echo number_format($week['mortality_rate'], 2); ?>%</td>
                <td style="color: <?php echo $week['net_profit'] >= 0 ? 'green' : 'red'; ?>">
                    <?php echo number_format($week['net_profit'], 2); ?>
                </td>
                <td>
                    <a href="view.php?id=<?php echo $week['id']; ?>" style="color: var(--primary-green); font-weight: bold;">View Full</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($weeks)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #777;">No records found. Start by creating one.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
