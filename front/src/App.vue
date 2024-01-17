<script setup>
import DescribedContentRow from './components/DescribedContentRow.vue';
import AddExpense from './components/Expense/AddExpense.vue';
import ExpenseList from './components/Expense/ExpenseList.vue';
import Sidebar from './components/Sidebar/Sidebar.vue';
import CategoryList from './components/Category/CategoryList.vue';

</script>

<template>
  <div class="flex">
    <Sidebar/>

    <div class="w-full p-10">
      <DescribedContentRow>
        <template #header>
          <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.addExpense.header') }}</h2>
        </template>
        <template #content>
          <AddExpense class="mx-auto w-full"/>
        </template>
      </DescribedContentRow>

      <ExpenseList/>


      <CategoryList/>

    </div>
  </div>
</template>

