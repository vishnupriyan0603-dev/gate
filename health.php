<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=utf-8');

echo "<h2>GATE App Live Diagnostics</h2>";

// 1. Check PHP version & extensions
echo "<h3>1. PHP Runtime</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "mysqli extension: " . (extension_loaded('mysqli') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "intl extension: " . (extension_loaded('intl') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "mbstring extension: " . (extension_loaded('mbstring') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";

// 2. Check Paths & .env files
echo "<h3>2. Configuration & Files</h3>";
$rootEnv = __DIR__ . '/.env';
$mainEnv = __DIR__ . '/main/.env';
echo "Root .env: " . (file_exists($rootEnv) ? '<b style="color:green">Exists</b> (' . filesize($rootEnv) . ' bytes)' : '<b style="color:red">Missing</b>') . "<br>";
echo "main/.env: " . (file_exists($mainEnv) ? '<b style="color:green">Exists</b> (' . filesize($mainEnv) . ' bytes)' : '<b style="color:red">Missing</b>') . "<br>";

// 3. Test Database Connection
echo "<h3>3. Database Connection</h3>";
require_once __DIR__ . '/main/app/Config/Paths.php';
$paths = new \Config\Paths();

// Attempt to read Database.php
require_once __DIR__ . '/main/app/Config/Database.php';
$dbConfig = new \Config\Database();
$cfg = $dbConfig->default;

echo "Host: <b>" . htmlspecialchars($cfg['hostname']) . "</b><br>";
echo "User: <b>" . htmlspecialchars($cfg['username']) . "</b><br>";
echo "Database: <b>" . htmlspecialchars($cfg['database']) . "</b><br>";
echo "Port: <b>" . (int)$cfg['port'] . "</b><br>";
echo "Password Set: " . (strlen($cfg['password']) > 0 ? '<b style="color:green">Yes (' . strlen($cfg['password']) . ' chars)</b>' : '<b style="color:red">EMPTY / NOT SET!</b>') . "<br>";

if (strlen($cfg['password']) === 0) {
    echo "<p style='color:red;'><b>Database password is empty in Config/Database.php!</b></p>";
}

echo "<h4>Attempting mysqli_connect...</h4>";
$start = microtime(true);
$link = @mysqli_connect($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], (int)$cfg['port']);
$elapsed = round((microtime(true) - $start) * 1000, 2);

if (!$link) {
    echo "<div style='color:red;padding:10px;background:#fee;border:1px solid #fcc;border-radius:6px;'>";
    echo "<b>Connection FAILED after {$elapsed}ms:</b><br>";
    echo "Error (" . mysqli_connect_errno() . "): " . htmlspecialchars(mysqli_connect_error());
    echo "</div>";
} else {
    echo "<div style='color:green;padding:10px;background:#efe;border:1px solid #cfc;border-radius:6px;'>";
    echo "<b>Connected Successfully in {$elapsed}ms!</b><br>";
    $res = mysqli_query($link, "SHOW TABLES");
    $tables = [];
    if ($res) {
        while ($row = mysqli_fetch_row($res)) {
            $tables[] = $row[0];
        }
    }
    echo "Tables in database (" . count($tables) . "): " . (empty($tables) ? '<b style="color:red">NO TABLES FOUND! (Database is empty)</b>' : implode(', ', $tables));
    echo "</div>";
    mysqli_close($link);
}

// 4. CodeIgniter Logs
echo "<h3>4. CodeIgniter Logs</h3>";
$logFiles = glob(__DIR__ . '/main/writable/logs/log-*.log');
if ($logFiles) {
    foreach ($logFiles as $lf) {
        echo "<b>File: " . htmlspecialchars(basename($lf)) . "</b> (" . filesize($lf) . " bytes)<br>";
        $content = file_get_contents($lf);
        $tail = substr($content, -3000);
        echo "<pre style='background:#f4f4f4;padding:10px;border-radius:6px;max-height:300px;overflow:auto;font-size:12px;'>" . htmlspecialchars($tail) . "</pre>";
    }
} else {
    echo "No log files found in main/writable/logs/.<br>";
}
