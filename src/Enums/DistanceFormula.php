<?php

declare(strict_types=1);

namespace PHPDistance\Enums;

enum DistanceFormula: string
{
    case Haversine = 'haversine';
    case Vincenty = 'vincenty';
}
