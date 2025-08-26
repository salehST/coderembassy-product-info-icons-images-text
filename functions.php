<?php
defined('ABSPATH') or die('Nice Try!');

/**
 * Only for developer
 * @author Fazle Bari
 */
if (! function_exists('dd')) {
    function dd(...$vals)
    {
        if (! empty($vals) && is_array($vals)) {
            ob_start(); // Start output buffering
            foreach ($vals as $val) {
                echo "<pre>";
                var_dump($val);
                echo "</pre>";
            }
            $output = ob_get_clean(); // Get the buffered output and clear the buffer
            echo $output; // Output the buffered content
        }
    }
}

// Write all your custom codes here if you don't want to use OOP

/**
 * Get CMFW data for display on frontend
 * @param string $archive_type The archive type (product_cat, product_tag)
 * @param array $term_ids Array of term IDs to match
 * @return array Array of custom meta data
 */
function cmfw_get_custom_meta_data($archive_type = '', $term_ids = []) {
    $saved_data = get_option('woo_afaq_global_groups', []);
    $matching_data = [];
    
    if (empty($saved_data) || empty($archive_type) || empty($term_ids)) {
        return $matching_data;
    }
    
    foreach ($saved_data as $group) {
        if ($group['archive_type'] === $archive_type) {
            $group_terms = $group['archive_terms'] ?? [];
            // Check if any of the provided term IDs match the group's terms
            if (array_intersect($term_ids, $group_terms)) {
                $matching_data = array_merge($matching_data, $group['faqs'] ?? []);
            }
        }
    }
    
    return $matching_data;
}

/**
 * Display custom meta items with icons and images
 * @param array $custom_meta_items Array of custom meta items
 * @param array $args Display arguments
 */
function cmfw_display_custom_meta($custom_meta_items = [], $args = []) {
    if (empty($custom_meta_items)) {
        return;
    }
    
    $defaults = [
        'container_class' => 'cmfw-custom-meta-container',
        'item_class' => 'cmfw-custom-meta-item',
        'title_class' => 'cmfw-meta-title',
        'icon_class' => 'cmfw-meta-icon',
        'image_class' => 'cmfw-meta-image',
        'show_icons' => true,
        'show_images' => true,
        'image_size' => 'thumbnail'
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    echo '<div class="' . esc_attr($args['container_class']) . '">';
    
    foreach ($custom_meta_items as $item) {
        $question = $item['question'] ?? '';
        $icon = $item['icon'] ?? '';
        $image_id = $item['image_id'] ?? 0;
        
        if (empty($question)) {
            continue;
        }
        
        echo '<div class="' . esc_attr($args['item_class']) . '">';
        
        // Display icon if available and enabled
        if ($args['show_icons'] && !empty($icon)) {
            echo '<span class="' . esc_attr($args['icon_class']) . ' dashicons dashicons-' . esc_attr($icon) . '"></span>';
        }
        
        // Display image if available and enabled
        if ($args['show_images'] && $image_id > 0) {
            $image_html = wp_get_attachment_image($image_id, $args['image_size'], false, [
                'class' => $args['image_class'],
                'alt' => esc_attr($question)
            ]);
            if ($image_html) {
                echo $image_html;
            }
        }
        
        // Display title
        echo '<span class="' . esc_attr($args['title_class']) . '">' . esc_html($question) . '</span>';
        
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Get custom meta for current product categories
 * @return array
 */
function cmfw_get_current_product_custom_meta() {
    if (!is_product()) {
        return [];
    }
    
    global $product;
    if (!$product) {
        return [];
    }
    
    $product_id = $product->get_id();
    $category_ids = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
    $tag_ids = wp_get_post_terms($product_id, 'product_tag', ['fields' => 'ids']);
    
    $custom_meta = [];
    
    // Get custom meta for categories
    if (!empty($category_ids)) {
        $cat_meta = cmfw_get_custom_meta_data('product_cat', $category_ids);
        $custom_meta = array_merge($custom_meta, $cat_meta);
    }
    
    // Get custom meta for tags
    if (!empty($tag_ids)) {
        $tag_meta = cmfw_get_custom_meta_data('product_tag', $tag_ids);
        $custom_meta = array_merge($custom_meta, $tag_meta);
    }
    
    return $custom_meta;
}

/**
 * Auto display custom meta on single product page
 * Hook this function to display custom meta automatically
 */
function cmfw_auto_display_product_custom_meta() {
    $custom_meta = cmfw_get_current_product_custom_meta();
    
    if (!empty($custom_meta)) {
        cmfw_display_custom_meta($custom_meta, [
            'container_class' => 'cmfw-product-custom-meta',
            'item_class' => 'cmfw-product-meta-item',
        ]);
    }
}

// Uncomment the line below to automatically display custom meta on single product pages
// add_action('woocommerce_single_product_summary', 'cmfw_auto_display_product_custom_meta', 25);

/**
 * Check if PRO version is active
 * @return bool
 */
function cmfw_is_pro_active() {
    return function_exists('cmfw_pro_is_active') && cmfw_pro_is_active();
}