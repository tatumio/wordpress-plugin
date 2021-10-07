<?php

namespace Hathoriel\Tatum\tatum;

class LazyMintUtils
{
    public static function isBoughOrFailed($lazy_nfts) {
        foreach ($lazy_nfts as $lazy_nft) {

            if ($lazy_nft->transaction_id != "" || $lazy_nft->error_cause != "" || $lazy_nft->recipient_address != "") {
                return true;
            }
        }
        return false;
    }

    public static function minted($lazy_nfts) {
        foreach ($lazy_nfts as $lazy_nft) {

            if ($lazy_nft->transaction_id != "") {
                return true;
            }
        }
        return false;
    }
}