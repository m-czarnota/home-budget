<?php

namespace App\Expense\Domain;

use App\Category\Domain\Category;
use DateTimeImmutable;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class Expense implements JsonSerializable
{
    public readonly string $id;

    private DateTimeImmutable $dateOfExpense;

    /**
     * @throws ExpenseNotValidException
     */
    public function __construct(
        ?string $id,
        private string $name,
        private float $cost,
        private Category $category,
        private array $people,
        private bool $isWish = false,
        ?DateTimeImmutable $dateOfExpense = null,
    ) {
        $this->id = $id ?? Uuid::uuid7();
        $this->dateOfExpense = $dateOfExpense ?? new DateTimeImmutable();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new ExpenseNotValidException(json_encode($errors));
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cost' => $this->cost,
            'category' => $this->category->id,
            'people' => $this->people,
            'isWish' => $this->isWish,
        ];
    }

    private function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Name cannot be empty';
        }
        if (strlen($this->name) > 255) {
            $errors['name'] = 'Name cannot be longer than 255 characters';
        }

        if ($this->cost < 0) {
            $errors['cost'] = 'Cost cannot be negative';
        }
        if ($this->cost === 0.0) {
            $errors['cost'] = 'Cost cannot be 0';
        }

        return $errors;
    }
}
