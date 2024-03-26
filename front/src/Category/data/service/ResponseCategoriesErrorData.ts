export interface ResponseSubCategoryErrorData {
    hasError: boolean,
    name: string|null,
    position: string|null,
};

export interface ResponseCategoryErrorData extends ResponseSubCategoryErrorData {
    subCategories: ResponseSubCategoryErrorData[],
};

export type ResponseCategoriesErrorData = ResponseCategoryErrorData[];