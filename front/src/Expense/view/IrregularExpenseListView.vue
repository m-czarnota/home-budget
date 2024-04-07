<script setup lang="ts">
import { ref } from 'vue';

import VContent from '../../layout/ui/VContent.vue';
import VHeader from '../../layout/ui/VHeader.vue';

import IrregularExpenseList from '../component/IrregularExpenseList.vue';

const isLoadingError = ref(false);
</script>

<template>
    <VHeader>{{ $t('view.irregularExpenseList.header') }}</VHeader>

    <VContent>
        <Suspense v-if="!isLoadingError">
            <IrregularExpenseList/>
        
            <template #fallback>
                <div class="flex gap-1">
                    <span>{{ $t('view.irregularExpenseList.loading') }}</span>
                    <sub>
                        <span class="loading loading-dots loading-md"></span>
                    </sub>
                </div>
            </template>
        </Suspense>
        <div v-else class="space-x-2">
            <span class="text-red-600 space-x-1">
                <font-awesome-icon icon="fa-solid fa-xmark" />
                <span>{{ $t('view.irregularExpenseList.loadingError') }}</span>
            </span>
            <button 
                type="button" 
                @click="isLoadingError = false"
                class="button px-6 py-0.5"
            >
                {{ $t('view.irregularExpenseList.reload') }}
            </button>
        </div>
    </VContent>
</template>