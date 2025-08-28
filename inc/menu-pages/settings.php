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
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Product Info Position', 'coderembassy-product-info-icons-images-text')); ?></th>
                            <td>
                                <select name="cmfw_settings[meta_position]">
                                    <option value="woocommerce_after_add_to_cart_button" <?php selected($cmfw_meta_position, 'woocommerce_after_add_to_cart_button'); ?>><?php echo esc_html(__('After Cart Button', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="woocommerce_product_meta_end" <?php selected($cmfw_meta_position, 'woocommerce_product_meta_end'); ?>><?php echo esc_html(__('After Meta', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="woocommerce_after_single_product_summary" <?php selected($cmfw_meta_position, 'woocommerce_after_single_product_summary'); ?>><?php echo esc_html(__('After Summary', 'coderembassy-product-info-icons-images-text')); ?></option>
                                    <option value="woocommerce_after_single_product" <?php selected($cmfw_meta_position, 'woocommerce_after_single_product'); ?>><?php echo esc_html(__('After Single product', 'coderembassy-product-info-icons-images-text')); ?></option>     
                                </select>
                                <p class="description"><?php echo esc_html(__('Choose where to display the Product Info on product pages.', 'coderembassy-product-info-icons-images-text')); ?></p>
                            </td>
                        </tr>
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
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>