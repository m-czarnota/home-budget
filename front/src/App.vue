<script setup>
import ContentRow from './components/ContentRow.vue';
import AddExpense from './components/Expenses/AddExpense.vue';
import Expense from './components/Expenses/Expense.vue';
import Sidebar from './components/Sidebar/Sidebar.vue';

</script>

<template>
  <div class="flex">
    <Sidebar/>

    <div class="w-full p-10">
      <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.addExpense.header') }}</h2>
      <ContentRow>
        <AddExpense class="mx-auto w-full"/>
      </ContentRow>

      <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.expense.header') }}</h2>
      <ContentRow class="">
        <h3 class="text-center text-2xl font-bold text-purple-500 mb-2">24.10.2023</h3>
        <Expense/>
        <hr>
        <Expense/>
      </ContentRow>

    </div>
  </div>
</template>

