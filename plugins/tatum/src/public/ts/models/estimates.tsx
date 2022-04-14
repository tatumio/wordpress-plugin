import { Error } from "./error";

export interface Estimates extends Error {
    estimates: Estimate[];
}

export interface Estimate {
    starter: number;
    basic: number;
    advanced: number;
    chain: string;
    label: string;
}
