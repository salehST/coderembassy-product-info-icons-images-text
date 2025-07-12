<?php
// display global custom fields on single product page
if( !function_exists("customfo_display_global_custom_fields")){
    function customfo_display_global_custom_fields(){
        if ( !is_singular() && !is_product() ) {
            return false;
        }

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
}

$category_list = get_option("cmfwc_global_category_list");
add_action("woocommerce_after_add_to_cart_form","customfo_display_global_custom_fields");


