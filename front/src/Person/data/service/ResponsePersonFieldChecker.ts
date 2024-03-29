type UncheckedPerson = {
    [key: string]: unknown;
};

export class ResponsePersonFieldChecker {
    static readonly requiredFields = [
        'id',
        'name',
        'isDeleted',
        'lastModified',
    ];

    public static checkFields(people: Array<any>): void {
        people.forEach((person: UncheckedPerson) => {
            const personId = person.id || null;
            ResponsePersonFieldChecker.requiredFields.forEach(field => {
                if (person[field] === undefined) {
                    throw new Error(`No ${field} field in person ${personId}`);
                }
            });
        });
    }
}