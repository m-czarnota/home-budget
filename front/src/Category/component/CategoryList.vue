<script setup lang="ts">
import { reactive, ref, computed, Ref } from 'vue';
import draggable from 'vuedraggable';
import { useI18n } from 'vue-i18n';

import VListElement from './VListElement.vue';
import Alert from '../../components/Alert.vue';

import { CategoryService } from '../data/service/CategoryService';
import { CategoryDataView } from '../data/dataView/CategoryDataView';
import { SubCategory } from '../data/model/SubCategory';
import { Category } from '../data/model/Category';

import { RequestNotAcceptableError } from '../../http-client/RequestNotAcceptableError';
import { ResponseCategoriesErrorData } from '../data/service/ResponseCategoriesErrorData';

const { t } = useI18n();
const emit = defineEmits(['loadingError']);

let categoriesDv: CategoryDataView[] = [];

try {
    const categoriesFromResponse = await CategoryService.getCategories();
    categoriesDv = reactive(categoriesFromResponse.map(category => new CategoryDataView(category))) as CategoryDataView[];
} catch (e) {
    emit('loadingError');
}

// ------------------- viewing categories and subcategories -------------------
const canViewSubcategories = (categoryDv: CategoryDataView) => {
    return categoryDv.subCategoriesCount() && categoryDv.isExpanded;
};

const categoryTitle = (categoryDv: CategoryDataView) => {
    if (!categoryDv.subCategoriesCount()) {
        return null;
    }

    return categoryDv.isExpanded 
        ? t('component.categoryList.category.hideSubcategories') 
        : t('component.categoryList.category.showSubcategories');
};

const toggleExpanding = (categoryDv: CategoryDataView) => {
    if (!categoryDv.subCategoriesCount()) {
        return;
    }

    categoryDv.switchExpand();
};

// ------------------- adding a new subcategory -------------------
const turnOnAddingANewSubCategory = (categoryDv: CategoryDataView) => {
    if (!categoryDv.isExpanded) {
        categoryDv.switchExpand();
    }

    if (categoryDv.isInAddingNewSubcategory) {
        return;
    }

    categoryDv.isInAddingNewSubcategory = true;
};

const addNewSubCategory = (categoryDv: CategoryDataView) => {
    if (!categoryDv.newSubcategoryName) {
        categoryDv.isInAddingNewSubcategory = false;
        
        return;
    }

    const position = categoryDv.subCategoriesCount();
    const subCategory: SubCategory = {
        id: null,
        name: categoryDv.newSubcategoryName,
        position: position,
        isDeleted: false,
        lastModified: null,
    };

    categoryDv.addSubCategory(subCategory);
    categoryDv.isInAddingNewSubcategory = false;
    categoryDv.newSubcategoryName = '';
};

// ------------------- removing category -------------------
const removeCategory = (categoryDv: CategoryDataView) => {
    const index = categoriesDv.indexOf(categoryDv);
    if (index === -1) {
        throw new RangeError(`Category ${categoryDv.category.name} doesn't exist`);
    }

    categoriesDv.splice(index, 1);
};

// ------------------- adding a new category -------------------
const isNewCategoryAdding = ref(false);
const newCategoryName = ref('');

const newCategoryIconTitle = () => {
    const categoryName = newCategoryName.value.trim();

    return categoryName !== ''
        ? t('component.categoryList.category.create') 
        : t('component.categoryList.category.cancel');
};

const createNewCategory = async () => {
    const categoryName = newCategoryName.value.trim();
    if (!categoryName) {
        isNewCategoryAdding.value = false;

        return;
    }

    const position = categoriesDv.length;
    const category: Category = {
        id: null,
        name: categoryName,
        position: position,
        isDeleted: false,
        lastModified: null,
        subCategories: [],
    };
    const categoryDv = new CategoryDataView(category);
    categoriesDv.push(categoryDv);

    isNewCategoryAdding.value = false;
    newCategoryName.value = '';
}

// ------------------- viewing no categories -------------------
const isNoCategories = computed(() => {
    return categoriesDv.length === 0 && isNewCategoryAdding.value === false;
});


// ----------------------------- changes in any category -----------------------------
const originalState = ref(JSON.stringify(categoriesDv.map(categoryDv => categoryDv.stringify())));
const hasAnythingBeenChanged = computed(() => {
    const stringifiedCategories = JSON.stringify(categoriesDv.map(categoryDv => categoryDv.stringify()));

    return originalState.value !== stringifiedCategories;
});

// ----------------------------- error handling -----------------------------
let categoriesErrors: Ref<ResponseCategoriesErrorData> = ref([]);
const getError = (categoryIndex: number, subCategoryIndex: number|undefined): string|null => {
    const categoryErrors = categoriesErrors.value[categoryIndex];
    if (categoryErrors === undefined) {
        return null;
    }

    if (subCategoryIndex === undefined) {
        return categoryErrors.name;
    }

    const subCategoryErrors = categoryErrors.subCategories[subCategoryIndex];

    return subCategoryErrors?.name;
};

// ----------------------------- sync changes -----------------------------
const isSyncPending = ref(false);
const showSuccessAlert = ref(false);
const showErrorAlert = ref(false);

