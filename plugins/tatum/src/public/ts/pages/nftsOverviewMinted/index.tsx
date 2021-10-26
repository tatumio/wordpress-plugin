import { useGet } from "../../hooks/useGet";
import { Nfts } from "../../models/nft";
import { NftsOverview, Spinner } from "../../components";
import React from "react";
import "./index.scss";

export const NftsOverviewMinted = () => {
    const { data } = useGet<Nfts>("/nfts/minted");
    return data?.nfts ? <NftsOverview nfts={data.nfts} lazy={false} title="Sold NFTs" /> : <Spinner />;
};
