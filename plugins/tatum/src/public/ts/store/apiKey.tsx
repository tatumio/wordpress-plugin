import { action, observable } from "mobx";
import { RootStore } from "./stores";
import { ApiKey } from "../models";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
class ApiKeyStore {
    @observable
    public apiKey: ApiKey;

    public readonly rootStore: RootStore;

    constructor(rootStore: RootStore) {
        this.rootStore = rootStore;
    }

    @action
    public setApiKey(apiKey: ApiKey) {
        this.apiKey = apiKey;
    }
}

export { ApiKeyStore };
