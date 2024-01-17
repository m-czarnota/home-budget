<script setup>
import { reactive, ref } from 'vue';

import { Category } from './Category';
import { SubCategory } from './SubCategory';
import DescribedContentRow from './../DescribedContentRow.vue';

const response = {
    'Zakupy spożywcze': [
        'Picie',
    ],
    'Opłaty': [],
};

// ------------------- mapping categories from response to objects -------------------
const categoriesObject = [];
for (const [name, subcategories] of Object.entries(response)) {
    const category = new Category(name);
    category.subcategories = subcategories.map((subName) => new SubCategory(subName, category));
    

    categoriesObject.push(category);
}
const categories = reactive(categoriesObject);

// ------------------- viewing categories and subcategories -------------------
const canViewSubcategories = (category) => {
    return category.subcategories.length && category.isExpanded;
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
    <DescribedContentRow>
        <template #header>
            <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.categoryList.header') }}</h2>
        </template>

        <template #content>       
            <div class="category-list">
                <ul>
                    <li class="px-2 my-2" v-for="category in categories">
                        <p 
                            class="cursor-pointer select-none flex items-center gap-2 w-fit rounded px-2 py-4 text-lg hover:shadow-inner hover:shadow-purple-400 group"
                            @click.stop="category.switchExpand()"
                            :title="category.isExpanded ? $t('component.categoryList.category.hideSubcategories') : $t('component.categoryList.category.showSubcategories')"
                        >
                            <font-awesome-icon 
                                icon="fa-solid fa-angle-right" 
                                class="text-slate-600 cursor-pointer" 
                                :class="{'rotate-90': category.isExpanded}" />

                            <div :class="{'py-0': category.isEdited, 'py-1.5': !category.isEdited}">
                                <input type="text" v-model="category.name" v-if="category.isEdited" @keyup.enter="category.switchEdition()" @click.stop class="w-fit">
                                <span 
                                    v-else 
                                    :title="category.isExpanded ? $t('component.categoryList.category.hideSubcategories') : $t('component.categoryList.category.showSubcategories')"    
                                >
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
                        
                        <ul v-if="canViewSubcategories(category)" class="subcategories">
                            <li class="flex px-2 my-2" v-for="subcategory in category.subcategories">
                                <p class="flex items-center gap-2 w-fit rounded p-2 hover:shadow-inner hover:shadow-purple-400 group">
                                    <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />

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

                            <li class="flex px-2" v-if="category.isInAddingNewSubcategory">
                                <p class="flex items-center gap-2 w-fit rounded px-2 hover:shadow-inner hover:shadow-purple-400 group">
                                    <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />

                                    <input type="text" class="w-fit" v-model="category.newSubcategoryName" @keyup.enter="addNewSubCategory(category)">
                                    <button type="button" class="hover:text-purple-600" @click.stop="addNewSubCategory(category)" :title="$t('component.categoryList.subcategory.create')">
                                        <font-awesome-icon icon="fa-regular fa-circle-check" />
                                    </button>
                                </p>
                            </li>
                        </ul>
                    </li>

                    <li class="px-4 text-lg">
                        <button v-if="!isNewCategoryAdding" type="button" class="hover:text-purple-600" @click.stop="isNewCategoryAdding = true" :title="$t('component.categoryList.category.add')">
                            <font-awesome-icon icon="fa-solid fa-folder-plus" />
                        </button>
                        <div v-if="isNewCategoryAdding" class="flex gap-2">
                            <input type="text" v-model="newCategoryName" @keyup.enter="createNewCategory()">
                            <button type="button" class="hover:text-purple-600" @click.stop="createNewCategory()" :title="$t('component.categoryList.category.create')">
                                <font-awesome-icon icon="fa-regular fa-circle-check" />
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </template>
    </DescribedContentRow>
</template>