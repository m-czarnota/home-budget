import { SubCategories, SubCategory } from "./SubCategory";

export interface Category extends SubCategory {
    subCategories: SubCategories,
}

export type Categories = Category[];