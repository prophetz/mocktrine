<?php

namespace Firehed\Mocktrine;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\ServerVersionProvider;
use SensitiveParameter;

class InMemoryDriver implements Driver
{
    public function connect(#[SensitiveParameter] array $params): DriverConnection
    {
        return new InMemoryDriverConnection();
    }

    public function getDatabasePlatform(ServerVersionProvider $versionProvider): AbstractPlatform
    {
        return new InMemoryPlatform();
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return new InMemoryExceptionConverter();
    }
}
