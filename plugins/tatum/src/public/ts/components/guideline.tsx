import React, { FC } from "react";
import { observer } from "mobx-react";
import { Card, Avatar } from "antd";
import { CheckOutlined, CheckCircleOutlined, CheckCircleTwoTone } from "@ant-design/icons";

const WoocommercePluginInstalled = () => {
    return (
        <>
            <div>Woocommerce plugin installed</div>
            <Avatar icon={<CheckOutlined />} />
            <CheckCircleOutlined />
            <CheckCircleTwoTone twoToneColor="#5CFF5C" style={{ fontSize: "40px" }} />
        </>
    );
};

const Guideline: FC<{}> = observer(() => {
    return (
        <>
            <Card title="Complete these tasks to start selling your products as NFTs" style={{ width: "100%" }} />
            <Card title={<WoocommercePluginInstalled />} style={{ width: "100%" }} />
            <Card title="Complete these tasks to start selling your products as NFTs" style={{ width: "100%" }} />
        </>
    );
});

export { Guideline };
