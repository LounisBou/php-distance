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



// Number of iterations
$iterations = 1000;

// For each iteration
foreach(range(1, $iterations) as $iteration) {
    // Calculate distance for each rides
    foreach ($routes as $routeName => $route) {
        foreach(EarthRadius::values() as $radiusType => $radiusValue) {
            // Haversine
            $microtimeBefore = microtime(true);
            $haversineCalculator = new HaversineCalculator($radiusValue);
            $distances["Haversine $radiusType radius for $routeName"] = Route::getHumanReadableDistance($haversineCalculator->calculate($route));
            $microtimeAfter = microtime(true);
            if(!isset($averageTime["Haversine $radiusType radius for $routeName"])) {
                $averageTime["Haversine $radiusType radius for $routeName"] = 0;
            }
            $averageTime["Haversine $radiusType radius for $routeName"] += ($microtimeAfter - $microtimeBefore);
            // Vincenty
            $microtimeBefore = microtime(true);
            $vincentyCalculator = new VincentyCalculator($radiusValue);
            $distances["Vincenty $radiusType radius for $routeName"] = Route::getHumanReadableDistance($vincentyCalculator->calculate($route));
            $microtimeAfter = microtime(true);
            if(!isset($averageTime["Vincenty $radiusType radius for $routeName"])) {
                $averageTime["Vincenty $radiusType radius for $routeName"] = 0;
            }
            $averageTime["Vincenty $radiusType radius for $routeName"] +=  ($microtimeAfter - $microtimeBefore);
        }
        // MySQL
        $microtimeBefore = microtime(true);
        $mysqlCalculator = new SqlCalculator($connection);
        $distances["MySQL for $routeName"] = Route::getHumanReadableDistance($mysqlCalculator->calculate($route));
        $microtimeAfter = microtime(true);
        if(!isset($averageTime["MySQL for $routeName"])) {
            $averageTime["MySQL for $routeName"] = 0;
        }
        $averageTime["MySQL for $routeName"] +=  ($microtimeAfter - $microtimeBefore);
    }
}

// Calculate average time
foreach($averageTime as $key => $value) {
    $averageTime[$key] = $value / $iterations;
}

// Display results
echo "Average time taken for $iterations iterations :" . PHP_EOL . PHP_EOL;
foreach($averageTime as $key => $value) {
    echo "$key: ". $distances[$key]. " in " . number_format($value, 9) * 1000 * 1000 . " microseconds" . PHP_EOL . PHP_EOL;
}
