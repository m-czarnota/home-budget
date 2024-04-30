<?php

namespace App\Budget\Infrastructure\ORM;

use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetEntry;
use App\Budget\Domain\BudgetPeriod;
use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\InvalidBudgetPeriodException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineBudgetRepository extends ServiceEntityRepository implements BudgetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetEntry::class);
    }

    public function add(Budget $budget): void
    {
        $em = $this->getEntityManager();
        foreach ($budget->getEntries() as $entry) {
            $em->persist($entry);
        }
    }

    public function update(Budget $budget): void
    {
        // adding new entries when they don't exist
        $this->add($budget);

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        // finding original entries ids which aren't belong to passed $budget
        $stmt = $connection->prepare('SELECT id FROM budget_entry WHERE budget_id NOT IN :id');
        $stmt->bindValue(
            'id',
            implode(
                ',',
                array_map(fn(BudgetEntry $budgetEntry) => $budgetEntry->id, $budget->getEntries())
            )
        );
        $entryToRemoveIds = $stmt->executeQuery()->fetchFirstColumn();

        // removing entries when they aren't in updated budget
        foreach ($entryToRemoveIds as $entryToRemoveId) {
            $entry = $this->find($entryToRemoveId);

            // checking if it can be deleted
            $em->remove($entry);
        }
    }

    /**
     * @throws InvalidBudgetPeriodException
     */
    public function findByPeriod(BudgetPeriod $period): ?Budget
    {
        $entries = $this->createQueryBuilder('be')
            ->where('be.plannedTime >= :startDate')
            ->andWhere('be.plannedTime <= :endDate')
            ->setParameters(new ArrayCollection([
                'startDate' => $period->startDate,
                'endDate' => $period->endDate,
            ]))
            ->getQuery()
            ->getResult();

        if (empty($entries)) {
            return null;
        }

        $budget = new Budget($period);
        foreach ($entries as $entry) {
            $budget->addEntry($entry);
        }

        return $budget;
    }
}