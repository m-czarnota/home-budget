import { Person } from "../model/Person";

export class PersonDataView {
    public isSaveFail: boolean = false;
    public error: string|null = null;
    public isSaving: boolean = false;
    
    public isRemoveError: boolean = false;

    constructor(
        public person: Person,
    ) {}

    public reset(): void {
        this.isSaveFail = false;
        this.error = null;
        this.isSaving = false;
        this.isRemoveError = false;
    }
}