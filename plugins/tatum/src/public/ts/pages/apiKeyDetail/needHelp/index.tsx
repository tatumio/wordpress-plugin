import { useStores } from "../../../store";
import { Page } from "../../../models";
import React from "react";
import "./index.scss";

export const NeedHelp = () => {
    const { pageStore } = useStores();
    return (
        <div className="needHelpContainer">
            <div className="needHelp" onClick={() => pageStore.setPage(Page.HELP)}>
                Need Help?
            </div>
        </div>
    );
};