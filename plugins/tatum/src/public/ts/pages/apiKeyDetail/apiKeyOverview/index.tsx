import { CardGridItem, CardItemText, Container } from "../../../components";
import { Card, Progress } from "antd";
import React, { useState } from "react";
import { useStores } from "../../../store";
import { Tutorial } from "../tutorial";
import { useMutate } from "../../../hooks/useMutate";
import { RouteHttpVerb } from "@tatum/utils";
import { showSuccess } from "../../../utils/message";
import { ResponseError } from "../../../models/reponseError";
import { getImageUrl } from "../../../utils/image";
import "./index.scss";
import { Page } from "../../../models";

export const ApiKeyOverview = () => {
    const { apiKeyStore, pageStore } = useStores();

    const { mutate } = useMutate<ResponseError>({ path: "/dismiss-tutorial", method: RouteHttpVerb.POST });
    const [isDismissed, setDismissTutorial] = useState(apiKeyStore.apiKey.isTutorialDismissed);

    const dismissTutorial = async () => {
        setDismissTutorial(true);
        await mutate();
        showSuccess("Tutorial hidden.");
    };

    return (
        <>
            {!isDismissed && <Tutorial dismissTutorial={dismissTutorial} />}
            <Container isGridCard={true}>
                <Card title={<img className="header-overview" src={getImageUrl("header-overview.png")} />}>
                    <CardGridItem title="Your Tatum plan" description={apiKeyStore.apiKey.plan} />
                    <CardGridItem
                        hoverable={true}
                        title="Your Tatum api key"
                        description={apiKeyStore.apiKey.apiKey}
                        onClick={() => pageStore.setPage(Page.GET_API_KEY)}
                    />
                    <CardGridItemProgress
                        title="Remaining credits for month"
                        description={apiKeyStore.apiKey.remainingCredits.toString()}
                    />
                    <CardGridItem
                        title="Credits usage last month"
                        description={apiKeyStore.apiKey.usedCredits.toString()}
                    />
                    <CardGridItem title="Total NFTs created" description={apiKeyStore.apiKey.nftCreated.toString()} />

                    <CardGridItem title="Total NFTs sold" description={apiKeyStore.apiKey.nftSold.toString()} />
                </Card>
            </Container>
        </>
    );
};

export const CardGridItemProgress = ({
    hoverable = false,
    title,
    description,
    secondDescription
}: {
    hoverable?: boolean;
    title: string;
    description?: string;
    secondDescription?: string;
}) => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    const { apiKeyStore } = useStores();
    const percentUsed = Math.round(apiKeyStore.apiKey.usedCredits / apiKeyStore.apiKey.creditLimit);

    return (
        <Card.Grid hoverable={hoverable} style={gridStyle}>
            <div className="card-item-grid-content grid-table">
                <CardItemText title={title} description={description} secondDescription={secondDescription} />
                <Progress type="circle" percent={percentUsed} />
            </div>
        </Card.Grid>
    );
};
