import { Fees } from "./fees";
import { Button, Input } from "antd";
import "./index.scss";
import { useMutate } from "../../hooks/useMutate";
import { ResponseError } from "../../models/reponseError";
import { RouteHttpVerb } from "@tatum/utils";
import { useForm } from "react-hook-form";
import { FormProvider } from "antd/es/form/context";

export const Preferences = () => {
    const { mutate } = useMutate<ResponseError>({ path: "/fees", method: RouteHttpVerb.POST });
    const methods = useForm();

    return (
        <FormProvider {...methods}>
                <form onSubmit={methods.handleSubmit(mutate)}>
                <Fees />
                <div className="button-container">
                    <Input className="save-button" type="submit" value="Save" />
                </div>
            </form>
        </FormProvider>
    );
};
