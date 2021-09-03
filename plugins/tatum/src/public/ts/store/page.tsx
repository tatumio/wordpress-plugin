import { observable, action } from "mobx";
import { TodoModel } from "../models";
import { RootStore } from "./stores";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
class PageStore {
    @observable
    public page = "landingPage";

    public readonly rootStore: RootStore;

    constructor(rootStore: RootStore) {
        this.rootStore = rootStore;
    }

    @action
    public setPage(page: string) {
        this.page = page;
    }
}

export { PageStore };
