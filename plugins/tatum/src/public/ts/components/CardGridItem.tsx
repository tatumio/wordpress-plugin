import { Button, Card } from "antd";
import { CardItemText } from "./CardItemText";
import React from "react";

const CardGridItem = ({ hoverable }: { hoverable: boolean,  }) => {
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
