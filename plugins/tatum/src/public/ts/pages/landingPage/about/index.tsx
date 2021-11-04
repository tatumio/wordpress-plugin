import { Card } from "antd";
import { Container } from "../../../components/container";
import { getImageUrl } from "../../../utils/image";
import React from "react";
import "./index.scss";

export const About = () => (
    <Container>
        <Card>
            <Title />
        </Card>
    </Container>
);

const Title = () => {
    return (
        <div className="title-container">
            <img className="header-overview" src={getImageUrl("header-overview.png")} />
            <div className="title">
                NFT maker <span className="title-tatum">by Tatum.</span>
            </div>
            <div className="title-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ac dui arcu. Sed sed neque libero.
                Aliquam consequat urna non bibendum pellentesque. Proin vitae turpis eleifend, placerat justo vitae,
                congue lectus. Morbi molestie mi convallis condimentum convallis. Aliquam nec fermentum enim. Donec
                sagittis lacus efficitur massa fringilla, in porttitor lorem iaculis. Nunc varius eu libero in semper.
                Nulla feugiat sem vitae neque ornare auctor. Fusce posuere faucibus aliquet. Donec quis arcu tristique,
                faucibus erat at, consectetur massa.
            </div>
        </div>
    );
};
