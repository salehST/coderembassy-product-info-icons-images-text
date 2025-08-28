<?php

/**
 * Plugin Name:       Coderembassy Product Info Icons Images Text
 * Description:       CoderEmbassy Product Info: Add icons, images & custom text to product pages. Boost WooCommerce conversions with enhanced product information display.
 * Version:           1.0.0
 * Author:            codersaleh
 * Author URI:		  https://github.com/coderembassy
 * Plugin URI: 		  https://coderembassy.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       coderembassy-product-info-icons-images-text
 * Requires at least: 3.0.0
 * Requires PHP:      7.0
 * Requires Plugins:  woocommerce
 * Tested up to:      6.8.2
 * WC tested up to:	  10.0.4
 * Licence:           GPL Or leater 
 *
 * @package           cmfw
 */
defined('ABSPATH') or die('Nice Try!');

if (! defined('CMFW_DIR_PATH')) {
	define('CMFW_DIR_PATH', __DIR__);
}

define('CMFW_FILE', __FILE__);
define('CMFW_BASENAME', plugin_basename(__FILE__));
define('CMFW_URL', plugin_dir_url(CMFW_FILE));
define('CMFW_VERSION', '1.1.0');

require_once CMFW_DIR_PATH . '/inc/helpers/autoloader.php';

/**
 * If some task to perform during the plugin activation. Like create a new table.
 * Do not write any in this class code that does not need to execute during the plugin activation 
 * @since 1.0.0
 * @author Fazle Bari <fazlebarisn@gmail.com>
 */
function activate_cmfw()
{
	\CMFW\Inc\Activate::get_instance();
}
register_activation_hook(__FILE__, 'activate_cmfw');

/**
 * If some task to perform during the plugin deactivation. Like delete plugin table
 * Do not write any in this class code that does not need to execute during the plugin deactivation 
 * @since 1.0.0
 * @author Fazle Bari <fazlebarisn@gmail.com>
 */
function deactivate_cmfw()
{
	\CMFW\Inc\Deactivate::get_instance();
}
register_deactivation_hook(__FILE__, 'deactivate_cmfw');

/**
 * Declare HPOS compatibility.
 *
 * @since 1.0.0
 */
add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_orders_table',
			CMFW_FILE,
			true
		);
	}
});

/**
 * This is the pluign main class, We will active or deactive all class from here
 * @since 1.0.0
 * @author Fazle Bari <fazlebarisn@gmail.com>
 */
function cmfw_plugin_instance()
{

	if (in_array('coderembassy-product-info-icons-images-text/coderembassy-product-info-icons-images-text.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		\CMFW\Inc\CMFW::get_instance();


		// Load extra functions file
		require_once CMFW_DIR_PATH . '/functions.php';
	}
}
add_action('plugins_loaded', 'cmfw_plugin_instance');
