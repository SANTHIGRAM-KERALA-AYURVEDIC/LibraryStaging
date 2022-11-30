<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeometa_woocommerceView extends WDSeoAdminView {

  public function __construct() {
    if ( WD_SEO_Library::woocommerce_active() ) {
      add_action('wdseo_options_tabs', array($this, 'tab_title'));
      add_action('wdseo_options_tabs_content', array($this, 'body'));
    }
  }

  public function tab_title() {
    ?>
    <li class="tabs">
      <a href="#wdseo_tab_meta_woocommerce" class="wdseo-tablink"><?php _e( 'Meta WooCommerce', WD_SEO_PREFIX ); ?></a>
    </li>
    <?php
  }

  /**
   * Generate page body.
   *
   * @param object $options
   *
   * @return string Body html.
   */
  public function body( $options ) {
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    // Add all scripts, styles necessary to use media library.
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-tabs');
    ob_start();
    ?>
    <div id="wdseo_tab_meta_woocommerce" class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Improve WooCommerce SEO', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-group">
              <label class="wd-label"><?php echo __('Cart Page', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->cart_page_index) && $options->woocommerce->cart_page_index == 1); ?> id="wd-woocommerce-cart-page-index-1" class="wd-radio" value="1" name="wd_settings[woocommerce][cart_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-cart-page-index-1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->cart_page_index) && $options->woocommerce->cart_page_index == 0) || !isset($options->woocommerce->cart_page_index)); ?> id="wd-woocommerce-cart-page-index-0" class="wd-radio" value="0" name="wd_settings[woocommerce][cart_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-cart-page-index-0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
            </div>
            <div class="wd-group">
              <label class="wd-label"><?php echo __('Checkout Page', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->checkout_page_index) && $options->woocommerce->checkout_page_index == 1); ?> id="wd-woocommerce-checkout-page-index-1" class="wd-radio" value="1" name="wd_settings[woocommerce][checkout_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-checkout-page-index-1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->checkout_page_index) && $options->woocommerce->checkout_page_index == 0) || !isset($options->woocommerce->checkout_page_index)); ?> id="wd-woocommerce-checkout-page-index-0" class="wd-radio" value="0" name="wd_settings[woocommerce][checkout_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-checkout-page-index-0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
            </div>
            <div class="wd-group">
              <label class="wd-label"><?php echo __('Customer Account Pages', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->customer_account_page_index) && $options->woocommerce->customer_account_page_index == 1); ?> id="wd-woocommerce-customer-account-page-index-1" class="wd-radio" value="1" name="wd_settings[woocommerce][customer_account_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-customer-account-page-index-1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->customer_account_page_index) && $options->woocommerce->customer_account_page_index == 0) || !isset($options->woocommerce->customer_account_page_index)); ?> id="wd-woocommerce-customer-account-page-index-0" class="wd-radio" value="0" name="wd_settings[woocommerce][customer_account_page_index]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-customer-account-page-index-0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
            </div>
            <div class="wd-group">
              <label class="wd-label"><?php echo __('OG Price', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->og_price) && $options->woocommerce->og_price == 1) || !isset($options->woocommerce->og_price)); ?> id="wd-woocommerce-og-price-1" class="wd-radio" value="1" name="wd_settings[woocommerce][og_price]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-og-price-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->og_price) && $options->woocommerce->og_price == 0); ?> id="wd-woocommerce-og-price-0" class="wd-radio" value="0" name="wd_settings[woocommerce][og_price]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-og-price-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php _e('Add OG:PRICE meta to product page.', WD_SEO_PREFIX); ?></p>
            </div>
            <div class="wd-group">
              <label class="wd-label"><?php echo __('OG Currency', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->og_currency) && $options->woocommerce->og_currency == 1) || !isset($options->woocommerce->og_currency)); ?> id="wd-woocommerce-og-currency-1" class="wd-radio" value="1" name="wd_settings[woocommerce][og_currency]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-og-currency-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->og_currency) && $options->woocommerce->og_currency == 0); ?> id="wd-woocommerce-og-currency-0" class="wd-radio" value="0" name="wd_settings[woocommerce][og_currency]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-og-currency-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php _e('Add OG:CURRENCY meta to product page.', WD_SEO_PREFIX); ?></p>
            </div>
            <div class="wd-group">
              <label class="wd-label"><?php echo __('Remove WooCommerce Meta Generator', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked((isset($options->woocommerce->meta_generator) && $options->woocommerce->meta_generator == 1) || !isset($options->woocommerce->meta_generator)); ?> id="wd-woocommerce-meta-generator-1" class="wd-radio" value="1" name="wd_settings[woocommerce][meta_generator]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-meta-generator-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked(isset($options->woocommerce->meta_generator) && $options->woocommerce->meta_generator == 0); ?> id="wd-woocommerce-meta-generator-0" class="wd-radio" value="0" name="wd_settings[woocommerce][meta_generator]" type="radio" />
              <label class="wd-label-radio" for="wd-woocommerce-meta-generator-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php _e('Remove WooCommerce generator tag in your header.', WD_SEO_PREFIX); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    echo ob_get_clean();
  }
}

add_action('wdseo_init_after', function() {
  new WDSeometa_woocommerceView();
});
