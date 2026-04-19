<?php

/**
 * Frontend display functionality for custom meta
 *
 * @package cmfw
 */

namespace CMFW\Inc;

use CMFW\Inc\Traits\Singleton;

class Frontend
{
    use Singleton;

    /**
     * Constructor to set up hooks
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    protected function __construct()
    {
        $this->setup_hooks();
    }

    /**
     * Set up all hooks for frontend display
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    protected function setup_hooks()
    {
        // Only run on frontend
        if (!is_admin()) {
            add_action('init', [$this, 'init_frontend_hooks']);
        }
    }

    /**
     * Initialize frontend hooks based on settings
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function init_frontend_hooks()
    {
        $settings = get_option('cmfw_settings', array());
        $enable_meta = isset($settings['enable_meta']) ? $settings['enable_meta'] : '1';

        // Only proceed if meta is enabled
        if ($enable_meta !== '1') {
            return;
        }

        $position = isset($settings['meta_position']) ? $settings['meta_position'] : 'woocommerce_product_meta_end';

        // Force position for free version if Pro is not active
        if (!function_exists('cmfw_pro_is_active') || !cmfw_pro_is_active()) {
            $position = 'woocommerce_product_meta_end';
        }

        // Set priority based on position
        $priority = 25;
        if ($position === 'woocommerce_before_single_product_summary') {
            $priority = 15;
        }
        if ($position === 'woocommerce_before_add_to_cart_form') {
            $priority = 10;
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('CMFW Debug - Hooking into: ' . $position . ' with priority: ' . $priority);
        }
        
        // Add hook for displaying custom meta
        add_action($position, [$this, 'display_custom_meta'], $priority);

        // Add inline styles
        add_action('wp_enqueue_scripts', [$this, 'add_inline_styles'], 20);
    }

    /**
     * Display custom meta on single product pages
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function display_custom_meta()
    {

        // Only display on single product pages
        if (!is_product()) {
            return;
        }

        global $product;
        if (!$product || !is_a($product, 'WC_Product')) {
            return;
        }

        $groups = $this->get_matching_groups($product);

        if (empty($groups)) {
            return;
        }
        $settings = get_option('cmfw_settings', array());
        $show_heading = isset($settings['show_heading']) ? $settings['show_heading'] : '1';
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('CMFW Debug - show_heading: ' . $show_heading);
            error_log('CMFW Debug - settings: ' . print_r($settings, true));
        }
        
        $heading = '';

        if ($show_heading === '1') {
             $heading = (isset($settings['meta_heading']) && !empty($settings['meta_heading'])) 
                 ? $settings['meta_heading'] 
                 : __('Product Information', 'coderembassy-product-info-icons-images-text');
        }

        $this->render_custom_meta($groups, $heading);
    }

    /**
     * Get groups that match the current product's taxonomies
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     * @param WC_Product $product
     * @return array
     */
    private function get_matching_groups($product)
    {
        $saved_groups = cmfw_get_groups();
        $matching_groups = [];
        if (empty($saved_groups)) {
            return $matching_groups;
        }

        $product_id = $product->get_id();
        foreach ($saved_groups as $group) {
            $taxonomy = $group['taxonomy'] ?? '';
            $terms = $group['terms'] ?? [];
            $items = $group['items'] ?? [];

            // Skip if group has no items
            if (empty($items)) {
                continue;
            }

            // If no taxonomy is set, show for all products
            if (empty($taxonomy)) {
                $matching_groups[] = $group;
                continue;
            }

            // If no terms are set but taxonomy is, show for all products with that taxonomy
            if (empty($terms)) {
                if (taxonomy_exists($taxonomy)) {
                    $matching_groups[] = $group;
                }
                continue;
            }

            // Check if product has any of the specified terms
            $product_terms = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'ids'));

