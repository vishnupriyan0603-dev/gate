<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=utf-8');

echo "<style>body{font-family:system-ui,-apple-system,sans-serif;margin:30px;background:#f8fafc;color:#1e293b;}h2,h3{color:#0f172a;}pre{background:#1e293b;color:#f8fafc;padding:12px;border-radius:8px;overflow:auto;}.box{padding:15px;border-radius:8px;margin-bottom:15px;}.btn{display:inline-block;padding:10px 20px;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;font-weight:600;}.btn-danger{background:#dc2626;}</style>";

echo "<h2>GATE App Live Diagnostics & Database Setup</h2>";

// 1. Check PHP Runtime
echo "<h3>1. PHP Runtime</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "mysqli: " . (extension_loaded('mysqli') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "intl: " . (extension_loaded('intl') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "mbstring: " . (extension_loaded('mbstring') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";

// 2. Database Connection Test
echo "<h3>2. Database Connection</h3>";
require_once __DIR__ . '/main/app/Config/Paths.php';
$paths = new \Config\Paths();

require_once __DIR__ . '/main/app/Config/Database.php';
$dbConfig = new \Config\Database();
$cfg = $dbConfig->default;

echo "Host: <b>" . htmlspecialchars($cfg['hostname']) . "</b><br>";
echo "User: <b>" . htmlspecialchars($cfg['username']) . "</b><br>";
echo "Database: <b>" . htmlspecialchars($cfg['database']) . "</b><br>";
echo "Port: <b>" . (int)$cfg['port'] . "</b><br>";
echo "Password Set: " . (strlen($cfg['password']) > 0 ? '<b style="color:green">Yes (' . strlen($cfg['password']) . ' chars)</b>' : '<b style="color:red">EMPTY / NOT SET!</b>') . "<br><br>";

$start = microtime(true);
$link = @mysqli_connect($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], (int)$cfg['port']);
$elapsed = round((microtime(true) - $start) * 1000, 2);

if (!$link) {
    echo "<div class='box' style='background:#fee2e2;border:1px solid #f87171;color:#991b1b;'>";
    echo "<b>Database Connection FAILED ({$elapsed}ms):</b><br>";
    echo "Error: (" . mysqli_connect_errno() . ") " . htmlspecialchars(mysqli_connect_error());
    echo "</div>";
} else {
    echo "<div class='box' style='background:#dcfce7;border:1px solid #86efac;color:#166534;'>";
    echo "<b>Database Connected Successfully ({$elapsed}ms)!</b><br>";

    // Handle Auto-Import Request
    if (isset($_GET['action']) && $_GET['action'] === 'import') {
        echo "<hr><h4>Running Database Import...</h4>";
        $sqlFile = __DIR__ . '/main/db/infinityfree_gate.sql';
        if (!file_exists($sqlFile)) {
            echo "<p style='color:#991b1b;'>SQL file not found at: " . htmlspecialchars($sqlFile) . "</p>";
        } else {
            $queries = file_get_contents($sqlFile);
            if (mysqli_multi_query($link, $queries)) {
                $count = 0;
                do {
                    if ($res = mysqli_store_result($link)) {
                        mysqli_free_result($res);
                    }
                    $count++;
                } while (mysqli_more_results($link) && mysqli_next_result($link));
                echo "<p style='color:#166534;font-weight:bold;'>All database tables and seed data have been successfully imported!</p>";
                echo "<p><a href='health.php' class='btn'>Refresh Diagnostics</a> <a href='/' class='btn' style='background:#10b981'>Go to Homepage</a></p>";
            } else {
                echo "<p style='color:#991b1b;'>SQL Multi-query error: " . htmlspecialchars(mysqli_error($link)) . "</p>";
            }
        }
    }

    $res = mysqli_query($link, "SHOW TABLES");
    $tables = [];
    if ($res) {
        while ($row = mysqli_fetch_row($res)) {
            $tables[] = $row[0];
        }
    }

    echo "<b>Existing Tables (" . count($tables) . "):</b> ";
    if (empty($tables)) {
        echo "<b style='color:#b91c1c;'>Database is currently empty (0 tables).</b><br><br>";
        echo "<a href='health.php?action=import' class='btn'>Auto-Import Database Tables & Seed Data Now</a>";
    } else {
        echo "<span style='color:#166534;'>" . implode(', ', $tables) . "</span><br><br>";
        echo "<a href='/' class='btn'>Launch App Homepage</a>";
    }
    echo "</div>";
    mysqli_close($link);
}

// 3. CodeIgniter Logs
echo "<h3>3. Application Error Logs</h3>";
$logFiles = glob(__DIR__ . '/main/writable/logs/log-*.log');
if ($logFiles) {
    foreach ($logFiles as $lf) {
        echo "<b>" . htmlspecialchars(basename($lf)) . "</b> (" . filesize($lf) . " bytes):<br>";
        $content = file_get_contents($lf);
        $tail = substr($content, -3000);
        echo "<pre>" . htmlspecialchars($tail) . "</pre>";
    }
} else {
    echo "No log files found in main/writable/logs/.<br>";
}
