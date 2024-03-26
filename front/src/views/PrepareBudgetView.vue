<script setup>
import BudgetCreate from '../components/Budget/BudgetCreate.vue';
import DescribedContentRow from '../layout/DescribedContentRow.vue'

</script>

<template>
    <DescribedContentRow>
        <template #header>
            <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('view.prepareBudget.header') }}</h2>
        </template>
        <template #content>
            <BudgetCreate class="mx-auto w-full"/>
        </template>
    </DescribedContentRow>
</template>