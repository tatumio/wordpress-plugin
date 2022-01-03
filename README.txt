=== NFT Maker ===
Contributors: lukaskotol
Donate link: https://tatum.io/
Tags: tatum, blockchain, nft, erc721, ethereum, celo, bsc, mint
Requires at least: 5.5
Requires PHP: 7.0
Tested up to: 5.8.2
Stable tag: 2.0.15
License: MIT
License URI: https://opensource.org/licenses/MIT

[NFT Maker](https://tatum.io/) is the easiest and fastest free plugin to work with NFTs without any blockchain development experience.

== Description ==
If you want to sell NFTs but don’t want to build an entire NFT marketplace from scratch, then NFT Maker is the plugin you’ve been waiting for.

Lazy Minting. Free IPFS Storage, forever. Supports Ethereum, Polygon, Binance Smart Chain, Celo, and Harmony.

NFT Maker by Tatum allows you to turn your Woocommerce store into an NFT store with a simple plugin. Install our plugin, follow your usual WordPress product publishing flow, and just tick which blockchain you’d like to mint your NFTs on.

You are welcome to add your pull request to our Github [repository](https://github.com/tatumio/woocommerce-plugin). If you have any questions just drop us a line on the Tatum [Discord](https://discord.gg/Mg2vdtD4JQ).
Or feel free to contact developer via [Telegram](https://t.me/LukasKotol).

== Installation ==

This section describes how to install the Tatum plugin and get it working.

1. Download and install the [Woocommerce plugin](https://wordpress.org/plugins/woocommerce/) if you haven’t already.
2. Click “Get your Tatum API key.”
3. Login to the Tatum dashboard and purchase a paid plan.
4. When you have completed your purchase, you will be directed back to the Tatum dashboard. Next to your newly created API key, click “Show” and copy your API key.
5. Now, back in the NFT Maker dashboard, paste your API key in the field labeled “Paste your API key below.”
6. Okay, you’re all ready to mint NFT tokens! In the overview, you can see info about your API key.
7. Open the “Product” tab in the left-hand column of your WordPress dashboard.
8. Click “Add product.”
9. Name your NFT.
10. Set its price in the “General” tab of the “Product data” box.
11. In the “NFT Maker” tab of the same box, select the blockchain on which to mint the NFT upon purchase.
12. Upload an image by clicking “Set product image” in the “Product Image” field in the right-hand column of the screen.
13. Click “Publish” to create your product. Now it is available for purchase in your e-shop. You can also view it in your list of lazy-minted NFTs. You will not consume any credits from your paid plan for gas fees until someone purchases your NFT and it is minted on the blockchain you have selected.
14. When a buyer adds the NFT to their cart and proceeds to checkout, they will have to enter a blockchain address on the blockchain to which the NFT will be minted.
15. After a successful purchase of the NFT, the customer will be shown a link to the blockchain transaction.
16. The blockchain transaction link redirects the customer to the blockchain explorer of the respective blockchain where they can view NFT mint transaction details.
17. Admins of the WordPress shop can also view all lazy-minted and sold NFTs.

== Frequently Asked Questions ==

= Why do I need an API key? =
Normally, to create NFTs on different blockchains, you would need to have access to blockchain nodes and create wallets on each blockchain.
With NFT Maker, you can simply use an API key to connect to different blockchains through Tatum, and everything else is taken care of for you.

= Do I need to generate a blockchain wallet on each blockchain I want to mint NFTs on? =
No, since NFTs are lazy-minted, you do not need to create wallets on any blockchain. Once an NFT is purchased, it is minted directly to the buyer’s blockchain address.

= Do I have to choose which blockchain I will mint all of my NFTs on during setup? =
No, you can choose which NFT to mint on which blockchain when you are setting up each product.
You can select one blockchain per product, but you can also create multiple NFTs of the same image for multiple blockchains if you so choose.

= Is the plugin free? =
Yes, the NFT Maker plugin itself is completely free. However, in order to pay for the gas fees necessary to mint NFTs, you must buy a paid plan in the [Tatum Dashboard](https://dashboard.tatum.io/).
Each paid plan has different credit amounts, and credits will be consumed when your NFTs are purchased and minted to the blockchain, based on the current gas fees of the given blockchain.

= If I don’t use all my credits within a month, do they carry over to the next month? =
At the moment, no, unused credits do not carry over to the following month.

= Can my users sell their NFTs with NFT Maker? =
NFT Maker can only be used to sell NFTs created by admins to customers.
To create NFT marketplaces that allow users to sell their own NFTs, please refer to Tatum’s [How to build p2p NFT marketplaces guide.](https://docs.tatum.io/tutorials/how-to-create-a-peer-to-peer-nft-marketplace)

= Can I sell my existing NFTs on my e-shop using NFT Maker? =
No, again, NFT Maker can only be used to sell NFTs created with the plugin.
For a complete guide on how to build the backend to an NFT marketplace from scratch, please refer to Tatum’s How to build [NFT marketplaces part 2 - Backend guide.](https://blog.tatum.io/how-to-build-nft-marketplaces-part-2-backend-899f7d804066)

== Screenshots ==
1. Create an account in the [Tatum Dashboard](https://dashboard.tatum.io/login) and obtain paid API key.
2. Install [Woocommerce plugin](https://wordpress.org/plugins/woocommerce/) if you don't have already installed it yet.
3. Submit your API key.
4. You are ready to mint NFT tokens! In the overview, you can see info about your API key.
5. Create Woocommerce product. Don't forget to select chain on which NFT will be minted.
6. Don't forget to set an NFT image. Only images up to 50 MB are available for storing. If you don't set an image your NFT will not be minted!
7. After you create product can see it in the list of lazy minted NFTs.
8. If your customer adds product to the cart and proceed to checkout, he/she will need to submit blockchain address to which will be NFT minted.
9. After successful purchase of the NFT customer will see the blockchain transaction.
10. Blockchain transaction links can redirect customer to the blockchain explorer to see NFT mint transaction details.
11. Admin can also see all sold and minted NFT.

== Changelog ==

= 1.0.0 =
* Initial version

= 1.0.1 =
* Added deploy smart contract enough balance validation

= 1.0.2 =
* Added information about fees cost

= 1.0.3 =
* Fixed deploy NFT minor bug

= 1.0.5 =
* Fixed minor bugs

= 2.0.0 =
* Completely new version with IPFS and lazy minting support

= 2.0.11 =
* Fixed Safari date bug

= 2.0.12 =
* Fixed error with changing blockchain of NFT & Added videos & FAQ

= 2.0.13 =
* Fixed error with initial table chain column

= 2.0.14 =
* Tested with WordPress 5.7.2

= 2.0.15 =
* Tested with WordPress 5.8.2

== Upgrade Notice ==

= 1.0.0 =
* No upgrade notice yet

= 2.0.0 =
* Warning! This new version is a complete upgrade and not compatible with the previous version 1. Please make sure you have securely stored your private keys before upgrading to version 2.0.0.

