<?php

namespace Firehed\Mocktrine;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

class InMemoryEntityManagerDecorator extends EntityManagerDecorator
{
    public function __construct(InMemoryEntityManager $inMemoryEntityManager)
    {
        parent::__construct($inMemoryEntityManager);
    }
}
