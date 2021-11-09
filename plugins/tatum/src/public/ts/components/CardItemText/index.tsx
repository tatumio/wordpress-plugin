import React from "react";
import "./index.scss";

export const CardItemText = ({
    title,
    description,
    secondDescription
}: {
    title: string;
    description?: string;
    secondDescription?: string;
}) => (
    <>
        {description ? (
            <div>
                <div>{title}</div>
                <div className="tatum-card-description-text">{description}</div>
                <div className="tatum-card-description-text">{secondDescription}</div>
            </div>
        ) : (
            <div>{title}</div>
        )}
    </>
);
