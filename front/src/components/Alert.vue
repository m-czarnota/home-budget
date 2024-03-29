<script setup lang="ts">
import { computed } from "vue";

const props = defineProps({
    type: {
        type: String,
        validator(value: string) {
            return ['success', 'warning', 'danger'].includes(value);
        },
        default: 'success',
    },
    message: {
        type: String,
        required: true,
    },
    canBeClosed: {
        type: Boolean,
        required: false,
        default: true,
    },
});
const emit = defineEmits(['closed']);

const color = computed(() => {
    switch (props.type) {
        case 'warning':
            return 'bg-amber-200';
        case 'danger':
            return 'bg-red-200';
        case 'success':
        default:
            return 'bg-lime-300';
    }
});
const icon = computed(() => {
    switch (props.type) {
        case 'warning':
            return 'fa-solid fa-triangle-exclamation';
        case 'danger':
            return 'fa-regular fa-circle-xmark';
        case 'success':
        default:
            return 'fa-regular fa-circle-check';
    }
});


</script>

<template>
    <div class="p-2 rounded" :class="[color]">
        <div class="relative flex items-center gap-2 w-full">
            <font-awesome-icon :icon="icon"/>
            <p>{{ message }}</p>
            
            <button 
                v-if="canBeClosed"
                type="button" 
                class="alert-dismiss absolute right-0 flex items-center justify-center p-0.5 rounded-full size-6 transition-colors duration-150 hover:text-gray-50 hover:bg-slate-700"
                @click.once="$emit('closed')"
            >
                <font-awesome-icon icon="fa-solid fa-xmark"/>
            </button>
        </div>
        <div>
            <slot></slot>
        </div>
    </div>
</template>