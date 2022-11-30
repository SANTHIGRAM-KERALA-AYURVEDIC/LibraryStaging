<?php

/**
 * Class WD_SEO_WooCommerce
 */
class WD_SEO_WooCommerce {

  public static function meta_opengraph() {
    $meta = '';
    if ( is_product() ) {
      if ( WDSeo()->options->woocommerce->og_price ) {
        $product = wc_get_product(get_the_id());
        $meta .= '<meta property="product:price:amount" content="' . $product->get_price() . '" />' . "\n";
      }
      if ( WDSeo()->options->woocommerce->og_currency ) {
        $meta .= '<meta property="product:price:currency" content="' . get_woocommerce_currency() . '" />' . "\n";
      }
    }
    echo $meta;
  }

  public static function meta_generator_remove() {
    if ( WD_SEO_Library::woocommerce_active() && !empty(WDSeo()->options->woocommerce->meta_generator) ) {
      remove_action('get_the_generator_html', 'wc_generator_tag', 10, 2);
      remove_action('get_the_generator_xhtml', 'wc_generator_tag', 10, 2);
    }
  }
}