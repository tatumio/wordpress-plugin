import { Card, Table } from "antd";
import "./index.scss";
import { useGet } from "../../../hooks/useGet";
import BSC from "../../../assets/BSC.svg";
import CELO from "../../../assets/CELO.svg";
import ETH from "../../../assets/ETH.svg";
import MATIC from "../../../assets/MATIC.svg";
import ONE from "../../../assets/ONE.svg";
import { Paragraph, ParagraphHeader } from "../../../components";

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
                return (
                    <>
                        <img src={chains[chain]} className="chainImage" /> {chain}
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

    const { data } = useGet("/estimate");

    return (
        <Card title="What plan do I need?">
            <Paragraph>
                All Tatum API plans carry the same functionalities - the only differences between them are the credits
                and the amount of requests per second they can handle. The more you scale, the higher the plan you will
                need to use. Paying for a more premium plan allows you to handle more users and traffic.
            </Paragraph>
            <Paragraph>
                Check at the table below and see how many NFTs you can potentially mint, considering the current
                blockchain transaction fees.
            </Paragraph>
            {/*// @ts-ignore*/}
            <Table columns={columns} dataSource={data} pagination={false} loading={!data} rowKey="key" />
        </Card>
    );
};
