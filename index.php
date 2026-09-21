<?php

// Enable error reporting to identify any startup issues on shared hosting
error_reporting(E_ALL);
ini_set('display_errors', '1');

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 */

// LOAD OUR PATHS CONFIG FILE
if (file_exists(FCPATH . 'main/app/Config/Paths.php')) {
    require FCPATH . 'main/app/Config/Paths.php';
} elseif (file_exists(FCPATH . '../app/Config/Paths.php')) {
    require FCPATH . '../app/Config/Paths.php';
} else {
    require FCPATH . 'app/Config/Paths.php';
}

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

try {
    exit(Boot::bootWeb($paths));
} catch (\Throwable $e) {
    echo "<div style='font-family:sans-serif;padding:30px;max-width:800px;margin:40px auto;border:1px solid #f87171;border-radius:12px;background:#fef2f2;color:#991b1b;'>";
    echo "<h2 style='margin-top:0;'>Application Startup Notice</h2>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<details><summary style='cursor:pointer;font-weight:bold;'>Stack Trace</summary>";
    echo "<pre style='background:#fee2e2;padding:12px;border-radius:8px;overflow-x:auto;font-size:13px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</details>";
    echo "</div>";
    exit(1);
}
