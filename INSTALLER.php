<?php
/**
 * Farm System Auto-Installer
 * Upload ONLY this file to your InfinityFree htdocs folder
 * Then visit: http://your-site.rf.gd/INSTALLER.php
 */

// Step 1: Check if folders exist
$folders_needed = ['config', 'includes', 'assets', 'assets/css', 'records', 'records/partials'];
$missing_folders = [];

foreach ($folders_needed as $folder) {
    if (!is_dir($folder)) {
        $missing_folders[] = $folder;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Farm System Installer</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
        .btn { background: #2ecc71; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #27ae60; }
    </style>
</head>
<body>
    <h1>🚀 Farm System Installer</h1>
    
    <h2>Step 1: Folder Structure Check</h2>
    <?php if (empty($missing_folders)): ?>
        <p class="success">✅ All required folders exist!</p>
    <?php else: ?>
        <p class="error">❌ Missing folders detected:</p>
        <ul>
            <?php foreach ($missing_folders as $folder): ?>
                <li><?php echo htmlspecialchars($folder); ?></li>
            <?php endforeach; ?>
        </ul>
        
        <form method="post">
            <button type="submit" name="create_folders" class="btn">Create Missing Folders</button>
        </form>
        
        <?php
        if (isset($_POST['create_folders'])) {
            echo "<h3>Creating folders...</h3>";
            foreach ($folders_needed as $folder) {
                if (!is_dir($folder)) {
                    if (mkdir($folder, 0755, true)) {
                        echo "<p class='success'>✅ Created: $folder</p>";
                    } else {
                        echo "<p class='error'>❌ Failed to create: $folder</p>";
                    }
                }
            }
            echo "<p><strong>Refresh this page to continue.</strong></p>";
        }
        ?>
    <?php endif; ?>
    
    <hr>
    
    <h2>Step 2: Instructions</h2>
    <p>Since I cannot embed all files in a single PHP script, here's what you need to do:</p>
    
    <ol>
        <li><strong>Download the zip file</strong> from your computer: <code>C:\Apache24\htdocs\davina_rodgers_farm_system.zip</code></li>
        <li><strong>Extract it on your computer</strong> to see all the files</li>
        <li><strong>Upload files to the correct folders:</strong></li>
    </ol>
    
    <h3>Upload Guide:</h3>
    <pre>
📁 config/
   └── db.php (IMPORTANT: Edit password before uploading!)

📁 includes/
   ├── header.php
   ├── footer.php
   └── auth_check.php

📁 assets/css/
   └── style.css

📁 records/
   ├── index.php
   ├── create.php
   ├── save.php
   ├── view.php
   ├── export.php
   └── print.php

📁 records/partials/
   ├── 01_flock.php
   ├── 02_eggs.php
   ├── 03_feed.php
   ├── 04_water.php
   ├── 05_health.php
   ├── 06_biosecurity.php
   ├── 07_labour.php
   ├── 08_expenses.php
   ├── 09_sales.php
   ├── 10_inventory.php
   ├── 11_assets.php
   ├── 12_performance.php
   └── 13_notes.php

📄 Root htdocs/ files:
   ├── index.php
   ├── login.php
   ├── auth.php
   ├── register.php
   ├── logout.php
   ├── database.sql
   ├── .htaccess
   └── (other files...)
    </pre>
    
    <hr>
    
    <h2>Step 3: Quick Tests</h2>
    <ul>
        <li><a href="simple.php" target="_blank">Test PHP</a> (should say "PHP is working")</li>
        <li><a href="db_test.php" target="_blank">Test Database</a> (should connect to MySQL)</li>
        <li><a href="index.php" target="_blank">Go to Main Site</a></li>
    </ul>
    
    <hr>
    
    <h2>Current Server Info</h2>
    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>
    <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
    <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
    
</body>
</html>
