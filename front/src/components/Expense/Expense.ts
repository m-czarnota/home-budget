export class Expense {
    private _isExpanded: Boolean = false;
    private originalState: String = '';

    constructor(
        public name: String,
        public cost: Number,
        public category,
        public isWhim: Boolean = false,
        public isIrregular: Boolean = false,
    ) {
        this.originalState = this.buildState();
    }

    public get isExpanded(): Boolean {
        return this._isExpanded;
    }

    public switchExpand(): void {
        this._isExpanded = !this._isExpanded;
    }

    private buildState(): String {
        return JSON.stringify({
            name: this.name,
            cost: this.cost,
            category: this.category,
            isWhim: this.isWhim,
            isIrregular: this.isIrregular,
        });
    }

    public isChanged(): Boolean {
        return this.originalState !== this.buildState();
    }
}