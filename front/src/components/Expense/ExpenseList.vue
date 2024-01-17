<script setup>
    import DescribedContentRow from './../DescribedContentRow.vue';
    import Expense from './Expense.vue';
</script>

<template>
    <DescribedContentRow>
        <template #header>
          <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.expense.header') }}</h2>
        </template>
        <template #content>
          <h3 class="text-center text-2xl font-bold text-purple-500 mb-2">24.10.2023</h3>
          <Expense/>
          <hr>
          <Expense/>
        </template>
      </DescribedContentRow>
</template>