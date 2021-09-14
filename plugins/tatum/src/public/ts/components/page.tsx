import React from "react";
import { observer } from "mobx-react";
import { Layout as AntdLayout, Row, Col } from "antd";

import { useStores } from "../store";
import { LandingPage } from "../pages/landingPage";

export const Layout = observer(() => {
    const { Header, Footer, Content: AntdContent } = AntdLayout;
    const page = usePageComponent();
    return (
        <AntdLayout className="tatum">
            <Header style={{ backgroundColor: "#fff" }}>
                <Row justify="space-around" align="middle">
                    <Col span={8}>NFT Maker</Col>
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

const usePageComponent = () => {
    const { pageStore } = useStores();
    switch (pageStore.page) {
        case "landingPage":
            return <LandingPage />;
        default:
            return <LandingPage />;
    }
};
