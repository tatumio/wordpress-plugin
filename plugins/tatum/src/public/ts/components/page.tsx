import React from "react";
import { observer } from "mobx-react";
import { Col, Layout as AntdLayout, Row } from "antd";

import { useStores } from "../store";
import { LandingPage } from "../pages/landingPage";
import { GetApiKey } from "../pages/getApiKey";
import { Page } from "../models/page";
import { LeftOutlined } from "@ant-design/icons";

export const Layout = observer(() => {
    const { Header, Footer, Content: AntdContent } = AntdLayout;
    const { page, header } = usePageContent();
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
                <Row style={{ marginTop: "40px" }}>
                    <Col
                        span={12}
                        offset={6}
                        style={{ display: "flex", justifyContent: "center", flexDirection: "column" }}
                    >
                        {page}
                    </Col>
                </Row>
            </AntdContent>
            <Footer>Footer</Footer>
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
        case Page.LANDING:
            return defaultPage;
        case Page.GET_API_KEY:
            return {
                page: <GetApiKey />,
                header: <BackToLadingPage title="Get your Tatum API key" />
            };
        default:
            return defaultPage;
    }
};

const BackToLadingPage = ({ title }: { title: string }) => {
    const { pageStore } = useStores();

    return (
        <div className="back-to-landing-page-container" onClick={() => pageStore.setPage(Page.LANDING)}>
            <LeftOutlined />
            <div className="title">{title}</div>
        </div>
    );
};
