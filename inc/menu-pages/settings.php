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
                            <th scope="row"><?php echo esc_html(__('Product Faq', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->ProductFaq(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Faq Position', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->faqPosition(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Faq Heading', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->Heading(); ?></td>
                        </tr>
                    </table>
                </div>
                <div id="tab-design" class="tab-content">
                    <h2><?php echo esc_html(__('Design Settings', 'custom-meta-for-woocommerce')); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->HeadingColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Question Font Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->QuestionColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Answer Font Color', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->AnswerColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Heading Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->HeadingFontSize(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Question Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->QuestionFontSize(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html(__('Answer Font Size', 'custom-meta-for-woocommerce')); ?></th>
                            <td><?php //$menu_instance->AnswerFontSize(); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>