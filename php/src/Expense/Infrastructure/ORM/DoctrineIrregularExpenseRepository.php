<?php

declare(strict_types=1);

namespace App\Expense\Infrastructure\ORM;

use App\Category\Domain\Category;
use App\Expense\Domain\IrregularExpense;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IrregularExpense>
 *
 * @method IrregularExpense|null find($id, $lockMode = null, $lockVersion = null)
 * @method IrregularExpense|null findOneBy(array $criteria, array $orderBy = null)
 * @method IrregularExpense[]    findAll()
 * @method IrregularExpense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineIrregularExpenseRepository extends ServiceEntityRepository implements IrregularExpenseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IrregularExpense::class);
    }

    public function add(IrregularExpense $irregularExpense): void
    {
        $this->getEntityManager()->persist($irregularExpense);
    }

    public function remove(IrregularExpense $irregularExpense): void
    {
        $this->getEntityManager()->remove($irregularExpense);
    }

    public function findOneById(string $id): ?IrregularExpense
    {
        return $this->find($id);
    }

    public function findList(): array
    {
        return $this->findAll();
    }

    public function removeNotInList(IrregularExpense ...$irregularExpenses): void
    {
        $ids = array_map(fn (IrregularExpense $irregularExpense) => $irregularExpense->id, $irregularExpenses);

        $qb = $this->createQueryBuilder('ie');
        $qb
            ->delete()
            ->where($qb->expr()->notIn('ie.id', $ids))
            ->getQuery()
            ->execute();
    }

    public function hasCategoryAnyConnection(Category $category): bool
    {
        $qb = $this->createQueryBuilder('ir');

        return $qb
                ->select($qb->expr()->count('ir.id'))
                ->where('ir.category = :categoryId')
                ->setParameter('categoryId', $category->id)
                ->getQuery()
                ->getSingleScalarResult() !== 0;
    }
}
