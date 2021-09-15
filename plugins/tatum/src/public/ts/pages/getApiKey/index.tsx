import { GetTatumApiKey } from "./getTatumApiKey";
import { PasteApiKey } from "./pasteApiKey";
import { WhyApiKey } from "./whyApiKey";

export const GetApiKey = () => {
    return (
        <>
            <WhyApiKey />
            <GetTatumApiKey />
            <PasteApiKey />
        </>
    );
};
