<?php

namespace App\Budget\Domain;

use App\Category\Domain\Category;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

class BudgetEntry
{
    public readonly string $id;
    private float $cost;
    public readonly Category $category;
    public readonly DateTimeInterface $plannedTime;
    private DateTimeImmutable $lastModified;

    /** @var Collection<string, self> $subEntries */
    private Collection $subEntries;

    private ?self $parent = null;

    /**
     * @throws BudgetEntryIsNotValidException
     */
    public function __construct(
        ?string $id,
        float $cost,
        Category $category,
        DateTimeInterface $plannedTime,
        ?DateTimeInterface $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();
        $this->cost = $cost;
        $this->category = $category;
        $this->plannedTime = $plannedTime;
        $this->lastModified = $lastModified ?? new DateTimeImmutable();
        $this->subEntries = new ArrayCollection();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new BudgetEntryIsNotValidException(json_encode($errors));
        }
    }

    public function update(self $budgetEntry): void
    {
        $this->cost = $budgetEntry->getCost();
        $this->lastModified = new DateTimeImmutable();

        foreach ($this->subEntries as $key => $subEntry) {
            $subEntry->update($budgetEntry->getEntry($key));
        }
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

    public function addSubEntry(self $subEntry): self
    {
        $this->subEntries->set($subEntry->id, $subEntry);

        return $this;
    }

    /**
     * @return array<int, BudgetEntry>
     */
    public function getSubEntries(): array
    {
        return $this->subEntries->getValues();
    }

    public function getEntry(string $id): ?self
    {
        return $this->subEntries->get($id);
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