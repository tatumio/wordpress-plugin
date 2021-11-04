import { Button, Card } from "antd";
import { CardItemText } from "../CardItemText";
import React from "react";
import "./index.scss";
import { RightCircleOutlined } from "@ant-design/icons";

export const CardGridItem = ({
    hoverable = false,
    title,
    description,
    secondDescription,
    buttonText,
    buttonType,
    buttonLink,
    onClick,
    buttonDisabled
}: {
    hoverable?: boolean;
    title: string;
    description?: string;
    secondDescription?: string;
    buttonText?: string;
    buttonType?: "link" | "text" | "ghost" | "default" | "primary" | "dashed";
    buttonLink?: string;
    onClick?: () => void;
    buttonDisabled?: boolean;
}) => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Card.Grid hoverable={hoverable} style={gridStyle}>
            <div className="card-item-grid-content grid-table" onClick={onClick}>
                <CardItemText title={title} description={description} secondDescription={secondDescription} />
                {buttonText && (
                    <Button type={buttonType} disabled={buttonDisabled}>
                        <a href={buttonLink} target="_blank" rel="noreferrer">
                            {buttonText}
                        </a>
                    </Button>
                )}
                {hoverable && <RightCircleOutlined style={{ fontSize: "30px", color: "#9a9a9a" }} />}
            </div>
        </Card.Grid>
    );
};
