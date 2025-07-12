<?php

function customfo_repeater_field_edit_category($term){
    // Get the term ID
    $term_id = $term->term_id;
    
    // Retrieve the custom repeater field data using the correct term ID
    $custom_fields = get_term_meta($term_id, 'custom_repeater_field', true);

    ?>
    <div class='global_options_group'>
        <div id="repeater-wrapper">

            <?php
            
            if (!empty($custom_fields)) {
                $count = 0;
                foreach ($custom_fields as $field) {
                    ?>
                    <div class="field-wrapper">
                        <input type="text" name="custom_repeater_field[<?php echo esc_attr($count);?>][icon_class]" value="<?php echo esc_attr($field['icon_class']) ? esc_attr($field['icon_class']) : ''; ?>" placeholder="give icon class" />
                        <input type="text" name="custom_repeater_field[<?php echo esc_attr($count);?>][title]" value="<?php echo esc_attr($field['title']) ? esc_attr($field['title']) : ''; ?>" placeholder="give title text" />
                        <input type="hidden" name="custom_repeater_field[<?php echo esc_attr($count);?>][image]" value="<?php echo isset($field['image']) ? esc_attr($field['image']) : ''; ?>" />
                       
                        <button type="button" class="upload_image_button button"><?php echo esc_html('Upload/Add image', 'customfo'); ?></button>
                        <button type="button" class="remove_field_button button"><?php echo esc_html('Remove', 'customfo'); ?></button>
                        <div class="image_preview">
                            <?php
                            if (isset($field['image']) && !empty($field['image'])) {
                                $image_url = wp_get_attachment_url($field['image']);
                                
                                if (strpos($image_url, '.svg') !== false) {
                                    echo '<img src="' . esc_url($image_url) . '" alt="" class="custom-svg-class"/>';
                                } else {
                            
                                    echo wp_get_attachment_image($field['image'], 'thumbnail');
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $count++;
                }
            } else {
                // Default empty field
                ?>
                <div class="field-wrapper">
                    <input type="text" name="custom_repeater_field[0][icon_class]" class="icon_class" placeholder="give icon class" />
                    <input type="text" name="custom_repeater_field[0][title]" class="title" placeholder="give title text" />
                    <input type="hidden" name="custom_repeater_field[0][image]" class="image" />
                    <button type="button" class="upload_image_button button"><?php echo esc_html_e('Upload/Add image', 'customfo'); ?></button>
                    <button type="button" class="remove_field_button button"><?php echo esc_html_e('Remove', 'customfo'); ?></button>
                    <div class="image_preview"></div>
                </div>
                <?php
            }
            ?>
        </div>
        <button type="button" id="add-repeater-field" class="button"><?php esc_html_e('Add Field', 'customfo'); ?></button>
    </div>
    <?php
}
// Hook into the product category edit form
add_action("product_cat_edit_form_fields","customfo_repeater_field_edit_category");

/*display global field in category edit*/
function customfo_repeater_field_display_category_page($taxonomy){
    $global_custom_fields = get_option("cmfwc_global_repeater_fields");
    
    if(isset($global_custom_fields) && is_array($global_custom_fields) ){

        ?>
        <div class="row">
            <div class="detail-title">
                <?php
                    foreach( $global_custom_fields as $field):

                        ?>
                            <div class="meta_box">
                                <?php
                                    if (!empty($field['image'])) {
                                        echo wp_get_attachment_image($field['image'], 'full');
                                    }else {
                                        echo '<i class="'. esc_attr($field['icon_class']) .'"></i>';
                                    }
        
                                        echo '<span class="title">'. esc_html($field['title']) .'</span>'; 
                                ?>
                            </div>
                                
                        <?php

                    endforeach;
                 ?>
                
            </div>
                    
        </div>

        <?php
    }
}
add_action("product_cat_edit_form_fields","customfo_repeater_field_display_category_page",100,1);
/*display global field in category edit end*/

// Save the custom repeater fields
add_action('edited_product_cat', 'customfo_save_custom_repeater_field_as_meta', 10, 2);

function customfo_save_custom_repeater_field_as_meta($term_id, $tt_id) {
    if (isset($_POST['custom_repeater_field'])) {
        $sanitized_data = array();

        foreach ($_POST['custom_repeater_field'] as $key => $field) {
            $sanitized_data[$key]['icon_class'] = sanitize_text_field($field['icon_class']);
            $sanitized_data[$key]['title'] = sanitize_text_field($field['title']);
            $sanitized_data[$key]['image'] = isset($field['image']) ? sanitize_text_field($field['image']) : '';
        }

        update_term_meta($term_id, 'custom_repeater_field', $sanitized_data);
    }
}



// display custom fields on products in the specified category

function customfo_get_category_custom_fields($product_id) {
    // Get the categories of the product
    $categories = wp_get_post_terms($product_id, 'product_cat');

    foreach ($categories as $category) {
        $custom_fields = get_term_meta($category->term_id, 'custom_repeater_field', true);

        if (!empty($custom_fields)) {
            return $custom_fields;
        }
    }
    return false; 
}


function customfo_display_custom_category_fields() {

    global $product;
    $product_id = $product->get_id();
    $custom_fields = customfo_get_category_custom_fields($product_id);

    if ($custom_fields) {
     ?>
         <div class="row">
            <div class="detail-title">
                    <?php
                        foreach( $custom_fields as $field):
                            ?>
                        
                                <div class="meta_box">
                                    <?php
                                        if (!empty($field['image'])) {
                                            echo wp_get_attachment_image($field['image'], 'full');
                                        }else {
                                        echo '<i class="'. $field['icon_class'] .'"></i>';
                                        }
            
                                        echo '<span class="title">'. $field['title'] .'</span>'; 
                                    ?>
                                </div>
                                    
                            <?php

                        endforeach;
                     ?>
                    
            </div>           
        </div>
     <?php
    }





}

add_action('woocommerce_after_add_to_cart_form', 'customfo_display_custom_category_fields', 20);



?>





