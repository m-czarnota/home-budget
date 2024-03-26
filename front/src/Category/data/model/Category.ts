import { SubCategories } from "./SubCategory";

export interface Category {
    id: String|null,
    name: String, 
    position: Number,
    isDeleted: Boolean,
    lastModified: String|null,
    subCategories: SubCategories,
}

export type Categories = Category[];