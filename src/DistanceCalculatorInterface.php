<?php

declare(strict_types=1);

namespace PHPDistance;

interface DistanceCalculatorInterface
{
    /**
     * Calculate the distance between two points
     * @param Route $route Line between two points to calculate the distance for
     * @return int Distance in meters
     */
    public function calculate(Route $route): int;
}