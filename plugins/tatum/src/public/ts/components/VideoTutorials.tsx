import { Button, Card } from "antd";
import React from "react";
import { CardItemText } from "./CardItemText";

export const VideoTutorials = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };

    return (
        <Card className="video-tutorials-container grid-table" title="Tutorials">
            <Card.Grid style={gridStyle} hoverable={false}>
                <CardItemVideoContent />
            </Card.Grid>
            <Card.Grid style={gridStyle} hoverable={false}>
                <CardItemVideoContent />
            </Card.Grid>
        </Card>
    );
};

const CardItemVideoContent = () => (
    <div className="card-item-video-content grid-table">
        <CardItemText title="blabla" description="blabal" />
        <Button>Watch Tutorial</Button>
    </div>
);
