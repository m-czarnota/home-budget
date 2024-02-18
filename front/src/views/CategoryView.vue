<script setup>
import DescribedContentRow from '../components/DescribedContentRow.vue';
import CategoryList from '../components/Category/CategoryList.vue';
</script>

<template>
    <DescribedContentRow>
        <template #header>
          <h2 class="mt-5 mb-2 text-3xl font-bold">{{ $t('component.categoryList.header') }}</h2>
        </template>
        <template #content>
          <CategoryList/>
        </template>
      </DescribedContentRow>
</template>