import { CardGridItem, Container } from "../../../components";
import { Card } from "antd";
import React, { useState } from "react";
import { useStores } from "../../../store";
import { Tutorial } from "../tutorial";
import { ResponseError, useMutate } from "../../../hooks/useMutate";
import { RouteHttpVerb } from "@tatum/utils";
import { showSuccess } from "../../../utils/message";

export const ApiKeyOverview = () => {
    const { apiKeyStore } = useStores();

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
                <Card title="TODO: picture here">
                    <CardGridItem title="Your Tatum plan" description={apiKeyStore.apiKey.plan} />
                    <CardGridItem title="Your Tatum api key" description={apiKeyStore.apiKey.apiKey} />
                    <CardGridItem
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
