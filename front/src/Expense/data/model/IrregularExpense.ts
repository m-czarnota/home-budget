export class IrregularExpense {
    public isNew = false;

    private _isExpanded: Boolean = false;
    private _isEditingName: Boolean = false;
    private originalState: String = '';

    constructor(
        public name: String,
        public cost: Number,
        public category,
        public isWish: Boolean = false,
        public plannedYear: Number = new Date().getFullYear(),
    ) {
        this.originalState = this.buildState();
    }

    public get isExpanded(): Boolean {
        return this._isExpanded;
    }

    public switchExpand(): void {
        this._isExpanded = !this._isExpanded;
    }

    public get isEditingName(): Boolean {
        return this._isEditingName;
    }

    public switchEditingName(): void {
        this._isEditingName = !this._isEditingName;
    }

    private buildState(): String {
        return JSON.stringify({
            name: this.name,
            cost: this.cost,
            category: this.category,
            isWish: this.isWish,
            plannedYear: this.plannedYear,
        });
    }

    public isChanged(): Boolean {
        if (this.isNew) {
            return false;
        }

        return this.originalState !== this.buildState();
    }

    public stringify(): String {
        return JSON.stringify({
            name: this.name,
            cost: this.cost,
            category: this.category,
            isWish: this.isWish,
            plannedYear: this.plannedYear,
        });
    }
}