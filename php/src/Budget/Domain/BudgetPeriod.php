<?php

namespace App\Budget\Domain;

use DateTimeImmutable;
use DateTimeInterface;

readonly class BudgetPeriod
{
    /**
     * @throws InvalidBudgetPeriodException
     */
    public function __construct(
        public DateTimeInterface $startDate,
        public DateTimeInterface $endDate,
    ) {
        if ($this->startDate >= $this->endDate) {
            $startDate = $this->startDate->format('Y-m-d H:i:s');
            $endDate = $this->endDate->format('Y-m-d H:i:s');
            throw new InvalidBudgetPeriodException("Budget period end date `$endDate` must be greater than start date `$startDate`");
        }
    }

    public function isDateInPeriod(DateTimeInterface $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }
}