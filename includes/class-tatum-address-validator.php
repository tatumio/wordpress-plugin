<?php

use kornrunner\Keccak;
class Tatum_Address_Validator
{
    public function isETHAddress(string $address) : bool
    {
        if ($this->matchesPattern($address)) {
            return $this->isAllSameCaps($address) ?: $this->isValidChecksum($address);
        }
        return false;
    }
    protected function matchesPattern(string $address) : int
    {
        return preg_match('/^(0x)?[0-9a-f]{40}$/i', $address);
    }
    protected function isAllSameCaps(string $address) : bool
    {
        return preg_match('/^(0x)?[0-9a-f]{40}$/', $address) || preg_match('/^(0x)?[0-9A-F]{40}$/', $address);
    }
    protected function isValidChecksum($address)
    {
        $address = str_replace('0x', '', $address);
        $hash = Keccak::hash(strtolower($address), 256);
        // See: https://github.com/web3j/web3j/pull/134/files#diff-db8702981afff54d3de6a913f13b7be4R42
        for ($i = 0; $i < 40; $i++) {
            if (ctype_alpha($address[$i])) {
                // Each uppercase letter should correlate with a first bit of 1 in the hash char with the same index,
                // and each lowercase letter with a 0 bit.
                $charInt = intval($hash[$i], 16);
                if (ctype_upper($address[$i]) && $charInt <= 7 || ctype_lower($address[$i]) && $charInt > 7) {
                    return false;
                }
            }
        }
        return true;
    }
}