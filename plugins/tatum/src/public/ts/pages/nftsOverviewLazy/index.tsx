import { useGet } from "../../hooks/useGet";
import { Nfts } from "../../models/nft";
import { NftsOverview, Spinner } from "../../components";
import React from "react";
import "./index.scss";

export const NftsOverviewLazy = () => {
    const { data } = useGet<Nfts>("/nfts/lazy");
    return data?.nfts ? <NftsOverview nfts={data.nfts} lazy={true} title="NFTs Created" /> : <Spinner />;
};
