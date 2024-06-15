import { SubCategory } from "@/domains/Category/data/model/SubCategory";

export interface IrregularExpense {
    id: string|null,
    name: string,
    position: number,
    cost: number,
    category: SubCategory,
    isWish: boolean,
    plannedYear: number,
};

export type IrregularExpenses = IrregularExpense[];