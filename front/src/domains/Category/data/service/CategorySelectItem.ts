import { VSelectData, VSelectItem } from "@/components/VSelect/VSelectData";
import { Categories, Category } from "../model/Category";
import { SubCategories, SubCategory } from "../model/SubCategory";

export function mapCategoryToSelectItem(category: Category): VSelectItem {

    return {
        id: category.id,
        name: category.name,
        subItems: category.subCategories?.map(subCategory => ({
            id: subCategory.id,
            name: subCategory.name,
            subItems: [],
        })) || [],
    };
}

export function mapCategoriesToSelectData(categories: Categories): VSelectData {
    return categories.map(category => mapCategoryToSelectItem(category));
}

export function findSelectItemInCategories(selectItem: VSelectItem, flatCategories: SubCategories): SubCategory {
    const category = flatCategories.find(category => category.id === selectItem.id);
    if (category === undefined) {
        throw new Error(`Category ${selectItem.id} doesn't exist in categories: ${JSON.stringify(flatCategories)}`);
    }

    return category;
}