            if (!is_wp_error($product_terms) && !empty($product_terms)) {
                $intersect = array_intersect($terms, $product_terms);
                if (!empty($intersect)) {
                    $matching_groups[] = $group;
                }
            }
        }
        return $matching_groups;
    }

    /**
     * Render the custom meta HTML
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     * @param array $groups
     * @param string $heading
     */
    private function render_custom_meta($groups, $heading)
    {
        echo '<div class="cmfw-custom-meta-section">';

        if (!empty($heading)) {
            echo '<h3 class="cmfw-meta-heading">' . esc_html($heading) . '</h3>';
        }

        echo '<div class="cmfw-meta-groups">';

        foreach ($groups as $group_index => $group) {
            $items = $group['items'] ?? [];

            if (empty($items)) {
                continue;
            }

            echo '<div class="cmfw-meta-group" data-group="' . esc_attr($group_index) . '">';

            foreach ($items as $item_index => $item) {
                $title = $item['title'] ?? '';
                $subtitle = $item['subtitle'] ?? '';
                $icon = $item['icon'] ?? '';
                $image_id = $item['image_id'] ?? 0;

                if (empty($title)) {
                    continue;
                }

                $item_classes = apply_filters('cmfw_meta_item_class', 'cmfw-meta-item', $item, $item_index);
                echo '<div class="' . esc_attr($item_classes) . '" data-item="' . esc_attr($item_index) . '">';

                // Display icon or image
                if (!empty($icon)) {
                    if (strpos($icon, 'fa-') !== false) {
                        echo '<i class="cmfw-meta-icon ' . esc_attr($icon) . '"></i>';
                    } else {
                        echo '<span class="cmfw-meta-icon dashicons dashicons-' . esc_attr($icon) . '"></span>';
                    }
                } elseif (!empty($image_id) && intval($image_id) > 0) {
                    $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    if ($image_url) {
                        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" class="cmfw-meta-image" />';
                    }
                }

                echo '<div class="cmfw-meta-text-wrap">';
                    echo '<span class="cmfw-meta-title">' . esc_html($title) . '</span>';
                    if (!empty($subtitle) && function_exists('cmfw_pro_is_active') && cmfw_pro_is_active()) {
                        echo '<span class="cmfw-meta-subtitle">' . esc_html($subtitle) . '</span>';
                    }
                echo '</div>';
                echo '</div>';
            }

            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Add inline styles based on settings
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function add_inline_styles()
    {
        // Only add styles on single product pages
        if (! is_product()) {
            return;
        }

        $settings = get_option('cmfw_settings', []);
        $enable_meta = isset($settings['enable_meta']) ? $settings['enable_meta'] : '1';

        if ($enable_meta !== '1') {
            return;
        }

        $heading_color   = isset($settings['heading_color'])   ? sanitize_hex_color($settings['heading_color'])   : '#333333';
        $heading_size    = isset($settings['heading_size'])    ? absint($settings['heading_size'])                : 18;
        $meta_font_size  = isset($settings['meta_font_size'])  ? absint($settings['meta_font_size'])              : 14;
        $meta_text_color = isset($settings['meta_text_color']) ? sanitize_hex_color($settings['meta_text_color']) : '#666666';
        $meta_bg_color   = isset($settings['meta_bg_color'])   ? sanitize_hex_color($settings['meta_bg_color'])   : '#ffffff';
        $image_width     = isset($settings['image_width'])     ? absint($settings['image_width'])                 : 24;
        $image_height    = isset($settings['image_height'])    ? absint($settings['image_height'])                : 24;

        $custom_css = "
        .cmfw-meta-icon {
            width: " . esc_attr($image_width) . "px !important;
            height: " . esc_attr($image_height) . "px !important;
            font-size: " . esc_attr($image_width) . "px !important;
            min-width: " . esc_attr($image_width) . "px !important;
            min-height: " . esc_attr($image_height) . "px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .cmfw-meta-image {
            width: " . esc_attr($image_width) . "px !important;
            height: " . esc_attr($image_height) . "px !important;
            min-width: " . esc_attr($image_width) . "px !important;
            min-height: " . esc_attr($image_height) . "px !important;
            object-fit: contain !important;
        }
        .cmfw-custom-meta-section {
            background-color: " . esc_attr($meta_bg_color) . " !important;
            padding: 30px !important;
            margin: 40px 0 !important;
            border-radius: 8px !important;
            border: 1px solid #eee !important;
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .cmfw-meta-heading {
            color: " . esc_attr($heading_color) . " !important;
            font-size: " . esc_attr($heading_size) . "px !important;
            margin: 0 0 25px 0 !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
        }

        .cmfw-meta-groups {
            display: flex !important;
            flex-direction: column !important;
            gap: 30px !important;
        }

        .cmfw-meta-group {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 40px !important;
        }

        .cmfw-meta-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            gap: 12px !important;
            min-width: 120px !important;
            max-width: 200px !important;
        }

        .cmfw-meta-icon {
            color: " . esc_attr($meta_text_color) . " !important;
            font-size: 40px !important;
            width: 48px !important;
            height: 48px !important;
            line-height: 48px !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .cmfw-meta-image {
            width: 48px !important;
            height: 48px !important;
            object-fit: contain !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .cmfw-meta-title {
            color: " . esc_attr( $meta_text_color ) . " !important;
            font-size: " . esc_attr( $meta_font_size ) . "px !important;
            line-height: 1.4 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
        }

        .cmfw-meta-subtitle {
            color: " . esc_attr( $meta_text_color ) . " !important;
            font-size: " . esc_attr( floor($meta_font_size * 0.9) ) . "px !important;
            line-height: 1.4 !important;
            font-weight: 400 !important;
            opacity: 0.8 !important;
            margin-top: 4px !important;
        }

        @media (max-width: 768px) {
            .cmfw-meta-group {
                gap: 20px;
                justify-content: flex-start;
            }
            .cmfw-custom-meta-section {
                padding: 20px;
            }
        }
    ";

        // Attach inline CSS to your already registered/enqueued frontend stylesheet
        wp_add_inline_style('cmfw-frontend-css', $custom_css);
    }
}
