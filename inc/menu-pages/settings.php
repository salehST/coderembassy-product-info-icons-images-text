<?php
defined('ABSPATH') or die('Nice Try!');

// Get current settings
$cmfw_settings = get_option('cmfw_settings', array());


$cmfw_enable_meta = isset($cmfw_settings['enable_meta']) ? $cmfw_settings['enable_meta'] : '1';
$cmfw_meta_position = isset($cmfw_settings['meta_position']) ? $cmfw_settings['meta_position'] : 'woocommerce_product_additional_information';
$cmfw_meta_heading = isset($cmfw_settings['meta_heading']) ? $cmfw_settings['meta_heading'] : __('Product Information', 'coderembassy-product-info-icons-images-text');
$cmfw_heading_color = isset($cmfw_settings['heading_color']) ? $cmfw_settings['heading_color'] : '#333333';
$cmfw_heading_size = isset($cmfw_settings['heading_size']) ? $cmfw_settings['heading_size'] : '18';
$cmfw_meta_font_size = isset($cmfw_settings['meta_font_size']) ? $cmfw_settings['meta_font_size'] : '14';
$cmfw_meta_text_color = isset($cmfw_settings['meta_text_color']) ? $cmfw_settings['meta_text_color'] : '#666666';
$cmfw_meta_bg_color = isset($cmfw_settings['meta_bg_color']) ? $cmfw_settings['meta_bg_color'] : '#ffffff';

$cmfw_flex_direction = isset($cmfw_settings['flex_direction']) ? $cmfw_settings['flex_direction'] : 'column';
$cmfw_gap = isset($cmfw_settings['gap']) ? $cmfw_settings['gap'] : '15px';
$cmfw_padding = isset($cmfw_settings['padding']) ? $cmfw_settings['padding'] : '20px';
$cmfw_text_align = isset($cmfw_settings['text_align']) ? $cmfw_settings['text_align'] : 'left';
$cmfw_align_items = isset($cmfw_settings['align_items']) ? $cmfw_settings['align_items'] : 'center';
$cmfw_margin = isset($cmfw_settings['margin']) ? $cmfw_settings['margin'] : '50px 0 30px';
?>

<div class="wrap cmfw-admin">
    <h1><?php echo esc_html(__('Product Info Settings','coderembassy-product-info-icons-images-text')); ?></h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php 
        settings_fields('cmfw_settings_group'); 
        do_settings_sections('cmfw_settings_group');
        ?>

        <div class="woo-cmfw-settings-wrap">
            <div class="woo-cmfw-settings-nav">
                <button type="button" class="nav-tab nav-tab-active" data-target="tab-general"><?php echo esc_html(__('General', 'coderembassy-product-info-icons-images-text')); ?></button>
                <button type="button" class="nav-tab" data-target="tab-design"><?php echo esc_html(__('Design', 'coderembassy-product-info-icons-images-text')); ?></button>
            </div>
            <div class="woo-cmfw-settings-content">
                <div id="tab-general" class="tab-content active">
                    <h2><?php echo esc_html(__('General Settings', 'coderembassy-product-info-icons-images-text')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Enable Product info ', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="cmfw_settings[enable_meta]" value="1" <?php checked($cmfw_enable_meta, '1'); ?> />
                                    <?php echo esc_html(__('Enable Product info display on product pages', 'coderembassy-product-info-icons-images-text')); ?>
                                </label>
                                <p class="description"><?php echo esc_html(__('Check this to enable the Product Info functionality.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <?php
                        // Allow pro version to add position setting
                        do_action('cmfw_pro_position_setting', $cmfw_meta_position);
                        ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Product Info Heading', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_heading]" value="<?php echo esc_attr($cmfw_meta_heading); ?>" class="regular-text" />
                                <p class="description"><?php echo esc_html(__('Enter the heading text for the Product Info section.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tab-design" class="tab-content">
                    <h2><?php echo esc_html(__('Design Settings', 'coderembassy-product-info-icons-images-text')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Color', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[heading_color]" value="<?php echo esc_attr($cmfw_heading_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the color for the product info heading text.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Size', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="number" name="cmfw_settings[heading_size]" value="<?php echo esc_attr($cmfw_heading_size); ?>" class="small-text" />
                                <span><?php echo esc_html(__('px', 'coderembassy-product-info-icons-images-text')); ?></span>
                                <p class="description"><?php echo esc_html(__('Set the font size for the product info heading (10-48px).', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Title Font Size', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="number" name="cmfw_settings[meta_font_size]" value="<?php echo esc_attr($cmfw_meta_font_size); ?>" class="small-text" />
                                <span><?php echo esc_html(__('px', 'coderembassy-product-info-icons-images-text')); ?></span>
                                <p class="description"><?php echo esc_html(__('Set the font size for the product info content (10-24px).', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Product Info Text Color', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_text_color]" value="<?php echo esc_attr($cmfw_meta_text_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the color for the product info text content.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Background Color', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_bg_color]" value="<?php echo esc_attr($cmfw_meta_bg_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the background color for the product info section.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                    <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Padding', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[padding]" value="<?php echo esc_attr($cmfw_padding); ?>" class="regular-text" />
                                <p class="description"><?php echo esc_html(__('Set the padding for the product info section (e.g., 20px).', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Margin', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[margin]" value="<?php echo esc_attr($cmfw_margin); ?>" class="regular-text" />
                                <p class="description"><?php echo esc_html(__('Set the margin for the product info section (e.g., 50px 0 30px).', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Text Align', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <select name="cmfw_settings[text_align]">
                                    <option value="left" <?php selected($cmfw_text_align, 'left'); ?>><?php echo esc_html(__('Left', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="center" <?php selected($cmfw_text_align, 'center'); ?>><?php echo esc_html(__('Center', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="right" <?php selected($cmfw_text_align, 'right'); ?>><?php echo esc_html(__('Right', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="justify" <?php selected($cmfw_text_align, 'justify'); ?>><?php echo esc_html(__('Justify', 'coderembassy-product-info-icons-images-text')); ?></option>
                                </select>
                                <p class="description"><?php echo esc_html(__('Alignment of the content.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Gap', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[gap]" value="<?php echo esc_attr($cmfw_gap); ?>" class="small-text" />
                                <p class="description"><?php echo esc_html(__('Gap between items (e.g., 15px).', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Flex Direction', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <select name="cmfw_settings[flex_direction]">
                                    <option value="row" <?php selected($cmfw_flex_direction, 'row'); ?>><?php echo esc_html(__('Row', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="column" <?php selected($cmfw_flex_direction, 'column'); ?>><?php echo esc_html(__('Column', 'coderembassy-product-info-icons-images-text')); ?></option>
                                </select>
                                <p class="description"><?php echo esc_html(__('Direction of the items inside each group.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Align Items', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <select name="cmfw_settings[align_items]">
                                    <option value="stretch" <?php selected($cmfw_align_items, 'stretch'); ?>><?php echo esc_html(__('Stretch', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="center" <?php selected($cmfw_align_items, 'center'); ?>><?php echo esc_html(__('Center', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="flex-start" <?php selected($cmfw_align_items, 'flex-start'); ?>><?php echo esc_html(__('Flex Start', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="flex-end" <?php selected($cmfw_align_items, 'flex-end'); ?>><?php echo esc_html(__('Flex End', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="baseline" <?php selected($cmfw_align_items, 'baseline'); ?>><?php echo esc_html(__('Baseline', 'coderembassy-product-info-icons-images-text')); ?></option>
                                </select>
                                <p class="description"><?php echo esc_html(__('Vertical alignment of items.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>