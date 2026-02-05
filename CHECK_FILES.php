<?php
// File checker - Shows what files exist in each folder

echo "<h1>📂 File Structure Checker</h1>";
echo "<p>This will show you what files actually exist on your server.</p>";
echo "<hr>";

$folders_to_check = [
    'config' => ['db.php'],
    'includes' => ['header.php', 'footer.php', 'auth_check.php'],
    'assets/css' => ['style.css'],
    'records' => ['index.php', 'create.php', 'save.php', 'view.php'],
    '.' => ['index.php', 'login.php', 'register.php', 'auth.php', 'database.sql']
];

foreach ($folders_to_check as $folder => $files) {
    $display_folder = ($folder === '.') ? 'Root (htdocs)' : $folder;
    echo "<h2>📁 $display_folder/</h2>";
    echo "<ul>";
    
    foreach ($files as $file) {
        $path = ($folder === '.') ? $file : "$folder/$file";
        if (file_exists($path)) {
            $size = filesize($path);
            echo "<li style='color: green;'>✅ <strong>$file</strong> - " . number_format($size) . " bytes</li>";
        } else {
            echo "<li style='color: red;'>❌ <strong>$file</strong> - MISSING!</li>";
        }
    }
    
    echo "</ul>";
}

echo "<hr>";
echo "<h2>🔧 What to do if files are missing:</h2>";
echo "<ol>";
echo "<li>Extract <code>davina_rodgers_farm_system.zip</code> on your computer</li>";
echo "<li>Use InfinityFree File Manager to upload missing files to their folders</li>";
echo "<li>Refresh this page to verify</li>";
echo "</ol>";
?>
