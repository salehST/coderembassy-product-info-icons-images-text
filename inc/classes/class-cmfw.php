<?php

/**
 * Plugin main class. All other class will be active or deactive from here
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class CMFW
{
     use Singleton;

     public $cansoft_module;

     /**
      * If you need to create a new claas to do a specific task, you can create a class and load here.
      * Here i have created a class to add css and js files. uncomment to use it.
      * All class file should be in the 'inc/classes' folder and follow the name convention.
      */
     protected function __construct()
     {
          $this->setup_hooks();

          // Load classes
          Assets::get_instance();
          Menu::get_instance();
     }

     /**
      * Set up all hook here
      * @since 1.0.0
      * @author Fazle Bari <fazlebarisn@gmail.com>
      */
     public function setup_hooks()
     {

          if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
               add_action('admin_notices', [$this, 'admin_notice_missing_woocommerce_plugin']);
          }

          add_action('before_woocommerce_init', [$this, 'cmfw_hpos']);
          
          // AJAX handlers for image functionality
          add_action('wp_ajax_cmfw_get_image_url', [$this, 'ajax_get_image_url']);
     }

     /**
      * Add missing woocommerce plugin notice
      * @since 1.0.0
      * @author Fazle Bari <fazlebarisn@gmail.com>
      */
     public function admin_notice_missing_woocommerce_plugin()
     {
          $class = 'notice notice-error';
          $message = __("Custom Meta for WooCommerce Requires WooCommerce to be Activated", "custom-meta-for-woocommerce");

          printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
     }

     /**
      * Declare compatibility with custom order tables for WooCommerce.
      * Support WooCommerce High-performance order storage
      * @since 1.0.0
      * @author Fazle Bari <fazlebarisn@gmail.com>
      */
     public function cmfw_hpos()
     {
          if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
               \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
          }
     }

     /**
      * AJAX handler to get image URL from attachment ID
      * @since 1.0.0
      */
     public function ajax_get_image_url()
     {
          // Verify nonce
          if (!wp_verify_nonce($_POST['nonce'] ?? '', 'cmfw_ajax_nonce')) {
               wp_die(__('Security check failed', 'custom-meta-for-woocommerce'));
          }

          $image_id = intval($_POST['image_id'] ?? 0);
          
          if ($image_id <= 0) {
               wp_send_json_error(['message' => __('Invalid image ID', 'custom-meta-for-woocommerce')]);
          }

          // Get image URL
          $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
          
          if (!$image_url) {
               // Try to get full size if thumbnail doesn't exist
               $image_url = wp_get_attachment_image_url($image_id, 'full');
          }

          if ($image_url) {
               wp_send_json_success([
                    'url' => $image_url,
                    'id' => $image_id
               ]);
          } else {
               wp_send_json_error(['message' => __('Image not found', 'custom-meta-for-woocommerce')]);
          }
     }
}
