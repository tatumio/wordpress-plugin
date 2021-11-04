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
            <ParagraphHeader>What are credits for?</ParagraphHeader>
            <Paragraph>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis.
            </Paragraph>
            <ParagraphHeader>How many NFTs can I potentionally sell per month per plan option?</ParagraphHeader>
            <Paragraph>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis.
            </Paragraph>
            {/*// @ts-ignore*/}
            <Table columns={columns} dataSource={data} pagination={false} loading={!data} rowKey="key" />
        </Card>
    );
};
