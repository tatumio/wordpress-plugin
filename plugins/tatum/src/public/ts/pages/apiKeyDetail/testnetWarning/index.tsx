import { Container } from "../../../components";
import { Alert, Card } from "antd";
import React from "react";

export const TestnetWarning = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Container isGridCard={true}>
            <Card>
                <Card.Grid hoverable={false} style={gridStyle}>
                    <Alert
                        message="Testnet Network"
                        description={
                            <div>
                                Your Tatum API key is the Testnet network type. It should be used only for testing
                                purposes and NFT minted with this API key dont have any value. If want work with the
                                real Mainnet NFTs buy paid API key plan at{" "}
                                <a href="https://dashboard.tatum.io" target="_blank" rel="noreferrer">
                                    Tatum dashboard.
                                </a>
                            </div>
                        }
                        type="warning"
                        showIcon
                    />
                </Card.Grid>
            </Card>
        </Container>
    );
};
