import { HttpClient } from "../../../http-client/HttpClient";
import { RequestNotAcceptableError } from "../../../http-client/RequestNotAcceptableError";
import { Budget } from "../model/Budget";
import { BudgetEntry } from "../model/BudgetEntry";
import { ResponseBudgetFieldChecker } from "./ResponseBudgetFieldChecker";

export class BudgetService {
    static readonly RESOURCE = '/budget';

    public static async getBudgetForCurrentMonth(): Promise<Budget> {
        const now = new Date();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();

        const url = `${BudgetService.RESOURCE}/?month=${month}&year=${year}`;
        
        return await BudgetService.getBudget(url);
    }

    public static async getBudgetForNextMonth(): Promise<Budget> {
        const now = new Date();
        const month = now.getMonth() + 1 + 1;
        const year = now.getFullYear();

        const url = `${BudgetService.RESOURCE}/?month=${month}&year=${year}`;
        
        return await BudgetService.getBudget(url);
    }

    public static async update(budget: Budget): Promise<Budget> {
        const response = await HttpClient.put(BudgetService.RESOURCE, JSON.stringify(budget));
        const responseBody = response.body;

        if (response.statusCode === 400) {
            throw new Error(`Bad request sended to API. | ${responseBody}`);
        }

        if (![200, 406].includes(response.statusCode)) {
            throw new Error('Cannot save sync changes');
        }

        const responseBudget = response.body;
        if (!(responseBudget instanceof Object)) {
            throw new Error(`Cannot read response. | ${responseBudget}`);
        }

        if (response.statusCode === 406) {
            // TODO 👉 validation counts for subentries - maybe after validation fields

            // TODO 👉 validation fields of response errors
            throw new RequestNotAcceptableError(JSON.stringify(responseBody));
        }

        ResponseBudgetFieldChecker.checkFields(responseBudget);

        return BudgetService.mapResponseBodyToBudget(budget);
    }

    private static async getBudget(url: string): Promise<Budget> {
        try {
            const response = await HttpClient.get(url);      

            if (!response.isOk) {
                throw new Error("Cannot fetch categories. Try again later.");
            }

            const budget = response.body;
            if (!(budget instanceof Object)) {
                throw new Error("Cannot fetch categories. Try again later.");
            }
            
            ResponseBudgetFieldChecker.checkFields(budget);

            return BudgetService.mapResponseBodyToBudget(budget);
        } catch (error) {
            throw new Error(`Error while fetching categories. | ${error}`);
        }
    }

    private static mapResponseBodyToBudget(responseBody: any): Budget {
        return {
            month: responseBody.month,
            entries: responseBody.entries.map((budgetEntry: BudgetEntry) => ({
                id: budgetEntry.id,
                cost: budgetEntry.cost,
                categoryId: budgetEntry.categoryId,
                categoryName: budgetEntry.categoryName,
                subEntries: budgetEntry.subEntries.map(subEntry => ({
                    id: subEntry.id,
                    cost: subEntry.cost,
                    categoryId: subEntry.categoryId,
                    categoryName: subEntry.categoryName,
                })),
            })),
        };
    }
};