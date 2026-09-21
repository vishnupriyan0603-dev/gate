<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=utf-8');

echo "<style>body{font-family:system-ui,-apple-system,sans-serif;margin:30px;background:#f8fafc;color:#1e293b;}h2,h3{color:#0f172a;}pre{background:#1e293b;color:#f8fafc;padding:12px;border-radius:8px;overflow:auto;}.box{padding:15px;border-radius:8px;margin-bottom:15px;}.btn{display:inline-block;padding:8px 16px;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;font-weight:600;margin-right:8px;margin-top:6px;}.btn-success{background:#16a34a;}.btn-danger{background:#dc2626;}.table{width:100%;border-collapse:collapse;margin-top:10px;}.table th,.table td{padding:10px;text-align:left;border-bottom:1px solid #e2e8f0;}</style>";

echo "<h2>GATE App Live Diagnostics & Maintenance</h2>";

// Action: Fix Permissions
if (isset($_GET['action']) && $_GET['action'] === 'fix_permissions') {
    $fixLog = [];
    $writableDirs = [
        __DIR__ . '/main/writable',
        __DIR__ . '/main/writable/cache',
        __DIR__ . '/main/writable/logs',
        __DIR__ . '/main/writable/session',
        __DIR__ . '/main/writable/uploads',
        __DIR__ . '/main/writable/debugbar',
    ];
    foreach ($writableDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        @chmod($dir, 0777);
        $fixLog[] = "Set chmod 0777 on " . basename(dirname($dir)) . '/' . basename($dir) . " -> " . (is_writable($dir) ? 'Writable OK' : 'Check manually');
    }

    echo "<div class='box' style='background:#fef3c7;border:1px solid #fcd34d;color:#92400e;'>";
    echo "<b>Permission Fix Applied:</b><br>";
    echo implode('<br>', $fixLog);
    echo "<br><br><a href='health.php' class='btn'>Back to Overview</a>";
    echo "</div>";
}

// 1. Check PHP Runtime
echo "<h3>1. PHP Runtime</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "mysqli: " . (extension_loaded('mysqli') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "intl: " . (extension_loaded('intl') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";
echo "mbstring: " . (extension_loaded('mbstring') ? '<b style="color:green">Loaded</b>' : '<b style="color:red">Missing</b>') . "<br>";

// 2. Folder & File Permissions
echo "<h3>2. Folder & File Permissions</h3>";
$checkPaths = [
    'App Root Directory'          => [__DIR__, '0755', 'dir'],
    'Static Assets (assets/)'     => [__DIR__ . '/assets', '0755', 'dir'],
    'Main Directory (main/)'      => [__DIR__ . '/main', '0755', 'dir'],
    'Writable Root (main/writable)' => [__DIR__ . '/main/writable', '0777', 'writable'],
    'Writable Cache'              => [__DIR__ . '/main/writable/cache', '0777', 'writable'],
    'Writable Logs'               => [__DIR__ . '/main/writable/logs', '0777', 'writable'],
    'Writable Session'            => [__DIR__ . '/main/writable/session', '0777', 'writable'],
    'Writable Uploads'            => [__DIR__ . '/main/writable/uploads', '0777', 'writable'],
    'Writable Debugbar'           => [__DIR__ . '/main/writable/debugbar', '0777', 'writable'],
    'Front Controller (index.php)'=> [__DIR__ . '/index.php', '0644', 'file'],
    'Apache Config (.htaccess)'   => [__DIR__ . '/.htaccess', '0644', 'file'],
];

echo "<table class='table'><thead><tr><th>Item</th><th>Target</th><th>Actual Mode</th><th>Writable?</th><th>Status</th></tr></thead><tbody>";
$allGood = true;
foreach ($checkPaths as $label => $item) {
    [$path, $target, $type] = $item;
    $exists = file_exists($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    $isWritable = $exists ? is_writable($path) : false;
    $isDir = $exists ? is_dir($path) : false;

    if ($type === 'writable') {
        $statusOk = $exists && $isWritable;
    } else {
        $statusOk = $exists;
    }

    if (!$statusOk) {
        $allGood = false;
    }

    echo "<tr>";
    echo "<td><b>{$label}</b></td>";
    echo "<td><code>{$target}</code></td>";
    echo "<td><code>{$perms}</code></td>";
    echo "<td>" . ($isWritable ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "</td>";
    echo "<td>" . ($statusOk ? '<b style="color:green">✓ OK</b>' : '<b style="color:red">✗ Issue</b>') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

if (!$allGood) {
    echo "<p><a href='health.php?action=fix_permissions' class='btn btn-danger'>Apply 1-Click Permission Fix (chmod 777 on writable)</a></p>";
} else {
    echo "<p><span style='color:green;font-weight:600;'>✓ All essential folder and file permissions are properly configured.</span> <a href='health.php?action=fix_permissions' class='btn' style='background:#64748b;'>Re-apply 0777 Permissions</a></p>";
}

// 3. Database Connection Test
echo "<h3>3. Database Connection</h3>";
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
                echo "<p><a href='health.php' class='btn'>Refresh Diagnostics</a> <a href='/' class='btn btn-success'>Go to Homepage</a></p>";
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
        echo "<a href='/' class='btn btn-success'>Launch App Homepage</a>";
    }
    echo "</div>";
    mysqli_close($link);
}

// 4. CodeIgniter Logs
echo "<h3>4. Application Error Logs</h3>";
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
