export interface ResponseIrregularExpenseErrorData {
    hasError: boolean,
    name: string|null,
    cost: number|null,
};

export type ResponseIrregularExpensesErrorData = ResponseIrregularExpenseErrorData[];