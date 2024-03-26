<?php

declare(strict_types=1);

namespace App\Expense\Domain;

use App\Category\Domain\Category;
use App\Person\Domain\Person;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CurrentExpense extends AbstractExpense
{
    public readonly string $id;

    private DateTimeImmutable $dateOfExpense;

    private Collection $people;

    /**
     * @throws ExpenseNotValidException
     */
    public function __construct(
        ?string $id,
        string $name,
        float $cost,
        Category $category,
        bool $isWish = false,
        ?DateTimeImmutable $dateOfExpense = null,
    ) {
        parent::__construct($id, $name, $cost, $category, $isWish);

        $this->dateOfExpense = $dateOfExpense ?? new DateTimeImmutable();
        $this->people = new ArrayCollection();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'people' => $this->people->getKeys(),
                'dateOfExpense' => $this->dateOfExpense,
            ]
        );
    }

    public function addPerson(Person $person): self
    {
        $this->people->set($person->id, $person);

        return $this;
    }

    public function removePerson(Person $person): self
    {
        $this->people->remove($person->id);

        return $this;
    }

    /**
     * @return array<int, Person>
     */
    public function getPeople(): array
    {
        return $this->people->getValues();
    }
}
