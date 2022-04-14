import { Error } from "./error";

export interface Nfts extends Error {
    nfts: Nft[];
}

export interface Nft {
    name: string;
    transactionId: string | null;
    transactionLink: string | null;
    errorCause: string | null;
    imageUrl: string | null;
    openSeaUrl: string | null;
    tokenId: string | null;
    chain: string;
    productId: string;
    sold: Date;
    created: Date;
}

interface Date {
    date: string;
}
