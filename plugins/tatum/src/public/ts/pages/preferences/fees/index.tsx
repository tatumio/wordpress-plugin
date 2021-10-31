import { CardGridInputItem, Container } from "../../../components";
import { Card } from "antd";
import React from "react";
import "./index.scss";

export const Fees = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Container isGridCard={true}>
            <Card title="Bill your customers transactions fees">
                <Card.Grid className="description" hoverable={false} style={gridStyle}>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                    Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                    congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                    sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis. Nunc varius eu libero in
                    semper. Nulla feugiat sem vitae neque ornare auctor. Fusce posuere faucibus aliquet. Donec quis arcu
                    tristique, faucibus erat at, consectetur massa.
                </Card.Grid>
                <CardGridInputItem title="Ethereum (ETH)" description="We recommend $30" />
            </Card>
        </Container>
    );
};
