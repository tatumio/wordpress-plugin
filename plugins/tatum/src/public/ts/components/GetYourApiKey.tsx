import { Button, Card } from "antd";
import React from "react";
import { CardItemText } from "./CardItemText";
import { CardGridItem } from "./CardGridItem";

export const GetYourApiKey = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };

    return (
        <div className="get-your-api-key-cards grid-table">
            <Card title="Step 1 - Get your API key">
                <CardGridItem
                    title="Start - $9 per month"
                    description="Credits: 1,000,000"
                    secondDescription="Billed monthly"
                    buttonText="Get API key"
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                />
                <CardGridItem
                    title="Start - $49 per month"
                    description="Credits: 5,000,000"
                    secondDescription="Billed monthly"
                    buttonText="Get API key"
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                />
                <CardGridItem
                    title="Start - $249 per month"
                    description="Credits: 25,000,000"
                    secondDescription="Billed monthly"
                    buttonText="Get API key"
                    buttonType="primary"
                    buttonLink="https://dashboard.tatum.io"
                />
                <Card.Grid hoverable={false} style={gridStyle}>
                    <div className="card-item-grid-content grid-table">Accepted payment methods</div>
                </Card.Grid>
            </Card>
        </div>
    );
};
