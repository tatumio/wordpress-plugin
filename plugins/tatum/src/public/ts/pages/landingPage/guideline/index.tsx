import React, { FC } from "react";
import { observer } from "mobx-react";
import { Avatar, Card } from "antd";
import { CheckCircleFilled } from "@ant-design/icons";
import { useStores } from "../../../store";
import { Page } from "../../../models/page";
import { Container, CardItemText, Spinner } from "../../../components";
import "./index.scss";
import { useGet } from "../../../hooks/useGet";
import { Error } from "../../../models/error";

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
            <CardItemText title={title} description={description} secondDescription={durationText} />
        </div>
    );
};

const SpinnerCard = () => {
    return <Card style={{ display: "flex", justifyContent: "center" }} title={<Spinner />} />;
};

const CardsContent = ({ data }: { data: SetupInterface }) => {
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
    const { data } = useGet<SetupInterface>("/setup");

    return (
        <Container isGridCard={true}>
            <Card title="Complete these tasks to start selling your products as NFTs">
                {data ? <CardsContent data={data} /> : <SpinnerCard />}
            </Card>
        </Container>
    );
});

interface SetupInterface extends Error {
    isWoocommerceInstalled?: boolean;
}

export { Guideline };
