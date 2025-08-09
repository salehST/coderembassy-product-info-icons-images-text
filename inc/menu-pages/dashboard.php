<?php
if (isset($_POST['save_cmfw']) && check_admin_referer('save_cmfw_data', 'cmfw_nonce')) {
    $raw_groups = $_POST['cmfw_groups'] ?? [];

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

    update_option('cmfw_groups', $cmfw_groups);

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Custom Meta groups saved successfully!', 'custom-meta-for-woocommerce') . '</p></div>';
}
?>

<div class="wrap">
    <div class="cmfw-groups">
        <h1><?php echo esc_html__('Custom Meta Groups', 'custom-meta-for-woocommerce'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('save_cmfw_data', 'cmfw_nonce'); ?>
            <div id="cmfw-groups-container"></div>
            <p><button type="button" class="button cmfw-add-group"><?php echo esc_html__('Add New Group', 'custom-meta-for-woocommerce'); ?></button></p>
            <hr>
            <input type="submit" name="save_cmfw" class="button button-primary" value="<?php echo esc_attr__('Save', 'custom-meta-for-woocommerce'); ?>">
        </form>
    </div>
    
    <!-- Templates -->
    <script type="text/html" id="cmfw-group-template">
        <div class="cmfw-group" data-group-index="_INDEX_">
            <button type="button" class="button cmfw-remove-group" title="<?php echo esc_attr__('Remove Group', 'custom-meta-for-woocommerce'); ?>"><span class="dashicons dashicons-no-alt"></span></button>
            <h2><?php echo esc_html__('Custom Meta Group', 'custom-meta-for-woocommerce'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label><?php echo esc_html__('Taxonomy', 'custom-meta-for-woocommerce'); ?></label></th>
                    <td>
                        <select class="taxonomy-select" name="cmfw_groups[_INDEX_][taxonomy]">
                            <option value=""><?php echo esc_html__('Select taxonomy', 'custom-meta-for-woocommerce'); ?></option>
                            <option value="product_cat"><?php echo esc_html__('Category', 'custom-meta-for-woocommerce'); ?></option>
                            <option value="product_tag"><?php echo esc_html__('Tag', 'custom-meta-for-woocommerce'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="term-row" style="display:none;">
                    <th scope="row"><label><?php echo esc_html__('Terms', 'custom-meta-for-woocommerce'); ?></label></th>
                    <td>
                        <input type="text" class="term-search regular-text" name="" placeholder="<?php echo esc_attr__('Search terms...', 'custom-meta-for-woocommerce'); ?>" />
                        <div class="selected-terms"></div>
                    </td>
                </tr>
            </table>
            <div class="cmfw-items"></div>
            <p><button type="button" class="button cmfw-add-item"><?php echo esc_html__('Add Custom Meta', 'custom-meta-for-woocommerce'); ?></button></p>
        </div>
    </script>

    <script type="text/html" id="cmfw-item-template">
        <div class="cmfw-item">
            <button type="button" class="button cmfw-remove-item" title="<?php echo esc_attr__('Remove Item', 'custom-meta-for-woocommerce'); ?>"><span class="dashicons dashicons-no-alt"></span></button>
            <p>
                <label><?php echo esc_html__('Title', 'custom-meta-for-woocommerce'); ?><br>
                    <input type="text" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][title]" class="regular-text" />
                </label>
            </p>
            <p>
                <label><?php echo esc_html__('Icon', 'custom-meta-for-woocommerce'); ?><br>
                    <div class="cmfw-icon-picker-container">
                        <input type="hidden" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][icon]" class="cmfw-icon-value" />
                        <div class="cmfw-icon-preview" style="display: inline-block; margin-right: 10px;">
                            <span class="dashicons dashicons-admin-generic" style="font-size: 24px; width: 24px; height: 24px;"></span>
                        </div>
                        <button type="button" class="button cmfw-open-icon-picker"><?php echo esc_html__('Select Icon', 'custom-meta-for-woocommerce'); ?></button>
                    </div>
                </label>
            </p>
            <p>
                <label><?php echo esc_html__('Image', 'custom-meta-for-woocommerce'); ?><br>
                    <div class="cmfw-image-picker-container">
                        <input type="hidden" name="cmfw_groups[_GROUP_INDEX_][items][_ITEM_INDEX_][image_id]" class="cmfw-image-value" />
                        <div class="cmfw-image-preview" style="display: inline-block; margin-right: 10px; vertical-align: top;">
                            <img src="" alt="Preview" style="max-width: 100px; max-height: 100px; display: none; border: 1px solid #ddd; border-radius: 4px;" />
                            <div class="cmfw-no-image" style="width: 100px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px; text-align: center; border-radius: 4px;">
                                <?php echo esc_html__('No image selected', 'custom-meta-for-woocommerce'); ?>
                            </div>
                        </div>
                        <div style="display: inline-block; vertical-align: top;">
                            <button type="button" class="button cmfw-select-image"><?php echo esc_html__('Select Image', 'custom-meta-for-woocommerce'); ?></button>
                            <button type="button" class="button cmfw-remove-image" style="display: none; margin-left: 5px;"><?php echo esc_html__('Remove', 'custom-meta-for-woocommerce'); ?></button>
                            <br><small style="color: #666; margin-top: 5px; display: block;"><?php echo esc_html__('Recommended size: 100x100px', 'custom-meta-for-woocommerce'); ?></small>
                        </div>
                    </div>
                </label>
            </p>
        </div>
    </script>

    <?php
    $saved_groups = get_option('cmfw_groups', []);
    // Attach term names for pills
    if (!empty($saved_groups)) {
        foreach ($saved_groups as &$group) {
            $taxonomy = $group['taxonomy'] ?? '';
            $term_names = [];
            if (!empty($group['terms']) && taxonomy_exists($taxonomy)) {
                foreach ((array) $group['terms'] as $term_id) {
                    $term_obj = get_term((int) $term_id, $taxonomy);
                    if ($term_obj && !is_wp_error($term_obj)) {
                        $term_names[(int) $term_id] = esc_html($term_obj->name);
                    }
                }
            }
            $group['term_names'] = $term_names;
        }
        unset($group);
    }
    ?>
    <script>
        jQuery(document).ready(function($) {
            const groupTemplate = $('#cmfw-group-template').html();
            const itemTemplate = $('#cmfw-item-template').html();

            // Render saved groups/items
            const savedGroups = <?php echo wp_json_encode($saved_groups); ?>;
            $.each(savedGroups, function(gIndex, group) {
                let groupHtml = groupTemplate.replace(/_INDEX_/g, gIndex);
                const $group = $(groupHtml);
                const $itemsContainer = $group.find('.cmfw-items');

                // Taxonomy and terms
                if (group.taxonomy) {
                    $group.find('.taxonomy-select').val(group.taxonomy);
                    $group.find('.term-row').show();
                }
                const termNames = group.term_names || {};
                if (Array.isArray(group.terms)) {
                    const $selected = $group.find('.selected-terms');
                    group.terms.forEach(function(termId) {
                        const name = termNames[termId] || ('<?php echo esc_js(__('Term #', 'custom-meta-for-woocommerce')); ?> ' + termId);
                        const pill = `
                            <span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;">
                                ${$('<div>').text(name).html()}
                                <a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">&times;</a>
                                <input type="hidden" name="cmfw_groups[${gIndex}][terms][]" value="${termId}">
                            </span>`;
                        $selected.append(pill);
                    });
                }

                const items = group.items || [];
                $.each(items, function(itemIndex, item) {
                    let itemHtml = itemTemplate
                        .replace(/_GROUP_INDEX_/g, gIndex)
                        .replace(/_ITEM_INDEX_/g, itemIndex);

                    const $item = $(itemHtml);
                    $item.find('input[name$="[title]"]').val(item.title || '');

                    if (item.icon) {
                        $item.find('.cmfw-icon-value').val(item.icon);
                        $item.find('.cmfw-icon-preview .dashicons').attr('class', 'dashicons dashicons-' + item.icon);
                    }

                    if (item.image_id && parseInt(item.image_id, 10) > 0) {
                        $item.find('.cmfw-image-value').val(item.image_id);
                        $item.find('.cmfw-image-picker-container').attr('data-image-id', item.image_id);
                    }

                    $itemsContainer.append($item);
                });

                $('#cmfw-groups-container').append($group);
            });
        });
    </script>
</div>
