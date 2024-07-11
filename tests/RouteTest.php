<?php

declare(strict_types=1);


namespace PHPDistance\tests;

use PHPDistance\Enums\EarthRadius;
use PHPDistance\HaversineCalculator;
use PHPDistance\Point;
use PHPDistance\Route;
use PHPDistance\VincentyCalculator;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    /** @var array|array[] $points */
    public array $points = [
        "Euratech, Lille, France" => ['lat' => 50.63328, 'lon' => 3.02014],
        "Citadelle, Lille, France" => ['lat' => 50.64126, 'lon' => 3.04464],
        "Biarritz, France" => ['lat' => 43.48997, 'lon' => -1.50331],
        "Strasbourg, France" => ['lat' => 48.58754, 'lon' => 7.74420],
    ];

    public function testHaversineDistance(): void
    {

        // Create routes to test
        $routes = [
            "Euratech => Lille" => new Route(
                Point::fromArray($this->points["Euratech, Lille, France"]),
                Point::fromArray($this->points["Citadelle, Lille, France"])
            ),
            "Biarritz => Strasbourg" => new Route(
                Point::fromArray($this->points["Biarritz, France"]),
                Point::fromArray($this->points["Strasbourg, France"])
            ),
        ];

        foreach ($routes as $routeName => $route) {
            foreach(EarthRadius::values() as $radiusType => $radiusValue) {
                $haversineCalculator = new HaversineCalculator($radiusValue);
                $haversineDistance = $haversineCalculator->calculate($route);
                $this->assertIsInt($haversineDistance);
            }
        }
    }

    public function testVincentyDistance(): void
    {
        // Create routes to test
        $routes = [
            "Euratech => Lille" => new Route(
                Point::fromArray($this->points["Euratech, Lille, France"]),
                Point::fromArray($this->points["Citadelle, Lille, France"])
            ),
            "Biarritz => Strasbourg" => new Route(
                Point::fromArray($this->points["Biarritz, France"]),
                Point::fromArray($this->points["Strasbourg, France"])
            ),
        ];

        foreach ($routes as $routeName => $route) {
            foreach(EarthRadius::values() as $radiusType => $radiusValue) {
                $vincentyCalculator = new VincentyCalculator($radiusValue);
                $vincentyDistance = $vincentyCalculator->calculate($route);
                $this->assertIsInt($vincentyDistance);
            }
        }
    }

}
