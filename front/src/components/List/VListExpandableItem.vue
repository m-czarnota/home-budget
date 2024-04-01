<script setup lang="ts">
import { PropType } from "vue";
import VListItem from "./VListItem.vue";

defineProps({
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
    isSavingProgress: {
        type: Boolean,
        required: false,
        default: false,
    },
    isSaveFail: {
        type: Boolean,
        required: false,
        default: false,
    },
});
const emit = defineEmits(['addSubElement', 'remove', 'updated']);
</script>

<template>
    <VListItem
        :is-draggable="isDraggable"
        :is-editable="isEditable"
        :value="value"
        :error="error"
        :is-saving-progress="isSavingProgress"
        :is-save-fail="isSaveFail"
        @remove="$emit('remove')"
        @updated="value => $emit('updated', value)"
    >
        <template #position>
            <font-awesome-icon 
                v-if="isExpanded !== undefined"
                icon="fa-solid fa-angle-right" 
                class="text-slate-600 cursor-pointer" 
                :class="{'rotate-90': isExpanded}" />
            <font-awesome-icon
                v-else 
                icon="fa-solid fa-minus" />
        </template>

        <template #buttons>
            <button type="button" v-if="canHaveSubElements" :disabled="isSavingProgress" class="hover:text-purple-600" @click.stop="$emit('addSubElement')" :title="$t('component.list.expandableItem.addSubItem')">
                <font-awesome-icon icon="fa-solid fa-file-circle-plus" />
            </button>
        </template>

        <template #additional-info>
            <p class="text-sm text-slate-600" v-if="canHaveSubElements">
                {{ $t('component.list.expandableItem.subItemsCount') }}:
                {{ subElementsCount }}
            </p>
        </template>
    </VListItem>
</template>