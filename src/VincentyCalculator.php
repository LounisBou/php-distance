<?php

declare(strict_types=1);

namespace PHPDistance;

use InvalidArgumentException;
use PHPDistance\Enums\EarthRadius;
use RuntimeException;

class VincentyCalculator implements DistanceCalculatorInterface
{
    // @see WGS 84 : https://en.wikipedia.org/wiki/World_Geodetic_System
    public const EARTH_FLATTENING = 1 / 298.257223563;
    private float $earthRadius;

    public function __construct(float $earthRadius = EarthRadius::EQUATORIAL)
    {
        $this->earthRadius = $earthRadius;
    }

    /**
     * Calculate the distance between two points using the Vincenty formula
     * @param Route $route Line between two points to calculate the distance for
     * @param int $iterationLimit Maximum number of iterations for the formula
     * @return int Distance in meters
     */
    public function calculate(Route $route, int $iterationLimit = 100): int
    {
        // Iteration limit must be greater than 0
        if ($iterationLimit <= 0) {
            throw new InvalidArgumentException('Iteration limit must be greater than 0');
        }

        // Early return if the points are identical
        if ($route->start->latitude === $route->end->latitude && $route->start->longitude === $route->end->longitude) {
            return 0;
        }

        // Semi-minor axis of the Earth (in meters)
        $earth_semi_minor_axis = (1 - self::EARTH_FLATTENING) * $this->earthRadius;

        // Convert latitude and longitude from degrees to radians
        $fromLatitudeRadian = deg2rad($route->start->latitude);
        $fromLongitudeRadian = deg2rad($route->start->longitude);
        $toLatitudeRadian = deg2rad($route->end->latitude);
        $toLongitudeRadian = deg2rad($route->end->longitude);

        $U1 = atan((1 - self::EARTH_FLATTENING) * tan($fromLatitudeRadian));
        $U2 = atan((1 - self::EARTH_FLATTENING) * tan($toLatitudeRadian));
        $lonDelta = $toLongitudeRadian - $fromLongitudeRadian;

        $sinU1 = sin($U1);
        $cosU1 = cos($U1);
        $sinU2 = sin($U2);
        $cosU2 = cos($U2);

        $lambda = $lonDelta;
        $lambdaP = 2 * M_PI;
        while (abs($lambda - $lambdaP) > 1e-12 && --$iterationLimit > 0) {
            $sinLambda = sin($lambda);
            $cosLambda = cos($lambda);
            $sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) +
                ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));
            if ($sinSigma == 0) {
                return 0; // co-incident points
            }
            $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
            $sigma = atan2($sinSigma, $cosSigma);
            $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
            $cos2Alpha = 1 - $sinAlpha * $sinAlpha;
            $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cos2Alpha;
            $C = self::EARTH_FLATTENING / 16 * $cos2Alpha * (4 + self::EARTH_FLATTENING * (4 - 3 * $cos2Alpha));
            $lambdaP = $lambda;
            $lambda = $lonDelta + (1 - $C) * self::EARTH_FLATTENING * $sinAlpha * ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
        }

        // Check if the formula failed to converge
        if ($iterationLimit == 0) {
            throw new RuntimeException('Vincenty formula failed to converge');
        }

        $uSq = $cos2Alpha * ($this->earthRadius * $this->earthRadius - $earth_semi_minor_axis * $earth_semi_minor_axis) / ($earth_semi_minor_axis * $earth_semi_minor_axis);
        $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) -
                    $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));

        $distance = $earth_semi_minor_axis * $A * ($sigma - $deltaSigma);

        return (int) round($distance);
    }
}
