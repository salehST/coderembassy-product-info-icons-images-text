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
		]);

		// Add inline script for dashboard functionality
		$saved_groups = get_option('cmfw_groups', []);
		// Attach term names for pills
		if (!empty($saved_groups)) {
			foreach ($saved_groups as &$group) {
				$taxonomy = $group['taxonomy'] ?? '';
				$term_names = [];
				if (!empty($group['terms']) && taxonomy_exists($taxonomy)) {
					foreach ((array) $group['terms'] as $term_id) {
						$term_obj = get_term((int) $term_id, $taxonomy);
						if ($term_obj && !is_wp_error($term_obj)) {
							$term_names[(int) $term_id] = esc_html($term_obj->name);
						}
					}
				}
				$group['term_names'] = $term_names;
			}
			unset($group);
		}

		// Free version doesn't need dynamic rendering - it has fixed structure
		$inline_script = '';

		wp_add_inline_script('cmfw-admin-js', $inline_script);
	}
}
