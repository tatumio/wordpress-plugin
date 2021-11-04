import { Card } from "antd";
import "./index.scss";
import { Container } from "../../../components";
import { getImageUrl } from "../../../utils/image";
import React from "react";

export const WhyApiKey = () => {
    return (
        <Container>
            <Card title={<img className="header-logo" src={getImageUrl("tatum-logo.svg")} />}>
                <div className="whyApiKey">Why do I need an API key to sell NFTs?</div>
                <div>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                    Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                    congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                    sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis. Nunc varius eu libero in
                    semper. Nulla feugiat sem vitae neque ornare auctor. Fusce posuere faucibus aliquet. Donec quis arcu
                    tristique, faucibus erat at, consectetur massa.
                </div>
            </Card>
        </Container>
    );
};
