import React, { useEffect } from "react";
import { observer } from "mobx-react";
import { Col, Layout as AntdLayout, Row } from "antd";

import { useStores } from "../../store";
import { LandingPage } from "../../pages/landingPage";
import { GetApiKey } from "../../pages/getApiKey";
import { Page } from "../../models";
import { LeftOutlined } from "@ant-design/icons";
import "./index.scss";
import { ApiKeyDetail } from "../../pages/apiKeyDetail";
import { Spinner } from "../spinner";
import { useGet } from "../../hooks/useGet";
import { ApiKey } from "../../models";
import { Help } from "../../pages/help";
import { NftsOverviewLazy } from "../../pages/nftsOverviewLazy";
import { NftsOverviewMinted } from "../../pages/nftsOverviewMinted";

export const Layout = observer(() => {
    const { Header, Footer, Content: AntdContent } = AntdLayout;
    const { page, header } = usePageContent();
    const { data } = useGet<ApiKey>("/api-key");
    const { pageStore, apiKeyStore } = useStores();

    useEffect(() => {
        if (data) {
            if (data.apiKey) {
                apiKeyStore.setApiKey(data);
                pageStore.setPage(Page.API_KEY_DETAIL);
            } else {
                pageStore.setPage(Page.LANDING);
            }
        }
    }, [data]);

    return (
        <AntdLayout className="tatum">
            <Header style={{ backgroundColor: "#fff" }}>
                <Row justify="space-around" align="middle">
                    <Col span={8}>{header}</Col>
                    <Col span={8} offset={8} style={{ display: "flex", justifyContent: "flex-end" }}>
                        <div>A plugin by Tatum</div>
                    </Col>
                </Row>
            </Header>
            <AntdContent style={{ backgroundColor: "#f9f9f9" }}>
                <Row style={{ backgroundColor: "#f9f9f9" }}>
                    <Col
                        span={12}
                        offset={6}
                        style={{ display: "flex", justifyContent: "center", flexDirection: "column" }}
                    >
                        {!data ? (
                            <div style={{ height: "100vh", marginTop: "100px" }}>
                                <Spinner />
                            </div>
                        ) : (
                            page
                        )}
                    </Col>
                </Row>
            </AntdContent>
        </AntdLayout>
    );
});

const usePageContent = () => {
    const { pageStore } = useStores();

    const defaultPage = {
        page: <LandingPage />,
        header: "NFT Maker"
    };

    switch (pageStore.page) {
        case Page.API_KEY_DETAIL:
            return {
                page: <ApiKeyDetail />,
                header: "NFT Maker"
            };
        case Page.LANDING:
            return defaultPage;
        case Page.HELP:
            return {
                page: <Help />,
                header: <BackToMainPage title="Need Help?" />
            };
        case Page.GET_API_KEY:
            return {
                page: <GetApiKey />,
                header: <BackToMainPage title="Get your Tatum API key" />
            };
        case Page.NFTS_MINTED:
            return {
                page: <NftsOverviewMinted />,
                header: <BackToMainPage title="NFTs Sold" />
            };
        case Page.NFTS_LAZY:
            return {
                page: <NftsOverviewLazy />,
                header: <BackToMainPage title="NFTs created" />
            };
        default:
            return defaultPage;
    }
};

const BackToMainPage = ({ title }: { title: string }) => {
    const { pageStore, apiKeyStore } = useStores();

    const nextPage = () => {
        if (apiKeyStore.apiKey) {
            pageStore.setPage(Page.API_KEY_DETAIL);
        } else {
            pageStore.setPage(Page.LANDING);
        }
    };

    return (
        <div className="back-to-landing-page-container" onClick={nextPage}>
            <LeftOutlined />
            <div className="title">{title}</div>
        </div>
    );
};
