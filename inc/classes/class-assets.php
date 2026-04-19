<?php

/**
 * Plugin assets will be added here
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Assets
{

	use Singleton;

	protected function __construct()
	{
		$this->setup_hooks();
	}

	/**
	 * Setup the hooks
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	protected function setup_hooks()
	{
		// Frontend Enqueue
		add_action('wp_enqueue_scripts', [$this, 'frontendStyles']);
		add_action('wp_enqueue_scripts', [$this, 'frontendScripts']);

		// Admin Enqueue
		add_action('admin_enqueue_scripts', [$this, 'adminAssets']);
	}

	/**
	 * Enqueue frontend styles
	 * @since 1.0.0
	 */
	public function frontendStyles()
	{
		wp_register_style(
			'cmfw-frontend-css',
			CMFW_URL . 'assets/css/cmfw.css',
			[],
			CMFW_VERSION,
			'all'
		);

		wp_enqueue_style('cmfw-frontend-css');
	}

	/**
	 * Enqueue frontend scripts
	 * @since 1.0.0
	 */
	public function frontendScripts()
	{
		wp_register_script(
			'cmfw-frontend-js',
			CMFW_URL . 'assets/js/cmfw.js',
			['jquery'],
			CMFW_VERSION,
			true
		);

		wp_enqueue_script('cmfw-frontend-js');
	}

	/**
	 * Enqueue admin assets
	 * @since 1.0.0
	 */
	public function adminAssets($hook)
	{
		// If Pro version is active, it handles the SPA assets
		if (function_exists('cmfw_pro_is_active') && cmfw_pro_is_active()) {
			return;
		}

		// Only enqueue on our plugin pages
		if (strpos($hook, 'coderembassy') === false) {
			return;
		}

		// Enqueue SPA CSS
		wp_enqueue_style(
			'cmfw-admin-spa-css',
			CMFW_URL . 'assets/admin/admin.css',
			[],
			CMFW_VERSION,
			'all'
		);

		// WordPress built-in assets
		wp_enqueue_media(); // handles media-upload
		
		// Enqueue SPA JS
		wp_enqueue_script(
			'cmfw-admin-spa-js',
			CMFW_URL . 'assets/admin/admin.js',
			['jquery', 'media-views'],
			filemtime(CMFW_DIR_PATH . '/assets/admin/admin.js'),
			true
		);

		// Process groups and term names for localization
		$saved_groups = cmfw_get_groups();
		
		$term_names = [];
		if (!empty($saved_groups)) {
			foreach ($saved_groups as $group) {
				$taxonomy = $group['taxonomy'] ?? '';
				if (!empty($group['terms']) && taxonomy_exists($taxonomy)) {
					foreach ((array) $group['terms'] as $term_id) {
						$term_obj = get_term((int) $term_id, $taxonomy);
						if ($term_obj && !is_wp_error($term_obj)) {
							$term_names[(int) $term_id] = esc_html($term_obj->name);
						}
					}
				}
			}
		}

		// Process settings with defaults
		$settings = get_option('cmfw_settings', array());
		$default_settings = array(
			'enable_meta' => '1',
			'meta_position' => 'woocommerce_product_meta_end',
			'meta_heading' => 'Product Information',
			'show_heading' => '1',
			'heading_color' => '#333333',
			'heading_size' => 18,
			'meta_font_size' => 14,
			'meta_text_color' => '#666666',
			'meta_bg_color' => '#ffffff',
			'image_width' => 24,
			'image_height' => 24
		);
		$settings = wp_parse_args($settings, $default_settings);

		$cmfwAjaxData = [
			'ajax_url'     => admin_url('admin-ajax.php'),
			'nonce'        => wp_create_nonce('cmfw_ajax_nonce'),
			'media_title'  => __('Select Image', 'coderembassy-product-info-icons-images-text'),
			'media_button' => __('Use This Image', 'coderembassy-product-info-icons-images-text'),
			'pro_active'   => function_exists('cmfw_pro_is_active') && cmfw_pro_is_active() ? '1' : '0',
			'groups'       => $saved_groups,
			'term_names'   => $term_names,
			'settings'     => $settings,
			'logo_light'   => CMFW_URL . 'assets/admin/logo-light.png',
			'logo_dark'    => CMFW_URL . 'assets/admin/logo-dark.png',
			'current_user' => ['display_name' => wp_get_current_user()->display_name],
			'allowed_positions' => apply_filters('cmfw_allowed_positions', [
				'woocommerce_product_meta_end' => __('After Product Meta Section', 'coderembassy-product-info-icons-images-text')
			]),
		];

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CMFW Debug - cmfwAjaxData: ' . print_r($cmfwAjaxData, true));
		}

		// Localize script
		wp_localize_script('cmfw-admin-spa-js', 'cmfwAjax', $cmfwAjaxData);
	}
}
