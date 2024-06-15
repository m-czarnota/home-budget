<script setup lang="ts">
import { reactive, ref, computed } from 'vue';

import VListItem from '@/components/List/VListItem.vue';
import Alert from '@/components/Alert.vue';

import { Person } from '../data/model/Person';
import { PersonDataView } from '../data/dataView/PersonDataView';
import { AddUpdatePersonResponseError } from '../data/service/AddUpdatePersonResponseError';
import { RequestNotAcceptableError } from '@/http-client/RequestNotAcceptableError';
import { usePersonStore } from '../store/personStore';

const emit = defineEmits(['loadingError']);

const { getPeople, updatePerson, addPerson, removePerson } = usePersonStore();

let peopleDv: PersonDataView[] = [];
try {
    const peopleFromResponse = await getPeople();
    peopleDv = reactive(peopleFromResponse.value.map((person: Person) => new PersonDataView(person))) as PersonDataView[];
} catch (e) {
    emit('loadingError');
}

// ------------------- adding a new person -------------------
const isNewPersonAdding = ref(false);
const newPersonName = ref('');

const addNew = async () => {
    const personName = newPersonName.value.trim();
    if (!personName) {
        isNewPersonAdding.value = false;

        return;
    }

    const person: Person = {
        id: null,
        name: personName,
        isDeleted: false,
        lastModified: null,
    };
    const personFromResponse = await addPerson(person);
    const personDataView = new PersonDataView(personFromResponse);
    peopleDv.push(personDataView);

    isNewPersonAdding.value = false;
    newPersonName.value = '';
};

// ------------------- removing a new person -------------------
const remove = async (personDv: PersonDataView) => {
    const index = peopleDv.indexOf(personDv);
    if (index === -1) {
        throw new Error('You try remove a wrong person');
    }

    try {
        personDv.reset();
        personDv.isSaving = true;

        await removePerson(personDv.person);
    } catch(e) {
        if (!(e instanceof Error)) {
            return;
        }

        console.error(e.message);
        personDv.isRemoveError = true;

        return;
    } finally {
        personDv.isSaving = false;
    }

    peopleDv.splice(index, 1);
};

const update = async (personDv: PersonDataView, updatedName?: string) => {
    if (updatedName) {
        personDv.person.name = updatedName.trim();
    }

    try {
        personDv.reset();
        personDv.isSaving = true;

        const personFromResponse = await updatePerson(personDv.person);
        personDv.person = personFromResponse;
    } catch(e) {
        if (!(e instanceof Error)) {
            return;
        }

        if (e instanceof RequestNotAcceptableError) {
            const errors = JSON.parse(e.message) as AddUpdatePersonResponseError;
            personDv.error = errors.name;
        }

        console.error(e.message);
        personDv.isSaveFail = true;
    } finally {
        personDv.isSaving = false;
    }
};

// ------------------- viewing no people -------------------
const isNoPeople = computed(() => {
    return peopleDv.length === 0 && isNewPersonAdding.value === false;
});
</script>

<template>
    <form>
        <p v-if="isNoPeople" class="p-2">
            {{ $t('component.personList.noPeople') }}
        </p>

        <div v-for="personDv in peopleDv">
            <Alert
                v-if="personDv.isRemoveError"
                type="danger"
                :can-be-closed="false"
                :message="$t('component.personList.removeFail')"/>
            <Alert
                v-if="personDv.isSaveFail"
                type="danger"
                :can-be-closed="false"
                :message="$t('component.personList.saveFail')"/>

            <VListItem
                class="px-2 py-4"
                :class="{'shadow-inner shadow-red-300': personDv.isSaveFail}"
                :is-editable="true" 
                :value="personDv.person.name"
                :error="personDv.error"
                :is-saving-progress="personDv.isSaving"
                :is-save-fail="personDv.isSaveFail"
                @remove="remove(personDv)"
                @updated="(name) => update(personDv, name)"/>
        </div>

        <div class="px-4">
            <button 
                v-if="!isNewPersonAdding" 
                type="button" 
                class="hover:text-purple-600 text-lg" 
                :class="{'animate-bounce': isNoPeople}"
                @click.stop="isNewPersonAdding = true" 
                :title="$t('component.personList.person.add')"
            >
                <font-awesome-icon icon="fa-solid fa-folder-plus" />
            </button>

            <div v-if="isNewPersonAdding" class="space-y-2">
                <p>{{ $t('component.personList.person.new') }}:</p>

                <div class="flex gap-2">
                    <input type="text" v-model="newPersonName" @keyup.enter="addNew()">

                    <button type="button" class="hover:text-purple-600" @click.stop="addNew()">
                        <font-awesome-icon icon="fa-regular fa-circle-xmark" v-if="newPersonName.trim() === ''" :title="$t('form.cancel')"/>
                        <font-awesome-icon icon="fa-regular fa-circle-check" v-else :title="$t('form.save')"/>
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>