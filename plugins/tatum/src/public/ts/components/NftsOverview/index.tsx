import { Nft } from "../../models/nft";
import { Card } from "antd";
import React from "react";
import { Container } from "../Container";
import "./index.scss";

export const NftsOverview = ({ nfts, lazy, title }: { nfts: Nft[]; lazy: boolean; title: string }) => {
    return (
        <Container isGridCard={true}>
            <Card title={title}>{nfts.length === 0 ? <NoNFTs /> : <Nfts nfts={nfts} lazy={lazy} />}</Card>
        </Container>
    );
};

const Nfts = ({ nfts, lazy }: { nfts: Nft[]; lazy: boolean }) => {
    return <>{nfts && nfts.map((nft, index) => <NftItem nft={nft} key={index} lazy={lazy} />)}</>;
};

const NoNFTs = () => {
    return <div className="no-nfts">No NFTs yet.</div>;
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
                    {nft.transactionId && (
                        <div>
                            Tx ID:{" "}
                            <a target="_blank" rel="noreferrer" href={nft.transactionLink}>
                                {nft.transactionId}
                            </a>
                        </div>
                    )}
                    <div>Chain: {nft.chain}</div>
                    {nft.transactionId || nft.errorCause ? (
                        <div>Sold: {new Date(nft.sold.date).toLocaleString()}</div>
                    ) : (
                        <div>Created: {new Date(nft.created.date).toLocaleString()}</div>
                    )}
                    {nft.errorCause && <div>Error cause: {nft.errorCause}</div>}
                </div>
            </div>
        </Card.Grid>
    );
};
