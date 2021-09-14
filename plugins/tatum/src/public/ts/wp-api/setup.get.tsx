import {
    RouteLocationInterface,
    RouteHttpVerb,
    RouteResponseInterface,
    RouteRequestInterface,
    RouteParamsInterface
} from "@tatum/utils";

export const locationRestSetupGet: RouteLocationInterface = {
    path: "/setup",
    method: RouteHttpVerb.GET
};

export type RequestRouteSetupGet = RouteRequestInterface;

export type ParamsRouteSetupGet = RouteParamsInterface;

export interface ResponseRouteSetupGet extends RouteResponseInterface {
    isWoocommerceInstalled: boolean;
}
