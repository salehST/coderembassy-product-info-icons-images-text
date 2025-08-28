<?php

/**
 * All menu and submenu will be here
 *
 * @package cmfw
 */
namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Menu
{

    use Singleton;
    /**
     * Constructor to set up hooks
     * This class is used to manage the admin menu for the plugin.
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    protected function __construct()
    {
        $this->setup_hooks();
    }

    /**
     * Set up all hooks for the menu
     * This method is used to register the admin menu and submenu items.
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    protected function setup_hooks()
    {
        // Add menu
        add_action('admin_menu', [$this, 'adminMenu'], 20);

        // Register settings
        add_action('admin_init', [$this, 'registerSettings']);

        // add settings link 
        add_filter('plugin_action_links_'.CMFW_BASENAME, [$this, 'addSettingsLink']);

    }

    /**
     * Add menu in wordpress dashboard menu
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function adminMenu()
    {
        add_menu_page(__('Product Info', 'coderembassy-product-info-icons-images-text'), __('PRODUCT INFO', 'coderembassy-product-info-icons-images-text'), 'manage_options', 'coderembassy-product-info-icons-images-text', [$this, 'adminPage'], 'dashicons-admin-generic',55);
        add_submenu_page('coderembassy-product-info-icons-images-text', __('Settings', 'coderembassy-product-info-icons-images-text'), __('Settings', 'coderembassy-product-info-icons-images-text'), 'manage_options', 'coderembassy-meta-settings', [$this, 'settingsPage']);
        
        // Add test limits page for development/testing
        if (WP_DEBUG) {
            add_submenu_page('coderembassy-product-info-icons-images-text', __('Test Limits', 'coderembassy-product-info-icons-images-text'), __('Test Limits', 'coderembassy-product-info-icons-images-text'), 'manage_options', 'cmfw-test-limits', [$this, 'testLimitsPage']);
        }
    }

    /**
     * Add adminPage method for menu dashboard page
     * @since 1.0.0
     * @author Hannan <hannannexus@gmail.com> 
    
    */
    public function adminPage(){
        if( !current_user_can('manage_options')){
            return;
        }

        include_once CMFW_DIR_PATH . '/inc/menu-pages/dashboard.php';
    }
    
    /**
     * Test limits page for development/testing
     * @since 1.0.0
     * @author Assistant
     */
    public function testLimitsPage(){
        if( !current_user_can('manage_options')){
            return;
        }

        include_once CMFW_DIR_PATH . '/inc/menu-pages/test-limits.php';
    }

    /*
        *Add settigns link to plugin intallation page
        * @since 1.0.0
        * @author Hannan <hannannexus@gmail.com>
    */
    public function addSettingsLink($links){

        $settings_url = esc_url( 
            add_query_arg( 
                'page', 
                'coderembassy-product-info-icons-images-text', 
                admin_url( 'admin.php' ) 
            )
        );
        
        
        $settings_link = sprintf( 
            '<a href="%s">%s</a>',
            $settings_url,
            __( 'Settings', 'coderembassy-product-info-icons-images-text' ) 
        );
        
       
        array_unshift( $links, $settings_link );
        
        return $links;

    }
    /**
     * Register plugin settings
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function registerSettings()
    {
        // Register the settings group
        register_setting(
            'cmfw_settings_group', // Option group
            'cmfw_settings', // Option name
            array(
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitizeSettings'],
                'default' => array()
            )
        );

        // Add settings section
        add_settings_section(
            'cmfw_general_section', // ID
            '', // Title (empty because we handle it in the template)
            null, // Callback
            'cmfw_settings_group' // Page
        );
    }

    /**
     * Sanitize plugin settings
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     * @param array $input Raw input from form
     * @return array Sanitized settings
     */
    public function sanitizeSettings($input)
    {
        $sanitized = array();

        // Sanitize enable_meta
        $sanitized['enable_meta'] = isset($input['enable_meta']) ? '1' : '0';

        // Sanitize meta_position
        $allowed_positions = array(
            'woocommerce_after_add_to_cart_button',
            'woocommerce_product_meta_end',
            'woocommerce_after_single_product_summary',
            'woocommerce_after_single_product'
        );
        $sanitized['meta_position'] = isset($input['meta_position']) && in_array($input['meta_position'], $allowed_positions) 
            ? $input['meta_position'] 
            : 'woocommerce_after_add_to_cart_button';

        // Sanitize meta_heading
        $sanitized['meta_heading'] = isset($input['meta_heading']) 
            ? sanitize_text_field($input['meta_heading']) 
            : __('Product Information', 'coderembassy-product-info-icons-images-text');

        // Sanitize heading_color
        $sanitized['heading_color'] = isset($input['heading_color']) 
            ? sanitize_hex_color($input['heading_color']) 
            : '#333333';

        // Sanitize heading_size
        $heading_size = isset($input['heading_size']) ? intval($input['heading_size']) : 18;
        $sanitized['heading_size'] = ($heading_size >= 10 && $heading_size <= 48) ? $heading_size : 18;

        // Sanitize meta_font_size
        $meta_font_size = isset($input['meta_font_size']) ? intval($input['meta_font_size']) : 14;
        $sanitized['meta_font_size'] = ($meta_font_size >= 10 && $meta_font_size <= 24) ? $meta_font_size : 14;

        // Sanitize meta_text_color
        $sanitized['meta_text_color'] = isset($input['meta_text_color']) 
            ? sanitize_hex_color($input['meta_text_color']) 
            : '#666666';

        // Sanitize meta_bg_color
        $sanitized['meta_bg_color'] = isset($input['meta_bg_color']) 
            ? sanitize_hex_color($input['meta_bg_color']) 
            : '#ffffff';

        return $sanitized;
    }

    /**
     * Add settingsPage method for menu settings page
     * @since 1.0.0
     * Fazle Bari <fazlebarisn@gmail.com>
    */
    public function settingsPage(){
        if( !current_user_can('manage_options')){
            return;
        }
        include_once CMFW_DIR_PATH . '/inc/menu-pages/settings.php';
    }
}