const submit = async () => {
    try {
        isSyncPending.value = true;
        showSuccessAlert.value = false;
        showErrorAlert.value = false;
        categoriesErrors.value = [];

        const categories = categoriesDv.map(categoryDv => categoryDv.category);
        const updatedCategories = await CategoryService.updateCategories(categories);

        categoriesDv = reactive(updatedCategories.map(category => new CategoryDataView(category))) as CategoryDataView[];
        originalState.value = JSON.stringify(categoriesDv.map(categoryDv => categoryDv.stringify()));

        showSuccessAlert.value = true;
    } catch(e) {
        if (!(e instanceof Error)) {
            return;
        }

        if (e instanceof RequestNotAcceptableError) {
            categoriesErrors.value = JSON.parse(e.message) as ResponseCategoriesErrorData;

            return;
        } 

        console.error(e.message);
        showErrorAlert.value = true;
    } finally {
        isSyncPending.value = false;

        setTimeout(() => {
            showSuccessAlert.value = false;
            showErrorAlert.value = false;
        }, 5000);
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <Alert 
            v-if="showSuccessAlert" 
            @closed="showSuccessAlert = false"
            :message="$t('component.categoryList.successSave')"/>

        <Alert
            v-if="showErrorAlert"
            type="danger"
            @closed="showErrorAlert = false"
            :message="$t('component.categoryList.errorSave')"/>

        <p v-if="hasAnythingBeenChanged" class="bg-red-200 rounded p-2" role="alert">
            <font-awesome-icon 
                icon="fa-solid fa-floppy-disk"
                class="mr-2"
                :title="$t('form.unsavedChanges')" />
            <span>{{ $t('component.irregularExpenses.unsavedChanges') }}</span>
        </p>

        <p v-if="isNoCategories" class="p-2">
            {{ $t('component.categoryList.noCategories') }}
        </p>

        <draggable :list="categoriesDv" tag="ul" item-key="index" :animation="300">
            <template #item="{ element: categoryDv }">
                <li class="px-2 my-2">
                    <v-list-element 
                        @click.stop="toggleExpanding(categoryDv)"
                        :title="categoryTitle(categoryDv)"
                        class="px-2 py-4"
                        :class="{'cursor-pointer': categoryDv.subCategoriesCount()}"
                        :is-draggable="true" 
                        :is-expanded="categoryDv.isExpanded" 
                        :is-editable="true" 
                        :value="categoryDv.category.name" 
                        :can-have-sub-elements="true"
                        :sub-elements-count="categoryDv.subCategoriesCount()"
                        :error="getError(categoryDv.category.position, undefined)"
                        @add-sub-element="turnOnAddingANewSubCategory(categoryDv)"
                        @remove="removeCategory(categoryDv)"
                        @updated="(name) => categoryDv.category.name = name"/>
                    
                    <draggable :list="categoryDv.subCategories" tag="ul" item-key="index" :animation="300" v-if="canViewSubcategories(categoryDv)" class="subcategories">
                        <template #item="{ element: subcategory }">
                            <li class="flex px-2 my-2">
                                <v-list-element
                                    class="p-2"
                                    :is-editable="true"
                                    :value="subcategory.name"
                                    :error="getError(categoryDv.category.position, subcategory.position)"
                                    @remove="categoryDv.removeSubCategory(subcategory)"
                                    @updated="(name) => subcategory.name = name"/>
                            </li>
                        </template>
                    </draggable>

                    <div class="flex px-2" v-if="categoryDv.isInAddingNewSubcategory">
                        <p class="flex items-center gap-2 w-full rounded p-2 transition-colors duration-150 hover:bg-slate-200">
                            <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />

                            <input type="text" class="w-fit" v-model="categoryDv.newSubcategoryName" @keyup.enter="addNewSubCategory(categoryDv)">
                            <button type="button" class="hover:text-purple-600" @click.stop="addNewSubCategory(categoryDv)" :title="$t('component.categoryList.subcategory.create')">
                                <font-awesome-icon icon="fa-regular fa-circle-xmark" v-if="categoryDv.newSubcategoryName.trim() === ''"/>
                                <font-awesome-icon icon="fa-regular fa-circle-check" v-else/>
                            </button>
                        </p>
                    </div>
                </li>
            </template>
        </draggable>

        <div class="px-4">
            <button 
                v-if="!isNewCategoryAdding" 
                type="button" 
                class="hover:text-purple-600 text-lg" 
                :class="{'animate-bounce': isNoCategories}"
                @click.stop="isNewCategoryAdding = true" 
                :title="$t('component.categoryList.category.add')"
            >
                <font-awesome-icon icon="fa-solid fa-folder-plus" />
            </button>

            <div v-if="isNewCategoryAdding" class="space-y-2">
                <p>{{ $t('component.categoryList.category.newCategory') }}:</p>
                <div class="flex gap-2">
                    <input type="text" v-model="newCategoryName" @keyup.enter="createNewCategory()">
                    <button type="button" class="hover:text-purple-600" @click.stop="createNewCategory()" :title="newCategoryIconTitle()">
                        <font-awesome-icon icon="fa-regular fa-circle-xmark" v-if="newCategoryName.trim() === ''"/>
                        <font-awesome-icon icon="fa-regular fa-circle-check" v-else/>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" :disabled="!hasAnythingBeenChanged || isSyncPending" class="flex items-center justify-center gap-2">
            {{ $t('component.categoryList.save') }}
            <span class="loading loading-bars loading-md text-purple-700" v-if="isSyncPending"></span>
        </button>
    </form>
</template>