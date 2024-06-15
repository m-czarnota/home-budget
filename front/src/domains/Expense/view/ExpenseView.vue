<script setup lang="ts">
import AddExpense from '../component/AddExpense.vue';
import ExpenseList from '../component/ExpenseList.vue';
import VHeader from '@/layout/ui/VHeader.vue';
import VContent from '@/layout/ui/VContent.vue';
</script>

<template>
    <VHeader>{{ $t('component.addExpense.header') }}</VHeader>
    <VContent>
        <AddExpense class="mx-auto w-full"/>
    </VContent>

    <VHeader>{{ $t('component.expense.header') }}</VHeader>
    <VContent>
        <ExpenseList/>
    </VContent>
</template>