<?php
/**
 * Plugin deactivation class
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Deactivate {
    use Singleton;

	protected function __construct() {
        flush_rewrite_rules();
		// $this->setup_hooks();
	}

	/**
	 * Setup the hooks
	 * This method is not needed for deactivation, but can be used for future hooks if necessary.
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