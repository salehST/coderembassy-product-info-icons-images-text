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
		// Only load assets on plugin admin pages
		$plugin_pages = [
			'toplevel_page_coderembassy-product-info-icons-images-text',
			'cmfw_page_custom-meta-settings',
		];

		// if (! in_array($hook, $plugin_pages, true)) {
		// 	return;
		// }

		// Admin CSS
		wp_enqueue_style(
			'cmfw-admin-settings-css',
			CMFW_URL . 'assets/css/cmfw-admin-settings.css',
			[],
			CMFW_VERSION,
			'all'
		);

		wp_enqueue_style(
			'cmfw-admin-css',
			CMFW_URL . 'assets/css/cmfw-admin.css',
			[],
			CMFW_VERSION,
			'all'
		);

		// Optionally reuse frontend CSS inside admin
		wp_enqueue_style(
			'cmfw-shared-css',
			CMFW_URL . 'assets/css/cmfw.css',
			[],
			CMFW_VERSION,
			'all'
		);

		// WordPress built-in assets
		wp_enqueue_media(); // handles media-upload
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('jquery-ui-autocomplete');

		// Admin JS
		wp_enqueue_script(
			'cmfw-admin-settings-js',
			CMFW_URL . 'assets/js/cmfw-admin-settings.js',
			['jquery', 'wp-color-picker'],
			CMFW_VERSION,
			true
		);

		wp_enqueue_script(
			'cmfw-admin-js',
			CMFW_URL . 'assets/js/cmfw-admin.js',
			['jquery', 'media-views'],
			CMFW_VERSION,
			true
		);

		// Localize for AJAX
		wp_localize_script('cmfw-admin-js', 'cmfwAjax', [
			'ajax_url'     => admin_url('admin-ajax.php'),
			'nonce'        => wp_create_nonce('cmfw_ajax_nonce'),
			'media_title'  => __('Select Image', 'coderembassy-product-info-icons-images-text'),
			'media_button' => __('Use This Image', 'coderembassy-product-info-icons-images-text'),
			'pro_active'   => function_exists('cmfw_is_pro_active') && cmfw_is_pro_active() ? '1' : '0',
		]);
	}
}
