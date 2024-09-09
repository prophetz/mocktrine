<?php

declare(strict_types=1);

namespace Firehed\Mocktrine;

use Doctrine\ORM\Mapping;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;

#[CoversClass(InMemoryEntityManager::class)]
#[Small]
class XmlDriverTest extends InMemoryEntityManagerTest
{
    protected function getEntityManager(): InMemoryEntityManager
    {
        $driver = new Mapping\Driver\XmlDriver([__DIR__ . '/Entities/XmlMappings']);
        return new InMemoryEntityManager($driver);
    }
}
