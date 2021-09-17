import { ReactNode } from "react";
import "./index.scss";

export const ParagraphHeader = ({ children }: { children: ReactNode }) => (
    <div className="paragraphHeader">{children}</div>
);
