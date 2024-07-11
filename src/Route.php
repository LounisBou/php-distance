<?php

declare(strict_types=1);

namespace PHPDistance;

use PHPDistance\Enums\DistanceUnit;

class Route
{
    public function __construct(
        public Point $start,
        public Point $end,
    ) {
    }

    /**
     * Get the distance between two points in human-readable format
     * @param int $distance Distance in meters
     * @return string Distance in human-readable format
     */
    public static function getHumanReadableDistance(int $distance): string
    {
        if ($distance < 10000) {
            return $distance .  DistanceUnit::Meters->value;
        }

        return round($distance / 1000, 3) . DistanceUnit::Kilometers->value;
    }
}