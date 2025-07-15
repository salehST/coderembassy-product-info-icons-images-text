<?php 

add_action("wp","customfo_display_custom_fields_categroy_based");

if( !function_exists("customfo_display_custom_fields_categroy_based")){
    function customfo_display_custom_fields_categroy_based(){
        if(is_admin()){
            return;
        }

        global $post;

        $category_list = get_option("customfo_global_category_list");

       if( is_product() && isset($category_list) && is_array($category_list)){
        $product_cats = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'ids'));
            if( array_intersect( $product_cats, $category_list)){
                add_action("woocommerce_after_add_to_cart_form","customfo_display_custom_fileds");
            }
       }
        
    }
}

// display custom fields function

function customfo_display_custom_fileds(){
    $global_custom_fields = get_option("customfo_global_repeater_fields");
    if(isset($global_custom_fields) && is_array($global_custom_fields) ){
        foreach( $global_custom_fields as $field):
            ?>
            <div class="row">
                 <div class="detail-title">
                    <?php
                        foreach( $global_custom_fields as $field):
                            ?>
                        
                                <div class="meta_box">
                                    <?php
                                        if (!empty($field['image'])) {
                                            echo wp_get_attachment_image(intval($field['image']), 'full');
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

        endforeach;
    }
}
