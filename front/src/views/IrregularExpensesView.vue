<script setup>
import IrregularExpenses from '../components/Budget/IrregularExpenses.vue';
import DescribedContentRow from '../layout/DescribedContentRow.vue'
</script>

<template>
    <DescribedContentRow>
        <template #header>
            <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('view.irregularExpenses.header') }}</h2>
        </template>
        <template #content>
            <IrregularExpenses class="mx-auto w-full"/>
        </template>
    </DescribedContentRow>
</template>