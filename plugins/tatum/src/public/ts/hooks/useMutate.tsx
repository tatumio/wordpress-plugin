import { useState } from "react";
import { request } from "../utils";
import { RouteLocationInterface, RouteParamsInterface, RouteRequestInterface } from "@tatum/utils";
import { showError } from "../utils/message";
import { Error } from "../models/error";

export const useMutate = <T extends Error>(location: RouteLocationInterface, body?: Record<string, unknown>) => {
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
            if (result?.status === "error") {
                showError(result);
            }

            setData(result);
        } catch (e) {
            showError({ message: "An error occurred. Please contact support." });
        } finally {
            setLoading(false);
        }
    };
    return { data, mutate, loading };
};
