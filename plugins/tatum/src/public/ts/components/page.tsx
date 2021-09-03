import React from "react";
import { observer } from "mobx-react";
import { Layout as AntdLayout, Row, Col } from "antd";
import "antd/dist/antd.css";

type Props = {
    children?: React.ReactNode;
};

export const Layout = observer(({ children }: Props) => {
    const { Header, Footer, Content: AntdContent } = AntdLayout;
    return (
        <AntdLayout>
            <Header style={{ backgroundColor: "#fff" }}>
                <Row justify="space-around" align="middle">
                    <Col span={8}>NFT Maker</Col>
                    <Col span={8} offset={8} style={{ display: "flex", justifyContent: "flex-end" }}>
                        <div>A plugin by Tatum</div>
                    </Col>
                </Row>
            </Header>
            <AntdContent style={{ backgroundColor: "#f0f2f5" }}>
                <Row style={{ marginTop: "40px" }}>
                    <Col
                        span={12}
                        offset={6}
                        style={{ display: "flex", justifyContent: "center", flexDirection: "column" }}
                    >
                        {children}
                    </Col>
                </Row>
            </AntdContent>
            <Footer>Footer</Footer>
        </AntdLayout>
    );
});
