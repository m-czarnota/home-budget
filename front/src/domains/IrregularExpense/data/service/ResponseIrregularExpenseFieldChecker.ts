type UncheckedIrregularExpense = {
    [key: string]: unknown;
};

export class ResponseIrregularExpenseFieldChecker {
    static readonly requiredFields = [
        'id',
        'name',
        'position',
        'cost',
        'category',
        'isWish',
        'plannedYear',
    ];

    public static checkFields(irregularExpenses: Array<any>): void {
        irregularExpenses.forEach((irregularExpense: UncheckedIrregularExpense) => {
            const expenseId = irregularExpense.id || null;
            ResponseIrregularExpenseFieldChecker.requiredFields.forEach(field => {
                if (irregularExpense[field] === undefined) {
                    throw new Error(`No ${field} field in category ${expenseId}`)
                }
            });
        });
    }
}