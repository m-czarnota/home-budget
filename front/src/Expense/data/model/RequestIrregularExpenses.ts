export interface RequestIrregularExpense {
    id: string|null,
    name: string,
    cost: number, 
    category: string,
    isWish: boolean,
    plannedYear: number,
};

export type RequestIrregularExpenses = RequestIrregularExpense[];