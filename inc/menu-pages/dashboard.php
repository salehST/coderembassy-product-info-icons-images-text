<?php
defined('ABSPATH') or die('Nice Try!');

/**
 * Render group content (taxonomy, terms, items)
 * @param int $group_index
 * @param array $group_data
 */
function cmfw_render_group_content($group_index, $group_data) {
    // Check if this is a PRO group (not the first group)
    $is_pro_group = $group_index > 0;
    ?>
    <?php if ($is_pro_group): ?>
    <div style="text-align: right; margin-bottom: 10px;">
        <button type="button" class="button cmfw-remove-group" title="<?php echo esc_attr__('Remove Group', 'coderembassy-product-info-icons-images-text'); ?>" style="color: #a00; border-color: #a00;">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
    </div>
    <?php endif; ?>
    <table class="form-table">
        <tr>
            <th scope="row" class="taxonomy-type"><label><?php echo esc_html__('Select Taxonomy Type', 'coderembassy-product-info-icons-images-text'); ?></label></th>
            <td>
                <select class="taxonomy-select" name="cmfw_groups[<?php echo esc_attr($group_index); ?>][taxonomy]">
                    <option value=""><?php echo esc_html__('Select taxonomy', 'coderembassy-product-info-icons-images-text'); ?></option>
                    <option value="product_cat" <?php selected($group_data['taxonomy'] ?? '', 'product_cat'); ?>><?php echo esc_html__('Category', 'coderembassy-product-info-icons-images-text'); ?></option>
                    <option value="product_tag" <?php selected($group_data['taxonomy'] ?? '', 'product_tag'); ?>><?php echo esc_html__('Tag', 'coderembassy-product-info-icons-images-text'); ?></option>
                </select>
            </td>
        </tr>
        <tr class="term-row" style="<?php echo !empty($group_data['taxonomy']) ? '' : 'display:none;'; ?>">
            <th scope="row" class="terms-type"><label><?php echo esc_html__('Select category/tags', 'coderembassy-product-info-icons-images-text'); ?></label></th>
            <td>
                <input type="text" class="term-search regular-text" name="" placeholder="<?php echo esc_attr__('Search terms...', 'coderembassy-product-info-icons-images-text'); ?>" />
                <div class="selected-terms">
                    <?php
                    if (!empty($group_data['terms'])) {
                        $taxonomy = $group_data['taxonomy'] ?? '';
                        foreach ($group_data['terms'] as $term_id) {
                            $term_obj = get_term((int) $term_id, $taxonomy);
                            if ($term_obj && !is_wp_error($term_obj)) {
                                echo '<span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;">';
                                echo esc_html($term_obj->name);
                                echo '<a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">&times;</a>';
                                echo '<input type="hidden" name="cmfw_groups[' . esc_attr($group_index) . '][terms][]" value="' . esc_attr($term_id) . '">';
                                echo '</span>';
                            }
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
    
    <div class="cmfw-items">
        <?php
        $items = $group_data['items'] ?? [];
        $max_items = function_exists('cmfw_pro_is_active') && cmfw_pro_is_active() ? count($items) : 3;
        
        for ($i = 0; $i < $max_items; $i++):
            $item = $items[$i] ?? ['title' => '', 'icon' => '', 'image_id' => 0];
        ?>
        <div class="cmfw-item cmfw-item-wrap">
            <button type="button" class="button cmfw-remove-item" title="<?php echo esc_attr__('Remove Item', 'coderembassy-product-info-icons-images-text'); ?>" style="color: #a00; border-color: #a00;">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
            <h4>
                <?php echo esc_html__('Product Info Item', 'coderembassy-product-info-icons-images-text'); ?>
                <?php echo esc_html($i + 1); ?>
            </h4>
            <div class="cmfw-excl-note"><?php echo esc_html__('Tip: Choose either an icon or an image (not both).', 'coderembassy-product-info-icons-images-text'); ?></div>
            <p>
                <label><?php echo esc_html__('Product Info Text', 'coderembassy-product-info-icons-images-text'); ?><br>
                    <input type="text" name="cmfw_groups[<?php echo esc_attr($group_index); ?>][items][<?php echo esc_attr($i); ?>][title]" class="regular-text" value="<?php echo esc_attr($item['title']); ?>" />
                </label>
            </p>
            <p class="cmfw-choose-note"><?php echo esc_html__('Select icon or image', 'coderembassy-product-info-icons-images-text'); ?></p>
            <div class="cmfw-fields">
                <div class="cmfw-field">
                    <label><?php echo esc_html__('Icon', 'coderembassy-product-info-icons-images-text'); ?><br>
                        <div class="cmfw-icon-picker-container">
                            <input type="hidden" name="cmfw_groups[<?php echo esc_attr($group_index); ?>][items][<?php echo esc_attr($i); ?>][icon]" class="cmfw-icon-value" value="<?php echo esc_attr($item['icon']); ?>" />
                            <div class="cmfw-icon-preview cmfw-clickable" style="display: inline-block; margin-right: 10px;">
                                <span class="dashicons <?php echo !empty($item['icon']) ? 'dashicons-' . esc_attr($item['icon']) : ''; ?>" style="<?php echo !empty($item['icon']) ? 'font-size: 24px; width: 24px; height: 24px;' : 'display:none; font-size: 24px; width: 24px; height: 24px;'; ?>"></span>
                                <div class="cmfw-no-icon" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: <?php echo !empty($item['icon']) ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                    <?php echo esc_html__('No icon selected', 'coderembassy-product-info-icons-images-text'); ?>
                                </div>
                            </div>
                            <div style="display: inline-block; vertical-align: top;">
                                <button type="button" class="button cmfw-remove-icon" style="<?php echo !empty($item['icon']) ? '' : 'display:none;'; ?> margin-left:5px;">&times;</button>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="cmfw-field">
                    <label><?php echo esc_html__('Image', 'coderembassy-product-info-icons-images-text'); ?><br>
                        <div class="cmfw-image-picker-container" data-image-id="<?php echo esc_attr($item['image_id']); ?>">
                            <input type="hidden" name="cmfw_groups[<?php echo esc_attr($group_index); ?>][items][<?php echo esc_attr($i); ?>][image_id]" class="cmfw-image-value" value="<?php echo esc_attr($item['image_id']); ?>" />
                            <div class="cmfw-image-preview cmfw-clickable" style="display: inline-block; margin-right: 10px; vertical-align: top;">
                                <?php
                                if ( ! empty( $item['image_id'] ) ) {
                                    echo wp_get_attachment_image( $item['image_id'], 'thumbnail', false, [
                                        'alt'   => esc_attr__('Preview', 'coderembassy-product-info-icons-images-text'),
                                        'style' => 'max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;',
                                    ] );
                                }
                                ?>
                                <div class="cmfw-no-image" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: <?php echo !empty($item['image_id']) ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                    <?php echo esc_html__('No image selected', 'coderembassy-product-info-icons-images-text'); ?>
                                </div>
                            </div>
                            <div style="display: inline-block; vertical-align: top;">
                                <button type="button" class="button cmfw-remove-image" style="<?php echo !empty($item['image_id']) ? '' : 'display: none;'; ?> margin-left: 5px;">&times;</button>
                                <br><small style="color: #666; margin-top: 5px; display: block;">&nbsp;</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <?php endfor; ?>
        
    </div>
    
    <?php
}

if (isset($_POST['save_cmfw']) && check_admin_referer('save_cmfw_data', 'cmfw_nonce')) {
    
    // Debug: Log that form was submitted
    error_log('CMFW Debug - Form submitted successfully');

    // Validation function
    function coderembassy_sanitize_recursive($data) {
        if (is_array($data)) {
            return array_map('coderembassy_sanitize_recursive', $data);
        } else {
            return sanitize_text_field($data);
        }
    }

    $input_groups = filter_input( INPUT_POST, 'cmfw_groups', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
    $raw_groups   = $input_groups ? coderembassy_sanitize_recursive( $input_groups ) : [];
    
    // Debug: Log input data
    error_log('CMFW Debug - Input groups: ' . print_r($raw_groups, true));

    $cmfw_groups = [];

    if (is_array($raw_groups)) {
        foreach ($raw_groups as $group) {
            $taxonomy = '';
            $terms = [];
            $items = [];

            // Get taxonomy
            if (!empty($group['taxonomy'])) {
                $maybe_tax = sanitize_text_field($group['taxonomy']);
                if (in_array($maybe_tax, ['product_cat', 'product_tag'], true)) {
                    $taxonomy = $maybe_tax;
                }
            }

            // Get terms
            if (!empty($group['terms']) && is_array($group['terms'])) {
                $terms = array_map('intval', (array) $group['terms']);
            }

            // Get items - always process all items
            if (!empty($group['items']) && is_array($group['items'])) {
                foreach ($group['items'] as $item) {
                    $title = sanitize_text_field($item['title'] ?? '');
                    $icon = sanitize_text_field($item['icon'] ?? '');
                    $image_id = intval($item['image_id'] ?? 0);

                    $items[] = [
                        'title' => $title,
                        'icon' => $icon,
                        'image_id' => $image_id,
                    ];
                }
            }

            // Always save the group - free version structure
                $cmfw_groups[] = [
                    'taxonomy' => $taxonomy,
                    'terms' => $terms,
                    'items' => $items,
                ];
            }
        }
    
    // Always ensure we have at least one group for free version
    if (empty($cmfw_groups)) {
        $cmfw_groups = [[
            'taxonomy' => '',
            'terms' => [],
            'items' => [
                ['title' => 'Test Item 1', 'icon' => 'star', 'image_id' => 0],
                ['title' => 'Test Item 2', 'icon' => 'heart', 'image_id' => 0],
                ['title' => 'Test Item 3', 'icon' => 'check', 'image_id' => 0]
            ]
        ]];
    }

    // Debug: Log data being saved
    error_log('CMFW Debug - Data being saved: ' . print_r($cmfw_groups, true));

    // Save groups using the new function
    $save_result = cmfw_save_groups($cmfw_groups);
    
    // Debug: Log save result
    error_log('CMFW Debug - Save result: ' . ($save_result ? 'SUCCESS' : 'FAILED'));

    // Add success message
    if ($save_result) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 esc_html__('Product Info data saved successfully!', 'coderembassy-product-info-icons-images-text') . 
                 '</p></div>';
        });
    }

    // Redirect back to the same page
    wp_redirect(admin_url('admin.php?page=coderembassy-product-info-icons-images-text'));
    exit;
}


