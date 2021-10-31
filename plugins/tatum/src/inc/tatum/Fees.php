<?php

namespace Hathoriel\Tatum\tatum;

class Fees
{
    public static function get() {
        get_option(TATUM_SLUG . '_fees');
    }

    public static function set($fees) {
        update_option(TATUM_SLUG . '_fees', $fees);
    }
}