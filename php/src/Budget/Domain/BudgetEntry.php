<?php

namespace App\Budget\Domain;

use App\Category\Domain\Category;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;

class BudgetEntry
{
    public readonly string $id;
    private string $cost;
    public readonly Category $category;
    public readonly DateTimeImmutable $plannedTime;
    private DateTimeImmutable $lastModified;

    /**
     * @throws BudgetEntryIsNotValidException
     */
    public function __construct(
        ?string $id,
        float $cost,
        Category $category,
        DateTimeImmutable $plannedTime,
        ?DateTimeInterface $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();
        $this->cost = $cost;
        $this->category = $category;
        $this->plannedTime = $plannedTime;
        $this->lastModified = $lastModified ?? new DateTimeImmutable();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new BudgetEntryIsNotValidException(json_encode($errors));
        }
    }

    public function update(self $budgetEntry): void
    {
        $this->cost = $budgetEntry->getCost();

        $this->lastModified = new DateTimeImmutable();
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->id)) {
            $errors['id'] = 'ID cannot be empty';
        }
        if (!Uuid::isValid($this->id)) {
            $errors['id'] = 'Budget ID is not valid UUID';
        }

        $now = new DateTimeImmutable();
        if ($this->plannedTime->format('Y-m') < $now->format('Y-m')) {
            $errors['plannedTime'] = 'Planned time cannot be earlier than current month';
        }

        if ($this->cost < 0) {
            $errors['cost'] = 'Cost cannot be negative';
        }

        return $errors;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getLastModified(): DateTimeInterface
    {
        return $this->lastModified;
    }
}