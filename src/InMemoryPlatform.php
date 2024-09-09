<?php

namespace Firehed\Mocktrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\DateIntervalUnit;
use Doctrine\DBAL\Platforms\Keywords\KeywordList;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\TransactionIsolationLevel;
use RuntimeException;

class InMemoryPlatform extends AbstractPlatform
{
    public function getBooleanTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getIntegerTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getBigIntTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getSmallIntTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    protected function _getCommonIntegerTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    protected function initializeDoctrineTypeMappings(): void
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getClobTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getBlobTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getLocateExpression(string $string, string $substring, ?string $start = null): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getDateDiffExpression(string $date1, string $date2): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    protected function getDateArithmeticIntervalExpression(string $date, string $operator, string $interval, DateIntervalUnit $unit): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getCurrentDatabaseExpression(): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getAlterTableSQL(TableDiff $diff): array
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getListViewsSQL(string $database): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getSetTransactionIsolationSQL(TransactionIsolationLevel $level): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getDateTimeTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getDateTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function getTimeTypeDeclarationSQL(array $column): string
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    protected function createReservedKeywordsList(): KeywordList
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }

    public function createSchemaManager(Connection $connection): AbstractSchemaManager
    {
        throw new RuntimeException(__METHOD__ . ' not yet implemented');
    }
}
