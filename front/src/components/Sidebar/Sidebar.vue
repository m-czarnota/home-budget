<script setup>
import SidebarLink from './SidebarLink.vue';
import { ref, computed, provide } from 'vue';

const collapsed = ref(false);
const toggleSidebar = () => (collapsed.value = !collapsed.value);

const SIDEBAR_WIDTH = 180;
const SIDEBAR_WIDTH_COLLAPSED = 45;
const sidebarWidth = computed(
  () => `${collapsed.value ? SIDEBAR_WIDTH_COLLAPSED : SIDEBAR_WIDTH}px`
);

provide('collapsed', collapsed);
</script>

<template>
    <nav class="p-3 bg-zinc-800 text-white h-dvh transition-all duration-300 flex flex-col justify-between" :style="{ width: sidebarWidth }">
        <div>
            <h1 class="text-center">
                <span class="*:block" v-if="collapsed">
                    <span>H</span>
                    <span>B</span>
                </span>
                <span v-else>Home Budget</span>
            </h1>
            <hr class="rounded my-2">

            <div class="*:py-1.5">
                <SidebarLink to="/" icon="fas fa-home">Home</SidebarLink>
                <SidebarLink to="/dashboard" icon="fas fa-columns">Dashboard Dashboard Dashboard</SidebarLink>
                <SidebarLink to="/analytics" icon="fas fa-chart-bar">Analytics</SidebarLink>
                <SidebarLink to="/friends" icon="fas fa-users">Friends</SidebarLink>
                <SidebarLink to="/image" icon="fas fa-image">Images</SidebarLink>
            </div>
        </div>

        <button
            type="button"
            class="flex justify-center items-center px-3 border-2 rounded-full border-transparent transition duration-150 hover:border-white"
            @click="toggleSidebar"
        >
            <span :class="{ 'rotate-180': collapsed }">&lt;</span>
            <!-- <i class="fas fa-angle-double-left" /> -->
        </button>
    </nav>
</template>