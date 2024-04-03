import { SubCategories } from "./SubCategory";

export interface Category {
    id: string|null,
    name: string, 
    position: number,
    isDeleted: boolean,
    lastModified: string|null,
    subCategories: SubCategories,
}

export type Categories = Category[];