<?php

namespace Firehed\Mocktrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use RuntimeException;

class InMemoryDriverConnection implements Connection
{
    public function prepare(string $sql): Statement
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function query(string $sql): Result
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function quote(string $value): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function exec(string $sql): int|string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function lastInsertId(): int|string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function beginTransaction(): void
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function commit(): void
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function rollBack(): void
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getNativeConnection()
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getServerVersion(): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }
}
