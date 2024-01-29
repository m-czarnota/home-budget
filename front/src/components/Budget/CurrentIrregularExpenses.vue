<script setup>
import { reactive, computed } from 'vue';
import { Expense } from '../Expense/Expense';
import draggable from 'vuedraggable';

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

const addIrregularExpense = () => {
    const expense = new Expense(
        '',
        0,
        '',
    );
    expenses.push(expense);
};

const hasAnythingBeenChanged = computed(() => {
    const changedExpenses = expenses.filter(expense => expense.isChanged());
    console.log(changedExpenses.length);

    return changedExpenses.length > 0;
});
</script>

<template>
    <form>
        <h2 class="text-xl underline font-semibold">
            {{ $t('component.currentIrregularExpenses.onCurrentYear') }}
            {{ new Date().getFullYear() }}
        </h2>

        <p v-if="hasAnythingBeenChanged" class="bg-red-200 rounded p-2" role="alert">
            <font-awesome-icon 
                icon="fa-solid fa-floppy-disk"
                class="mr-2"
                :title="$t('form.unsavedChanges')" />
            <span>{{ $t('component.currentIrregularExpenses.unsavedChanges') }}</span>
        </p>

        <draggable :list="expenses" item-key="name" :animation="300">
            <template #item="{ element: expense }">
                <div 
                    class="relative flex flex-col w-full my-2 p-2 rounded cursor-grab transition-colors duration-150 hover:bg-slate-200"
                    :class="{'shadow-inner shadow-red-200': expense.isChanged()}"
                >
                    <div class="flex gap-2 items-center">
                        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" />
                        <span class="w-1/4">{{ expense.name }}</span>
                        <input type="number" class="col-start-2 col-end-3 h-fit" v-model="expense.cost">
                        <span>zł</span>
                    </div>

                    <div class="pl-10 flex flex-col gap-2">
                        <select class="category">
                            <option selected>{{ expense.category }}</option>
                            <option>Other</option>
                        </select>
                        <div class="flex gap-2">
                            <input type="checkbox" v-model="expense.isWhim" :id="`is-whim-${expense.name}`">
                            <label :for="`is-whim-${expense.name}`">
                                {{ $t('component.currentIrregularExpenses.form.whim') }}
                            </label>
                        </div>
                    </div>

                    <font-awesome-icon 
                        v-if="expense.isChanged()"
                        icon="fa-solid fa-floppy-disk"
                        class="absolute top-3 right-3"
                        :title="$t('form.unsavedChanges')" />
                </div>
            </template>
        </draggable>

        <button type="button" @click="addIrregularExpense()">
            {{ $t('component.currentIrregularExpenses.form.addNew') }}
        </button>

        <button type="submit" :disabled="!hasAnythingBeenChanged">
            {{ $t('component.currentIrregularExpenses.form.save') }}
        </button>
    </form>
</template>