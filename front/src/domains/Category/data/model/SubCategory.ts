export interface SubCategory {
    id: string|null,
    name: string, 
    position: number,
    isDeleted: boolean,
    lastModified: string|null,
}

export type SubCategories = SubCategory[];