<?php

declare(strict_types=1);

namespace App\Person\Infrastructure\ORM;

use App\Person\Domain\Person;
use App\Person\Domain\PersonRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 *
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrinePersonRepository extends ServiceEntityRepository implements PersonRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function add(Person $person): void
    {
        $this->getEntityManager()->persist($person);
    }

    public function remove(Person $person): void
    {
        $this->getEntityManager()->remove($person);
    }

    /**
     * @return array<int, Person>
     */
    public function findList(): array
    {
        return $this->findBy(['isDeleted' => false]);
    }

    public function findOneById(string $id): ?Person
    {
        return $this->find($id);
    }
}
