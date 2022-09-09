<?php

namespace Hathoriel\NftMaker\Utils;

class Constants
{
    const CONTRACT_ADDRESS = [
        'TESTNET' => [
            'MATIC' => '0xCd2AdA00c48A27FAa5Cc67F9A1ed55B89dDf7F77',
            'BSC' => '0xF73075aa67561791352fbEe8278115487Fd90ab6',
            'ONE' => '0x427ddbe3ad5e1e77e010c02e61e9bdef82dcaeea',
            'ETH' => '0xAe7D8842D0295B1f24a8842cBd5eB83Ae2fd0946',
            'CELO' => '0x45871ED5F15203C0ce791eFE5f4B5044833aE10e',
        ],
        'MAINNET' => [
            'MATIC' => '0x03582C4C2cc7fC8dEd9377A3f8e94a4C9f72ecCe',
            'BSC' => '0x4f83793245abE92cc8B978a16C898005c69e5e27',
            'ONE' => '0x559f11123bb892159cd33f652624e40e8b43d4ad',
            'ETH' => '0x789c00ed7ddd72a806dbac40df926df32fde3c2f',
            'CELO' => '0x5F35fd593243B059cBf580D0335B1c21881a248b',
        ]
    ];

    const EXPLORER = [
        'TESTNET' => [
            'ETH' => "https://sepolia.etherscan.io/",
            'CELO' => "https://alfajores-blockscout.celo-testnet.org/",
            'BSC' => "https://testnet.bscscan.com/",
            'MATIC' => "https://mumbai.polygonscan.com/",
            'ONE' => "https://explorer.testnet.harmony.one/"
        ],
        'MAINNET' => [
            'ETH' => "https://etherscan.io/",
            'CELO' => "https://explorer.celo.org/",
            'BSC' => "https://bscscan.com/",
            'MATIC' => "https://polygonscan.com/",
            'ONE' => "https://explorer.harmony.one/"
        ]
    ];

    const DEFAULT_API_KEY = '4ce2274723354471a7b65d1f726a8a68_100';
    const TATUM_URL = [
        'eu1' => 'https://api-eu1.tatum.io',
        'us1' => 'https://api-us-west1.tatum.io'
    ];

    const BLOCKCHAIN_URL_MAPPING = [
        'ETH' => 'ethereum',
        'CELO' => 'celo',
        'BSC' => 'bsc',
        'MATIC' => 'polygon',
        'ONE' => 'one'
    ];

    const CHAIN_CODES = ['ETH', 'BSC', 'CELO', 'MATIC', 'ONE'];
    const CHAIN_LABELS = [
        'ETH' => 'Ethereum (ETH)',
        'BSC' => 'Binance Smart Chain (BSC)',
        'CELO' => 'Celo (CELO)',
        'MATIC' => 'Polygon (MATIC)',
        'ONE' => 'Harmony (ONE)'
    ];
}