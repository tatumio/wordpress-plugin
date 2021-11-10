import { ResponseError } from "./reponseError";

export interface Estimates extends ResponseError {
    estimates: Estimate[];
}

export interface Estimate {
    starter: number;
    basic: number;
    advanced: number;
    chain: string;
    label: string;
}
