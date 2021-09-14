import { WhyDoINeedApiKey } from "../components/WhyDoINeedApiKey";
import { GetYourApiKey } from "../components/GetYourApiKey";
import { PasteApiKey } from "../components/PasteApiKey";

export const GetApiKey = () => {
    return (
        <>
            <WhyDoINeedApiKey />
            <GetYourApiKey />
            <PasteApiKey />
        </>
    );
};
