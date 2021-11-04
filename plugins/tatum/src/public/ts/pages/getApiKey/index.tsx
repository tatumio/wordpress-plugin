import { GetTatumApiKey } from "./getTatumApiKey";
import { PasteApiKey } from "./pasteApiKey";
import { WhyApiKey } from "./whyApiKey";
import { PriceOverview } from "./priceOverview";
import { MoreTatum } from "./moreTatum";

export const GetApiKey = () => {
    return (
        <>
            <WhyApiKey />
            <GetTatumApiKey />
            <PasteApiKey />
            <PriceOverview />
            <MoreTatum />
        </>
    );
};
