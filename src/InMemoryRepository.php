<?php

declare(strict_types=1);

namespace Firehed\Mocktrine;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Persisters\Exception\InvalidOrientation;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use TypeError;
use UnexpectedValueException;

use function assert;
use function count;
use function current;
use function is_array;
use function spl_object_hash;
use function sprintf;
use function strtoupper;
use function trim;

/**
 * @template Entity of object
 *
 * @implements ObjectRepository<Entity>
 * @implements Selectable<array-key, Entity>
 */
class InMemoryRepository extends EntityRepository implements ObjectRepository, Selectable
{
    /**
     * @var class-string<Entity>
     */
    private string $className;

    private string $idField;

    private string $idType;

    private bool $isIdGenerated;

    /** @var Entity[] */
    private $managedEntities = [];

    private MappingDriver $mappingDriver;

    /**
     * @var ClassMetadataInterface<Entity>
     */
    private ClassMetadataInterface $metadata;

    /**
     * @param class-string<Entity> $fqcn
     */
    public function __construct(string $fqcn, MappingDriver $mappingDriver)
    {
        $this->className = $fqcn;
        $this->mappingDriver = $mappingDriver;

        // Use provided driver to figure out what field is marked as @Id
        $metadata = new ClassMetadata($this->className);
        $metadata->initializeReflection(new RuntimeReflectionService());
        $this->metadata = $metadata;
        $this->mappingDriver->loadMetadataForClass($this->className, $metadata);

        $ids = $metadata->getIdentifier();
        // Entity does not have an id field!
        if (count($ids) === 0) {
            throw MappingException::identifierRequired($fqcn);
        }

        assert(count($ids) === 1);
        $idField = $ids[0];
        assert(is_string($idField));
        $this->idField = $idField;
        $idType = $metadata->getTypeOfField($idField);
        assert($idType !== null);
        $this->idType = $idType;
        $this->isIdGenerated = $metadata->usesIdGenerator();
    }

    /**
     * @internal
     * @param Entity $entity
     */
    public function manage(object $entity): void
    {
        if (!$entity instanceof $this->className) {
            throw new TypeError(sprintf(
                'Argument 1 passed to manage() must be of the type %s, %s given',
                $this->getClassName(),
                get_class($entity)
            ));
        }
        $this->managedEntities[spl_object_hash($entity)] = $entity;
    }

    /**
     * Used by the EntityManager when entities with deletion pending are
     * flushed.
     *
     * @internal
     *
     * @param Entity $entity
     */
    public function remove(object $entity): void
    {
        unset($this->managedEntities[spl_object_hash($entity)]);
    }

    // ObjectRepository implementation

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return ?Entity The object.
     */
    public function find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null): object|null
    {
        return $this->findOneBy([$this->idField => $id]);
    }

    /**
     * Finds all objects in the repository.
     *
     * @return Entity[] The objects.
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array<array-key, mixed>       $criteria
     * @param array<array-key, string>|null $orderBy
     * @param int|null      $limit
     * @param int|null      $offset
     *
     * @return Entity[] The objects.
     *
     * @throws UnexpectedValueException
     */
    public function findBy(array $criteria, array|null $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        $expr = Criteria::expr();
        $crit = Criteria::create();
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                // Convert list arguments to IN(...)
                $crit->andWhere($expr->in($field, $value));
            } else {
                $crit->andWhere($expr->eq($field, $value));
            }
        }

        if ($orderBy) {
            // Criteria::orderBy silently converts any invalid inputs to 'DESC'
            // This pre-validates them
            foreach ($orderBy as $field => $direction) {
                $direction = strtoupper(trim($direction));
                if ($direction !== Criteria::ASC && $direction !== Criteria::DESC) {
                    throw InvalidOrientation::fromClassNameAndField($this->getClassName(), $field);
                }
            }
            $crit->orderBy($orderBy);
        }
        if ($offset) {
            $crit->setFirstResult($offset);
        }
        if ($limit) {
            $crit->setMaxResults($limit);
        }

        return $this->doMatch($crit);
    }

    /**
     * Counts entities by a set of critieria.
     *
     * NOTE: this is not part of the official interface; there's
     * a Doctrine-internal TODO to make it so.
     *
     * @param array<string, mixed> $criteria
     */
    public function count(array $criteria = []): int
    {
        return count($this->findBy($criteria));
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param mixed[] $criteria The criteria.
     *
     * @return ?Entity The object.
     */
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null
    {
        $results = $this->findBy($criteria);
        if (count($results) > 0) {
            return current($results);
        }
        return null;
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return class-string<Entity>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Selectable implementation
     * {@inheritdoc}
     *
     * @return Collection<array-key, Entity>
     */
    public function matching(Criteria $criteria): AbstractLazyCollection&Selectable
    {
        return new PersistentCollection(null, null, new ArrayCollection($this->doMatch($criteria)));
    }

    /**
     * @return Entity[]
     */
    private function doMatch(Criteria $criteria): array
    {
        $expr = $criteria->getWhereExpression();

        /** @var CriteriaEvaluator<Entity> */
        $ce = CriteriaEvaluatorFactory::getInstance($this->metadata);
        return $ce->evaluate($this->managedEntities, $criteria);
    }

    /**
     * This is used to generate identifiers when flush() is called. It should
     * not be used except by the EntityManager.
     *
     * @internal
     */
    public function getIdField(): string
    {
        return $this->idField;
    }

    /**
     * This is used to generate identifiers when flush() is called. It should
     * not be used except by the EntityManager.
     *
     * @internal
     */
    public function getIdType(): string
    {
        return $this->idType;
    }

    /**
     * This is used to determine if IDs need to be generated when
     * EntityManager's flush() method is called. Is should not be used by
     * anything else.
     *
     * @internal
     */
    public function isIdGenerated(): bool
    {
        return $this->isIdGenerated;
    }
}
