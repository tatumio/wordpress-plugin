import { ResponseError } from "./reponseError";

export interface Nfts extends ResponseError {
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
