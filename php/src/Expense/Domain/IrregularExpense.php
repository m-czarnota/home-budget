<?php

namespace App\Expense\Domain;

use App\Category\Domain\Category;
use DateTimeImmutable;

class IrregularExpense extends AbstractExpense
{
    private int $position;
    private int $plannedYear;

    public function __construct(
        ?string $id,
        string $name,
        float $cost,
        Category $category,
        int $position,
        bool $isWish = false,
        ?int $plannedYear = null,
    ) {
        try {
            parent::__construct($id, $name, $cost, $category, $isWish);
        } catch (ExpenseNotValidException) {
        }

        $this->position = $position;
        $this->plannedYear = $plannedYear ?? (new DateTimeImmutable())->format('Y');

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new ExpenseNotValidException(json_encode($errors));
        }
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'category' => $this->category->id,
                'position' => $this->position,
                'plannedYear' => $this->plannedYear,
            ]
        );
    }

    public function update(self $irregularExpense): self
    {
        $this->name = $irregularExpense->getName();
        $this->cost = $irregularExpense->getCost();
        $this->category = $irregularExpense->getCategory();
        $this->position = $irregularExpense->getPosition();
        $this->isWish = $irregularExpense->isWish();
        $this->plannedYear = $irregularExpense->getPlannedYear();

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isWish(): bool
    {
        return $this->isWish;
    }

    public function getPlannedYear(): int
    {
        return $this->plannedYear;
    }

    protected function validate(): array
    {
        $errors = parent::validate();

        if ($this->position < 0) {
            $errors['position'] = 'Position cannot be negative';
        }

        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));
        if ($this->plannedYear < $currentYear) {
            $errors['plannedYear'] = "Planned year cannot be earlier than current year $currentYear";
        }

        return $errors;
    }
}
