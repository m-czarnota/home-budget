export interface ResponseIrregularExpenseErrorData {
    hasError: boolean,
    name: string|null,
    position: string|null,
    cost: string|null,
    plannedYear: string|null,
    category: string|null,
};

export type ResponseIrregularExpensesErrorData = ResponseIrregularExpenseErrorData[];