import { ResponseCategoryFieldChecker } from "../../../Category/data/service/ResponseCategoryFieldChecker";
import { HttpClient } from "../../../http-client/HttpClient";
import { RequestNotAcceptableError } from "../../../http-client/RequestNotAcceptableError";
import { IrregularExpense, IrregularExpenses } from "../model/IrregularExpense";
import { RequestIrregularExpenses } from "../model/RequestIrregularExpenses";
import { ResponseIrregularExpenseFieldChecker } from "./ResponseIrregularExpenseFieldChecker";

export class IrregularExpenseService {
    static readonly RESOURCE = "/expenses/irregular";

    public static async getExpenses(): Promise<IrregularExpenses> {
        try {
            const response = await HttpClient.get(IrregularExpenseService.RESOURCE);      

            if (!response.isOk) {
                throw new Error("Cannot fetch irregular expenses. Try again later.");
            }

            const expenses = response.body;
            if (!(expenses instanceof Array)) {
                throw new Error("Cannot fetch irregular expenses. Try again later.");
            }
            
            ResponseIrregularExpenseFieldChecker.checkFields(expenses);

            return IrregularExpenseService.mapResponseBodyToIrregularExpenses(expenses);
        } catch (error) {
            throw new Error(`Error while fetching categories. | ${error}`);
        }
    }

    public static async updateExpenses(irregularExpenses: IrregularExpenses): Promise<IrregularExpenses> {
        const requestIrregularExpenses = IrregularExpenseService.mapIrregularExpensesToRequest(irregularExpenses);

        const response = await HttpClient.put(IrregularExpenseService.RESOURCE, JSON.stringify(requestIrregularExpenses));
        const responseBody = response.body;

        if (response.statusCode === 400) {
            throw new Error(`Bad request sended to API. | ${responseBody}`);
        }

        if (![200, 406].includes(response.statusCode)) {
            throw new Error('Cannot save sync changes');
        }

        const responseCategories = response.body;
        if (!(responseCategories instanceof Array)) {
            throw new Error(`Cannot read response. | ${responseCategories}`);
        }

        if (response.statusCode === 406) {
            if (responseBody.length !== irregularExpenses.length) {
                throw new Error(`Response errors count ${responseBody.length} is not suitable to categories count ${irregularExpenses.length}`);
            }
            // TODO 👉 validation counts for subcategories - maybe after validation fields

            // TODO 👉 validation fields of response errors
            throw new RequestNotAcceptableError(JSON.stringify(responseBody));
        }

        ResponseCategoryFieldChecker.checkFields(responseCategories);

        return IrregularExpenseService.mapResponseBodyToIrregularExpenses(responseCategories);
    }

    private static mapResponseBodyToIrregularExpenses(responseBody: Array<any>): IrregularExpenses {
        return responseBody.map((expense: IrregularExpense) => ({
            id: expense.id,
            name: expense.name,
            cost: expense.cost,
            category: {},
            isWish: expense.isWish,
            plannedYear: expense.plannedYear,
        }));
    }

    private static mapIrregularExpensesToRequest(irregularExpenses: IrregularExpenses): RequestIrregularExpenses {
        return irregularExpenses.map((irregularExpense: IrregularExpense) => ({
            id: irregularExpense.id,
            name: irregularExpense.name,
            cost: irregularExpense.cost,
            category: String(irregularExpense.category.id),
            isWish: irregularExpense.isWish,
            plannedYear: irregularExpense.plannedYear,
        }));
    }
}