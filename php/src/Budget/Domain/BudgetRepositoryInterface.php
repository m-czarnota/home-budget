<?php

namespace App\Budget\Domain;

use DateTimeImmutable;

interface BudgetRepositoryInterface
{
    public function findByPeriod(BudgetPeriod $period): ?Budget;

    public function update(Budget $budget): void;

    public function add(Budget $budget): void;
}