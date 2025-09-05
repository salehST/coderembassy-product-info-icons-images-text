<?php
defined('ABSPATH') or die('Nice Try!');

if (isset($_POST['save_cmfw']) && check_admin_referer('save_cmfw_data', 'cmfw_nonce')) {

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

    $cmfw_groups = [];

    if (is_array($raw_groups)) {
        foreach ($raw_groups as $group) {
            $taxonomy = '';
            $terms = [];
            $items = [];

            if (!empty($group['taxonomy'])) {
                $maybe_tax = sanitize_text_field($group['taxonomy']);
                if (in_array($maybe_tax, ['product_cat', 'product_tag'], true)) {
                    $taxonomy = $maybe_tax;
                }
            }

            if (!empty($group['terms']) && is_array($group['terms'])) {
                $terms = array_map('intval', (array) $group['terms']);
            }

            if (!empty($group['items']) && is_array($group['items'])) {
                foreach ($group['items'] as $item) {
                    $title = sanitize_text_field($item['title'] ?? '');
                    $icon = sanitize_text_field($item['icon'] ?? '');
                    $image_id = intval($item['image_id'] ?? 0);

                    // Save all items to maintain structure (free version has fixed 3 items)
                    $items[] = [
                        'title' => $title,
                        'icon' => $icon,
                        'image_id' => $image_id,
                    ];
                }
            }

            // Save group only if it has items
            if (!empty($items)) {
                $cmfw_groups[] = [
                    'taxonomy' => $taxonomy,
                    'terms' => $terms,
                    'items' => $items,
                ];
            }
        }
    }

    // Save groups using the new function
    cmfw_save_groups($cmfw_groups);

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
        <h1><?php echo esc_html__('PRODUCT INFO Groups', 'coderembassy-product-info-icons-images-text'); ?></h1>

        <?php
        // Get saved groups or create default structure
        $saved_groups = cmfw_get_groups();
        
        // Ensure we have at least one group for free version
        if (empty($saved_groups)) {
            $saved_groups = cmfw_apply_free_version_structure([]);
        }
        ?>

        <form method="post" action="" id="cmfw-save-form">
            <?php wp_nonce_field('save_cmfw_data', 'cmfw_nonce'); ?>
            <div id="cmfw-groups-container">
                <!-- Free version: Fixed structure with 1 group and 3 items -->
                <div class="cmfw-group cmfw-free-version-group" data-group-index="0">
                    <h2><?php echo esc_html__('PRODUCT INFO Group (Free Version)', 'coderembassy-product-info-icons-images-text'); ?></h2>
                    <p class="description"><?php echo esc_html__('Free version includes 1 group with 3 product info items. Upgrade to PRO to add more groups and items.', 'coderembassy-product-info-icons-images-text'); ?></p>
                    
                    <?php
                    // Allow pro version to add content before the group
                    $first_group = $saved_groups[0] ?? [];
                    do_action('cmfw_before_group_content', 0, $first_group);
                    ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label><?php echo esc_html__('Taxonomy', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                                <td>
                                    <select class="taxonomy-select" name="cmfw_groups[0][taxonomy]">
                                        <option value=""><?php echo esc_html__('Select taxonomy', 'coderembassy-product-info-icons-images-text'); ?></option>
                                        <option value="product_cat" <?php selected($first_group['taxonomy'] ?? '', 'product_cat'); ?>><?php echo esc_html__('Category', 'coderembassy-product-info-icons-images-text'); ?></option>
                                        <option value="product_tag" <?php selected($first_group['taxonomy'] ?? '', 'product_tag'); ?>><?php echo esc_html__('Tag', 'coderembassy-product-info-icons-images-text'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="term-row" style="<?php echo !empty($first_group['taxonomy']) ? '' : 'display:none;'; ?>">
                                <th scope="row"><label><?php echo esc_html__('Terms', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                                <td>
                                    <input type="text" class="term-search regular-text" name="" placeholder="<?php echo esc_attr__('Search terms...', 'coderembassy-product-info-icons-images-text'); ?>" />
                                    <div class="selected-terms">
                                        <?php
                                        if (!empty($first_group['terms'])) {
                                            $taxonomy = $first_group['taxonomy'] ?? '';
                                            foreach ($first_group['terms'] as $term_id) {
                                                $term_obj = get_term((int) $term_id, $taxonomy);
                                                if ($term_obj && !is_wp_error($term_obj)) {
                                                    echo '<span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;">';
                                                    echo esc_html($term_obj->name);
                                                    echo '<a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">&times;</a>';
                                                    echo '<input type="hidden" name="cmfw_groups[0][terms][]" value="' . esc_attr($term_id) . '">';
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
                            $items = $first_group['items'] ?? [];
                            for ($i = 0; $i < 3; $i++):
                                $item = $items[$i] ?? ['title' => '', 'icon' => '', 'image_id' => 0];
                            ?>
                            <div class="cmfw-item cmfw-free-item">
                                <h4><?php echo esc_html__('Product Info Item', 'coderembassy-product-info-icons-images-text'); ?> <?php echo $i + 1; ?></h4>
                                <div class="cmfw-excl-note"><?php echo esc_html__('Tip: Choose either an icon or an image (not both).', 'coderembassy-product-info-icons-images-text'); ?></div>
                                <p>
                                    <label><?php echo esc_html__('Title', 'coderembassy-product-info-icons-images-text'); ?><br>
                                        <input type="text" name="cmfw_groups[0][items][<?php echo $i; ?>][title]" class="regular-text" value="<?php echo esc_attr($item['title']); ?>" />
                                    </label>
                                </p>
                                <p class="cmfw-choose-note"><?php echo esc_html__('Select icon or image', 'coderembassy-product-info-icons-images-text'); ?></p>
                                <div class="cmfw-fields">
                                    <div class="cmfw-field">
                                        <label><?php echo esc_html__('Icon', 'coderembassy-product-info-icons-images-text'); ?><br>
                                            <div class="cmfw-icon-picker-container">
                                                <input type="hidden" name="cmfw_groups[0][items][<?php echo $i; ?>][icon]" class="cmfw-icon-value" value="<?php echo esc_attr($item['icon']); ?>" />
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
                                                <input type="hidden" name="cmfw_groups[0][items][<?php echo $i; ?>][image_id]" class="cmfw-image-value" value="<?php echo esc_attr($item['image_id']); ?>" />
                                                <div class="cmfw-image-preview cmfw-clickable" style="display: inline-block; margin-right: 10px; vertical-align: top;">
                                                    <img src="<?php echo !empty($item['image_id']) ? wp_get_attachment_image_url($item['image_id'], 'thumbnail') : ''; ?>" alt="Preview" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px; <?php echo !empty($item['image_id']) ? '' : 'display: none;'; ?>" />
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
                    // Allow pro version to add content after the group
                    do_action('cmfw_after_group_content', 0, $first_group);
                    ?>
                </div>
                
            <?php
                // Allow pro version to render its own dashboard
                do_action('cmfw_pro_dashboard_content', $saved_groups);
            ?>
            </div>
            <hr>
            <input type="submit" name="save_cmfw" class="button button-primary" value="<?php echo esc_attr__('Save', 'coderembassy-product-info-icons-images-text'); ?>">
        </form>
        
        <!-- Validation Error Display Area -->
        <div id="cmfw-validation-errors" style="display: none;" class="notice notice-error is-dismissible">
            <p><strong><?php echo esc_html__('Validation Errors:', 'coderembassy-product-info-icons-images-text'); ?></strong></p>
            <ul id="cmfw-error-list"></ul>
        </div>
    </div>

    <?php
    // Allow pro version to add its own templates
    do_action('cmfw_pro_templates');
    ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Add validation before form submission
    $('#cmfw-save-form').on('submit', function(e) {
        var hasValidItems = false;
        
        // Check all title inputs - at least one must have a title
        $('input[name*="[title]"]').each(function() {
            var title = $(this).val().trim();
            if (title !== '') {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems) {
            e.preventDefault();
            alert('<?php echo esc_js(__('Please enter at least one title before saving.', 'coderembassy-product-info-icons-images-text')); ?>');
            return false;
        }
        
        return true;
    });
    
    // Add visual feedback for empty title fields
    $('input[name*="[title]"]').on('blur', function() {
        var $input = $(this);
        var $item = $input.closest('.cmfw-item, .cmfw-free-item');
        
        if ($input.val().trim() === '') {
            $item.addClass('cmfw-item-empty');
        } else {
            $item.removeClass('cmfw-item-empty');
        }
    });
});
</script>

<style>
.cmfw-item-empty {
    border-left: 3px solid #ff6b6b !important;
    background-color: #fff5f5 !important;
}

.cmfw-item-empty input[name*="[title]"] {
    border-color: #ff6b6b !important;
    background-color: #fff5f5 !important;
}
</style>