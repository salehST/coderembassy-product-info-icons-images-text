<?php
/**
 * Plugin assets will be added here
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Assets {

	use Singleton;

	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Setup the hooks
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	protected function setup_hooks() {

		// Frontend Enqueue
		add_action( 'wp_enqueue_scripts' , [ $this, 'frontendStyles' ] );
		add_action( 'wp_enqueue_scripts' , [ $this, 'frontendScripts' ] );

		// Admin Enqueue
		add_action('admin_enqueue_scripts', [$this, 'adminStyle'] );
		// add_action('admin_enqueue_scripts', [$this, 'adminScripts'], 20 );
	}

	/**
	 * Enqueue admin scripts
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	public function frontendStyles(){
		// Register Syle
		wp_register_style('cmfw', CMFW_URL . '/assets/css/cmfw.css', [], filemtime( CMFW_DIR_PATH . '/assets/css/cmfw.css'), 'all');

		// Enqueue Style
		wp_enqueue_style('cmfw');
	}

	/**
	 * Enqueue admin scripts
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	public function frontendScripts(){
		// Register Scripts
		wp_register_script( 'cmfw', CMFW_URL . '/assets/js/cmfw.js', ['jquery'], filemtime( CMFW_DIR_PATH . '/assets/js/cmfw.js'), true );

		// Enqueue Script
		wp_enqueue_script('cmfw');
	}

	/**
	 * Enqueue admin scripts
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	public function adminStyle($hook){
		// Register Syle
		wp_register_style('cmfw-admin-settings', CMFW_URL . '/assets/css/cmfw-admin-settings.css', [], filemtime( CMFW_DIR_PATH . '/assets/css/cmfw-admin-settings.css'), 'all');
		wp_register_style('cmfw-admin-css', CMFW_URL . '/assets/css/cmfw-admin.css', [], filemtime( CMFW_DIR_PATH . '/assets/css/cmfw-admin.css'), 'all');
		wp_register_style('cmfw-css', CMFW_URL . '/assets/css/cmfw.css', [], filemtime( CMFW_DIR_PATH . '/assets/css/cmfw.css'), 'all');

		// Only load assets on plugin pages
		$plugin_pages = array(
			'toplevel_page_coderembassy-product-info-icons-images-text',
			'cmfw_page_custom-meta-settings'
		);
		

		//if (in_array($hook, $plugin_pages)) {
			// Enqueue Style
			wp_enqueue_style('cmfw-admin-settings');
			wp_enqueue_style('cmfw-admin-css');
			wp_enqueue_style('cmfw-css');

			// Enqueue WordPress Media Library for image uploads
			if (function_exists('wp_enqueue_media')) {
				wp_enqueue_media();
			}

			// Enqueue WordPress Color Picker
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');

			// jQuery UI Autocomplete for term search
			wp_enqueue_script('jquery-ui-autocomplete');

			// Enqueue js
			wp_enqueue_script('cmfw-admin-settings-js', CMFW_URL . '/assets/js/cmfw-admin-settings.js', ['jquery', 'wp-color-picker'], filemtime( CMFW_DIR_PATH . '/assets/js/cmfw-admin-settings.js'), true);
			wp_enqueue_script('cmfw-admin-js', CMFW_URL . '/assets/js/cmfw-admin.js', ['jquery', 'media-upload', 'media-views'], filemtime( CMFW_DIR_PATH . '/assets/js/cmfw-admin.js'), true);
			
			// Localize script for AJAX
			wp_localize_script('cmfw-admin-js', 'cmfwAjax', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('cmfw_ajax_nonce'),
				'media_title' => __('Select Image', 'coderembassy-product-info-icons-images-text'),
				'media_button' => __('Use This Image', 'coderembassy-product-info-icons-images-text'),
			]);
		//}
	}
}