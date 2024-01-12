<template>
    <form class="mx-auto p-10 w-1/2">
        <div class="form-row">
            <label for="expense">Wydatek:</label>
            <input type="text" id="expense">
        </div>
        <div class="form-row">
            <label for="cost">Kwota:</label>
            <input type="number" id="cost">
        </div>
        <div class="form-row">
            <label for="category">Kategoria:</label>
            <select id="category"></select>
        </div>
        <div class="form-row">
            <label for="persons">Osoby:</label>
            <select id="persons"></select>
        </div>
        <div class="form-row-inline">
            <label for="whim">Zachcianka:</label>
            <input type="checkbox" id="whim">
        </div>

        <button type="submit">Add</button>
    </form>
</template>