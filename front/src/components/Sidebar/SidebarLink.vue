<script setup>
import { inject } from 'vue';

defineProps({
    to: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
});

const isActive = false;

const collapsed = inject('collapsed');
</script>

<template>
    <router-link :to="to" class="flex items-center cursor-pointer font-light select-none rounded text-slate-100 transition-all duration-150 hover:underline hover:text-white active:underline" :class="{ active: isActive }">
        <!-- <i class="shrink-0 w-5" :class="icon" /> -->
        <Transition name="fade">
            <span v-if="!collapsed">
                <slot />
            </span>
        </Transition>
    </router-link>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>