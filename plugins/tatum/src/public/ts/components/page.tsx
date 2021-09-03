import React, { Component, FC } from "react";
import { observer } from "mobx-react";
import { useStores } from "../store";
import { Layout, Row, Col } from "antd";
import "antd/dist/antd.css";
import { Guideline } from "./guideline";

const ComponentLibrary: FC<{}> = observer(() => {
    const { optionStore } = useStores();
    const { Header, Footer, Content } = Layout;
    return (
        <Layout>
            <Header style={{ backgroundColor: "#fff" }}>
                <Row justify="space-around" align="middle">
                    <Col span={8}>NFT Maker</Col>
                    <Col span={8} offset={8} style={{ display: "flex", justifyContent: "flex-end" }}>
                        <div>A plugin by Tatum</div>
                    </Col>
                </Row>
            </Header>
            <Content style={{ backgroundColor: "#f0f2f5" }}>
                <Row style={{ marginTop: "40px" }}>
                    <Col
                        span={12}
                        offset={6}
                        style={{ display: "flex", justifyContent: "center", flexDirection: "column" }}
                    >
                        <Guideline />
                    </Col>
                </Row>
            </Content>
            <Footer>Footer</Footer>
        </Layout>
    );
});
export { ComponentLibrary };
