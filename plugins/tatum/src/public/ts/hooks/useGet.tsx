import { useEffect, useState } from "react";
import { request } from "../utils";
import { RouteHttpVerb, RouteParamsInterface, RouteRequestInterface } from "@tatum/utils";

export const useGet = <T,>(path: string) => {
    const [data, setData] = useState<T | null>(null);
    useEffect(() => {
        async function fetchMyAPI() {
            const result = await request<RouteRequestInterface, RouteParamsInterface, T>({
                location: {
                    path,
                    method: RouteHttpVerb.GET
                }
            });
            setData(result);
        }
        fetchMyAPI();
    }, []);
    return { data };
};
