<script setup lang="ts">
import { ref } from 'vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { PropType } from 'vue';

const emit = defineEmits(['changed']);

type UnknownObject = {
    id: string|null,
    name: string,
};

const props = defineProps({
    elements: {
        type: Array<UnknownObject>,
        required: true,
    },
    selected: {
        type: Object as PropType<UnknownObject|null>,
        required: false,
        default: null,
    }
});
const selected = ref(props.selected ?? props.elements[0]);
</script>

<template>
    <Listbox 
        v-model="selected" 
        @update:model-value="emit('changed', selected)"
        v-slot="{ open }"
    >
        <div class="relative">
            <ListboxButton class="relative">
                <span class="block truncate mr-2">{{ selected.name }}</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <font-awesome-icon icon="fa-solid fa-chevron-down" class="size-3"/>
                </span>
            </ListboxButton>

            <transition 
                v-show="open"
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-out"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <ListboxOptions static class="absolute mt-1 max-h-60 bg-white rounded-lg border border-purple-700 z-10">
                    <ListboxOption
                        v-slot="{ active, selected, disabled }"
                        v-for="element in elements"
                        :key="String(element.id)"
                        :value="element"
                        :disabled="!element.id"
                        as="template"
                    >
                        <li 
                            :class="{
                                'cursor-not-allowed text-gray-500': disabled,
                                'bg-purple-100': active && !disabled,
                            }"
                            class="relative cursor-default select-none py-2 pl-10 pr-4 rounded-lg"
                        >
                            <span class="block truncate" :class="[selected ? 'font-medium' : 'font-normal']">
                                {{ element.name }}
                            </span>

                            <span 
                                v-if="selected"   
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600"
                            >
                                <font-awesome-icon icon="fa-solid fa-check" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>