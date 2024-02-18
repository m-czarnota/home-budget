import { Category } from "./Category";

export class SubCategory {
    private _isEdited: Boolean = false;

    constructor(
        public name: String, 
        public parent: Category,
    ) {
    }

    public get isEdited(): Boolean {
        return this._isEdited;
    }

    public switchEdition(): void {
        this._isEdited = !this._isEdited;
    }
}