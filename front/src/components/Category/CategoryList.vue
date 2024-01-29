<script setup>
import { reactive, ref } from 'vue';
import draggable from 'vuedraggable';
import { useI18n } from 'vue-i18n';
import { Category } from './Category';
import { SubCategory } from './SubCategory';
import { CategoryFactory } from './CategoryFactory';

const { t } = useI18n();

const response = {
    'Zakupy spożywcze': [
        'Picie',
        'Picie i jedzenie',
    ],
    'Opłaty': [],
};

// ------------------- mapping categories from response to objects -------------------
const categories = reactive(CategoryFactory.createFromResponse(response));

// ------------------- viewing categories and subcategories -------------------
const canViewSubcategories = (category) => {
    return category.subcategories.length && category.isExpanded;
};

const categoryTitle = (category) => {
    if (!category.subcategories.length) {
        return null;
    }

    return category.isExpanded 
        ? t('component.categoryList.category.hideSubcategories') 
        : t('component.categoryList.category.showSubcategories');
};

// ------------------- adding a new subcategory -------------------
const turnOnAddingANewSubCategory = (category) => {
    if (!category.isExpanded) {
        category.switchExpand();
    }

    if (category.isInAddingNewSubcategory) {
        return;
    }

    category.isInAddingNewSubcategory = true;
};

const addNewSubCategory = (category) => {
    if (!category.newSubcategoryName) {
        category.isInAddingNewSubcategory = false;
        
        return;
    }

    const subcategory = new SubCategory(category.newSubcategoryName, category);

    category.subcategories.push(subcategory);
    category.isInAddingNewSubcategory = false;
    category.newSubcategoryName = '';
};

// ------------------- removing category -------------------
const removeCategory = (category) => {
    const index = categories.indexOf(category);
    if (index === -1) {
        throw new RangeError(`Category ${category.name} doesn't exist`);
    }

    categories.splice(index, 1);
};

// ------------------- adding a new category -------------------
const isNewCategoryAdding = ref(null);
let newCategoryName = '';

const createNewCategory = () => {
    if (newCategoryName) {
        const category = new Category(newCategoryName);
        categories.push(category);
    }

    isNewCategoryAdding.value = false;
    newCategoryName = '';
}

</script>

<template>
    <div class="category-list">
        <draggable :list="categories" tag="ul" item-key="index" :animation="300">
            <template #item="{ element: category }">
                <li class="px-2 my-2">
                    <p 
                        class="cursor-pointer select-none flex items-center gap-2 w-fit rounded px-2 py-4 text-lg transition-colors duration-150 hover:bg-slate-200 group"
                        @click.stop="category.switchExpand()"
                        :title="categoryTitle(category)"
                    >
                        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" />

                        <font-awesome-icon 
                            v-if="category.subcategories.length"
                            icon="fa-solid fa-angle-right" 
                            class="text-slate-600 cursor-pointer" 
                            :class="{'rotate-90': category.isExpanded}" />
                        <font-awesome-icon
                            v-else 
                            icon="fa-solid fa-minus" />

                        <div :class="{'py-0': category.isEdited, 'py-1.5': !category.isEdited}">
                            <input type="text" v-model="category.name" v-if="category.isEdited" @keyup.enter="category.switchEdition()" @click.stop class="w-fit">
                            <span v-else>
                                {{ category.name }}
                            </span>
                        </div>

                        <div class="flex gap-2 invisible group-hover:visible">
                            <button type="button" class="hover:text-purple-600" @click.stop="category.switchEdition()">
                                <font-awesome-icon icon="fa-regular fa-circle-check" v-if="category.isEdited" :title="$t('component.categoryList.category.change')"/>
                                <font-awesome-icon icon="fa-solid fa-file-pen" v-else :title="$t('component.categoryList.category.edit')"/>
                            </button>
                            <button type="button" class="hover:text-purple-600" @click.stop="turnOnAddingANewSubCategory(category)" :title="$t('component.categoryList.category.addSubcategory')">
                                <font-awesome-icon icon="fa-solid fa-file-circle-plus" />
                            </button>
                            <button type="button" class="hover:text-purple-600" @click.stop="removeCategory(category)" :title="$t('component.categoryList.category.delete')">
                                <font-awesome-icon icon="fa-solid fa-trash" />
                            </button>
                        </div>
                    </p>
                    
                    <draggable :list="category.subcategories" tag="ul" item-key="index" :animation="300" v-if="canViewSubcategories(category)" class="subcategories">
                        <template #item="{ element: subcategory }">
                            <li class="flex px-2 my-2">
                                <p class="flex items-center gap-2 w-fit rounded p-2 hover:shadow-inner hover:shadow-purple-400 group">
                                    <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" />

                                    <div :class="{'py-0': subcategory.isEdited, 'py-1.5': !subcategory.isEdited}">
                                        <input type="text" v-model="subcategory.name" v-if="subcategory.isEdited" @keyup.enter="subcategory.switchEdition()" class="w-fit">
                                        <span v-else class="select-none">
                                            {{ subcategory.name }}
                                        </span>
                                    </div>

                                    <div class="flex gap-2 invisible group-hover:visible">
                                        <button type="button" class="hover:text-purple-600" @click.stop="subcategory.switchEdition()">
                                            <font-awesome-icon icon="fa-regular fa-circle-check" v-if="subcategory.isEdited" :title="$t('component.categoryList.subcategory.change')"/>
                                            <font-awesome-icon icon="fa-solid fa-file-pen" v-else :title="$t('component.categoryList.subcategory.edit')"/>
                                        </button>
                                        <button type="button" class="hover:text-purple-600" @click.stop="category.removeSubCategory(subcategory)" :title="$t('component.categoryList.subcategory.delete')">
                                            <font-awesome-icon icon="fa-solid fa-trash" :title="$t('component.categoryList.subcategory.delete')"/>
                                        </button>
                                    </div>
                                </p>
                            </li>
                        </template>
                    </draggable>

                    <div class="flex px-2" v-if="category.isInAddingNewSubcategory">
                        <p class="flex items-center gap-2 w-fit rounded px-2 hover:shadow-inner hover:shadow-purple-400 group">
                            <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />

                            <input type="text" class="w-fit" v-model="category.newSubcategoryName" @keyup.enter="addNewSubCategory(category)">
                            <button type="button" class="hover:text-purple-600" @click.stop="addNewSubCategory(category)" :title="$t('component.categoryList.subcategory.create')">
                                <font-awesome-icon icon="fa-regular fa-circle-check" />
                            </button>
                        </p>
                    </div>
                </li>
            </template>
        </draggable>

        <div class="px-4 text-lg">
            <button v-if="!isNewCategoryAdding" type="button" class="hover:text-purple-600" @click.stop="isNewCategoryAdding = true" :title="$t('component.categoryList.category.add')">
                <font-awesome-icon icon="fa-solid fa-folder-plus" />
            </button>
            <div v-if="isNewCategoryAdding" class="flex gap-2">
                <input type="text" v-model="newCategoryName" @keyup.enter="createNewCategory()">
                <button type="button" class="hover:text-purple-600" @click.stop="createNewCategory()" :title="$t('component.categoryList.category.create')">
                    <font-awesome-icon icon="fa-regular fa-circle-check" />
                </button>
            </div>
        </div>
    </div>
</template>