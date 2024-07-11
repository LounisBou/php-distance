<?php

declare(strict_types=1);

namespace PHPDistance;

class Point
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    /**
     * Convert an array to a Point object
     * @param array $point First value as latitude and second value as longitude ignoring string keys
     * @return Point
     */
    public static function fromArray(array $point): Point
    {
        // Get first value as latitude and second value as longitude ignoring string keys
        return new Point(array_values($point)[0], array_values($point)[1]);
    }

    public function __toString(): string
    {
        return sprintf('(%f, %f)', $this->latitude, $this->longitude);
    }
}