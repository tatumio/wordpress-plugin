import { Card, Input } from "antd";
import React from "react";
import { Container } from "../../../components/container";
import "./index.scss";

export const PasteApiKey = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Container isGridCard={true}>
            <Card title="Step 2 - Paste your API key below (Final step)">
                <Card.Grid hoverable={false} style={gridStyle}>
                    <div>Paste your API key</div>
                    {/* TODO: change style of input */}
                    <Input placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" />
                </Card.Grid>
            </Card>
        </Container>
    );
};
