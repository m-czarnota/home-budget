import { CategorySelect } from "../../../Category/data/model/CategorySelect";

export interface IrregularExpense {
    id: string|null,
    name: string,
    position: number,
    cost: number,
    category: CategorySelect,
    isWish: boolean,
    plannedYear: number,
};

export type IrregularExpenses = IrregularExpense[];