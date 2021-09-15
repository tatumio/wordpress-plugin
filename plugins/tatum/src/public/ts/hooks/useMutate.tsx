import { useState } from "react";
import { request } from "../utils";
import { RouteLocationInterface, RouteParamsInterface, RouteRequestInterface } from "@tatum/utils";

export const useMutate = <T,>(location: RouteLocationInterface) => {
    const [data, setData] = useState<T | null>(null);
    const mutate = async () => {
        const result = await request<RouteRequestInterface, RouteParamsInterface, T>({
            location,
            params: {
                x: "ds"
            }
        });
        setData(result);
    };
    return { data, mutate };
};
