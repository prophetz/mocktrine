<?php

namespace Firehed\Mocktrine;

use Doctrine\DBAL\Connection;

class InMemoryConnection extends Connection
{
    public function __construct()
    {
        parent::__construct([], new InMemoryDriver());
    }
}
