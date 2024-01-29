<script setup>
import CurrentIrregularExpenses from '../components/Budget/CurrentIrregularExpenses.vue';
import DescribedContentRow from '../components/DescribedContentRow.vue'
</script>

<template>
    <DescribedContentRow>
        <template #header>
            <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('view.irregularExpenses.header') }}</h2>
        </template>
        <template #content>
            <CurrentIrregularExpenses class="mx-auto w-full"/>
        </template>
    </DescribedContentRow>
</template>