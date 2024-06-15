import { defineStore } from "pinia";
import { ComputedRef, computed, ref } from "vue";
import { Budget } from "../data/model/Budget";
import { BudgetService } from "../data/service/BudgetService";

export const useBudgetStore = defineStore('budget-store', () => {
    const currentBudget = ref<Budget>();
    const nextMonthBudget = ref<Budget>();

    async function getCurrentBudget(): Promise<ComputedRef<Budget>> {
        if (!currentBudget.value) {
            currentBudget.value = await BudgetService.getBudgetForCurrentMonth();
        }

        return computed(() => currentBudget.value as Budget);
    }

    async function getNextMonthBudget(): Promise<ComputedRef<Budget|undefined>> {
        if (nextMonthBudget.value) {
            return computed(() => nextMonthBudget.value);
        }

        const now = new Date();
        const currentDay = now.getDate();

        now.setDate(0);
        const countOfDaysInCurrentMonth = now.getDate();
        const leftDaysToEndOfCurrentMonth = countOfDaysInCurrentMonth - currentDay;

        const numberOfDaysWhenShowNextMonth = 5;
        if (leftDaysToEndOfCurrentMonth > numberOfDaysWhenShowNextMonth) {
            return computed(() => undefined);
        }

        nextMonthBudget.value = await BudgetService.getBudgetForCurrentMonth();

        return computed(() => nextMonthBudget.value);
    }

    async function updateBudget(budgeToUpdate: Budget): Promise<Budget> {
        const updatedBudget = await BudgetService.update(budgeToUpdate);
        currentBudget.value = updatedBudget;

        return updatedBudget;
    }

    return {
        getCurrentBudget,
        getNextMonthBudget,
        updateBudget,
    };
});