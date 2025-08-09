<div class="wrap cmfw-admin">
    <h1><?php echo esc_html('Custom Meta Settings','custom-meta-for-woocommerce'); ?></h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php //settings_fields('woofaq-settings-group'); ?>

        <div class="woo-cmfw-settings-wrap">
            <div class="woo-cmfw-settings-nav">
                <button type="button" class="nav-tab" data-target="tab-general"><?php echo esc_html(__('General', 'custom-meta-for-woocommerce')); ?></button>
                <button type="button" class="nav-tab" data-target="tab-design"><?php echo esc_html(__('Design', 'custom-meta-for-woocommerce')); ?></button>
            </div>
            <div class="woo-cmfw-settings-content">
                <div id="tab-general" class="tab-content">
                    <h2><?php echo esc_html(__('General Settings', 'custom-meta-for-woocommerce')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Product Meta', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->ProductFaq(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Position', 'custom-meta-for-woocommerce')); ?></th>
                            <td></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Heading', 'custom-meta-for-woocommerce')); ?></th>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div id="tab-design" class="tab-content">
                    <h2><?php echo esc_html(__('Design Settings', 'custom-meta-for-woocommerce')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Meta Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>