<script setup>
import { reactive, computed } from 'vue';
import { Expense } from '../Expense/Expense';
import draggable from 'vuedraggable';
import { useI18n } from 'vue-i18n';

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
        name: 'Wakacje w Budapeszcie',
        cost: 2500,
        category: 'Wypoczynek',
        whim: true, 
    }, 
];

const expenses = reactive(response.map(expenseJson => new Expense(
    expenseJson.name,
    expenseJson.cost,
    expenseJson.category,
    expenseJson.whim,
    true,
)));

// ----------------------------- new expense -----------------------------
const addIrregularExpense = () => {
    const expense = new Expense(
        '',
        0,
        '',
        false,
        true,
    );
    expense.isNew = true;
    expense.switchEditingName();
    expenses.push(expense);
};

// ----------------------------- changes in any expenses -----------------------------
let originalExpensesCount = expenses.length;
const hasAnythingBeenChanged = computed(() => {
    if (originalExpensesCount !== expenses.length) {
        return true;
    }

    const changedExpenses = expenses.filter(expense => expense.isChanged());

    return changedExpenses.length > 0;
});

// ----------------------------- expense name -----------------------------
const switchEditingName = (expense) => {
    expense.switchEditingName();

    // const expenseNameInput = document.querySelector(`#expense-name-input-${index}`);
    // expenseNameInput.focus();
};
const switchingEditNameTitle = (expense) => {
    return expense.isEditingName
        ? t('component.irregularExpenses.form.changeName')
        : t('component.irregularExpenses.form.editName');
};
</script>

<template>
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
            <template #item="{ element: expense }">
                <div 
                    class="relative flex flex-col w-full my-2 p-4 rounded cursor-grab transition-colors duration-150 hover:bg-slate-200"
                    :class="{'shadow-inner shadow-red-200': expense.isChanged() || expense.isNew}"
                >
                    <div class="flex gap-2 items-center">
                        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" />

                        <div class="w-1/4">
                            <span v-if="!expense.isEditingName">{{ expense.name }}</span>
                            <input v-else type="text" v-model="expense.name">
                        </div>

                        <button type="button" class="hover:text-purple-600" @click.stop="switchEditingName(expense)" :title="switchingEditNameTitle(expense)">
                            <font-awesome-icon icon="fa-regular fa-circle-check" v-if="expense.isEditingName"/>
                            <font-awesome-icon icon="fa-solid fa-file-pen" v-else/>
                        </button>
                        
                        <input type="number" class="col-start-2 col-end-3 h-fit" v-model="expense.cost">
                        <span>zł</span>
                    </div>

                    <div class="pl-10 mt-2 flex flex-col gap-2">
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
</template>