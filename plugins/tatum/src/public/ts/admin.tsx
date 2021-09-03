/* istanbul ignore file: we do not need to care about the entry point file as errors are detected through integration tests (E2E) */

/**
 * The entry point for the admin side wp-admin resource.
 */
import "@tatum/utils"; // Import once for startup polyfilling (e. g. setimmediate)
import { render } from "react-dom";
import { RootStore } from "./store";
import "./style/admin.scss";
import { LandingPage } from "./pages/landingPage";

const node = document.getElementById(`${RootStore.get.optionStore.slug}-component`);

if (node) {
    render(
        <RootStore.StoreProvider>
            <LandingPage />
        </RootStore.StoreProvider>,
        node
    );
}

// Expose this functionalities to add-ons, but you need to activate the library functionality
// in your webpack configuration, see also https://webpack.js.org/guides/author-libraries/
export * from "@tatum/utils";
export * from "./wp-api";
export * from "./store";
