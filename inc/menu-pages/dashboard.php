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

    // Check if PRO version is active for limits
    $pro_active = cmfw_is_pro_active();

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

                    if ($title !== '') {
                        $items[] = [
                            'title' => $title,
                            'icon' => $icon,
                            'image_id' => $image_id,
                        ];
                    }
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
        // Check if PRO version is active
        $pro_active = cmfw_is_pro_active();
        
        // Get saved groups or create default structure
        $saved_groups = cmfw_get_groups();
        $dashboard_config = cmfw_get_dashboard_config();
        ?>

        <form method="post" action="" id="cmfw-save-form">
            <?php wp_nonce_field('save_cmfw_data', 'cmfw_nonce'); ?>
            <div id="cmfw-groups-container">
                <?php if (!$pro_active): ?>
                    <!-- Free version: Fixed structure with 1 group and 3 items -->
                    <div class="cmfw-group cmfw-free-version-group" data-group-index="0">
                        <h2><?php echo esc_html__('PRODUCT INFO Group (Free Version)', 'coderembassy-product-info-icons-images-text'); ?></h2>
                        <p class="description"><?php echo esc_html__('Free version includes 1 group with 3 product info items. Upgrade to PRO to add more groups and items.', 'coderembassy-product-info-icons-images-text'); ?></p>
                        
                        <?php
                        // Allow pro version to add content before the group
                        do_action('cmfw_before_group_content', 0, $saved_groups[0] ?? []);
                        ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label><?php echo esc_html__('Taxonomy', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                                <td>
                                    <select class="taxonomy-select" name="cmfw_groups[0][taxonomy]">
                                        <option value=""><?php echo esc_html__('Select taxonomy', 'coderembassy-product-info-icons-images-text'); ?></option>
                                        <option value="product_cat" <?php selected($saved_groups[0]['taxonomy'] ?? '', 'product_cat'); ?>><?php echo esc_html__('Category', 'coderembassy-product-info-icons-images-text'); ?></option>
                                        <option value="product_tag" <?php selected($saved_groups[0]['taxonomy'] ?? '', 'product_tag'); ?>><?php echo esc_html__('Tag', 'coderembassy-product-info-icons-images-text'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="term-row" style="<?php echo !empty($saved_groups[0]['taxonomy']) ? '' : 'display:none;'; ?>">
                                <th scope="row"><label><?php echo esc_html__('Terms', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                                <td>
                                    <input type="text" class="term-search regular-text" name="" placeholder="<?php echo esc_attr__('Search terms...', 'coderembassy-product-info-icons-images-text'); ?>" />
                                    <div class="selected-terms">
                                        <?php
                                        if (!empty($saved_groups[0]['terms'])) {
                                            $taxonomy = $saved_groups[0]['taxonomy'] ?? '';
                                            foreach ($saved_groups[0]['terms'] as $term_id) {
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
                            $items = $saved_groups[0]['items'] ?? [];
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
                                                    <?php if (!empty($item['image_id'])): ?>
                                                        <?php echo wp_get_attachment_image($item['image_id'], 'thumbnail', false, ['style' => 'max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;']); ?>
                                                    <?php else: ?>
                                                        <div class="cmfw-no-image" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                                            <?php echo esc_html__('No image selected', 'coderembassy-product-info-icons-images-text'); ?>
                                                        </div>
                                                    <?php endif; ?>
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
                        do_action('cmfw_after_group_content', 0, $saved_groups[0] ?? []);
                        ?>
                    </div>
                <?php else: ?>
                    <!-- Pro version: Dynamic structure -->
                    <?php
                    // Allow pro version to render its own dashboard
                    do_action('cmfw_pro_dashboard_content', $saved_groups, $dashboard_config);
                    ?>
                <?php endif; ?>
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

    <!-- Templates -->
    <script type="text/html" id="cmfw-group-template">
        <div class="cmfw-group cmfw-cm-archive-group" data-group-index="_INDEX_">
            <button type="button" class="button cmfw-remove-group cmfw-archive-remove-cm-group" title="<?php echo esc_attr__('Remove Group', 'coderembassy-product-info-icons-images-text'); ?>"><span class="dashicons dashicons-no-alt"></span></button>
            <h2><?php echo esc_html__('PRODUCT INFO Group', 'coderembassy-product-info-icons-images-text'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label><?php echo esc_html__('Taxonomy', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                    <td>
                        <select class="taxonomy-select" name="cmfw_groups[_INDEX_][taxonomy]">
                            <option value=""><?php echo esc_html__('Select taxonomy', 'coderembassy-product-info-icons-images-text'); ?></option>
                            <option value="product_cat"><?php echo esc_html__('Category', 'coderembassy-product-info-icons-images-text'); ?></option>
                            <option value="product_tag"><?php echo esc_html__('Tag', 'coderembassy-product-info-icons-images-text'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="term-row" style="display:none;">
                    <th scope="row"><label><?php echo esc_html__('Terms', 'coderembassy-product-info-icons-images-text'); ?></label></th>
                    <td>
                        <input type="text" class="term-search regular-text" name="" placeholder="<?php echo esc_attr__('Search terms...', 'coderembassy-product-info-icons-images-text'); ?>" />
                        <div class="selected-terms"></div>
                    </td>
                </tr>
            </table>
            <div class="cmfw-items cmfw-archive-cm-items"></div>
            <p><button type="button" class="button cmfw-add-item" data-group-index="_INDEX_"><?php echo esc_html__('Add Product Info', 'coderembassy-product-info-icons-images-text'); ?></button></p>
        </div>
    </script>

    <script type="text/html" id="cmfw-item-template">
        <div class="cmfw-item cmfw-archive-cm-item">
            <button type="button" class="button cmfw-remove-item cmfw-archive-remove-cm-item" title="<?php echo esc_attr__('Remove Item', 'coderembassy-product-info-icons-images-text'); ?>"><span class="dashicons dashicons-no-alt"></span></button>
            <div class="cmfw-excl-note"><?php echo esc_html__('Tip: Choose either an icon or an image (not both).', 'coderembassy-product-info-icons-images-text'); ?></div>
            <p>
                <label><?php echo esc_html__('Title', 'coderembassy-product-info-icons-images-text'); ?><br>
                    <input type="text" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][title]" class="regular-text" />
                </label>
            </p>
            <p class="cmfw-choose-note"><?php echo esc_html__('Select icon or image', 'coderembassy-product-info-icons-images-text'); ?></p>
            <div class="cmfw-fields">
                <div class="cmfw-field">
                    <label><?php echo esc_html__('Icon', 'coderembassy-product-info-icons-images-text'); ?><br>
                        <div class="cmfw-icon-picker-container">
                            <input type="hidden" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][icon]" class="cmfw-icon-value" />
                            <div class="cmfw-icon-preview cmfw-clickable" style="display: inline-block; margin-right: 10px;">
                                <span class="dashicons" style="display:none; font-size: 24px; width: 24px; height: 24px;"></span>
                                <div class="cmfw-no-icon" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                    <?php echo esc_html__('No icon selected', 'coderembassy-product-info-icons-images-text'); ?>
                                </div>
                            </div>
                            <div style="display: inline-block; vertical-align: top;">
                                <button type="button" class="button cmfw-remove-icon" style="display:none; margin-left:5px;">&times;</button>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="cmfw-field">
                    <label><?php echo esc_html__('Image', 'coderembassy-product-info-icons-images-text'); ?><br>
                        <div class="cmfw-image-picker-container">
                            <input type="hidden" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][image_id]" class="cmfw-image-value" />
                            <div class="cmfw-image-preview cmfw-clickable" style="display: inline-block; margin-right: 10px; vertical-align: top;">
                                <img src="" alt="Preview" style="max-width: 100px; max-height: 100px; display: none; border: 1px solid #ddd; border-radius: 4px;" />
                                <div class="cmfw-no-image" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                    <?php echo esc_html__('No image selected', 'coderembassy-product-info-icons-images-text'); ?>
                                </div>
                            </div>
                            <div style="display: inline-block; vertical-align: top;">
                                <button type="button" class="button cmfw-remove-image" style="display: none; margin-left: 5px;">&times;</button>
                                <br><small style="color: #666; margin-top: 5px; display: block;">&nbsp;</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </script>
</div>