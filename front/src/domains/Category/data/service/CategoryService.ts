import { VSelectData } from "@/components/VSelect/VSelectData";
import { HttpClient } from "@/http-client/HttpClient";
import { RequestNotAcceptableError } from "@/http-client/RequestNotAcceptableError";
import { Categories, Category } from "../model/Category";
import { SubCategory } from "../model/SubCategory";
import { ResponseCategoryFieldChecker } from "./ResponseCategoryFieldChecker";

export class CategoryService {
    static readonly RESOURCE = "/categories";

    public static async getCategories(): Promise<Categories> {
        try {
            const response = await HttpClient.get(CategoryService.RESOURCE);      

            if (!response.isOk) {
                throw new Error("Cannot fetch categories. Try again later.");
            }

            const categories = response.body;
            if (!(categories instanceof Array)) {
                throw new Error("Cannot fetch categories. Try again later.");
            }
            
            ResponseCategoryFieldChecker.checkFields(categories);

            return CategoryService.mapResponseBodyToCategories(categories);
        } catch (error) {
            throw new Error(`Error while fetching categories. | ${error}`);
        }
    }

    public static async getCategoriesToSelect(): Promise<VSelectData> {
        const categories = await CategoryService.getCategories();

        return categories.map((category: Category) => ({
            id: category.id,
            name: category.name,
            subItems: category.subCategories.map((subCategory: SubCategory) => ({
                id: subCategory.id,
                name: subCategory.name,
                subItems: [],
            })),
        }));
    }

    public static async updateCategories(categories: Categories): Promise<Categories> {
        // set position in categories
        for (const [index, category] of categories.entries()) {
            category.position = index;

            for (const [subIndex, subCategory] of category.subCategories.entries()) {
                subCategory.position = subIndex;
            }
        }

        const response = await HttpClient.put(CategoryService.RESOURCE, JSON.stringify(categories));
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
            if (responseBody.length !== categories.length) {
                throw new Error(`Response errors count ${responseBody.length} is not suitable to categories count ${categories.length}`);
            }
            // TODO 👉 validation counts for subcategories - maybe after validation fields

            // TODO 👉 validation fields of response errors
            throw new RequestNotAcceptableError(JSON.stringify(responseBody));
        }

        ResponseCategoryFieldChecker.checkFields(responseCategories);

        return CategoryService.mapResponseBodyToCategories(responseCategories);
    }

    private static mapResponseBodyToCategories(responseBody: Array<any>): Categories {
        return responseBody.map((category: Category) => ({
            id: category.id,
            name: category.name,
            position: category.position,
            isDeleted: category.isDeleted,
            lastModified: category.lastModified,
            subCategories: category.subCategories.map((subCategory: SubCategory) => ({
                id: subCategory.id,
                name: subCategory.name,
                position: subCategory.position,
                isDeleted: subCategory.isDeleted,
                lastModified: subCategory.lastModified,
            })),
        }));
    }
}