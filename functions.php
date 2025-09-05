<?php
defined('ABSPATH') or die('Nice Try!');

/**
 * Get CMFW data for display on frontend
 * @param string $archive_type The archive type (product_cat, product_tag)
 * @param array $term_ids Array of term IDs to match
 * @return array Array of custom meta data
 */
function cmfw_get_custom_meta_data($archive_type = '', $term_ids = [])
{
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
function cmfw_display_custom_meta($custom_meta_items = [], $args = [])
{
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
        $image_id = absint($item['image_id'] ?? 0);

        if (empty($question)) {
            continue;
        }

        // Sanitize classes
        $item_class  = ! empty($args['item_class']) ? sanitize_html_class($args['item_class']) : '';
        $icon_class  = ! empty($args['icon_class']) ? sanitize_html_class($args['icon_class']) : '';
        $image_class = ! empty($args['image_class']) ? sanitize_html_class($args['image_class']) : '';
        $title_class = ! empty($args['title_class']) ? sanitize_html_class($args['title_class']) : '';

        echo '<div class="' . esc_attr($item_class) . '">';

        // Display icon if available and enabled
        if (! empty($args['show_icons']) && ! empty($icon)) {
            echo '<span class="' . esc_attr($icon_class) . ' dashicons dashicons-' . esc_attr($icon) . '"></span>';
        }

        // Display image if available and enabled
        if (! empty($args['show_images']) && $image_id > 0) {
            $allowed_sizes = ['thumbnail', 'medium', 'large', 'full'];
            $image_size = in_array($args['image_size'], $allowed_sizes, true) ? $args['image_size'] : 'thumbnail';

            $image_html = wp_get_attachment_image(
                $image_id,
                $image_size,
                false,
                [
                    'class' => $image_class,
                    'alt'   => esc_attr($question),
                ]
            );

            if ($image_html) {
                echo wp_kses_post($image_html);
            }
        }

        // Display title
        echo '<span class="' . esc_attr($title_class) . '">' . esc_html($question) . '</span>';

        echo '</div>';
    }

    echo '</div>';
}

/**
 * Get custom meta for current product categories
 * @return array
 */
function cmfw_get_current_product_custom_meta()
{
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
 * Check if PRO version is active
 * @return bool
 */
function cmfw_is_pro_active()
{
    $pro_function_exists = function_exists('cmfw_pro_is_active');
    $pro_is_active = $pro_function_exists ? cmfw_pro_is_active() : false;
    return $pro_function_exists && $pro_is_active;
}

/**
 * Apply free version structure - 1 group with 3 items maximum
 * @param array $groups
 * @return array
 */
function cmfw_apply_free_version_structure($groups) {
    // Ensure we have exactly 1 group
    if (empty($groups)) {
        // Create default group with 3 empty items
        $groups = [[
            'taxonomy' => '',
            'terms' => [],
            'items' => [
                ['title' => '', 'icon' => '', 'image_id' => 0],
                ['title' => '', 'icon' => '', 'image_id' => 0],
                ['title' => '', 'icon' => '', 'image_id' => 0]
            ]
        ]];
    } else {
        // Take only the first group and limit to 3 items
        $first_group = $groups[0];
        $first_group['items'] = array_slice($first_group['items'], 0, 3);
        
        // Ensure we have exactly 3 items
        while (count($first_group['items']) < 3) {
            $first_group['items'][] = ['title' => '', 'icon' => '', 'image_id' => 0];
        }
        
        $groups = [$first_group];
    }
    
    return $groups;
}

/**
 * Get groups with proper structure based on version
 * @return array
 */
function cmfw_get_groups() {
    $groups = get_option('cmfw_groups', []);
    
    // Apply free version structure if pro is not active
    if (!cmfw_is_pro_active()) {
        $groups = cmfw_apply_free_version_structure($groups);
    }
    
    // Allow pro version to modify groups
    return apply_filters('cmfw_get_groups', $groups);
}

/**
 * Save groups with proper validation
 * @param array $groups
 * @return bool
 */
function cmfw_save_groups($groups) {
    // Apply free version structure if pro is not active
    if (!cmfw_is_pro_active()) {
        $groups = cmfw_apply_free_version_structure($groups);
    }
    
    // Allow pro version to modify groups before saving
    $groups = apply_filters('cmfw_save_groups', $groups);
    
    return update_option('cmfw_groups', $groups);
}

/**
 * Get maximum groups allowed
 * @return int
 */
function cmfw_get_max_groups() {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_max_groups', 999); // Unlimited for pro
    }
    return 1; // Free version limit
}

/**
 * Get maximum items per group
 * @return int
 */
function cmfw_get_max_items_per_group() {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_max_items_per_group', 999); // Unlimited for pro
    }
    return 3; // Free version limit
}

/**
 * Check if user can add groups
 * @return bool
 */
function cmfw_can_add_groups() {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_can_add_groups', true);
    }
    return false; // Free version cannot add groups
}

/**
 * Check if user can add items to group
 * @param int $group_index
 * @return bool
 */
function cmfw_can_add_items($group_index = 0) {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_can_add_items', true, $group_index);
    }
    
    // Free version can only add items to the first group if it has less than 3 items
    $groups = cmfw_get_groups();
    if ($group_index === 0 && isset($groups[0])) {
        return count($groups[0]['items']) < 3;
    }
    
    return false;
}

/**
 * Check if user can remove groups
 * @return bool
 */
function cmfw_can_remove_groups() {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_can_remove_groups', true);
    }
    return false; // Free version cannot remove groups
}

/**
 * Check if user can remove items from group
 * @param int $group_index
 * @return bool
 */
function cmfw_can_remove_items($group_index = 0) {
    if (cmfw_is_pro_active()) {
        return apply_filters('cmfw_can_remove_items', true, $group_index);
    }
    
    // Free version cannot remove items (always keep 3 items)
    return false;
}

/**
 * Get dashboard UI configuration
 * @return array
 */
function cmfw_get_dashboard_config() {
    $config = [
        'can_add_groups' => cmfw_can_add_groups(),
        'can_remove_groups' => cmfw_can_remove_groups(),
        'can_add_items' => cmfw_can_add_items(),
        'can_remove_items' => cmfw_can_remove_items(),
        'max_groups' => cmfw_get_max_groups(),
        'max_items_per_group' => cmfw_get_max_items_per_group(),
        'is_pro_active' => cmfw_is_pro_active(),
        'free_version_limits' => [
            'groups' => 1,
            'items_per_group' => 3
        ]
    ];
    
    return apply_filters('cmfw_dashboard_config', $config);
}