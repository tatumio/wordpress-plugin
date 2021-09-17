import { CardGridItem, Container } from "../../../components";
import { Button, Card } from "antd";
import React from "react";
import { useStores } from "../../../store";

export const ApiKeyOverview = () => {
    const { apiKeyStore } = useStores();
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <>
            <Container isGridCard={true}>
                <Card title="🎉 You're ready to start selling NFTs!">
                    <CardGridItem
                        title="How to upload your first NFT on your webshop"
                        description="Learn how to use your product upload flow and create NFTs"
                        buttonText="Watch tutorial"
                    />
                    <Card.Grid hoverable={false} style={gridStyle}>
                        <Button>Dismiss</Button>
                    </Card.Grid>
                </Card>
            </Container>
            <Container isGridCard={true}>
                <Card title="TODO: picture here">
                    <CardGridItem title="Your Tatum plan" description={apiKeyStore.apiKey.plan} />
                    <CardGridItem title="Your Tatum api key" description={apiKeyStore.apiKey.apiKey} />
                    <CardGridItem
                        title="Remaining credits for month"
                        description={apiKeyStore.apiKey.remainingCredits.toString()}
                    />
                    <CardGridItem
                        title="Credits usage last month"
                        description={apiKeyStore.apiKey.usedCredits.toString()}
                    />
                    <CardGridItem title="Total NFTs created" description={apiKeyStore.apiKey.nftCreated.toString()} />
                    <CardGridItem title="Total NFTs sold" description={apiKeyStore.apiKey.nftSold.toString()} />
                </Card>
            </Container>
        </>
    );
};
