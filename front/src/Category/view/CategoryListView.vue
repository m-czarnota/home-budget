<script setup lang="ts">
import VHeader from '../../layout/ui/VHeader.vue';
import VContent from '../../layout/ui/VContent.vue';
import VDescription from '../../layout/ui/VDescription.vue';
import CategoryList from '../component/CategoryList.vue'
import { ref } from 'vue';

const isLoadingError = ref(false);
</script>

<template>
    <VHeader>{{ $t('component.categoryList.header') }}</VHeader>
    <VDescription>{{ $t('component.categoryList.description') }}</VDescription>

    <VContent>
        <!-- create async component -->
        <Suspense v-if="!isLoadingError">
            <CategoryList @loading-error="isLoadingError = true"/>

            <template #fallback>
                <div class="flex gap-1">
                    <span>{{ $t('view.categoryListView.loading') }}</span>
                    <sub>
                        <span class="loading loading-dots loading-md"></span>
                    </sub>
                </div>
            </template>
        </Suspense>
        <div v-else class="space-x-2">
            <span class="text-red-600 space-x-1">
                <font-awesome-icon icon="fa-solid fa-xmark" />
                <span>{{ $t('view.categoryListView.loadingError') }}</span>
            </span>
            <button 
                type="button" 
                @click="isLoadingError = false"
                class="button px-6 py-0.5">
                {{ $t('view.categoryListView.reload') }}
                </button>
        </div>
    </VContent>
</template>