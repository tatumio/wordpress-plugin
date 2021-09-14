import React, { FC, useEffect, useState } from "react";
import { observer } from "mobx-react";
import { Card, Avatar, Spin } from "antd";
import { CheckCircleFilled } from "@ant-design/icons";
import {
    locationRestSetupGet,
    ParamsRouteSetupGet,
    RequestRouteSetupGet,
    ResponseRouteSetupGet
} from "../wp-api/setup.get";
import { request } from "../utils";

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
}) => (
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

const SpinnerCard = () => {
    return <Card style={{ display: "flex", justifyContent: "center" }} title={<Spin />} />;
};

const Guideline: FC<{}> = observer(() => {
    const { data } = useSetup();
    return (
        <div className="tatum-empty-body-cards">
            <Card title="Complete these tasks to start selling your products as NFTs" style={{ width: "100%" }} />
            {!data ? (
                <SpinnerCard />
            ) : (
                <>
                    <CardItem title="Woocommerce plugin installed" done={data.isWoocommerceInstalled} />
                    <CardItem
                        title="Get your Tatum API key"
                        done={false}
                        description="Choose your API plan and start minting NFTs"
                        durationText="3 minutes"
                    />
                </>
            )}
        </div>
    );
});

const useSetup = () => {
    const [setup, setSetup] = useState<ResponseRouteSetupGet | null>(null);
    useEffect(() => {
        async function fetchMyAPI() {
            const result = await request<RequestRouteSetupGet, ParamsRouteSetupGet, ResponseRouteSetupGet>({
                location: locationRestSetupGet
            });
            setSetup(result);
        }
        fetchMyAPI();
    }, []);
    return { data: setup };
};

export { Guideline };
