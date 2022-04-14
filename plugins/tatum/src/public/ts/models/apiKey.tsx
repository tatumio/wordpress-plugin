import { Error } from "./error";

export interface ApiKey extends Error {
    apiKey: string;
    plan: string;
    remainingCredits: number;
    usedCredits: number;
    creditLimit: number;
    nftCreated: number;
    nftSold: number;
    isTutorialDismissed: boolean;
    version: string;
    testnet: boolean;
}
