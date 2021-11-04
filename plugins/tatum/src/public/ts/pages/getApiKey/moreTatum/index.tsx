import { Container } from "../../../components";
import { Card } from "antd";
import "./index.scss";

export const MoreTatum = () => {
    const gridStyle = {
        width: "100%",
        align: "center"
    };
    return (
        <Container isGridCard={true}>
            <Card title="More about Tatum">
                <Card.Grid hoverable={false} style={gridStyle} className="more-tatum-container">
                    <div>
                        Leverage Tatum’s infrastructure, SDK, and powerful unified API to build blockchain apps quickly
                        and
                        easily. You don't need to worry about blockchain node configuration or maintenance, and you
                        don't
                        need to learn to code for individual blockchains. Code once, and deploy to any blockchain.
                    </div>
                    <div className="social-links">Social Links</div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://discord.gg/7ZKCRD5bG3">Discord</a>
                    </div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://www.reddit.com/r/tatum_io/">Reddit</a>
                    </div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://t.me/tatumio">Telegram</a>
                    </div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://twitter.com/tatum_io">Twitter</a>
                    </div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://www.youtube.com/channel/UCF-OAfXNJ9h3U2ycHE1NGNw">Youtube</a>
                    </div>
                    <div>
                        <a target="_blank" rel="noreferrer" href="https://www.linkedin.com/company/tatumio/">LinkedIn</a>
                    </div>
                </Card.Grid>
            </Card>

        </Container>
    );
};
