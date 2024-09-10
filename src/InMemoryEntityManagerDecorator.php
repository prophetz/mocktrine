<?php

namespace Firehed\Mocktrine;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;

class InMemoryEntityManagerDecorator extends EntityManagerDecorator
{
    private EntityManagerInterface $originalEntityManager;

    public function __construct(
        InMemoryEntityManager $inMemoryEntityManager,
        EntityManagerInterface $originalEntityManager
    ) {
        parent::__construct($inMemoryEntityManager);

        $this->originalEntityManager = $originalEntityManager;
    }

    public function getConfiguration(): Configuration
    {
        return $this->originalEntityManager->getConfiguration();
    }
}
