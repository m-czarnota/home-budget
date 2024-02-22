<?php

namespace App\Expense\Infrastructure\ORM;

use App\Expense\Domain\CurrentExpense;
use App\Expense\Domain\ExpenseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrentExpense>
 *
 * @method CurrentExpense|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrentExpense|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrentExpense[]    findAll()
 * @method CurrentExpense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineCurrentExpenseRepository extends ServiceEntityRepository implements ExpenseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrentExpense::class);
    }

    public function add(CurrentExpense $expense): void
    {
        $this->getEntityManager()->persist($expense);
    }

    public function remove(CurrentExpense $expense): void
    {
        $this->getEntityManager()->remove($expense);
    }
}
