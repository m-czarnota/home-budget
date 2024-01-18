<script setup></script>

<template>
    <!-- <h2 class="text-2xl font-bold text-purple-500">24.10.2023</h2> -->
    <div class="grid grid-cols-3 px-5 py-3 border-2 rounded border-transparent hover:border-purple-400 hover:bg-purple-50">
        <div>
            <h3 class="text-xl font-medium">Picie Asi</h3>
            <span class="text-slate-600 font-light">24.10.2023</span>
            <div class="mt-1 size-8 bg-purple-600 rounded-full text-white flex justify-center items-center">A</div>
        </div>
        <div class="text-center text-nowrap pt-1">
            <span class="py-1 px-2 border-2 rounded-full border-purple-400 font-semibold text-purple-600 text-sm">
                Zakupy spożywcze -> Picie
            </span>
        </div>
        <div class="text-right">
            <h4 class="text-lg font-medium">-24.99 zł</h4>
            <span class="font-light">{{ $t('component.expense.whim') }}</span>
        </div>
    </div>
</template>