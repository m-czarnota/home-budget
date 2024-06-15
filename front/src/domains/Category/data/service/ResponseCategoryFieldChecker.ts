type UncheckedCategory = {
    [key: string]: unknown;
};

export class ResponseCategoryFieldChecker {
    static readonly requiredFields = [
        'id',
        'name',
        'position',
        'isDeleted',
        'lastModified',
    ];

    public static checkFields(categories: Array<any>): void {
        categories.forEach((category: UncheckedCategory) => {
            const categoryId = category.id || null;
            ResponseCategoryFieldChecker.requiredFields.forEach(field => {
                if (category[field] === undefined) {
                    throw new Error(`No ${field} field in category ${categoryId}`);
                }
            });

            const subCategories: any = category['subCategories'];
            if (!(subCategories instanceof Array)) {
                throw new Error(`Subcategories isn't an array`);
            }

            subCategories.forEach((subCategory: UncheckedCategory) => {
                const subCategoryId = subCategory.id || null;
                ResponseCategoryFieldChecker.requiredFields.forEach(field => {
                    if (!subCategory[field] === undefined) {
                        throw new Error(`No ${field} field in sub category ${subCategoryId} belonged to category ${categoryId}`);
                    }
                });
            });
        });
    }
}