import { Card } from "antd";
import { Container } from "../../../components/Container";
import { getImageUrl } from "../../../utils/image";
import React from "react";
import "./index.scss";

export const About = () => (
    <Container>
        <Card>
            <Title />
        </Card>
    </Container>
);

const Title = () => {
    return (
        <div className="title-container">
            <img className="header-overview" src={getImageUrl("header-overview.png")} />
            <div className="title-description">
                If you want to sell NFTs but don’t want to build an entire NFT marketplace from scratch, then NFT Maker
                is the plugin you’ve been waiting for.
                <br />
                <br />
                Lazy Minting. Free IPFS Storage, forever. Supports Ethereum, Polygon, Binance Smart Chain, Celo, and
                Harmony.
                <br />
                <br />
                NFT Maker by Tatum allows you to turn your Woocommerce store into an NFT store with a simple plugin.
                Install our plugin, follow your usual WordPress product publishing flow, and just tick which blockchain
                you’d like to mint your NFTs on.
            </div>
        </div>
    );
};
