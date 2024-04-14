export interface VSelectItem {
    id: string|null,
    name: string,
    subItems: Array<VSelectItem>,
};

export type VSelectData = VSelectItem[];