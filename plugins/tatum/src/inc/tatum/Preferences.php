<?php

namespace Hathoriel\Tatum\tatum;

class Preferences
{
    public static function getFees() {
      return get_option(TATUM_SLUG . '_fees');
    }

    public static function getDefaultChains() {
        return get_option(TATUM_SLUG . '_default_chains');
    }

    public static function setFees($data) {
        $fees = array();
        foreach (Chains::getChainCodes() as $chain) {
            $fees[$chain] = $data["fee$chain"];
        }
        update_option(TATUM_SLUG . '_fees', $fees);
    }

    public static function setDefaultChains($data) {
        $defaultChains = array();
        foreach (Chains::getChainCodes() as $chain) {
            $defaultChains[$chain] = $data["defaultChain$chain"];
        }
        update_option(TATUM_SLUG . '_default_chains', $defaultChains);
    }
}