<?php
// Get current settings
$cmfw_settings = get_option('cmfw_settings', array());


$cmfw_enable_meta = isset($cmfw_settings['enable_meta']) ? $cmfw_settings['enable_meta'] : '1';
$cmfw_meta_position = isset($cmfw_settings['meta_position']) ? $cmfw_settings['meta_position'] : 'woocommerce_product_additional_information';
$cmfw_meta_heading = isset($cmfw_settings['meta_heading']) ? $cmfw_settings['meta_heading'] : __('Product Information', 'custom-meta-for-woocommerce');
$cmfw_heading_color = isset($cmfw_settings['heading_color']) ? $cmfw_settings['heading_color'] : '#333333';
$cmfw_heading_size = isset($cmfw_settings['heading_size']) ? $cmfw_settings['heading_size'] : '18';
$cmfw_meta_font_size = isset($cmfw_settings['meta_font_size']) ? $cmfw_settings['meta_font_size'] : '14';
$cmfw_meta_text_color = isset($cmfw_settings['meta_text_color']) ? $cmfw_settings['meta_text_color'] : '#666666';
$cmfw_meta_bg_color = isset($cmfw_settings['meta_bg_color']) ? $cmfw_settings['meta_bg_color'] : '#ffffff';
?>

<div class="wrap cmfw-admin">
    <h1><?php echo esc_html(__('Custom Meta Settings','custom-meta-for-woocommerce')); ?></h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php 
        settings_fields('cmfw_settings_group'); 
        do_settings_sections('cmfw_settings_group');
        ?>

        <div class="woo-cmfw-settings-wrap">
            <div class="woo-cmfw-settings-nav">
                <button type="button" class="nav-tab nav-tab-active" data-target="tab-general"><?php echo esc_html(__('General', 'custom-meta-for-woocommerce')); ?></button>
                <button type="button" class="nav-tab" data-target="tab-design"><?php echo esc_html(__('Design', 'custom-meta-for-woocommerce')); ?></button>
            </div>
            <div class="woo-cmfw-settings-content">
                <div id="tab-general" class="tab-content active">
                    <h2><?php echo esc_html(__('General Settings', 'custom-meta-for-woocommerce')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Enable Custom Meta', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="cmfw_settings[enable_meta]" value="1" <?php checked($cmfw_enable_meta, '1'); ?> />
                                    <?php echo esc_html(__('Enable custom meta display on product pages', 'custom-meta-for-woocommerce')); ?>
                                </label>
                                <p class="description"><?php echo esc_html(__('Check this to enable the custom meta functionality.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Position', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <select name="cmfw_settings[meta_position]">
                                    <option value="woocommerce_product_additional_information" <?php selected($cmfw_meta_position, 'woocommerce_product_additional_information'); ?>><?php echo esc_html(__('After Additional Information Tab', 'custom-meta-for-woocommerce')); ?></option>
                                    <option value="woocommerce_after_single_product_summary" <?php selected($cmfw_meta_position, 'woocommerce_after_single_product_summary'); ?>><?php echo esc_html(__('After Product Summary', 'custom-meta-for-woocommerce')); ?></option>
                                    <option value="woocommerce_before_single_product_summary" <?php selected($cmfw_meta_position, 'woocommerce_before_single_product_summary'); ?>><?php echo esc_html(__('Before Product Summary', 'custom-meta-for-woocommerce')); ?></option>
                                    <option value="woocommerce_product_meta_end" <?php selected($cmfw_meta_position, 'woocommerce_product_meta_end'); ?>><?php echo esc_html(__('After Product Meta', 'custom-meta-for-woocommerce')); ?></option>
                                    <option value="woocommerce_before_add_to_cart_form" <?php selected($cmfw_meta_position, 'woocommerce_before_add_to_cart_form'); ?>><?php echo esc_html(__('Before Add to Cart Form', 'custom-meta-for-woocommerce')); ?></option>
                                    <option value="woocommerce_share" <?php selected($cmfw_meta_position, 'woocommerce_share'); ?>><?php echo esc_html(__('After Share section', 'custom-meta-for-woocommerce')); ?></option>
                                </select>
                                <p class="description"><?php echo esc_html(__('Choose where to display the custom meta on product pages.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Heading', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_heading]" value="<?php echo esc_attr($cmfw_meta_heading); ?>" class="regular-text" />
                                <p class="description"><?php echo esc_html(__('Enter the heading text for the custom meta section.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tab-design" class="tab-content">
                    <h2><?php echo esc_html(__('Design Settings', 'custom-meta-for-woocommerce')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[heading_color]" value="<?php echo esc_attr($cmfw_heading_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the color for the meta heading text.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="number" name="cmfw_settings[heading_size]" value="<?php echo esc_attr($cmfw_heading_size); ?>" class="small-text" />
                                <span><?php echo esc_html(__('px', 'custom-meta-for-woocommerce')); ?></span>
                                <p class="description"><?php echo esc_html(__('Set the font size for the meta heading (10-48px).', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Title Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="number" name="cmfw_settings[meta_font_size]" value="<?php echo esc_attr($cmfw_meta_font_size); ?>" class="small-text" />
                                <span><?php echo esc_html(__('px', 'custom-meta-for-woocommerce')); ?></span>
                                <p class="description"><?php echo esc_html(__('Set the font size for the meta content (10-24px).', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Text Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_text_color]" value="<?php echo esc_attr($cmfw_meta_text_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the color for the meta text content.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Background Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td>
                                <input type="text" name="cmfw_settings[meta_bg_color]" value="<?php echo esc_attr($cmfw_meta_bg_color); ?>" class="cmfw-color-picker" />
                                <p class="description"><?php echo esc_html(__('Choose the background color for the meta section.', 'custom-meta-for-woocommerce')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>