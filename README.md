# PHPDistance

PHPDistance is a simple PHP library for calculating the distance between two geographical points on Earth. It supports different Earth radius values and various distance calculation formulas.

## Installation

Use Composer to install the library:

```bash
composer require lounisbou/phpdistance
```

## Usage
Define Points
Define geographical points using the `Point` class:

```php
use PHPDistance\Point;

$start = new Point(52.5200, 13.4050); // Berlin
$end = new Point(48.8566, 2.3522); // Paris
```

### Calculate Distance Using Haversine Formula
Use the `Route` class with the `HaversineCalculator` to calculate the distance using the Haversine formula:

```php
use PHPDistance\Route;
use PHPDistance\HaversineCalculator;
use PHPDistance\Enums\EarthRadius;

$start = new Point(52.5200, 13.4050); // Berlin
$end = new Point(48.8566, 2.3522); // Paris
$calculator = new HaversineCalculator(EarthRadius::MEAN_RADIUS);
$route = new Route($start, $end, $calculator);

echo "Distance using Haversine formula with mean radius: " . $route->calculateDistance() . " meters\n";
```

### Calculate Distance Using Vincenty Formula
Use the `Route` class with the `VincentyCalculator` to calculate the distance using Vincenty's formula:

```php
use PHPDistance\Route;
use PHPDistance\VincentyCalculator;

$start = new Point(52.5200, 13.4050); // Berlin
$end = new Point(48.8566, 2.3522); // Paris
$calculator = new VincentyCalculator();
$route = new Route($start, $end, $calculator);

echo "Distance using Vincenty formula: " . $route->calculateDistance() . " meters\n";
```

## Distance Calculation Formulas

### Haversine Formula
The Haversine formula is an equation giving great-circle distances between two points on a sphere from their longitudes and latitudes. It is useful for calculating the shortest distance over the earth's surface.

### Vincenty Formula
Vincenty's formulae are two related iterative methods used in geodesy to calculate the distance between two points on the surface of an ellipsoid. They are accurate for long distances.

### Custom Earth Radius
You can specify a custom Earth radius if needed:

```php
$customRadius = 6356752.3142; // Custom radius in meters
$calculator = new HaversineCalculator($customRadius);
$route = new Route($start, $end, $calculator);

echo "Distance using Haversine formula with custom radius: " . $route->calculateDistance() . " meters\n";
```

## Adding More Formulas
You can extend the library by adding more distance calculation formulas. Implement the `DistanceCalculatorInterface` to create a new calculator:

```php
use PHPDistance\Point;
use PHPDistance\DistanceCalculatorInterface;

class NewFormulaCalculator implements DistanceCalculatorInterface
{
public function calculate(Point $from, Point $to): int
{
// Implement the new formula here
return 0; // Placeholder
}
}
```

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request or open an Issue on GitHub.

## License
This library is licensed under the MIT License. See the LICENSE file for more details.
