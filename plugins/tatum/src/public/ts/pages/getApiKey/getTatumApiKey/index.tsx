import { Button, Card } from "antd";
import React from "react";
import { CardGridItem } from "../../../components/CardGridItem";
import { Container } from "../../../components/container";
import { useStores } from "../../../store";

export const GetTatumApiKey = () => {
    const { apiKeyStore } = useStores();
    const gridStyle = {
        width: "100%",
        align: "center"
    };

    return (
        <Container isGridCard={true}>
            <Card title="Step 1 - Get your API key">
                <CardGridItem
                    title="Start - $9 per month"
                    description="Credits: 1,000,000"
                    secondDescription="Billed monthly"
                    buttonText={apiKeyStore?.apiKey?.plan === "Free" ? "Current key" : "Get API key"}
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                    buttonDisabled={apiKeyStore?.apiKey?.plan === "Free"}
                />
                <CardGridItem
                    title="Basic - $49 per month"
                    description="Credits: 5,000,000"
                    secondDescription="Billed monthly"
                    buttonText={apiKeyStore?.apiKey?.plan === "Basic" ? "Current key" : "Get API key"}
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                    buttonDisabled={apiKeyStore?.apiKey?.plan === "Basic"}
                />
                <CardGridItem
                    title="Advanced - $249 per month"
                    description="Credits: 25,000,000"
                    secondDescription="Billed monthly"
                    buttonText={apiKeyStore?.apiKey?.plan === "Advanced" ? "Current key" : "Get API key"}
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                    buttonDisabled={apiKeyStore?.apiKey?.plan === "Advanced"}
                />
            </Card>
        </Container>
    );
};
