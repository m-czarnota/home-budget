<?php

declare(strict_types=1);

namespace App\Expense\Domain;

use App\Category\Domain\Category;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

abstract class AbstractExpense implements JsonSerializable
{
    public readonly string $id;

    /**
     * @throws ExpenseNotValidException
     */
    public function __construct(
        ?string $id,
        protected string $name,
        protected float $cost,
        protected Category $category,
        protected bool $isWish = false,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();

        $errors = self::validate();
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
            'isWish' => $this->isWish,
        ];
    }

    protected function validate(): array
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
