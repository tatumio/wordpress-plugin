import { useGet } from "../../hooks/useGet";
import { Nft, Nfts } from "../../models/nft";
import { Container } from "../../components";
import { Card } from "antd";
import React from "react";
import "./index.scss";

export const NftsOverviewLazy = () => {
    const { data } = useGet<Nfts>("/nfts");
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    console.log(data);
    return (
        <Container isGridCard={true}>
            <Card title="Lazy Minted NFTs">
                {data?.nfts && data.nfts.map((nft, index) => <NftItem nft={nft} key={index} />)}
            </Card>
        </Container>
    );
};

const NftItem = ({ nft }: { nft: Nft }) => {
    console.log(nft);
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Card.Grid hoverable={false} style={gridStyle}>
            <div className="card-item-grid-content grid-table nftItem">
                <img className="nftImage" src={nft.imageUrl} />
                <div className="nftInfo">
                    <div>{nft.name}</div>
                    <div>Product ID: {nft.productId}</div>
                    <div>Chain: {nft.chain}</div>
                    <div>Created: {nft.created.date}</div>
                </div>
            </div>
        </Card.Grid>
    );
};
