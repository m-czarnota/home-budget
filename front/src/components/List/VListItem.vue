<script setup lang="ts">
import { PropType, ref } from "vue";

const props = defineProps({
    isDraggable: {
        type: Boolean,
        required: false,
        default: false,
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
const emit = defineEmits(['remove', 'updated']);

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
    <div 
        class="flex gap-2 items-center w-full select-none rounded transition-colors duration-150 hover:bg-slate-200"
        :class="{'cursor-grab': isDraggable}"
    >
        <font-awesome-icon icon="fa-solid fa-bars" class="text-slate-600 mr-3" v-if="isDraggable" />

        <slot name="position">
            <font-awesome-icon icon="fa-solid fa-minus" />
        </slot>

        <div class="w-full flex flex-col justify-center">
            <div>
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
            </div>

            <slot name="additional-info"/>

            <div class="text-error font-medium mt-2">
                {{ error }}
            </div>
        </div>

        <div class="flex gap-2 ml-2">
            <slot name="buttons"/>

            <button 
                type="button" 
                title="Try again" 
                class="text-purple-600 flex justify-center items-center" 
                v-if="isSaveFail || isSavingProgress" 
                @click="completeEdition()" 
                :disabled="isSavingProgress"
            >
                <span class="loading loading-spinner loading-sm" v-if="isSavingProgress"></span>
                <font-awesome-icon icon="fa-solid fa-cloud-arrow-up" v-else/>
            </button>

            <button 
                type="button" 
                v-if="isEditable" 
                class="hover:text-purple-600" 
                @click.stop="switchEdition()" 
                :disabled="isSavingProgress"
            >
                <font-awesome-icon icon="fa-regular fa-circle-check" v-if="isEdited" :title="$t('component.list.item.change')"/>
                <font-awesome-icon icon="fa-solid fa-file-pen" v-else :title="$t('component.list.item.edit')"/>
            </button>

            <button 
                type="button" 
                class="hover:text-purple-600" 
                :disabled="isSavingProgress" 
                @click.stop="$emit('remove')" 
                :title="$t('component.list.item.delete')"
            >
                <font-awesome-icon icon="fa-solid fa-trash" />
            </button>
        </div>
    </div>
</template>