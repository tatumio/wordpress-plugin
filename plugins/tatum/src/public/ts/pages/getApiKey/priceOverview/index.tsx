import { Card, Table } from "antd";
import "./index.scss";
import { useGet } from "../../../hooks/useGet";
import BSC from "../../../assets/BSC.svg";
import CELO from "../../../assets/CELO.svg";
import ETH from "../../../assets/ETH.svg";
import MATIC from "../../../assets/MATIC.svg";
import ONE from "../../../assets/ONE.svg";
import { Paragraph, ParagraphHeader } from "../../../components";
import { Estimates } from "../../../models/estimates";

export const PriceOverview = () => {
    const chains: Record<string, string> = {
        BSC,
        CELO,
        ETH,
        MATIC,
        ONE
    };

    const columns = [
        {
            dataIndex: "chain",
            key: "chain",
            render: (chain: string) => {
                const label = data.estimates.find((estimate) => estimate.chain === chain)?.label;
                return (
                    <>
                        <img src={chains[chain]} className="chainImage" /> {label}
                    </>
                );
            }
        },
        {
            title: "Starter",
            dataIndex: "starter",
            key: "starter",
            align: "center"
        },
        {
            title: "Basic",
            dataIndex: "basic",
            key: "basic",
            align: "center"
        },
        {
            title: "Advanced",
            dataIndex: "advanced",
            key: "advanced",
            align: "center"
        }
    ];

    const { data } = useGet<Estimates>("/estimate");

    return (
        <Card title="What plan do I need?">
            <Paragraph>
                Your monthly plan is used to pay for the gas fees necessary to mint NFTs. Some blockchains have higher
                gas fees, some have lower, so the number of NFTs you can mint per month will depend on the blockchain
                you are minting on. Check at the table below and see how many NFTs you can potentially mint, considering
                the current blockchain transaction fees.
            </Paragraph>
            {/*// @ts-ignore*/}
            <Table columns={columns} dataSource={data?.estimates} pagination={false} loading={!data} rowKey="key" />
        </Card>
    );
};
