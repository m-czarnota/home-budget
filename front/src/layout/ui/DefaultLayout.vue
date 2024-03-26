<script setup lang="ts">
import Sidebar from '../Sidebar/Sidebar.vue';
</script>

<template>
  <div class="flex">
    <Sidebar/>

    <div class="mx-auto w-full xl:w-1/2 p-10">
      <RouterView/>
    </div>
  </div>
</template>
