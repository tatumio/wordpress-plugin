import { Spin } from "antd";
import React from "react";
import "./index.scss";

export const Spinner = () => {
    return (
        <div className="spinner-container">
            <Spin />
        </div>
    );
};
