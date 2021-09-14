import React from "react";

export const CardItemText = ({
    title,
    description,
    durationText
}: {
    title: string;
    description?: string;
    durationText?: string;
}) => (
    <>
        {description ? (
            <div className="tatum-card-item-text-container">
                <div>{title}</div>
                <div className="tatum-card-description-text">{description}</div>
                <div className="tatum-card-description-text">{durationText}</div>
            </div>
        ) : (
            <div>{title}</div>
        )}
    </>
);
