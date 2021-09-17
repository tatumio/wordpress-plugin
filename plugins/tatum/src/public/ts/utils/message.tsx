import { message } from "antd";

export const showError = (content: string) => message.error({ content });
export const showSuccess = (content: string) => message.success({ content });
