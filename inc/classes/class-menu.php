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
        add_action('admin_menu', [$this, 'hideSubmenuItems'], 1000);

        // Register settings
        add_action('admin_init', [$this, 'registerSettings']);

        // add settings link 
        add_filter('plugin_action_links_'.CMFW_BASENAME, [$this, 'addSettingsLink']);

        // SPA AJAX calls
        add_action('wp_ajax_cmfw_save_spa_data', [$this, 'ajax_save_spa_data']);
        add_action('wp_ajax_cmfw_search_terms', [$this, 'ajax_search_terms']);
    }

    /**
     * Add menu in wordpress dashboard menu
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function adminMenu()
    {
        add_menu_page(
            __('Product Info', 'coderembassy-product-info-icons-images-text'),
            __('Product Info', 'coderembassy-product-info-icons-images-text'),
            'manage_options',
            'coderembassy-product-info-icons-images-text',
            [$this, 'adminPage'],
            'dashicons-cart',
            55
        );

        // Add a hidden submenu page to handle the settings URL on refresh
        add_submenu_page(
            'coderembassy-product-info-icons-images-text',
            __('Settings', 'coderembassy-product-info-icons-images-text'),
            __('Settings', 'coderembassy-product-info-icons-images-text'),
            'manage_options',
            'coderembassy-meta-settings',
            [$this, 'adminPage']
        );
    }

    /**
     * Keep sidebar clean: only show dashboard menu item.
     *
     * We keep hidden SPA routes registered so direct URLs continue to work:
     * - coderembassy-meta-settings
     * - coderembassy-design
     */
    public function hideSubmenuItems()
    {
        $parent_slug = 'coderembassy-product-info-icons-images-text';

        // Remove default duplicate submenu generated from add_menu_page().
        remove_submenu_page($parent_slug, $parent_slug);

        // Hide SPA route slugs from sidebar while keeping route handlers active.
        remove_submenu_page($parent_slug, 'coderembassy-meta-settings');
        remove_submenu_page($parent_slug, 'coderembassy-design');
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

        echo '<div id="dpp-root"></div>';
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
        $sanitized['enable_meta'] = (isset($input['enable_meta']) && $input['enable_meta'] === '1') ? '1' : '0';

        // Sanitize show_heading
        $sanitized['show_heading'] = (isset($input['show_heading']) && $input['show_heading'] === '1') ? '1' : '0';

        // Sanitize meta_position
        $allowed_positions_map = apply_filters('cmfw_allowed_positions', [
            'woocommerce_product_meta_end' => __('After Product Meta Section', 'coderembassy-product-info-icons-images-text')
        ]);
        $allowed_positions = array_keys($allowed_positions_map);
        
        // Default position
        $sanitized['meta_position'] = 'woocommerce_product_meta_end';
        
        // Allow selection from allowed positions
        if (isset($input['meta_position']) && in_array($input['meta_position'], $allowed_positions)) {
            $sanitized['meta_position'] = $input['meta_position'];
        }

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

        // Sanitize image dimensions
        $sanitized['image_width']  = isset($input['image_width']) ? absint($input['image_width']) : 24;
        $sanitized['image_height'] = isset($input['image_height']) ? absint($input['image_height']) : 24;

        return $sanitized;
    }

    /**
     * Add settingsPage method for menu settings page
     * @since 1.0.0
     * Fazle Bari <fazlebarisn@gmail.com>
    */
    public function ajax_save_spa_data() {
        check_ajax_referer('cmfw_ajax_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        if (isset($_POST['groups'])) {
            $groups_json = wp_unslash($_POST['groups']);
            $groups = json_decode($groups_json, true);
            if (is_array($groups)) {
                $success = cmfw_save_groups($groups);
                if ($success !== false) {
                    wp_send_json_success('Groups saved');
                } else {
                    wp_send_json_error('Failed to save groups');
                }
            } else {
                wp_send_json_error('Invalid payload');
            }
        } elseif (isset($_POST['settings'])) {
            $settings_json = wp_unslash($_POST['settings']);
            $settings_raw = json_decode($settings_json, true);
            if (is_array($settings_raw)) {
                $sanitized = $this->sanitizeSettings($settings_raw);
                update_option('cmfw_settings', $sanitized);
                wp_send_json_success('Settings saved');
            } else {
                wp_send_json_error('Invalid payload');
            }
        } else {
            wp_send_json_error('Nothing to save');
        }
    }

    public function ajax_search_terms() {
        check_ajax_referer('cmfw_ajax_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $q = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_key($_POST['taxonomy']) : '';
        
        if (empty($q) || empty($taxonomy) || !taxonomy_exists($taxonomy)) {
            wp_send_json_success([]);
        }

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'name__like' => $q,
            'hide_empty' => false,
            'number' => 20
        ]);

        $results = [];
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $results[] = [
                    'id' => $term->term_id,
                    'name' => html_entity_decode($term->name)
                ];
            }
        }
        wp_send_json_success($results);
    }

    public function settingsPage(){
        if( !current_user_can('manage_options')){
            return;
        }
        echo '<div id="dpp-root"></div>';
    }
}
