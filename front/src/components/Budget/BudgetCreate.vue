<script setup>
import { reactive, ref } from 'vue';
import { CategoryFactory } from '../Category/CategoryFactory';

const response = {
    'Zakupy spożywcze w tym picie i jedzenie': [
        'Picie',
        'Picie i dodatki',
    ],
    'Opłaty': [],
};
const categories = reactive(CategoryFactory.createFromResponse(response, true));

const canViewSubcategories = (category) => {
    return category.subcategories.length && category.isExpanded;
};
</script>

<template>
    <form method="post">
        <ul>
            <li class="flex flex-col px-2 my-2 gap-2" v-for="(category, index) in categories">
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

                <div 
                    v-if="canViewSubcategories(category)"
                    v-for="subcategory in category.subcategories" 
                    class="subcategories flex gap-2 items-center"
                >
                    <font-awesome-icon icon="fa-solid fa-minus" class="text-slate-600" />
                    <span class="w-1/4">{{ subcategory.name }}</span>
                    <input type="number" class="col-start-2 col-end-3 h-fit">
                    <span>zł</span>
                </div>

                <hr class="rounded-full my-1" v-if="index < categories.length - 1">
            </li>
        </ul>

        <button type="submit">{{ $t('component.budgetCreate.form.create') }}</button>
    </form>
</template>