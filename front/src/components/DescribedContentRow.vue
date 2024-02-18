<script setup>
import ContentRow from './ContentRow.vue';
</script>

<template>
    <slot name="header"></slot>
    <slot name="description"></slot>
    <ContentRow>
        <slot name="content"></slot>
    </ContentRow>
</template>