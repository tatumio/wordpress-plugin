import { CardGridItem, Container } from "../../../components";
import { Card } from "antd";
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
    const [isDismissed, setDismissTutorial] = useState(apiKeyStore?.apiKey?.isTutorialDismissed);

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
                        title="Your Tatum API key"
                        description={apiKeyStore.apiKey.apiKey}
                        onClick={() => pageStore.setPage(Page.GET_API_KEY)}
                    />
                    <CardGridItem
                        title="Remaining credits for month"
                        description={apiKeyStore.apiKey.remainingCredits.toString()}
                    />
                    <CardGridItem
                        title="Credits usage last month"
                        description={apiKeyStore.apiKey.usedCredits.toString()}
                    />
                    <CardGridItem
                        hoverable={true}
                        title="Total NFTs created"
                        description={apiKeyStore.apiKey.nftCreated.toString()}
                        onClick={() => pageStore.setPage(Page.NFTS_LAZY)}
                    />

                    <CardGridItem
                        hoverable={true}
                        title="Total NFTs sold"
                        description={apiKeyStore.apiKey.nftSold.toString()}
                        onClick={() => pageStore.setPage(Page.NFTS_MINTED)}
                    />
                </Card>
            </Container>
        </>
    );
};
