<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ComputedRef, Ref, computed, reactive, ref } from 'vue';

import { Budget } from '../data/model/Budget';
import { BudgetService } from '../data/service/BudgetService';
import { BudgetEntry } from '../data/model/BudgetEntry';
import { RouteGenerator } from '../../router/RouteGenerator';
import { RequestNotAcceptableError } from '../../http-client/RequestNotAcceptableError';
import { ResponseBudgetErrorData } from '../data/service/ResponseBudgetErrorData';

import Alert from '../../components/Alert.vue';

const emit = defineEmits(['loadingError']);
const { locale } = useI18n();

let currentBudget: Budget|undefined = undefined;
try {
    currentBudget = reactive(await BudgetService.getBudget());
} catch (e) {
    console.error(e);
    emit('loadingError');
}

// ------------------- viewing categories and subcategories -------------------
const isNoBudget = computed(() => {
    return currentBudget?.entries.length === 0;
});

const totalCost: ComputedRef<number> = computed(() => {
    return currentBudget?.entries
        .map(entry => entry.cost)
        .reduce((acc, currentVal) => {
            return acc + currentVal;
        }) || 0;
});

const getMonthNameForNumber = (month: number): string => {
    const now = new Date();
    now.setDate(1);
    now.setMonth(month);
    
    const localeCode = `${locale.value}-${locale.value.toUpperCase()}`
    const monthName = now.toLocaleDateString(localeCode, {
        month: 'long',
    });

    return monthName;
};

const hasEntryChildren = (entry: BudgetEntry) => {
    return entry.subEntries.length !== 0;
};

const parseCost = (cost: number): number => {
    if (!cost) {
        return 0;
    }

    cost = parseFloat(cost.toString());
    if (isNaN(cost)) {
        return 0;
    }

    const costString = cost.toString();
    if (costString.slice(0, 1) === '0') {
        return Number(costString.slice(1));
    }

    return cost;
};

const updatedEntry = (entry: BudgetEntry) => {
    entry.cost = parseCost(entry.cost);
};

const updatedSubEntry = (entry: BudgetEntry, subEntry: BudgetEntry) => {
    subEntry.cost = parseCost(subEntry.cost);

    entry.cost = entry.subEntries
        .map((subEntry: BudgetEntry) => subEntry.cost)
        .reduce((accumulator, currentValue) => {
            return accumulator + currentValue;
        });
};

// ----------------------------- changes in any budget entry -----------------------------
const originalState = ref(JSON.stringify(currentBudget));
const hasAnythingBeenChanged = computed(() => {
    const stringifiedBudget = JSON.stringify(currentBudget);

    return originalState.value !== stringifiedBudget;
});

// ----------------------------- error handling -----------------------------
const budgetErrors: Ref<ResponseBudgetErrorData> = ref({
    month: null,
    entries: [],
});
const getError = (entryIndex: number, subEntryIndex: number|undefined): string|null => {
    const entryErrors = budgetErrors.value.entries[entryIndex];
    if (entryErrors === undefined) {
        return null;
    }

    if (subEntryIndex === undefined) {
        return entryErrors.cost;
    }

    const subCategoryErrors = entryErrors.subEntries[subEntryIndex];

    return subCategoryErrors?.cost;
};

// ----------------------------- sync changes -----------------------------
const isSyncPending = ref(false);
const showSuccessAlert = ref(false);
const showErrorAlert = ref(false);

const submit = async () => {
    try {
        isSyncPending.value = true;
        showSuccessAlert.value = false;
        showErrorAlert.value = false;

        currentBudget = await BudgetService.update(currentBudget as Budget);
        originalState.value = JSON.stringify(currentBudget);

        showSuccessAlert.value = true;
    } catch (e) {
        if (!(e instanceof Error)) {
            return;
        }

        if (e instanceof RequestNotAcceptableError) {
            budgetErrors.value = JSON.parse(e.message) as ResponseBudgetErrorData;

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
    <form @submit.prevent="submit">
        <Alert 
            v-if="showSuccessAlert" 
            @closed="showSuccessAlert = false"
            :message="$t('component.categoryList.successSave')"/>

        <Alert
            v-if="showErrorAlert"
            type="danger"
            @closed="showErrorAlert = false"
            :message="$t('component.categoryList.errorSave')"/>

        <p v-if="hasAnythingBeenChanged" class="bg-red-200 rounded p-2" role="alert">
            <font-awesome-icon 
                icon="fa-solid fa-floppy-disk"
                class="mr-2"
                :title="$t('form.unsavedChanges')" />
            <span>{{ $t('component.irregularExpenses.unsavedChanges') }}</span>
        </p>

        <p v-if="isNoBudget" class="p-2">
            {{ $t('component.prepareBudget.noCategories') }}
            <!-- TODO styling link -->
            <RouterLink :to="RouteGenerator.getPath('category_list')">
                {{ $t('component.prepareBudget.addFirstCategories') }}
            </RouterLink>
        </p>

        <!-- TODO move it to translations -->
        <h2 class="text-xl">
            Budget for {{ getMonthNameForNumber(currentBudget?.month || 0) }}
        </h2>

        <div class="space-y-2 text-lg font-medium">
            <div class="flex">
                <div class="flex-1">Total cost</div>
                <div class="flex-1">
                    {{ totalCost }}
                </div>
            </div>
            <hr>
        </div>
        <div v-for="(budgetEntry, entryIndex) in currentBudget?.entries || []" class="space-y-2">
            <div class="flex">
                <div class="flex-1">{{ budgetEntry.categoryName }}</div>
                <div class="flex-1">
                    <input type="text" v-model="budgetEntry.cost" :disabled="hasEntryChildren(budgetEntry)" @input="updatedEntry(budgetEntry)"/>
                    <div class="text-error font-medium mt-2">
                        {{ getError(entryIndex, undefined) }}
                    </div>
                </div>
            </div>

            <div v-if="hasEntryChildren(budgetEntry)" class="space-y-2">
                <div v-for="(subEntry, subEntryIndex) in budgetEntry.subEntries" class="flex">
                    <div class="flex-1 pl-4">{{ subEntry.categoryName }}</div>
                    <div class="flex-1 pl-4">
                        <input type="text" v-model="subEntry.cost" @input="updatedSubEntry(budgetEntry, subEntry)"/>
                        <div class="text-error font-medium mt-2">
                            {{ getError(entryIndex, subEntryIndex) }}
                        </div>
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