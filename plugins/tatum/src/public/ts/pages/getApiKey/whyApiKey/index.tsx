import { Card } from "antd";
import "./index.scss";
import { Container } from "../../../components";
import { getImageUrl } from "../../../utils/image";
import React from "react";

export const WhyApiKey = () => {
    return (
        <Container>
            <Card title={<img className="header-logo" src={getImageUrl("tatum-logo.svg")} />}>
                <div className="whyApiKey">Why do I need an API key to sell NFTs?</div>
                <div className="description">
                    Normally, to create NFTs on different blockchains, you would need to have access to blockchain nodes
                    and create wallets on each blockchain. With NFT Maker, you can simply use an API key to connect to
                    different blockchains through Tatum, and everything else is taken care of for you.
                </div>
            </Card>
        </Container>
    );
};
