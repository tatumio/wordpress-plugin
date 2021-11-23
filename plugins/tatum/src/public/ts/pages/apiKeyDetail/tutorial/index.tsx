import { CardGridItem, Container } from "../../../components";
import { Button, Card } from "antd";
import React from "react";

export const Tutorial = ({ dismissTutorial }: { dismissTutorial: () => void }) => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };

    return (
        <Container isGridCard={true}>
            <Card title="🎉 You're ready to start selling NFTs!">
                <CardGridItem
                    title="How to upload your first NFT on your webshop"
                    description="Learn how to use your product upload flow and create NFTs"
                    buttonText="Watch tutorial"
                    buttonLink="https://www.youtube.com/watch?v=QHl7NoFY7ts"
                />
                <Card.Grid hoverable={false} style={gridStyle}>
                    <Button onClick={dismissTutorial}>Dismiss</Button>
                </Card.Grid>
            </Card>
        </Container>
    );
};
