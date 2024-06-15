import { Category } from "../model/Category";
import { SubCategories, SubCategory } from "../model/SubCategory";

export class CategoryDataView {
    public newSubcategoryName: string = '';

    private _isExpanded: boolean = false;
    private _isEdited: boolean = false;
    private _isInAddingNewSubcategory: boolean = false;

    constructor(
        public category: Category,
    ) {}

    public addSubCategory(subCategory: SubCategory): void {
        this.category.subCategories.push(subCategory);
    }

    public removeSubCategory(subcategory: SubCategory): void {
        const category = this.category;
        const index = category.subCategories.indexOf(subcategory);
        
        if (index === -1) {
            throw new RangeError(`Subcategory ${subcategory.name} doesn't belong to category ${category.name}`);
        }

        category.subCategories.splice(index, 1);
    }

    public get subCategories(): SubCategories {
        return this.category.subCategories;
    }

    public subCategoriesCount(): number {
        return this.category.subCategories.length;
    }

    public stringify(): string {
        return JSON.stringify(this.category);
    }

    public get isExpanded(): boolean {
        return this._isExpanded;
    }

    public switchExpand(): void {
        this._isExpanded = !this._isExpanded;
    }

    public get isEdited(): Boolean {
        return this._isEdited;
    }

    public switchEdition(): void {
        this._isEdited = !this._isEdited;
    }

    public get isInAddingNewSubcategory(): boolean {
        return this._isInAddingNewSubcategory;
    }

    public set isInAddingNewSubcategory(state: boolean) {
        this._isInAddingNewSubcategory = state;
    }
}