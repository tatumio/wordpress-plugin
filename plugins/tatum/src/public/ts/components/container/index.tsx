import "./index.scss";

export const Container = ({ children, isGridCard = false }: { children: React.ReactNode; isGridCard?: boolean }) => {
    const className = `container ${isGridCard ? "gridTable" : ""}`;
    return <div className={className}>{children}</div>;
};
