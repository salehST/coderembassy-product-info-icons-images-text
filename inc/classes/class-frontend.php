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

        $position = isset($settings['meta_position']) ? $settings['meta_position'] : 'woocommerce_product_additional_information';

        // Set priority based on position
        $priority = 25;
        if ($position === 'woocommerce_before_single_product_summary') {
            $priority = 15;
        }
        if ($position === 'woocommerce_before_add_to_cart_form') {
            $priority = 10;
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
        $heading = isset($settings['meta_heading']) ? $settings['meta_heading'] : __('Product Information', 'coderembassy-product-info-icons-images-text');

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
        $saved_groups = get_option('cmfw_groups', []);
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
                $icon = $item['icon'] ?? '';
                $image_id = $item['image_id'] ?? 0;

                if (empty($title)) {
                    continue;
                }

                echo '<div class="cmfw-meta-item" data-item="' . esc_attr($item_index) . '">';

                // Display icon or image
                if (!empty($icon)) {
                    echo '<span class="cmfw-meta-icon dashicons dashicons-' . esc_attr($icon) . '"></span>';
                } elseif (!empty($image_id) && intval($image_id) > 0) {
                    $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    if ($image_url) {
                        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" class="cmfw-meta-image" />';
                    }
                }

                echo '<span class="cmfw-meta-title">' . esc_html($title) . '</span>';
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

        $heading_color   = $settings['heading_color']   ?? '#333333';
        $heading_size    = $settings['heading_size']    ?? '18';
        $meta_font_size  = $settings['meta_font_size']  ?? '14';
        $meta_text_color = $settings['meta_text_color'] ?? '#666666';
        $meta_bg_color   = $settings['meta_bg_color']   ?? '#ffffff';

        $custom_css = "
        .cmfw-custom-meta-section {
            background-color: {$meta_bg_color};
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .cmfw-meta-heading {
            color: {$heading_color};
            font-size: {$heading_size}px;
            margin: 0 0 15px 0;
            font-weight: 600;
            line-height: 1.4;
        }
        
        .cmfw-meta-groups {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .cmfw-meta-group {
            flex: 1;
            min-width: 200px;
        }
        
        .cmfw-meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
        }
        
        .cmfw-meta-icon {
            color: {$meta_text_color};
            font-size: " . ((int) $meta_font_size + 4) . "px;
            margin-right: 10px;
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }
        
        .cmfw-meta-image {
            max-width: 24px;
            max-height: 24px;
            margin-right: 10px;
            border-radius: 3px;
            flex-shrink: 0;
        }
        
        .cmfw-meta-title {
            color: {$meta_text_color};
            font-size: {$meta_font_size}px;
            line-height: 1.5;
            font-weight: 400;
        }
        
        @media (max-width: 768px) {
            .cmfw-meta-groups {
                flex-direction: column;
            }
            .cmfw-meta-group {
                min-width: 100%;
            }
            .cmfw-custom-meta-section {
                padding: 15px;
            }
        }
    ";

        // Attach inline CSS to your already registered/enqueued frontend stylesheet
        wp_add_inline_style('cmfw-frontend-css', $custom_css);
    }
}
