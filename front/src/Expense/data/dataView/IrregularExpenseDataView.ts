import { IrregularExpense } from "../model/IrregularExpense";

export class IrregularExpenseDataView {
    public isNew = false;

    private _isExpanded: Boolean = false;
    private _isEditingName: Boolean = false;

    constructor(
        public expense: IrregularExpense,
    ) {}

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

    public stringify(): string {
        return JSON.stringify(this.expense);
    }
}