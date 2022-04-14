import { Fees } from "./fees";
import { Button, Input, Modal } from "antd";
import "./index.scss";
import { useMutate } from "../../hooks/useMutate";
import { Error } from "../../models/error";
import { RouteHttpVerb } from "@tatum/utils";
import { useForm, useFormContext, FormProvider } from "react-hook-form";
import { useGet } from "../../hooks/useGet";
import { DefaultChains } from "./defaultChains";
import { Page } from "../../models";
import { useStores } from "../../store";

export const Preferences = () => {
    const { data } = useGet<Error>("/preferences");
    const { mutate } = useMutate<Error>({ path: "/preferences", method: RouteHttpVerb.POST });
    const { pageStore } = useStores();
    const form = useForm();
    const onSubmit = (data: unknown) => {
        console.log(data);
        Modal.success({
            title: "Preferences updated.",
            onOk: () => {
                pageStore.setPage(Page.API_KEY_DETAIL);
            }
        });
    };
    return (
        <FormProvider {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)}>
                <Fees />
                <DefaultChains />
                <div className="button-container">
                    <Input className="save-button" type="submit" value="Save" />
                </div>
            </form>
        </FormProvider>
    );
};
