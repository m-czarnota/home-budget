<script setup lang="ts">
import { reactive, ref, computed } from 'vue';

import VListElement from '../../Category/component/VListElement.vue';
import Alert from '../../components/Alert.vue';

import { Person } from '../data/model/Person';
import { PersonDataView } from '../data/dataView/PersonDataView';
import { PersonService } from '../data/service/PersonService';
import { AddUpdatePersonResponseError } from '../data/service/AddUpdatePersonResponseError';
import { RequestNotAcceptableError } from '../../http-client/RequestNotAcceptableError';

const emit = defineEmits(['loadingError']);

let peopleDv: PersonDataView[] = [];

try {
    const peopleFromResponse = await PersonService.getPeople();
    peopleDv = reactive(peopleFromResponse.map((person: Person) => new PersonDataView(person))) as PersonDataView[];
} catch (e) {
    emit('loadingError');
}

// ------------------- adding a new person -------------------
const isNewPersonAdding = ref(false);
const newPersonName = ref('');

const addNewPerson = async () => {
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
    const personDataView = new PersonDataView(person);
    peopleDv.push(personDataView);

    setTimeout(async () => {
        const personDv = peopleDv[peopleDv.indexOf(personDataView)];
        updatePerson(personDv);
    }, 0);

    isNewPersonAdding.value = false;
    newPersonName.value = '';
};

// ------------------- removing a new person -------------------
const removePerson = async (personDv: PersonDataView) => {
    const index = peopleDv.indexOf(personDv);
    if (index === -1) {
        throw new Error('You try remove a wrong person');
    }

    try {
        personDv.reset();
        personDv.isSaving = true;

        await PersonService.removePerson(personDv.person);
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

const updatePerson = async (personDv: PersonDataView, updatedName?: string) => {
    if (updatedName) {
        personDv.person.name = updatedName.trim();
    }

    try {
        personDv.reset();
        personDv.isSaving = true;

        const personFromResponse = await PersonService.updatePerson(personDv.person);
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

            <VListElement
                class="px-2 py-4"
                :class="{'shadow-inner shadow-red-300': personDv.isSaveFail}"
                :is-editable="true" 
                :value="personDv.person.name"
                :error="personDv.error"
                :is-saving-progress="personDv.isSaving"
                :is-save-fail="personDv.isSaveFail"
                @remove="removePerson(personDv)"
                @updated="(name) => updatePerson(personDv, name)"/>
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
                    <input type="text" v-model="newPersonName" @keyup.enter="addNewPerson()">

                    <button type="button" class="hover:text-purple-600" @click.stop="addNewPerson()">
                        <font-awesome-icon icon="fa-regular fa-circle-xmark" v-if="newPersonName.trim() === ''" :title="$t('form.cancel')"/>
                        <font-awesome-icon icon="fa-regular fa-circle-check" v-else :title="$t('form.save')"/>
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>