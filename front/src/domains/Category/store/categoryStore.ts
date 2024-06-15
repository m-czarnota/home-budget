import { defineStore } from "pinia";
import { Categories } from "../data/model/Category";
import { ComputedRef, Ref, computed, ref } from "vue";
import { CategoryService } from "../data/service/CategoryService";
import { SubCategories } from "../data/model/SubCategory";

export const useCategoryStore = defineStore('category', () => {
    const categories: Ref<Categories|null> = ref(null);
    const flatCategories: Ref<SubCategories|null> = ref(null);

    async function getCategories(): Promise<ComputedRef<Categories>> {
        if (categories.value === null) {
            categories.value = await CategoryService.getCategories();

            flatCategories.value = [];
            for (const category of categories.value) {
                flatCategories.value.push(category);

                for (const subCategory of category.subCategories) {
                    flatCategories.value.push(subCategory);
                }
            }
        }

        return computed(() => categories.value as Categories);
    }

    async function getFlatCategories(): Promise<ComputedRef<SubCategories>> {
        if (flatCategories.value === null) {
            await CategoryService.getCategories();
        }

        return computed(() => flatCategories.value as SubCategories);
    }

    async function updateCategories(categoriesToUpdate: Categories): Promise<Categories> {
        const updatedCategories = await CategoryService.updateCategories(categoriesToUpdate);
        categories.value = updatedCategories;

        return updatedCategories;
    }

    return {
        getCategories,
        getFlatCategories,
        updateCategories,
    };
});