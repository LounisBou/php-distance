<?php

declare(strict_types=1);

namespace PHPDistance;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;

class SqlCalculator implements DistanceCalculatorInterface
{

    /**
     * @var int EPSG_SRID : European Petroleum Survey Group (EPSG) Spatial Reference Identifier (SRID)
     */
    public const EPSG_SRID = 4326;

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Calculate the distance between two points using the SQL ST_DISTANCE_SPHERE function
     * @param Route $route Line between two points to calculate the distance for
     * @return int Distance in meters
     * @throws Exception MySQL query exception
     */
    function calculate(Route $route): int
    {
        $sql = 'SELECT ST_DISTANCE_SPHERE(POINT(:lonFrom, :latFrom), POINT(:lonTo, :latTo)) AS distance';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('latFrom', $route->start->latitude);
        $statement->bindValue('lonFrom', $route->start->longitude);
        $statement->bindValue('latTo', $route->end->latitude);
        $statement->bindValue('lonTo', $route->end->longitude);
        $result = $statement->executeQuery();

        return (int) $result->fetchOne();

    }
}
