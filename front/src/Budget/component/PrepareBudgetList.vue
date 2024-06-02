<script setup lang="ts">
import BudgetView from './Budget.vue';

import { Budget } from '../data/model/Budget';
import { BudgetService } from '../data/service/BudgetService';

const emit = defineEmits(['loadingError']);

// ------------------- budget for current month -------------------
let currentBudget: Budget|undefined = undefined;
try {
    currentBudget = await BudgetService.getBudgetForCurrentMonth();
} catch (e) {
    console.error(e);
    emit('loadingError');
}

// ------------------- budget for next month -------------------
let nextMonthBudget: Budget|undefined = undefined;
const now = new Date();
const currentDay = now.getDate();

now.setDate(0);
const countOfDaysInCurrentMonth = now.getDate();
const leftDaysToEndOfCurrentMonth = countOfDaysInCurrentMonth - currentDay;

const numberOfDaysWhenShowNextMonth = 5;
if (leftDaysToEndOfCurrentMonth <= numberOfDaysWhenShowNextMonth) {
    try {
        nextMonthBudget = await BudgetService.getBudgetForNextMonth();
    } catch (e) {
        console.error(e);
        emit('loadingError');
    }
}
</script>

<template>
    <div>
        <BudgetView :passed-budget="currentBudget" @loading-error="$emit('loadingError')"/>
        <BudgetView :passed-budget="nextMonthBudget" @loading-error="$emit('loadingError')"/>
    </div>
</template>