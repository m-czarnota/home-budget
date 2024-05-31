export interface ResponseBudgetEntryErrorData {
    hasError: boolean,
    category: string|null,
    cost: string|null,
    subEntries: this[],
};

export interface ResponseBudgetErrorData {
    month: string|null,
    entries: ResponseBudgetEntryErrorData[],
};