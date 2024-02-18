<script setup>
import DescribedContentRow from '../components/DescribedContentRow.vue';
import AddExpense from '../components/Expense/AddExpense.vue';
import ExpenseList from '../components/Expense/ExpenseList.vue';
</script>

<template>
    <DescribedContentRow>
        <template #header>
            <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.addExpense.header') }}</h2>
        </template>
        <template #content>
            <AddExpense class="mx-auto w-full"/>
        </template>
    </DescribedContentRow>

    <DescribedContentRow>
        <template #header>
          <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.expense.header') }}</h2>
        </template>
        <template #content>
            <ExpenseList/>
        </template>
      </DescribedContentRow>
</template>