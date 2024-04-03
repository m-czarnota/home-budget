import { Category } from "../../../Category/data/model/Category";

export interface IrregularExpense {
    id: string|null,
    name: string,
    cost: number,
    category: Category,
    isWish: boolean,
    plannedYear: number,
};

export type IrregularExpenses = IrregularExpense[];