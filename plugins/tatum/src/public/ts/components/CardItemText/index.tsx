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
                <div className="tatum–card-title">{title}</div>
                <div className="tatum-card-description-text">{description}</div>
                <div className="tatum-card-description-text">{secondDescription}</div>
            </div>
        ) : (
            <div className="tatum-sigle–card-title">{title}</div>
        )}
    </>
);
