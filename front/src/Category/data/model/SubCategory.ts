export interface SubCategory {
    id: String|null,
    name: String, 
    position: Number,
    isDeleted: Boolean,
    lastModified: String|null,
}

export type SubCategories = SubCategory[];