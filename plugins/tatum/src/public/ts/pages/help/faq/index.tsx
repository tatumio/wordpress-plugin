import { Card, Table } from "antd";
import { Container, Paragraph, ParagraphHeader } from "../../../components";

export const Faq = () => {
    const dataSource = [
        {
            key: "1",
            chain: "MATIC",
            network: "Testnet",
            smartContractAddress: "0xCd2AdA00c48A27FAa5Cc67F9A1ed55B89dDf7F77"
        },
        {
            key: "2",
            chain: "BSC",
            network: "Testnet",
            smartContractAddress: "0xF73075aa67561791352fbEe8278115487Fd90ab6"
        },
        {
            key: "3",
            chain: "ONE",
            network: "Testnet",
            smartContractAddress: "0x427ddbe3ad5e1e77e010c02e61e9bdef82dcaeea"
        },
        {
            key: "4",
            chain: "ETH",
            network: "Testnet",
            smartContractAddress: "0xAe7D8842D0295B1f24a8842cBd5eB83Ae2fd0946"
        },
        {
            key: "5",
            chain: "CELO",
            network: "Testnet",
            smartContractAddress: "0x45871ED5F15203C0ce791eFE5f4B5044833aE10e"
        },
        {
            key: "6",
            chain: "MATIC",
            network: "Mainnet",
            smartContractAddress: "0x03582C4C2cc7fC8dEd9377A3f8e94a4C9f72ecCe"
        },
        {
            key: "7",
            chain: "BSC",
            network: "Mainnet",
            smartContractAddress: "0x4f83793245abE92cc8B978a16C898005c69e5e27"
        },
        {
            key: "8",
            chain: "ONE",
            network: "Mainnet",
            smartContractAddress: "0x559f11123bb892159cd33f652624e40e8b43d4ad"
        },
        {
            key: "9",
            chain: "ETH",
            network: "Mainnet",
            smartContractAddress: "0x789c00ed7ddd72a806dbac40df926df32fde3c2f"
        },
        {
            key: "10",
            chain: "CELO",
            network: "Mainnet",
            smartContractAddress: "0x5F35fd593243B059cBf580D0335B1c21881a248b"
        }
    ];

    const columns = [
        {
            title: "Chain",
            dataIndex: "chain",
            key: "chain"
        },
        {
            title: "Mainnet/Testnet",
            dataIndex: "network",
            key: "network"
        },
        {
            title: "Smart Contract Address",
            dataIndex: "smartContractAddress",
            key: "smartContractAddress"
        }
    ];
    return (
        <Container>
            <Card title="FAQ">
                <ParagraphHeader>Why do I need an API key?</ParagraphHeader>
                <Paragraph>
                    Normally, to create NFTs on different blockchains, you would need to have access to blockchain nodes
                    and create wallets on each blockchain. With NFT Maker, you can simply use an API key to connect to
                    different blockchains through Tatum, and everything else is taken care of for you.
                </Paragraph>
                <ParagraphHeader>
                    Do I need to generate a blockchain wallet on each blockchain I want to mint NFTs on?
                </ParagraphHeader>
                <Paragraph>
                    No, since NFTs are lazy-minted, you do not need to create wallets on any blockchain. Once an NFT is
                    purchased, it is minted directly to the buyer’s blockchain address.
                </Paragraph>
                <ParagraphHeader>
                    Do I have to choose which blockchain I will mint all of my NFTs on during setup?
                </ParagraphHeader>
                <Paragraph>
                    No, you can choose which NFT to mint on which blockchain when you are setting up each product. You
                    can select one blockchain per product, but you can also create multiple NFTs of the same image for
                    multiple blockchains if you so choose.
                </Paragraph>
                <ParagraphHeader>Is the plugin free?</ParagraphHeader>
                <Paragraph>
                    Yes, the NFT Maker plugin itself is completely free. However, in order to pay for the gas fees
                    necessary to mint NFTs, you must buy a paid plan in the{" "}
                    <a href="https://dashboard.tatum.io/" target="_blank" rel="noreferrer">
                        Tatum Dashboard
                    </a>
                    . Each paid plan has different credit amounts, and credits will be consumed when your NFTs are
                    purchased and minted to the blockchain, based on the current gas fees of the given blockchain. You
                    can try NFT Maker with Test API keys for free, but NFTs minted with Test API keys will not have any
                    value, because they are minted on Testnet network.
                </Paragraph>
                <ParagraphHeader>
                    If I don’t use all my credits within a month, do they carry over to the next month?
                </ParagraphHeader>
                <Paragraph>At the moment, no, unused credits do not carry over to the following month.</Paragraph>
                <ParagraphHeader>Can my users sell their NFTs with NFT Maker?</ParagraphHeader>
                <Paragraph>
                    NFT Maker can only be used to sell NFTs created by admins to customers. To create NFT marketplaces
                    that allow users to sell their own NFTs, please refer to Tatum’s{" "}
                    <a
                        target="_blank"
                        rel="noreferrer"
                        href="https://docs.tatum.io/tutorials/how-to-create-a-peer-to-peer-nft-marketplace"
                    >
                        How to build p2p NFT marketplaces guide.
                    </a>
                </Paragraph>
                <ParagraphHeader>Can I sell my existing NFTs on my e-shop using NFT Maker?</ParagraphHeader>
                <Paragraph>
                    No, again, NFT Maker can only be used to sell NFTs created with the plugin. For a complete guide on
                    how to build the backend to an NFT marketplace from scratch, please refer to Tatum’s{" "}
                    <a
                        href="https://blog.tatum.io/how-to-build-nft-marketplaces-part-2-backend-899f7d804066"
                        target="_blank"
                        rel="noreferrer"
                    >
                        How to build NFT marketplaces part 2 - Backend guide.{" "}
                    </a>
                </Paragraph>
                <ParagraphHeader>Which smart contract are used for minting NFTs?</ParagraphHeader>
                <Paragraph>
                    We are using following smart contracts:
                    <Table dataSource={dataSource} columns={columns} pagination={false} />
                </Paragraph>
                Need more support, ask us on our{" "}
                <a target="_blank" rel="noreferrer" href="https://discord.gg/7ZKCRD5bG3">
                    Discord Server.
                </a>
            </Card>
        </Container>
    );
};
