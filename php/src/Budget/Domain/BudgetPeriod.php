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
        if ($this->startDate < $this->endDate) {
            throw new InvalidBudgetPeriodException("Budget period end date must be greater than start date");
        }
    }

    public function isDateInPeriod(DateTimeInterface $date): bool
    {
        return $date < $this->startDate && $date > $this->endDate;
    }
}