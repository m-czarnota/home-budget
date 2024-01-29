import { SubCategory } from "./SubCategory";

export class Category {
    public newSubcategoryName: String = '';

    private _isExpanded: Boolean = false;
    private _isEdited: Boolean = false;
    private _isInAddingNewSubcategory: Boolean = false;

    constructor(
        public name: String, 
        public subcategories: Array<SubCategory> = [], 
    ) {
    }

    public removeSubCategory(subcategory: SubCategory): void {
        const index = this.subcategories.indexOf(subcategory);
        
        if (index === -1) {
            throw new RangeError(`Subcategory ${subcategory.name} doesn't belong to category ${this.name}`);
        }

        this.subcategories.splice(index, 1);
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