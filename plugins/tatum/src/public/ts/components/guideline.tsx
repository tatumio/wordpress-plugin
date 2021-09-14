import React, { FC, useEffect, useState } from "react";
import { observer } from "mobx-react";
import { Avatar, Card, Spin } from "antd";
import { CheckCircleFilled } from "@ant-design/icons";
import {
    locationRestSetupGet,
    ParamsRouteSetupGet,
    RequestRouteSetupGet,
    ResponseRouteSetupGet
} from "../wp-api/setup.get";
import { request } from "../utils";
import { useStores } from "../store";
import { Page } from "../models/page";
import { CardItemText } from "./CardItemText";

const CardItem = ({
    title,
    done,
    description,
    durationText,
    onClick
}: {
    title: string;
    done: boolean;
    description?: string;
    durationText?: string;
    onClick?: () => void;
}) => {
    return (
        <div className="tatum-card-item" onClick={onClick}>
            {done ? (
                <CheckCircleFilled style={{ color: "#76B947", fontSize: "40px", marginRight: "10px" }} />
            ) : (
                <Avatar style={{ height: "40px", width: "40px", marginRight: "10px" }} />
            )}
            <CardItemText title={title} description={description} durationText={durationText} />
        </div>
    );
};

const SpinnerCard = () => {
    return <Card style={{ display: "flex", justifyContent: "center" }} title={<Spin />} />;
};

const CardsContent = ({ data }: { data: ResponseRouteSetupGet }) => {
    const { pageStore } = useStores();
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <>
            <Card.Grid hoverable={false} style={gridStyle}>
                <CardItem title="Woocommerce plugin installed" done={data.isWoocommerceInstalled} />
            </Card.Grid>
            <Card.Grid hoverable={true} style={gridStyle}>
                <CardItem
                    title="Get your Tatum API key"
                    done={false}
                    description="Choose your API plan and start minting NFTs"
                    durationText="3 minutes"
                    onClick={() => pageStore.setPage(Page.GET_API_KEY)}
                />
            </Card.Grid>
        </>
    );
};

const Guideline: FC<{}> = observer(() => {
    const { data } = useSetup();

    return (
        <Card title="Complete these tasks to start selling your products as NFTs" className="grid-table">
            {data ? <CardsContent data={data} /> : <SpinnerCard />}
        </Card>
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
