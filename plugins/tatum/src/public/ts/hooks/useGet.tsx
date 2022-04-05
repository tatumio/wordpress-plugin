import { useEffect, useState } from "react";
import { request } from "../utils";
import { RouteHttpVerb, RouteParamsInterface, RouteRequestInterface } from "@tatum/utils";
import { showError } from "../utils/message";
import { ResponseError } from "../models/reponseError";

export const useGet = <T extends ResponseError>(path: string) => {
    const [data, setData] = useState<T | null>(null);
    useEffect(() => {
        async function fetchMyAPI() {
            try {
                const result = await request<RouteRequestInterface, RouteParamsInterface, T>({
                    location: {
                        path,
                        method: RouteHttpVerb.GET
                    }
                });

                if (result?.status === "error" && result.message) {
                    showError(result);
                }

                setData(result);
            } catch (e) {
                showError({ message: "An error occurred. Please contact support." });
            }
        }

        fetchMyAPI();
    }, []);
    return { data };
};
