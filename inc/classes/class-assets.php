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
	public function adminStyle(){
		// Register Syle
		wp_register_style('cmfw-admin-settings', CMFW_URL . '/assets/css/cmfw-admin-settings.css', [], filemtime( CMFW_DIR_PATH . '/assets/css/cmfw-admin.css'), 'all');

		// Enqueue Style
		wp_enqueue_style('cmfw-admin-settings');

		// Enqueue js
		wp_enqueue_script('cmfw-admin-settings-js', CMFW_URL . '/assets/js/cmfw-admin-settings.js', ['jquery'], filemtime( CMFW_DIR_PATH . '/assets/js/cmfw-admin-settings.js'), true);
	}
}