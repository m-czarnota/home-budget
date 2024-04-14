<script setup lang="ts">
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';

import VListItem from '../../components/List/VListItem.vue';

import { IrregularExpense } from '../data/model/IrregularExpense';
import { IrregularExpenseDataView } from '../data/dataView/IrregularExpenseDataView';
import { reactive } from 'vue';
import { CategoryService } from '../../Category/data/service/CategoryService';
import VSelect from '../../components/VSelect/VSelect.vue';
import { IrregularExpenseService } from '../data/service/IrregularExpenseService';
import { RequestNotAcceptableError } from '../../http-client/RequestNotAcceptableError';
import { IrregularExpenseErrors } from '../data/model/IrregularExpensesError';
import { VSelectData, VSelectItem } from '../../components/VSelect/VSelectData';
import Alert from '../../components/Alert.vue';

const emit = defineEmits(['loadingError']);

// ----------------------------- handling response -----------------------------
const categoriesSelect: VSelectData = [{
    id: null,
    name: 'Please select category',
    subItems: [],
}];
try {
    const categories = await CategoryService.getCategoriesToSelect();
    categoriesSelect.push(...categories);
} catch (e) {
    emit('loadingError');
}

let irregularExpensesDv: IrregularExpenseDataView[] = reactive([]);
try {
    const expenses = await IrregularExpenseService.getExpenses();
    irregularExpensesDv = reactive(expenses.map(expense => new IrregularExpenseDataView(expense))) as IrregularExpenseDataView[];
} catch (e) {
    emit('loadingError');
}

// ----------------------------- new expense -----------------------------
const createEmptyIrregularExpense = (): IrregularExpense => ({
    id: null,
    name: '',
    position: 0,
    cost: 0,
    category: categoriesSelect[0],
    isWish: false,
    plannedYear: new Date().getFullYear(),
});
let newExpense: IrregularExpense = reactive(createEmptyIrregularExpense());

const canAddNewExpense = computed(() => {
    if (!newExpense.name.trim()) {
        return false;
    }
    if (newExpense.category.id === null) {
        return false;
    }

    return true;
});

const addIrregularExpense = () => {
    const expense = JSON.parse(JSON.stringify(newExpense));
    irregularExpensesDv.push(new IrregularExpenseDataView(expense));

    newExpense = createEmptyIrregularExpense();
};

// ----------------------------- removing expense -----------------------------
const removeIrregularExpense = (irregularExpenseDv: IrregularExpenseDataView) => {
    const index = irregularExpensesDv.indexOf(irregularExpenseDv);
    if (index === -1) {
        throw new RangeError(`Category ${irregularExpenseDv.expense.name} doesn't exist`);
    }

    irregularExpensesDv.splice(index, 1);
};

// ----------------------------- changes in any expenses -----------------------------
const originalState = ref(JSON.stringify(irregularExpensesDv.map(expenseDv => expenseDv.stringify())));
const hasAnythingBeenChanged = computed(() => {
    const currentExpensesState = JSON.stringify(irregularExpensesDv.map(expenseDv => expenseDv.stringify()));

    return originalState.value !== currentExpensesState;
});

// ----------------------------- sync changes -----------------------------
const isSyncPending = ref(false);
const showSuccessAlert = ref(false);
const showErrorAlert = ref(false);

const submit = async () => {
    try {
        isSyncPending.value = true;
        showSuccessAlert.value = false;
        showErrorAlert.value = false;
        irregularExpensesDv.forEach((irregularExpenseDv: IrregularExpenseDataView) => irregularExpenseDv.resetErrors());

        const expenses = irregularExpensesDv.map(irregularExpenseDv => irregularExpenseDv.expense);
        const updatedExpenses = await IrregularExpenseService.updateExpenses(expenses);

        irregularExpensesDv = reactive(updatedExpenses.map(expense => new IrregularExpenseDataView(expense))) as IrregularExpenseDataView[];
        originalState.value = JSON.stringify(irregularExpensesDv.map(expenseDv => expenseDv.stringify()));

        showSuccessAlert.value = true;
    } catch (e) {
        if (!(e instanceof Error)) {
            return;
        }

        if (e instanceof RequestNotAcceptableError) {
            console.log(e.message)
            const errors = JSON.parse(e.message) as IrregularExpenseErrors;
            for (const [index, irregularExpenseDv] of irregularExpensesDv.entries()) {
                const expenseErrors = errors[index];
                irregularExpenseDv.errors = expenseErrors;
            }

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
    <div>
        <form @submit.prevent="submit">
            <h2 class="text-xl underline font-semibold">
                {{ $t('component.irregularExpenses.onCurrentYear') }}
                {{ new Date().getFullYear() }}
            </h2>

            <Alert 
                v-if="showSuccessAlert" 
                @closed="showSuccessAlert = false"
                :message="$t('component.categoryList.successSave')"/>

            <Alert
                v-if="showErrorAlert"
                type="danger"
                @closed="showErrorAlert = false"
                :message="$t('component.categoryList.errorSave')"/>

            <p v-if="hasAnythingBeenChanged && !isSyncPending" class="bg-red-200 rounded p-2" role="alert">
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
                            :error="expenseDv.errors.name"
                            @updated="(val) => expenseDv.expense.name = val"
                            @remove="removeIrregularExpense(expenseDv)"
                            class="p-3"
                        >
                            <template #position>{{ null }}</template>
                        </VListItem>

                        <div class="pl-11">
                            <div>
                                <div class="space-x-1">
                                    <input type="number" v-model="expenseDv.expense.cost">
                                    <span>zł</span>
                                </div>
                                <div class="text-error font-medium mt-2">
                                    {{ expenseDv.errors.cost }}
                                </div>
                            </div>

                            <div class="mt-2 flex flex-col gap-2">
                                <div>
                                    <VSelect 
                                        :elements="categoriesSelect" 
                                        :selected="expenseDv.expense.category"
                                        @changed="(category: VSelectItem) => expenseDv.expense.category = category"/>
                                    <div class="text-error font-medium mt-2">
                                        {{ expenseDv.errors.category }}
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <label>
                                        <input type="checkbox" v-model="expenseDv.expense.isWish">
                                        {{ $t('component.irregularExpenses.expense.wish') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <button type="submit" :disabled="!hasAnythingBeenChanged || isSyncPending" class="flex items-center justify-center gap-2">
                {{ $t('component.irregularExpenses.save') }}
                <span class="loading loading-bars loading-md text-purple-700" v-if="isSyncPending"></span>
            </button>
        </form>

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
                        <input type="number" inputmode="numeric" min="0" v-model="newExpense.cost">
                        zł
                    </label>
                    <label class="flex gap-1 items-center">
                        {{ $t('component.irregularExpenses.expense.category') }}:
                        <VSelect 
                            :elements="categoriesSelect" 
                            @changed="(category: VSelectItem) => newExpense.category = category"/>
                    </label>
                    <label>
                        <input type="checkbox" v-model="newExpense.isWish">
                        {{ $t('component.irregularExpenses.expense.wish') }}
                    </label>

                    <button type="submit" :disabled="!canAddNewExpense">
                        {{ $t('component.irregularExpenses.expense.addNew') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>