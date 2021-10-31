import { Button, Card, Input } from "antd";
import { CardItemText } from "../CardItemText";
import React from "react";
import "./index.scss";
import { useFormContext } from "react-hook-form";

export const CardGridInputItem = ({
    hoverable = false,
    title,
    description,
    inputValue
}: {
    hoverable?: boolean;
    title: string;
    description?: string;
    inputValue?: string;
}) => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    const { register } = useFormContext();
    return (
        <Card.Grid hoverable={hoverable} style={gridStyle}>
            <div className="card-item-grid-content grid-table">
                <CardItemText title={title} description={description} />
                <Input {...register("ETH")} className="fee-input" addonBefore="$" defaultValue={inputValue} />
            </div>
        </Card.Grid>
    );
};
