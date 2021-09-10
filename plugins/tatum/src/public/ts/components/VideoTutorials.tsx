import { Button, Card } from "antd";
import React from "react";

export const VideoTutorials = () => {
    return (
        <div className="video-tutorials-container tatum-empty-body-cards">
            <Card title="Tutorials" />
            <Card title={<CardItemVideoContent />} />
        </div>
    );
};

const CardItemVideoContent = () => (
    <div className="card-item-video-content">
        <CardItemText title="blabla" description="blabal" />
        <Button title="Watch Tutorial" />
    </div>
);

const CardItemText = ({
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
