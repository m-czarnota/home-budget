<?php

namespace App\Budget\Domain;

class Budget
{
    /** @var array<string, BudgetEntry> $entries */
    private array $entries = [];

    public function __construct(
        public readonly BudgetPeriod $period,
    ) {
    }

    public function update(self $budget): void
    {
        foreach ($budget->getEntries() as $entry) {
            $existedEntry = $this->getEntry($entry->id);
            if ($existedEntry) {
                $existedEntry->update($entry);
                continue;
            }

            $this->addEntry($entry);
        }
    }

    /**
     * @throws InvalidBudgetPeriodException
     */
    public function addEntry(BudgetEntry $entry): self
    {
        if (!$this->period->isDateInPeriod($entry->plannedTime)) {
            throw new InvalidBudgetPeriodException('Budget entry is not in budget range');
        }

        $this->entries[$entry->id] = $entry;

        return $this;
    }

    /**
     * @return array<int, BudgetEntry>
     */
    public function getEntries(): array
    {
        return array_values($this->entries);
    }

    public function getEntry(string $entryId): ?BudgetEntry
    {
        return $this->entries[$entryId] ?? null;
    }
}