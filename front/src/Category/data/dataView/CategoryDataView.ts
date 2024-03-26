import { Category } from "../model/Category";
import { SubCategories, SubCategory } from "../model/SubCategory";

export class CategoryDataView {
    public newSubcategoryName: String = '';

    private _isExpanded: Boolean = false;
    private _isEdited: Boolean = false;
    private _isInAddingNewSubcategory: Boolean = false;

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

    public subCategoriesCount(): Number {
        return this.category.subCategories.length;
    }

    public stringify(): String {
        return JSON.stringify(this.category);
    }

    public get isExpanded(): Boolean {
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

    public get isInAddingNewSubcategory(): Boolean {
        return this._isInAddingNewSubcategory;
    }

    public set isInAddingNewSubcategory(state: Boolean) {
        this._isInAddingNewSubcategory = state;
    }
}