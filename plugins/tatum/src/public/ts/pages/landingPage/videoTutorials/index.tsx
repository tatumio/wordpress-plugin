import { Card } from "antd";
import React from "react";
import { CardGridItem } from "../../../components/CardGridItem";
import { Container } from "../../../components/Container";

export const VideoTutorials = () => {
    return (
        <Container isGridCard={true}>
            <Card title="Tutorials">
                <CardGridItem buttonText="Watch Tutorial" title="blabla" description="blabla" />
                <CardGridItem buttonText="Watch Tutorial" title="blabla" description="blabla" />
                <CardGridItem buttonText="Watch Tutorial" title="blabla" description="blabla" />
            </Card>
        </Container>
    );
};
