import { Card } from "antd";
import React from "react";
import { CardGridItem } from "./CardGridItem";

export const VideoTutorials = () => {
    return (
        <Card className="video-tutorials-container grid-table" title="Tutorials">
            <CardGridItem buttonText="Watch Tutorial" title="blabla" description="blabla" />
            <CardGridItem buttonText="Watch Tutorial" title="blabla" description="blabla" />
        </Card>
    );
};
