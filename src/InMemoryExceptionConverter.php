<?php

namespace Firehed\Mocktrine;

use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Query;
use RuntimeException;

class InMemoryExceptionConverter implements ExceptionConverter
{
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }
}
