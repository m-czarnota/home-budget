<script setup>
import Sidebar from './components/Sidebar/Sidebar.vue';

</script>

<template>
  <div class="flex">
    <Sidebar/>

    <div class="w-full p-10">
      <RouterView/>
    </div>
  </div>
</template>

