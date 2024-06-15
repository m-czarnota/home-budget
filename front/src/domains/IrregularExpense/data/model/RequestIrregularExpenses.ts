export interface RequestIrregularExpense {
    id: string|null,
    name: string,
    position: number,
    cost: number, 
    category: string,
    isWish: boolean,
    plannedYear: number,
};

export type RequestIrregularExpenses = RequestIrregularExpense[];