import { CardGridCheckboxInput, CardGridNumberInput, Container } from "../../../components";
import { Card } from "antd";
import React from "react";

export const DefaultChains = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Container isGridCard={true}>
            <Card title="Default chains">
                <Card.Grid className="description" hoverable={false} style={gridStyle}>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                    Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                    congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                    sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis. Nunc varius eu libero in
                    semper. Nulla feugiat sem vitae neque ornare auctor. Fusce posuere faucibus aliquet. Donec quis arcu
                    tristique, faucibus erat at, consectetur massa.
                </Card.Grid>
                <CardGridCheckboxInput title="Ethereum (ETH)" description="Mint" />
                <CardGridCheckboxInput title="Binance smart chain (BSC)" description="Mint" />
                <CardGridCheckboxInput title="Celo ()" description="Mint" />
                <CardGridCheckboxInput title="Polygon (Matic)" description="Mint" />
                <CardGridCheckboxInput title="One (ETH)" description="Mint" />
            </Card>
        </Container>
    );
}