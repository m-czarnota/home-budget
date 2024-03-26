<script setup lang="ts">
import { PropType, ref } from "vue";

const props = defineProps({
    isDraggable: {
        type: Boolean,
        required: false,
        default: false,
    },
    isExpanded: {
        type: Boolean,
        required: false,
        default: undefined,
    },
    isEditable: {
        type: Boolean,
        required: false,
        default: false,
    },
    value: {
        type: String,
        required: false,
    },
    canHaveSubElements: {
        type: Boolean,
        required: false,
        default: false,
    },
    subElementsCount: {
        type: Number,
        default: 0,
        required: false,
    },
    error: {
        type: String as PropType<string | null>,
        required: false,
        default: null,
    },
});
const emit = defineEmits(['addSubElement', 'remove', 'updated']);

const inputValue = ref(props.value);
const isEdited = ref(false);

const switchEdition = () => {
    if (isEdited.value) {
        completeEdition();

        return;
    }

    isEdited.value = !isEdited.value;
};
const completeEdition = () => {
    isEdited.value = false;
    emit('updated', inputValue.value);
};
</script>

<template>
    <div class="flex gap-2 items-center w-full cursor-grab select-none rounded transition-colors duration-150 hover:bg-slate-200">
        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" v-if="isDraggable" />

        <font-awesome-icon 
            v-if="isExpanded !== undefined"
            icon="fa-solid fa-angle-right" 
            class="text-slate-600 cursor-pointer" 
            :class="{'rotate-90': isExpanded}" />
        <font-awesome-icon
            v-else 
            icon="fa-solid fa-minus" />

        <div class="w-full">
            <input 
                type="text" 
                v-model="inputValue" 
                v-if="isEdited" 
                @keyup.enter="completeEdition()" 
                @click.stop 
                class="w-fit select-text app-input py-1">
            <span v-else>
                {{ value }}
            </span>
            <p class="text-sm text-slate-600" v-if="canHaveSubElements">
                {{ $t('component.categoryList.category.countOfSubcategories') }}:
                {{ subElementsCount }}
            </p>
            <div class="text-error font-medium mt-2">
                {{ error }}
            </div>
        </div>

        <div class="flex gap-2 ml-2">
            <button type="button" v-if="isEditable" class="hover:text-purple-600" @click.stop="switchEdition()">
                <font-awesome-icon icon="fa-regular fa-circle-check" v-if="isEdited" :title="$t('component.categoryList.category.change')"/>
                <font-awesome-icon icon="fa-solid fa-file-pen" v-else :title="$t('component.categoryList.category.edit')"/>
            </button>

            <button type="button" v-if="canHaveSubElements" class="hover:text-purple-600" @click.stop="$emit('addSubElement')" :title="$t('component.categoryList.category.addSubcategory')">
                <font-awesome-icon icon="fa-solid fa-file-circle-plus" />
            </button>

            <button type="button" class="hover:text-purple-600" @click.stop="$emit('remove')" :title="$t('component.categoryList.category.delete')">
                <font-awesome-icon icon="fa-solid fa-trash" />
            </button>
        </div>
    </div>
</template>