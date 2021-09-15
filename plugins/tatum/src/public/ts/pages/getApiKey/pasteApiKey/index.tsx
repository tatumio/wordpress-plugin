import { Button, Card, Input } from "antd";
import React from "react";
import { Container } from "../../../components/container";
import "./index.scss";
import { useMutate } from "../../../hooks/useMutate";
import { RouteHttpVerb } from "@tatum/utils";

export const PasteApiKey = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    const { data, mutate } = useMutate<unknown>({ path: "/api-key", method: RouteHttpVerb.POST });
    console.log(data);
    return (
        <Container isGridCard={true}>
            <Card title="Step 2 - Paste your API key below (Final step)">
                <Card.Grid hoverable={false} style={gridStyle}>
                    <div>Paste your API key</div>
                    <div className="input-container">
                        <Input size="large" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" />
                    </div>
                </Card.Grid>
                <Card.Grid hoverable={false} style={gridStyle}>
                    <Button type="primary" onClick={() => mutate()}>
                        Finish Setup
                    </Button>
                </Card.Grid>
            </Card>
        </Container>
    );
};
