import { defineStore } from "pinia";
import { IrregularExpenses } from "../data/model/IrregularExpense";
import { ComputedRef, Ref, computed, ref } from "vue";

export const useIrregularExpenseStore = defineStore('irregular-expense', () => {
    const irregularExpenses: Ref<IrregularExpenses|null> = ref(null);

    async function getIrregularExpenses(): Promise<ComputedRef<IrregularExpenses>> {
        if (irregularExpenses.value === null) {
            // TODO fetch
            // TODO also map to full categories in service
            // TODO so server has to return full category
        }

        return computed(() => irregularExpenses.value);
    }

    async function updateIrregularExpenses(expensesToUpdate: IrregularExpenses): Promise<IrregularExpenses> {

    }
});