import React, { FC } from "react";
import { observer } from "mobx-react";
import { Card, Avatar } from "antd";
import { CheckCircleFilled } from "@ant-design/icons";

const CardItem = ({
    title,
    done,
    description,
    durationText
}: {
    title: string;
    done: boolean;
    description?: string;
    durationText?: string;
}) => {
    const Content = () => (
        <div className="tatum-card-item">
            {done ? (
                <CheckCircleFilled style={{ color: "#76B947", fontSize: "40px", marginRight: "10px" }} />
            ) : (
                <Avatar style={{ height: "40px", width: "40px", marginRight: "10px" }} />
            )}
            <CardItemText title={title} description={description} durationText={durationText} />
        </div>
    );

    return <Card title={<Content />} style={{ width: "100%" }} />;
};

const CardItemText = ({
    title,
    description,
    durationText
}: {
    title: string;
    description?: string;
    durationText?: string;
}) => {
    console.log(description);
    return (
        <>
            {description ? (
                <div className="tatum-card-item-text-container">
                    <div>{title}</div>
                    <div className="tatum-card-description-text">{description}</div>
                    <div className="tatum-card-description-text">{durationText}</div>
                </div>
            ) : (
                <div>{title}</div>
            )}
        </>
    );
};

const Guideline: FC<{}> = observer(() => {
    return (
        <>
            <Card title="Complete these tasks to start selling your products as NFTs" style={{ width: "100%" }} />
            <CardItem title="Woocommerce plugin installed" done={true} />
            <CardItem
                title="Complete these tasks to start selling your products as NFTs"
                done={false}
                description="Choose your API plan and start minting NFTs"
                durationText="3 minutes"
            />
        </>
    );
});

export { Guideline };
