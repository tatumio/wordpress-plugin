import { Card, Input } from "antd";
import React from "react";

export const PasteApiKey = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <div className="get-your-api-key-cards grid-table">
            <Card title="Step 2 - Paste your API key below (Final step)">
                <Card.Grid hoverable={false} style={gridStyle}>
                    <div className="card-item-grid-content grid-table">Paste your API key</div>
                    {/* TODO: change style of input */}
                    <Input placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" />
                </Card.Grid>
            </Card>
        </div>
    );
};
