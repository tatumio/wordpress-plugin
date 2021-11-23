import { Card } from "antd";
import React from "react";
import { CardGridItem } from "../../../components/CardGridItem";
import { Container } from "../../../components/Container";

export const VideoTutorials = () => {
    return (
        <Container isGridCard={true}>
            <Card title="Tutorials">
                <CardGridItem
                    buttonText="Watch Tutorial"
                    title="How to create a product as an NFT in your Woocommerce Store?"
                    description="Learn how to use your product upload flow and create NFTs"
                    buttonLink="https://www.youtube.com/watch?v=QHl7NoFY7ts"
                />
                <CardGridItem
                    buttonText="Watch Tutorial"
                    title="How to get your Tatum API key?"
                    description="Learn how to use your product upload flow and create NFTs"
                    buttonLink="https://youtube.com/watch?v=V830p6DwnIw"
                />
            </Card>
        </Container>
    );
};
