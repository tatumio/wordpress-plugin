import { Button, Card, Input, Modal } from "antd";
import React, { useEffect, useState } from "react";
import "./index.scss";
import { ResponseError, useMutate } from "../../../hooks/useMutate";
import { RouteHttpVerb } from "@tatum/utils";
import { Spinner, Container } from "../../../components";
import { useStores } from "../../../store";
import { Page } from "../../../models/page";

interface ApiKeyResponse extends ResponseError {
    version: string;
}

export const PasteApiKey = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    const { data, mutate, loading } = useMutate<ApiKeyResponse>({ path: "/api-key", method: RouteHttpVerb.POST });
    const [apiKey, setApiKey] = useState<string>();
    const { pageStore } = useStores();

    useEffect(() => {
        if (data?.version) {
            Modal.success({
                title: "API key successfully set up.",
                content: "Your API key was successfully set up and you are ready to mint NFTs!",
                okText: "Mint your first NFT!",
                onOk: () => {
                    pageStore.setPage(Page.API_KEY_DETAIL);
                }
            });
        }
    }, [data]);

    return (
        <Container isGridCard={true}>
            <Card title="Step 2 - Paste your API key below (Final step)">
                <Card.Grid hoverable={false} style={gridStyle}>
                    <div>Paste your API key</div>
                    <div className="input-container">
                        <Input
                            onChange={(event) => setApiKey(event.target.value)}
                            size="large"
                            placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                        />
                    </div>
                </Card.Grid>
                <Card.Grid hoverable={false} style={gridStyle}>
                    {loading ? (
                        <Spinner />
                    ) : (
                        <Button type="primary" onClick={() => mutate({ apiKey })}>
                            Finish Setup
                        </Button>
                    )}
                </Card.Grid>
            </Card>
        </Container>
    );
};
