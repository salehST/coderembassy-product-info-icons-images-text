<?php
 if(!function_exists("customfo_add_custom_fields_tab")){
    function customfo_add_custom_fields_tab($tabs){
        $tabs['custom_fields'] = array(
            'label'    => __('Custom Meta For WooCommerce', 'custom-meta-for-woocommerce'),
            'target'   => 'custom_fields_product_data',
            'class'    => array('show_if_simple'),
        );
        return $tabs;
    }
}
add_action("woocommerce_product_data_tabs","customfo_add_custom_fields_tab",100,1);

add_action('woocommerce_product_data_panels', 'customfo_custom_fields_product_data_panels');
function customfo_custom_fields_product_data_panels() {
    global $post;
    $hooks = array(
        'display_content_after_add_to_cart',
        'display_content_after_price',
        'display_custom_field_before_price',
        'display_overview_tab_content',
        'woocommerce_before_single_product_summary',
        'woocommerce_single_product_summary',
        'woocommerce_after_single_product_summary',
        'woocommerce_after_add_to_cart_form'
    );

        $hooks = apply_filters('eazyproo_custom_hooks', $hooks);


?>
        <div id='custom_fields_product_data' class='panel woocommerce_options_panel'>
            <div class='options_group'>
                <div id="repeater-wrapper">
                    <?php
                    $custom_fields = get_post_meta($post->ID, '_custom_repeater_field', true);

                    
                    
                    if (!empty($custom_fields)) {

                        $count = 0;
                        foreach ($custom_fields as $field) {
                            ?>
                            <div class="field-wrapper">
                                <input type="text" name="custom_repeater_field[<?php echo esc_attr($count);?>][icon_class]" value="<?php echo esc_attr($field['icon_class']) ?  esc_attr($field['icon_class']) : '' ?>"  placeholder="give icon class" />
                                <input type="text" name="custom_repeater_field[<?php echo esc_attr($count);?>][title]" value=" <?php echo esc_attr($field['title']) ?  esc_attr($field['title']) : '' ?> " placeholder="give title text " />
                                <input type="text" name="custom_repeater_field[<?php echo esc_attr($count);?>][title_value]" value=" <?php echo esc_attr($field['title_value']) ? esc_attr($field['title_value']) : '' ?> " placeholder="give title value " />

                                
                                <select name="custom_repeater_field[<?php echo esc_attr($count);?>][hook_name]" id="custom_field_hook_list">
                                    <option value=""><?php echo esc_html("Where you want to display:", "custom-meta-for-woocommerce"); ?></option>
                                    <?php
                                    foreach ($hooks as $hook) {
                                    ?>
                                        <option value="<?php echo esc_attr($hook) ? esc_attr($hook) : ''?>" <?php selected($field['hook_name'], $hook); ?>><?php echo esc_html($hook)?></option>
                                    <?php  
                                    }
                                    ?>
                                </select>
                                

                                <input type="hidden" name="custom_repeater_field[<?php echo esc_attr($count);?>][image]" value=" <?php echo isset($field['image']) ?  esc_attr($field['image']) : '' ?>" />
                               
                                <button type="button" class="upload_image_button button"><?php  echo esc_html('Upload/Add image', 'custom-meta-for-woocommerce') ?></button>
                                <button type="button" class="remove_field_button button"><?php  echo esc_html('Remove', 'custom-meta-for-woocommerce')  ?> </button>
                                <div class="image_preview">
                                    <?php
                                    if (isset($field['image']) && !empty($field['image'])) {
                                        echo  wp_get_attachment_image($field['image'], 'thumbnail') ;
                                    }
                                    ?>
                                </div>
                        
                            </div>
                            <?php

                            $count++;
                        }
                    } else {
                        ?>
                    <div class="field-wrapper">
                            <input type="text" name="custom_repeater_field[0][icon_class]" placeholder="give icon class" />
                            <input type="text" name="custom_repeater_field[0][title]" placeholder="give title text" />
                            <input type="text" name="custom_repeater_field[0][title_value]" placeholder="give title value" />

                            <select name="custom_repeater_field[0][hook_name]" id="custom_field_hook_list">
                                <option value="">Where you want to display:</option>
                                <option value="woocommerce_after_add_to_cart_form"><?php echo esc_html("woocommerce_after_add_to_cart_form","custom-meta-for-woocommerce");?></option>
                                <option value="woocommerce_after_single_product_summary"><?php echo esc_html("woocommerce_after_single_product_summary","custom-meta-for-woocommerce");?></option>
                                <option value="woocommerce_single_product_summary"><?php echo esc_html("woocommerce_single_product_summary","custom-meta-for-woocommerce");?></option>
                                <option value="woocommerce_before_single_product_summary"><?php echo esc_html("woocommerce_before_single_product_summary","custom-meta-for-woocommerce");?></option>
                                <option value="display_overview_tab_content"><?php echo esc_html("display_overview_tab_content","custom-meta-for-woocommerce");?></option>
                                <option value="display_custom_field_before_price"><?php echo esc_html("display_custom_field_before_price","custom-meta-for-woocommerce");?></option>
                                <option value="display_content_after_price"><?php echo esc_html("display_content_after_price","custom-meta-for-woocommerce");?></option>
                                <option value="display_content_after_add_to_cart"><?php echo esc_html("display_content_after_add_to_cart","custom-meta-for-woocommerce");?></option>
                            </select>

                            <input type="hidden" name="custom_repeater_field[0][image]" />
                            <button type="button" class="upload_image_button button"><?php  echo esc_html_e('Upload/Add image', 'custom-meta-for-woocommerce') ?> </button>
                            <button type="button" class="remove_field_button button"><?php echo esc_html_e('Remove', 'custom-meta-for-woocommerce') ?> </button>  
                            <div class="image_preview"></div>
                        </div>

                        <?php


                    }
                    ?>
                </div>
                <button type="button" id="add-repeater-field" class="button"><?php echo esc_html_e('Add Field', 'custom-meta-for-woocommerce'); ?></button>
                
            </div>
        </div>
        <?php
    }

    // save product meta data

    add_action('woocommerce_process_product_meta', 'customfo_save_custom_fields');
    function customfo_save_custom_fields($post_id) {
            
        if (isset($_POST['custom_repeater_field'])) {

            update_post_meta($post_id, '_custom_repeater_field',$_POST['custom_repeater_field']);
        }
    }


   


/*=======================  Custom meta fields product data tab section end =======================*/


