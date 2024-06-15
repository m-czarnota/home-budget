<script setup lang="ts">
import BudgetView from './Budget.vue';

import { Budget } from '../data/model/Budget';
import { useBudgetStore } from '../store/budgetStore';
import { ComputedRef } from 'vue';

const emit = defineEmits(['loadingError']);
const { getCurrentBudget, getNextMonthBudget } = useBudgetStore();

// ------------------- budget for current month -------------------
let currentBudget: ComputedRef<Budget|undefined>;
try {
    currentBudget = await getCurrentBudget();
} catch (e) {
    console.error(e);
    emit('loadingError');
}

// ------------------- budget for next month -------------------
let nextMonthBudget: ComputedRef<Budget|undefined>;
try {
    nextMonthBudget = await getNextMonthBudget();
} catch (e) {
    console.error(e);
    emit('loadingError');
}
</script>

<template>
    <div>
        <BudgetView :passed-budget="currentBudget" @loading-error="$emit('loadingError')"/>
        <BudgetView :passed-budget="nextMonthBudget" @loading-error="$emit('loadingError')"/>
    </div>
</template>