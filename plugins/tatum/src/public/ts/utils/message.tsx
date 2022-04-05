import { message } from "antd";
import { Error } from "../models/error";

export const showError = (error: Error) => message.error({ content: getErrorMsg(error) });
export const showSuccess = (content: string) => message.success({ content });

const errorMessages: ErrorMessagesType = {
    "tatum.not.active.api.key":
        "You API key must be active and not expired. If you dont have one, you can buy paid subscription at <a href='https://dashboard.tatum.io' target='_blank' rel='noreferrer'>Tatum dashboard</a>."
};

type ErrorMessagesType = {
    [key: string]: string;
};

const getErrorMsg = (error: Error) => {
    const message = errorMessages[error.code];
    return message ?? error.message;
};
