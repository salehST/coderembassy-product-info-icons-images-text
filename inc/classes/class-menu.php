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

        // add settings link 
        add_filter('plugin_action_links_' . plugin_basename(CMFW_BASENAME), [$this, 'addSettingsLink']);

    }

    /**
     * Add menu in wordpress dashboard menu
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function adminMenu()
    {
        add_menu_page(__('CMFW', 'custom-meta-for-woocommerce'), __('CMFW', 'custom-meta-for-woocommerce'), 'manage_options', 'custom-meta-for-woocommerce', [$this, 'adminPage'], 'dashicons-admin-generic',55);
        add_submenu_page('custom-meta-for-woocommerce', __('Settings', 'custom-meta-for-woocommerce'), __('Settings', 'custom-meta-for-woocommerce'), 'manage_options', 'custom-meta-settings', [$this, 'settingsPage']);

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

    /*
        *Add settigns link to plugin intallation page
        * @since 1.0.0
        * @author Hannan <hannannexus@gmail.com>
    */
    public function addSettingsLink($links){

        $settings_url = esc_url( 
            add_query_arg( 
                'page', 
                'custom-meta-for-woocommerce', 
                admin_url( 'admin.php' ) 
            )
        );
        
        
        $settings_link = sprintf( 
            '<a href="%s">%s</a>',
            $settings_url,
            __( 'Settings', 'custom-meta-for-woocommerce' ) 
        );
        
       
        array_unshift( $links, $settings_link );
        
        return $links;

    }
    /**
     * Add settingsPage method for menu settings page
     * @since 1.0.0
     * Fazle Bari <fazlebarisn@gmail.com>
    */
    public function settingsPage(){
        include_once CMFW_DIR_PATH . '/inc/menu-pages/settings.php';
    }
}
