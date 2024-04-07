import { IrregularExpense } from "../model/IrregularExpense";
import { IrregularExpenseError } from "../model/IrregularExpensesError";

export class IrregularExpenseDataView {
    private _isExpanded: Boolean = false;
    private _isEditingName: Boolean = false;

    public errors: IrregularExpenseError;

    constructor(
        public expense: IrregularExpense,
    ) {
        this.errors = this.generateEmptyErrors();
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

    public stringify(): string {
        return JSON.stringify(this.expense);
    }

    public resetErrors(): void {
        this.errors = this.generateEmptyErrors();
    }

    private generateEmptyErrors(): IrregularExpenseError {
        return {
            hasError: false,
            name: null,
            position: null,
            cost: null,
            plannedYear: null,
            category: null,
        }
    }
}