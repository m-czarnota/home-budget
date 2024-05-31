export interface BudgetEntry {
    id: string,
    cost: number,
    categoryId: string,
    categoryName: string,
    subEntries: Array<BudgetEntry>,
};