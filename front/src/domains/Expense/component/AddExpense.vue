<script setup lang="ts"></script>

<template>
    <form class="mx-auto p-10 w-1/2">
        <div class="form-row">
            <label for="expense">{{ $t('component.addExpense.expense') }}:</label>
            <input type="text" id="expense">
        </div>
        <div class="form-row">
            <label for="cost">{{ $t('component.addExpense.amount') }}:</label>
            <input type="number" id="cost">
        </div>
        <div class="form-row">
            <label for="category">{{ $t('component.addExpense.category') }}:</label>
            <select id="category"></select>
        </div>
        <div class="form-row">
            <label for="persons">{{ $t('component.addExpense.persons') }}:</label>
            <select id="persons"></select>
        </div>
        <div class="form-row-inline">
            <label for="whim">{{ $t('component.addExpense.whim') }}:</label>
            <input type="checkbox" id="whim">
        </div>

        <button type="submit">{{ $t('component.addExpense.add') }}</button>
    </form>
</template>