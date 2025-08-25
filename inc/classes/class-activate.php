<?php
/**
 * Plugin activation clss
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Activate {

    use Singleton;

	/**
	 * Protected class constructor to prevent direct object creation
	 * @since 1.0.0
 	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	protected function __construct() {
        flush_rewrite_rules();
		// $this->setup_hooks();
	}

	/**
	 * Setup hooks for the class
	 * @since 1.0.0
	 * @author Fazle Bari <fazlebarisn@gmail.com>
	 */
	protected function setup_hooks() {
        // actions hooks
		add_action( 'init' , [ $this, 'example' ] );
	}

    public static function example(){
		
	}

}