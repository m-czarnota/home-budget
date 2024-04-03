<script setup lang="ts">
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';

import VContent from '../../layout/ui/VContent.vue';
import VHeader from '../../layout/ui/VHeader.vue';

import VListItem from '../../components/List/VListItem.vue';

import { IrregularExpense } from '../data/model/IrregularExpense';
import { IrregularExpenseDataView } from '../data/dataView/IrregularExpenseDataView';
import { reactive } from 'vue';

// ----------------------------- handling response -----------------------------
let irregularExpensesDv: IrregularExpenseDataView[] = [];

// TODO handle data fetching

// ----------------------------- new expense -----------------------------
const newExpense: IrregularExpense = reactive({
    id: null,
    name: '',
    cost: 0,
    category: {},
    isWish: false,
    plannedYear: new Date().getFullYear(),
});

const addIrregularExpense = () => {
    // const expense: IrregularExpense = {
    //     id: null,
    //     name: '',
    //     category: {},
    //     cost: 0,
    //     isWish: false,
    //     plannedYear: new Date().getFullYear(),
    // };
    // expense.isNew = true;
    // expense.switchEditingName();
    // expenses.push(expense);
};

// ----------------------------- changes in any expenses -----------------------------
const originalState = ref(JSON.stringify(irregularExpensesDv.map(expenseDv => expenseDv.stringify())));
const hasAnythingBeenChanged = computed(() => {
    const currentExpensesState = JSON.stringify(irregularExpensesDv.map(expenseDv => expenseDv.stringify()));

    return originalState.value !== currentExpensesState;
});
</script>

<template>
    <VHeader>{{ $t('view.irregularExpenses.header') }}</VHeader>

    <VContent class="min-w-max">
        <form>
            <h2 class="text-xl underline font-semibold">
                {{ $t('component.irregularExpenses.onCurrentYear') }}
                {{ new Date().getFullYear() }}
            </h2>

            <p v-if="hasAnythingBeenChanged" class="bg-red-200 rounded p-2" role="alert">
                <font-awesome-icon 
                    icon="fa-solid fa-floppy-disk"
                    class="mr-2"
                    :title="$t('form.unsavedChanges')" />
                <span>{{ $t('component.irregularExpenses.unsavedChanges') }}</span>
            </p>

            <draggable :list="irregularExpensesDv" item-key="index" :animation="300">
                <template #item="{ element: expenseDv }">
                    <div class="hover:bg-slate-200 cursor-grab pb-3">
                        <VListItem
                            :value="expenseDv.expense.name"
                            :is-draggable="true"
                            :is-editable="true"
                            @updated="(val) => expenseDv.expense.name = val"
                            class="p-3"
                        >
                            <template #position>{{ null }}</template>
                        </VListItem>

                        <div class="pl-11">
                            <div class="space-x-1">
                                <input type="number" v-model="expenseDv.expense.cost">
                                <span>zł</span>
                            </div>

                            <div class="mt-2 flex flex-col gap-2">
                                <select class="category">
                                    <option selected>{{ expenseDv.expense.category.name }}</option>
                                    <option>Other</option>
                                </select>

                                <div class="flex gap-2">
                                    <input type="checkbox" v-model="expenseDv.expense.isWhim" :id="`is-whim-${expenseDv.expense.name}`">
                                    <label :for="`is-whim-${expenseDv.expense.name}`">
                                        {{ $t('component.irregularExpenses.form.whim') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <div class="collapse collapse-arrow border border-purple-700">
                <input type="checkbox" /> 
                <div class="collapse-title text-center">
                    {{ $t('component.irregularExpenses.expense.addNew') }}
                </div>
                <div class="collapse-content space-y-3"> 
                    <hr class="border-slate-300">
                    
                    <form @submit.prevent="addIrregularExpense()">
                        <label>
                            {{ $t('component.irregularExpenses.expense.name') }}:
                            <input type="text" v-model="newExpense.name">
                        </label>
                        <label>
                            {{ $t('component.irregularExpenses.expense.cost') }}:
                            <input type="number" inputmode="numeric" v-model="newExpense.cost">
                            zł
                        </label>
                        <label>
                            {{ $t('component.irregularExpenses.expense.category') }}:
                            <select>
                                <option>Category</option>
                            </select>
                        </label>
                        <label>
                            <input type="checkbox" v-model="newExpense.isWish">
                            {{ $t('component.irregularExpenses.expense.whim') }}
                        </label>

                        <button type="button">
                            {{ $t('component.irregularExpenses.expense.addNew') }}
                        </button>
                    </form>
                </div>
            </div>

            <button type="submit" :disabled="!hasAnythingBeenChanged">
                {{ $t('component.irregularExpenses.save') }}
            </button>
        </form>
    </VContent>
</template>