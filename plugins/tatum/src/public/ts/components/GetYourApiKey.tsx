import { Button, Card } from "antd";
import React from "react";
import { CardItemText } from "./CardItemText";

export const GetYourApiKey = () => {
    return (
        <div className="get-your-api-key-cards grid-table">
            <Card title="Step 1 - Get your API key">
                <CardItemVideoContent />
                <CardItemVideoContent />
                <CardItemVideoContent />
                <CardItemVideoContent />
            </Card>
        </div>
    );
};

const CardItemVideoContent = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Card.Grid hoverable={false} style={gridStyle}>
            <div className="card-item-video-content grid-table">
                <CardItemText title="blabla" description="blabal" />
                <Button type="primary">Watch Tutorial</Button>
            </div>
        </Card.Grid>
    );
};
