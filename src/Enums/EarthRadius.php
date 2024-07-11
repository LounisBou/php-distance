<?php

declare(strict_types=1);

namespace PHPDistance\Enums;

class EarthRadius
{
    public const MEAN = 6371000.0;
    public const EQUATORIAL = 6378137.0; // @see WGS 84 : https://en.wikipedia.org/wiki/World_Geodetic_System
    public const POLAR = 6356752.3142;

    private function __construct() {
    // Prevent instantiation
    }

    public static function values(): array
    {
        return [
            'mean' => self::MEAN,
            'equatorial' =>self::EQUATORIAL,
            'polar' => self::POLAR,
        ];
    }
}