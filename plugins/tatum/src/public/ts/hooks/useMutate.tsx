import { useState } from "react";
import { request } from "../utils";
import { RouteLocationInterface, RouteParamsInterface, RouteRequestInterface } from "@tatum/utils";
import { message } from "antd";
import { ResponseError } from "../models/reponseError";

export const useMutate = <T extends ResponseError>(
    location: RouteLocationInterface,
    body?: Record<string, unknown>
) => {
    const [data, setData] = useState<T | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const mutate = async (mutateBody?: Record<string, unknown>) => {
        try {
            setLoading(true);
            const result = await request<RouteRequestInterface, RouteParamsInterface, T>({
                location,
                request: {
                    ...body,
                    ...mutateBody
                }
            });

            if (result?.status === "error" && result.message) {
                showError(result.message);
            }

            setData(result);
        } catch (e) {
            showError("An error occurred. Please contact support.");
        } finally {
            setLoading(false);
        }
    };
    return { data, mutate, loading };
};

const showError = (content: string) => message.error({ content, style: { marginTop: "40px" } });
