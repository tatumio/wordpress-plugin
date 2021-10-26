import { Nft } from "../../models/nft";
import { Card } from "antd";
import React from "react";
import { Container } from "../container";

export const NftsOverview = ({ nfts, lazy, title }: { nfts: Nft[]; lazy: boolean; title: string }) => {
    return (
        <Container isGridCard={true}>
            <Card title={title}>{nfts && nfts.map((nft, index) => <NftItem nft={nft} key={index} lazy={lazy} />)}</Card>
        </Container>
    );
};

const NftItem = ({ nft, lazy }: { nft: Nft; lazy: boolean }) => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Card.Grid hoverable={false} style={gridStyle}>
            <div className="nftItem">
                <img className="nftImage" src={nft.imageUrl} />
                <div className="nftInfo">
                    <div className="nftName">{nft.name}</div>
                    <div>Product ID: {nft.productId}</div>
                    {!lazy && (
                        <div>
                            Transaction ID:
                            <a target="_blank" rel="noreferrer" href={nft.transactionLink}>
                                {nft.transactionId}
                            </a>
                        </div>
                    )}
                    <div>Chain: {nft.chain}</div>
                    <div>Created: {new Date(nft.created.date).toLocaleString()}</div>
                </div>
            </div>
        </Card.Grid>
    );
};
