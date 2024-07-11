<?php

declare(strict_types=1);

namespace PHPDistance;

use PHPDistance\Enums\EarthRadius;

class HaversineCalculator implements DistanceCalculatorInterface
{
    private float $earthRadius;

    public function __construct(float $earthRadius = EarthRadius::EQUATORIAL)
    {
        $this->earthRadius = $earthRadius;
    }

    /**
     * Calculate the distance between two points using the Haversine formula
     * @param Route $route Line between two points to calculate the distance for
     * @return int Distance in meters
     */
    function calculate(Route $route): int
    {
        // Early return if the points are identical
        if ($route->start->latitude === $route->end->latitude && $route->start->longitude === $route->end->longitude) {
            return 0;
        }

        // Convert latitude and longitude from degrees to radians
        $latFrom = deg2rad($route->start->latitude);
        $lonFrom = deg2rad($route->start->longitude);
        $latTo = deg2rad($route->end->latitude);
        $lonTo = deg2rad($route->end->longitude);

        // Haversine formula
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // Calculate the square of half the chord length between the points
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        // Calculate the central angle
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Calculate the distance
        $distance = $this->earthRadius * $c;

        // Return the distance as an integer
        return (int)round($distance);
    }
}
