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
            <div className="title">
                NFT maker <span className="title-tatum">by Tatum.</span>
            </div>
            <div className="title-description">
                Who said that creating NFTs requires years of blockchain development experience? The NFT Maker by Tatum
                allows you to turn your Woocommerce store into an NFT store with a simple plugin integration. Install
                our plugin, follow your usual WordPress product publishing flow, and simply select the extra tickbox —
                make NFT. Using the “lazy minting” feature your NFT is minted at the moment of purchase. You also get
                free IPFS storage for your metadata but keep in mind it only supports up to 50mbs product images upload.
                The plugin supports Ethereum, Binance Smart Chain, Polygon, Harmony and Celo.
            </div>
        </div>
    );
};
