import { BudgetEntry } from "./BudgetEntry";

export interface Budget {
    month: number,
    entries: Array<BudgetEntry>
};