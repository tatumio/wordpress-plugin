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
            <AntdContent>
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
                            <>
                                {page}
                                <NeedHelp />
                            </>
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
        case Page.GET_API_KEY:
            return {
                page: <GetApiKey />,
                header: <BackToLandingPage title="Get your Tatum API key" />
            };
        default:
            return defaultPage;
    }
};

const BackToLandingPage = ({ title }: { title: string }) => {
    const { pageStore } = useStores();

    return (
        <div className="back-to-landing-page-container" onClick={() => pageStore.setPage(Page.LANDING)}>
            <LeftOutlined />
            <div className="title">{title}</div>
        </div>
    );
};

const NeedHelp = () => {
    return (
        <div className="needHelpContainer">
            <div className="needHelp">Need Help?</div>
        </div>
    );
};
