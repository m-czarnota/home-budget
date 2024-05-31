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
use ReflectionClass;
use function Doctrine\ORM\QueryBuilder;

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

            foreach ($entry->getSubEntries() as $subEntry) {
                $this->addSubEntry($subEntry, $entry);
            }
        }
    }

    public function update(Budget $budget): void
    {
        $this->add($budget);
    }

    /**
     * @throws InvalidBudgetPeriodException
     */
    public function findByPeriod(BudgetPeriod $period): ?Budget
    {
        $qb = $this->createQueryBuilder('be');
        $entries = $qb
            ->where('be.plannedTime >= :startDate')
            ->andWhere('be.plannedTime <= :endDate')
            ->andWhere($qb->expr()->isNull('be.parent'))
            ->setParameter('startDate', $period->startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $period->endDate->format('Y-m-d H:i:s'))
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

    private function addSubEntry(BudgetEntry $subEntry, BudgetEntry $entry): void
    {
        // resolve doctrine proxy class problem with reflection and not existed property within this proxy class
        $existedSubEntry = $this->find($subEntry->id);
        if ($existedSubEntry) {
            return;
        }

        $reflectionClass = new ReflectionClass($subEntry);
        $parent = $reflectionClass->getProperty('parent');
        $parent->setAccessible(true);
        $parent->setValue($subEntry, $entry);

        $this->getEntityManager()->persist($subEntry);
    }
}