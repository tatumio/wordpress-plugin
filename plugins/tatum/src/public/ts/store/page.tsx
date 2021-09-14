import { action, observable } from "mobx";
import { RootStore } from "./stores";
import { Page } from "../models/page";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
class PageStore {
    @observable
    public page: Page = Page.LANDING;

    public readonly rootStore: RootStore;

    constructor(rootStore: RootStore) {
        this.rootStore = rootStore;
    }

    @action
    public setPage(page: Page) {
        this.page = page;
    }
}

export { PageStore };
