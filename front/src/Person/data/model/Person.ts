export interface Person {
    id: string|null,
    name: string, 
    isDeleted: boolean,
    lastModified: string|null,
};

export type People = Person[];