<?php

namespace App\Expense\Infrastructure\ORM;

use App\Expense\Domain\Expense;
use App\Expense\Domain\ExpenseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineExpenseRepository extends ServiceEntityRepository implements ExpenseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function add(Expense $expense): void
    {
        $this->getEntityManager()->persist($expense);
    }

    public function remove(Expense $expense): void
    {
        $this->getEntityManager()->remove($expense);
    }
}
