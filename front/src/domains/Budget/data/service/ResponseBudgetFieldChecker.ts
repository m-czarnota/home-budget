type UncheckedBudget = {
    [key: string]: unknown;
};

export class ResponseBudgetFieldChecker {
    static readonly requiredFields = [
        'month',
        'entries',
    ];
    static readonly requiredEntriesFields = [
        'id',
        'cost',
        'categoryId',
        'categoryName',
    ];

    public static checkFields(budget: any): void {
        ResponseBudgetFieldChecker.requiredFields.forEach((field: string) => {
            if (budget.hasOwnProperty(field) === false) {
                throw new Error(`No ${field} field in budget`);
            }
        });

        const entries: any = budget['entries'];
        entries.forEach((budgetEntry: UncheckedBudget) => {
            const entryId = budgetEntry.id || null;
            ResponseBudgetFieldChecker.requiredEntriesFields.forEach(field => {
                if (budgetEntry[field] === undefined) {
                    throw new Error(`No ${field} field in category ${entryId}`);
                }
            });
        });
    }
};