<?php

namespace Hathoriel\Tatum\hooks;

class Admin
{
    public function add_product_data_tab($tabs) {
        $tabs['tatum'] = array(
            'label' => 'Tatum',
            'target' => 'tatum_product_data',
            'priority' => 21,
        );

        return $tabs;
    }

    public function add_product_data_icon() {
        ?>
      <style>
          #woocommerce-product-data .tatum_options.active:hover > a:before,
          #woocommerce-product-data .tatum_options > a:before {
              background: url(<?php echo plugin_dir_url( 'tatum/public/assets/tatum.png' ). 'tatum.png'; ?>) center center no-repeat;
              content: " " !important;
              background-size: 100%;
              width: 13px;
              height: 13px;
              display: inline-block;
              line-height: 1;
          }

          @media only screen and (max-width: 900px) {
              #woocommerce-product-data .tatum_options.active:hover > a:before,
              #woocommerce-product-data .tatum_options > a:before,
              #woocommerce-product-data .tatum_options:hover a:before {
                  background-size: 35%;
              }
          }

          .tatum_options:hover a:before {
              background: url(<?php echo plugin_dir_url( 'tatum/public/assets/tatum.png' ). 'tatum.png'; ?>) center center no-repeat;
          }

      </style><?php

    }

    public static function instance() {
        return new Admin();
    }
}