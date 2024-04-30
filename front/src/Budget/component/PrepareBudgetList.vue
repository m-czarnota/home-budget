<script setup lang="ts">
import { ref } from 'vue';
import { CategoryService } from '../../Category/data/service/CategoryService';
import { CategoriesSelect, CategorySelect } from '../../Category/data/model/CategorySelect';

const emit = defineEmits(['loadingError']);

// TODO I should get budget from API with categories, I shouldn't fetching categories
let categories: CategoriesSelect = [];
try {
    categories = await CategoryService.getCategoriesToSelect();
} catch (e) {
    emit('loadingError');
}

const hasCategoryChildren = (category: CategorySelect) => {
    return category.subItems.length !== 0;
}

const hasAnythingBeenChanged = ref(false);
const isSyncPending = ref(false);
</script>

<template>
    <form>
        <div v-for="category of categories" class="space-y-2">
            <div class="flex">
                <div class="flex-1">{{ category.name }}</div>
                <div class="flex-1">
                    <input type="number" :disabled="hasCategoryChildren(category)"/>
                </div>
            </div>

            <div v-if="hasCategoryChildren(category)" class="space-y-2">
                <div v-for="subCategory in category.subItems" class="flex">
                    <div class="flex-1 pl-4">{{ subCategory.name }}</div>
                    <div class="flex-1 pl-4">
                        <input type="number"/>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" :disabled="!hasAnythingBeenChanged || isSyncPending" class="flex items-center justify-center gap-2">
            {{ $t('component.irregularExpenses.save') }}
            <span class="loading loading-bars loading-md text-purple-700" v-if="isSyncPending"></span>
        </button>
    </form>
</template>