import { HttpClient } from "../../../http-client/HttpClient";
import { RequestNotAcceptableError } from "../../../http-client/RequestNotAcceptableError";
import { People, Person } from "../model/Person";
import { ResponsePersonFieldChecker } from "./ResponsePersonFieldChecker";

export class PersonService {
    static readonly RESOURCE = "/people";

    public static async getPeople(): Promise<People> {
        try {
            const response = await HttpClient.get(PersonService.RESOURCE);      

            if (!response.isOk) {
                throw new Error("Cannot fetch people. Try again later.");
            }

            const people = response.body;
            if (!(people instanceof Array)) {
                throw new Error("Cannot fetch people. Try again later.");
            }
            
            ResponsePersonFieldChecker.checkFields(people);

            return PersonService.mapResponseBodyToPeople(people);
        } catch (error) {
            throw new Error(`Error while fetching people. | ${error}`);
        }
    }

    public static async addPerson(person: Person): Promise<Person> {
        const response = await HttpClient.post(PersonService.RESOURCE, JSON.stringify(person));
        const responseBody = response.body;
        
        if (response.statusCode === 400) {
            throw new Error(`Bad request sended to API. | ${responseBody}`);
        }

        if (response.statusCode === 201) {
            ResponsePersonFieldChecker.checkFields([responseBody]);

            return responseBody;
        }

        if (response.statusCode !== 406) {
            throw new Error('Cannot add new person');
        }

        throw new RequestNotAcceptableError(JSON.stringify(responseBody));
    }

    public static async removePerson(person: Person): Promise<void> {
        if (person.id === null) {
            return;
        }

        const link = `${PersonService.RESOURCE}/${person.id}`;
        const response = await HttpClient.delete(link);

        if (response.statusCode === 204) {
            return;
        }

        const responseBody = response.body;

        if (response.statusCode === 404) {
            throw new Error(`Cannot remove person. | ${responseBody}`);
        }
    }

    public static async updatePerson(person: Person): Promise<Person> {
        if (person.id === null) {
            const personFromResponse = await PersonService.addPerson(person);

            return personFromResponse;
        }

        const link = `${PersonService.RESOURCE}/${person.id}`;
        const response = await HttpClient.patch(link, JSON.stringify(person));
        const responseBody = response.body;
        
        if (response.statusCode === 400) {
            throw new Error(`Bad request sended to API. | ${responseBody}`);
        }

        if (response.statusCode === 200) {
            ResponsePersonFieldChecker.checkFields([responseBody]);

            return responseBody;
        }

        if (response.statusCode !== 406) {
            throw new Error('Cannot update the person');
        }

        throw new RequestNotAcceptableError(JSON.stringify(responseBody));
    }

    private static mapResponseBodyToPeople(responseBody: Array<any>): People {
        return responseBody.map((person: Person) => ({
            id: person.id,
            name: person.name,
            isDeleted: person.isDeleted,
            lastModified: person.lastModified,
        }));
    }
}