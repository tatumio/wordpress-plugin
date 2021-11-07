import { Card } from "antd";
import "./index.scss";
import { Container } from "../../../components";
import { getImageUrl } from "../../../utils/image";
import React from "react";

export const WhyApiKey = () => {
    return (
        <Container>
            <Card title={<img className="header-logo" src={getImageUrl("tatum-logo.svg")} />}>
                <div className="whyApiKey">Why do I need an API key to sell NFTs?</div>
                <div>
                    API keys are used for authenticating and authorizing access to how the API is being used, in order
                    to prevent malicious use. The API key acts as both a unique identifier and a secret token for
                    authentication, both unlocking and protecting the work you will be doing with it.
                </div>
            </Card>
        </Container>
    );
};
