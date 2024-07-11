<?php

declare(strict_types=1);


namespace PHPDistance\tests;

require_once __DIR__ . '/vendor/autoload.php';

use PHPDistance\Enums\EarthRadius;
use PHPDistance\HaversineCalculator;
use PHPDistance\Point;
use PHPDistance\Route;
use PHPDistance\VincentyCalculator;

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
        $haversineCalculator = new HaversineCalculator($radiusValue);
        echo "Distance between $routeName using $radiusType radius:" . PHP_EOL;
        echo "Using Haversine formula = ";
        echo Route::getHumanReadableDistance($haversineCalculator->calculate($route)) . PHP_EOL;

        $vincentyCalculator = new VincentyCalculator($radiusValue);
        echo "Using Vincenty formula = ";
        echo Route::getHumanReadableDistance($vincentyCalculator->calculate($route)) . PHP_EOL;
        echo PHP_EOL;
    }
}
