<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

import { Category } from '../../Category/data/model/Category';
import { CategoryFactory } from '../../Category/data/model/CategoryMapper';

import VHeader from '../../layout/ui/VHeader.vue';
import VContent from '../../layout/ui/VContent.vue';

const { t } = useI18n();

const response = {
    'Zakupy spożywcze w tym picie i jedzenie': [
        'Picie',
        'Picie i dodatki',
    ],
    'Opłaty': [],
};
const categories = reactive(CategoryFactory.createFromResponse(response, true));

const canViewSubcategories = (category: Category) => {
    return category.subcategories.length && category.isExpanded;
};

const currentMonth = new Date().getMonth();
const monthName = t(`month.${currentMonth}`);
</script>

<template>
    <VHeader>
        {{ $t('view.prepareBudget.header') }} 
        {{ monthName }}
    </VHeader>

    <VContent>
        <form method="post">
            <ul>
                <li class="flex flex-col px-2 my-2 gap-2" :key="categoryIndex" v-for="(category, categoryIndex) in categories">
                    <div 
                        class="flex gap-2 items-center" 
                        :class="{'cursor-pointer': category.subcategories.length}"
                        @click.stop="category.switchExpand()"
                    >
                        <font-awesome-icon 
                            v-if="category.subcategories.length"
                            icon="fa-solid fa-angle-right" 
                            class="text-slate-600" 
                            :class="{'rotate-90': category.isExpanded}"/>
                        <font-awesome-icon
                            v-else 
                            icon="fa-solid fa-minus" />

                        <span class="w-1/4">{{ category.name }}</span>
                        <input type="number" :disabled="category.subcategories.length" class="col-start-2 col-end-3 h-fit">
                        <span>zł</span>
                    </div>

                    <div v-if="canViewSubcategories(category)">
                        <div 
                            :key="subCategoryIndex"
                            v-for="(subcategory, subCategoryIndex) in category.subcategories" 
                            class="subcategories flex gap-2 items-center"
                        >
                            <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />
                            <span class="w-1/4">{{ subcategory.name }}</span>
                            <input type="number" class="col-start-2 col-end-3 h-fit">
                            <span>zł</span>
                        </div>
                    </div>

                    <hr class="rounded-full my-1" v-if="categoryIndex < categories.length - 1">
                </li>
            </ul>

            <button type="submit">{{ $t('component.budgetCreate.form.create') }}</button>
        </form>
    </VContent>
</template>