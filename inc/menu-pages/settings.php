<div class="wrap cmfw-admin">
    <h1><?php echo esc_html('Custom Meta Settings'); ?></h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php settings_fields('woofaq-settings-group'); ?>

        <div class="woo-cmfw-settings-wrap">
            <div class="woo-cmfw-settings-nav">
                <button type="button" class="nav-tab" data-target="tab-general"><?php echo esc_html('General'); ?></button>
                <button type="button" class="nav-tab" data-target="tab-design"><?php echo esc_html('Design'); ?></button>
            </div>
            <div class="woo-cmfw-settings-content">
                <div id="tab-general" class="tab-content">
                    <h2><?php echo esc_html('General Settings'); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Product Faq'); ?></th>
                            <td><?php $menu_instance->ProductFaq(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Faq Position'); ?></th>
                            <td><?php $menu_instance->faqPosition(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Faq Heading'); ?></th>
                            <td><?php $menu_instance->Heading(); ?></td>
                        </tr>
                    </table>
                </div>
                <div id="tab-design" class="tab-content">
                    <h2><?php echo esc_html('Design Settings'); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Heading Font Color'); ?></th>
                            <td><?php $menu_instance->HeadingColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Question Font Color'); ?></th>
                            <td><?php $menu_instance->QuestionColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Answer Font Color'); ?></th>
                            <td><?php $menu_instance->AnswerColor(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Heading Font Size'); ?></th>
                            <td><?php $menu_instance->HeadingFontSize(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Question Font Size'); ?></th>
                            <td><?php $menu_instance->QuestionFontSize(); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html('Answer Font Size'); ?></th>
                            <td><?php $menu_instance->AnswerFontSize(); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>