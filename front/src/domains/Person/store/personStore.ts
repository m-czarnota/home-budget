import { defineStore } from "pinia";
import { ComputedRef, Ref, computed, ref } from "vue";
import { People, Person } from "../data/model/Person";
import { PersonService } from "../data/service/PersonService";

export const usePersonStore = defineStore('person', () => {
    const people: Ref<People|null> = ref(null);

    async function getPeople(): Promise<ComputedRef<People>> {
        if (people.value === null) {
            people.value = await PersonService.getPeople();
        }

        return computed(() => people.value as People);
    }

    async function updatePerson(updatedPerson: Person): Promise<Person> {
        if (people.value === null) {
            people.value = await PersonService.getPeople();
        }

        const index = people.value.findIndex(person => person.id === updatedPerson.id);
        if (index === undefined) {
            throw new Error(`You cannot update person ${updatedPerson.id} because it doesn't exist!`);
        }

        const personFromResponse = await PersonService.updatePerson(updatedPerson);
        people.value[index] = personFromResponse;
        
        return personFromResponse;
    }

    async function addPerson(person: Person): Promise<Person> {
        const personFromResponse = await PersonService.addPerson(person);
        if (people.value === null) {
            people.value = await PersonService.getPeople();
        }

        people.value.push(personFromResponse);

        return person;
    }

    async function removePerson(personToRemove: Person): Promise<void> {
        if (people.value === null) {
            people.value = await PersonService.getPeople();
        }

        const index = people.value.findIndex(person => person.id === personToRemove.id);
        if (index === undefined) {
            throw new Error(`You cannot remove person ${personToRemove.id} because it doesn't exist!`);
        }

        await PersonService.removePerson(personToRemove);
        people.value.splice(index, 1);
    }

    return {
        getPeople,
        updatePerson,
        addPerson,
        removePerson,
    };
});