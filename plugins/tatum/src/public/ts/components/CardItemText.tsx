import React from "react";

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
            <div className="tatum-card-item-text-container">
                <div>{title}</div>
                <div className="tatum-card-description-text">{description}</div>
                <div className="tatum-card-description-text">{secondDescription}</div>
            </div>
        ) : (
            <div>{title}</div>
        )}
    </>
);
