import { message } from "antd";
import { Error } from "../models/error";
import { ReactNode } from "react";

export const showError = (error: Error) => message.error({ content: getErrorMsg(error), duration: 5 });
export const showSuccess = (content: string) => message.success({ content });

type ErrorMessagesType = {
    [key: string]: string | ReactNode;
};

const getErrorMsg = (error: Error) => {
    const errorMessages: ErrorMessagesType = {
        "tatum.not.active.api.key": (
            <div>
                If you want to work with the Mainnet network you need to have paid API key. If you dont have one, you
                can buy paid subscription at{" "}
                <a href="https://dashboard.tatum.io" target="_blank" rel="noreferrer">
                    Tatum dashboard
                </a>
                .
            </div>
        )
    };

    const message = errorMessages[error.errorCode];
    return message ?? error.message;
};
