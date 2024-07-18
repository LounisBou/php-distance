<?php

declare(strict_types=1);


namespace PHPDistance\tests;

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\Exception;
use PHPDistance\Enums\EarthRadius;
use PHPDistance\HaversineCalculator;
use PHPDistance\SqlCalculator;
use PHPDistance\Point;
use PHPDistance\Route;
use PHPDistance\VincentyCalculator;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
try {
    $connection = DriverManager::getConnection([
        'host' => $_ENV['DATABASE_HOST'],
        'port' => $_ENV['DATABASE_PORT'],
        'dbname' => $_ENV['DATABASE_NAME'],
        'user' => $_ENV['DATABASE_USER'],
        'password' => $_ENV['DATABASE_PASSWORD'],
        'version' => $_ENV['DATABASE_VERSION'],
        'driver' => $_ENV['DATABASE_DRIVER'],
    ]);
    echo "Connection established successfully." . PHP_EOL;
} catch (\Exception $e) {
    echo "Failed to establish connection: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$points = [
    "Euratech, Lille, France" => new Point(50.63328, 3.02014),
    "Citadelle, Lille, France" => new Point(50.64126, 3.04464),
    "Biarritz, France" => new Point(43.48997, -1.50331),
    "Strasbourg, France" => new Point(48.58754, 7.74420),
];

$routes = [
    "Euratech => Lille" => new Route($points["Euratech, Lille, France"], $points["Citadelle, Lille, France"]),
    "Biarritz => Strasbourg" => new Route($points["Biarritz, France"], $points["Strasbourg, France"]),
];


// Calculate distance for each rides
foreach ($routes as $routeName => $route) {
    echo "--------------------------" . PHP_EOL;
    foreach(EarthRadius::values() as $radiusType => $radiusValue) {

        $microtimeBefore = microtime(true);
        $haversineCalculator = new HaversineCalculator($radiusValue);
        echo "Distance between $routeName using $radiusType radius:" . PHP_EOL;
        echo "Using Haversine formula = ";
        echo Route::getHumanReadableDistance($haversineCalculator->calculate($route)) . PHP_EOL;
        $microtimeAfter = microtime(true);
        // Convert seconds to microseconds
        echo "Time taken: " . ($microtimeAfter - $microtimeBefore) * 1000 * 1000 . " microseconds" . PHP_EOL;

        $microtimeBefore = microtime(true);
        $vincentyCalculator = new VincentyCalculator($radiusValue);
        echo "Using Vincenty formula = ";
        echo Route::getHumanReadableDistance($vincentyCalculator->calculate($route)) . PHP_EOL;
        $microtimeAfter = microtime(true);
        echo "Time taken: " . ($microtimeAfter - $microtimeBefore) * 1000 * 1000 . " microseconds" . PHP_EOL;
        echo PHP_EOL;
    }
    $microtimeBefore = microtime(true);
    $mysqlCalculator = new SqlCalculator($connection);
    echo "Distance between $routeName using MySQL ST_DISTANCE_SPHERE function : ";
    try {
        echo Route::getHumanReadableDistance($mysqlCalculator->calculate($route)) . PHP_EOL;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
    }
    $microtimeAfter = microtime(true);
    echo "Time taken: " . ($microtimeAfter - $microtimeBefore) * 1000 * 1000 . " microseconds" . PHP_EOL;
}
