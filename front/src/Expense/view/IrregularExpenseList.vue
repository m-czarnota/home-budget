<script setup lang="ts">
import { reactive, computed } from 'vue';
import draggable from 'vuedraggable';
import { useI18n } from 'vue-i18n';

import VContent from '../../layout/ui/VContent.vue';
import VHeader from '../../layout/ui/VHeader.vue';

import { IrregularExpense } from '../data/model/IrregularExpense';

const { t } = useI18n();

// ----------------------------- handling response -----------------------------
const response = [
    {
        name: 'OC',
        cost: 500,
        category: 'Transport',
        whim: false, 
    },
    {
        name: 'Wakacje w Budapeszcie i cos jeszcze',
        cost: 2500,
        category: 'Wypoczynek',
        whim: true, 
    }, 
];

const expenses = reactive(response.map(expenseJson => new IrregularExpense(
    expenseJson.name,
    expenseJson.cost,
    expenseJson.category,
    expenseJson.whim,
)));

// ----------------------------- new expense -----------------------------
const addIrregularExpense = () => {
    const expense = new IrregularExpense(
        '',
        0,
        '',
        false,
    );
    expense.isNew = true;
    expense.switchEditingName();
    expenses.push(expense);
};

// ----------------------------- changes in any expenses -----------------------------
const originalExpensesState = JSON.stringify(expenses.map(expense => expense.stringify()));
const hasAnythingBeenChanged = computed(() => {
    const currentExpensesState = JSON.stringify(expenses.map(expense => expense.stringify()));

    return currentExpensesState !== originalExpensesState;
});

// ----------------------------- expense name -----------------------------
const nameRefs = reactive({});  // to focus on specific expense name
const switchEditingName = (expense: IrregularExpense) => {
    expense.switchEditingName();

    if (!expense.isEditingName) {
        return;
    }

    // focus on input with name
    setTimeout(() => {
        const index = expenses.indexOf(expense);
        const input = nameRefs[index];

        input.focus();
    }, 0);
};
const switchingEditNameTitle = (expense: IrregularExpense) => {
    return expense.isEditingName
        ? t('component.irregularExpenses.form.changeName')
        : t('component.irregularExpenses.form.editName');
};
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

            <draggable :list="expenses" item-key="index" :animation="300">
                <template #item="{ element: expense, index }">
                    <div 
                        class="relative flex w-full my-2 p-4 rounded cursor-grab transition-colors duration-150 hover:bg-slate-200"
                        :class="{'shadow-inner shadow-red-200': expense.isChanged() || expense.isNew}"
                    >
                        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-4 pt-2" />

                        <div class="space-y-2 w-full">
                            <div class="flex gap-2 items-center">
                                <div class="w-48">
                                    <span v-if="!expense.isEditingName">{{ expense.name }}</span>
                                    <input v-else type="text" :id="`expense-${index}`" v-model="expense.name" class="w-full" :ref="el => nameRefs[index] = el">
                                </div>

                                <button type="button" class="hover:text-purple-600" @click.stop="switchEditingName(expense)" :title="switchingEditNameTitle(expense)">
                                    <font-awesome-icon icon="fa-regular fa-circle-check" v-if="expense.isEditingName"/>
                                    <font-awesome-icon icon="fa-solid fa-file-pen" v-else/>
                                </button>
                            </div>

                            <div class="space-x-1">
                                <input type="number" class="h-fit w-4/5 lg:w-1/5 max-w-48" v-model="expense.cost">
                                <span>zł</span>
                            </div>

                            <div class="mt-2 flex flex-col gap-2">
                                <select class="category">
                                    <option selected>{{ expense.category }}</option>
                                    <option>Other</option>
                                </select>

                                <div class="flex gap-2">
                                    <input type="checkbox" v-model="expense.isWhim" :id="`is-whim-${expense.name}`">
                                    <label :for="`is-whim-${expense.name}`">
                                        {{ $t('component.irregularExpenses.form.whim') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="absolute top-3 right-3">
                            <font-awesome-icon 
                                v-if="expense.isChanged()"
                                icon="fa-solid fa-floppy-disk"
                                :title="$t('form.unsavedChanges')"
                                class="text-red-500" />
                            <font-awesome-icon 
                                v-if="expense.isNew"
                                icon="fa-solid fa-circle-plus"
                                :title="$t('form.newPosition')"
                                class="text-green-500" />
                        </div>
                    </div>
                </template>
            </draggable>

            <button type="button" @click.stop="addIrregularExpense()">
                {{ $t('component.irregularExpenses.form.addNew') }}
            </button>

            <button type="submit" :disabled="!hasAnythingBeenChanged">
                {{ $t('component.irregularExpenses.form.save') }}
            </button>
        </form>
    </VContent>
</template>