// Show settings reminder
$settings = get_option('cmfw_settings', array());
$enable_meta = isset($settings['enable_meta']) ? $settings['enable_meta'] : '1';
if ($enable_meta !== '1') {
    echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf(
        esc_html__('Custom Meta is currently disabled. Enable it in Settings to display on product pages.', 'coderembassy-product-info-icons-images-text'),
        '<a href="' . esc_url(admin_url('admin.php?page=coderembassy-meta-settings')) . '">',
        '</a>'
    ) . '</p></div>';
}
?>

<div class="wrap">
    <div class="cmfw-groups">
       <header>
           <h1><?php echo esc_html__('Product Info Group', 'coderembassy-product-info-icons-images-text'); ?></h1>
       </header>

        <?php
        // Get saved groups or create default structure
        $saved_groups = cmfw_get_groups();
        
        // Ensure we have at least one group for free version
        if (empty($saved_groups)) {
            $saved_groups = cmfw_apply_free_version_structure([]);
        }
        
        ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=coderembassy-product-info-icons-images-text')); ?>" id="cmfw-save-form">
            <?php wp_nonce_field('save_cmfw_data', 'cmfw_nonce'); ?>
            <div id="cmfw-groups-container">
                    <?php
                // Check if PRO version is active
                $is_pro_active = function_exists('cmfw_pro_is_active') && cmfw_pro_is_active();
                
                if ($is_pro_active) {
                    // PRO version: Display all groups
                    foreach ($saved_groups as $group_index => $group_data) {
                        echo '<div class="cmfw-group cmfw-group-wrap" data-group-index="' . esc_attr($group_index) . '">';
                        if ($group_index === 0) {
                            echo '<h2 class="header_sction">' . esc_html__('Product Info Group', 'coderembassy-product-info-icons-images-text') . ' ' . ($group_index + 1) . ' <span style="color: #666; font-size: 0.8em;">' . esc_html__('', 'coderembassy-product-info-icons-images-text') . '</span></h2>';
                        } else {
                            echo '<h2 class="header_sction">' . esc_html__('Product Info Group', 'coderembassy-product-info-icons-images-text') . ' ' . ($group_index + 1) . ' <span style="color: #0073aa; font-size: 0.8em;">' . esc_html__('', 'coderembassy-product-info-icons-images-text') . '</span></h2>';
                        }
                        
                        // Allow pro version to add content before the group
                        do_action('cmfw_before_group_content', $group_index, $group_data);
                        
                        // Display group content
                        cmfw_render_group_content($group_index, $group_data);
                        
                        // Allow pro version to add content after the group
                        do_action('cmfw_after_group_content', $group_index, $group_data);
                        
                        echo '</div>';
                    }
                } else {
                    // Free version: Fixed structure with 1 group and 3 items
                    $first_group = $saved_groups[0] ?? [];
                    echo '<div class="cmfw-group cmfw-group-wrap" data-group-index="0">';
                    echo '<h2>' . esc_html__('Product Info Group', 'coderembassy-product-info-icons-images-text') . ' <span style="color: #666; font-size: 0.8em;">(' . esc_html__('Free Version', 'coderembassy-product-info-icons-images-text') . ')</span></h2>';
                    
                    // Allow pro version to add content before the group
                    do_action('cmfw_before_group_content', 0, $first_group);
                    
                    // Display group content
                    cmfw_render_group_content(0, $first_group);
                    
                    // Allow pro version to add content after the group
                    do_action('cmfw_after_group_content', 0, $first_group);
                    
                    echo '</div>';
                }
                
                // Add "Add New Group" button after all groups when PRO is active
                if ($is_pro_active) {
                    do_action('cmfw_pro_group_actions', 0, []);
                }
            ?>
            </div>
            <hr>
            <input type="submit" name="save_cmfw" class="button button-primary" value="<?php echo esc_attr__('Save', 'coderembassy-product-info-icons-images-text'); ?>">
            <p class="description"><?php //echo esc_html__('Click Save to save your Product Info data. No validation required.', 'coderembassy-product-info-icons-images-text'); ?></p>
        </form>
        
    </div>

    <?php
    // Allow pro version to add its own templates
    do_action('cmfw_pro_templates');
    ?>
</div>
