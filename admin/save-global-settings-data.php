<?php

if( !function_exists("customfo_save_ajax_data")){
    function customfo_save_ajax_data(){

        // Verify nonce for security
        if ( !isset($_POST['cmfwc_nonce']) || !wp_verify_nonce($_POST['cmfwc_nonce'], 'cmfwc_save_global_settings') ) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if ( !current_user_can('manage_options') ) {
            wp_die('Insufficient permissions');
        }

        $customfo_img_height = sanitize_text_field(wp_unslash($_POST["img_height"]));
        $customfo_img_width = sanitize_text_field(wp_unslash($_POST["img_width"]));
        $customfo_font_size = sanitize_text_field(wp_unslash($_POST["font_size"]));
        $customfo_font_color = sanitize_text_field(wp_unslash($_POST["font_color"]));
        $customfo_bg_color = sanitize_text_field(wp_unslash($_POST["bg_color"]));

        $customfo_margin = sanitize_text_field(wp_unslash($_POST["margin"]));
        $customfo_padding = sanitize_text_field(wp_unslash($_POST["padding"]));
        $customfo_border_radius = sanitize_text_field(wp_unslash($_POST["border_radius"]));


        $customfo_icon_size = sanitize_text_field(wp_unslash($_POST["icon_size"]));
        $customfo_icon_color = sanitize_text_field(wp_unslash($_POST["icon_front_color"]));
        $customfo_icon_margin = sanitize_text_field(wp_unslash($_POST["icon_margin"]));
        $customfo_icon_padding = sanitize_text_field(wp_unslash($_POST["icon_padding"]));

        
       
        if ( isset($_POST['repeaterFields']) && is_array($_POST['repeaterFields']) ) {
            update_option('cmfwc_global_repeater_fields', wp_unslash($_POST['repeaterFields']));
        }


        if ( isset($_POST['category_list']) && is_array($_POST['category_list']) ) {
            update_option('cmfwc_global_category_list', wp_unslash($_POST['category_list']));
        }

        if(isset($customfo_bg_color)){
            update_option("cmfwc_bg_color", $customfo_bg_color);
        }

        if(isset($customfo_font_color)){
            update_option("cmfwc_font_color", $customfo_font_color);
        }

        if(isset($customfo_img_height)){
            update_option("cmfwc_img_height", $customfo_img_height);
        }

        if(isset($customfo_img_width)){
            update_option("cmfwc_img_width", $customfo_img_width);
        }

        if(isset($customfo_font_size)){
            update_option("cmfwc_font_size", $customfo_font_size);
        }

        if(isset($customfo_margin)){
            update_option("cmfwc_margin", $customfo_margin);
        }
       
        if(isset($customfo_padding)){
            update_option("cmfwc_padding", $customfo_padding);
        }

        if(isset($customfo_border_radius)){
            update_option("cmfwc_border_radius", $customfo_border_radius);
        }

        if(isset($customfo_icon_size)){
            update_option("cmfwc_icon_size", $customfo_icon_size);
        }

        if(isset($customfo_icon_color)){
            update_option("cmfwc_icon_color", $customfo_icon_color);
        }

        if(isset($customfo_icon_margin)){
            update_option("cmfwc_icon_margin", $customfo_icon_margin);
        }

        if(isset($customfo_icon_padding)){
            update_option("cmfwc_icon_padding", $customfo_icon_padding);
        }
        
        
        wp_die();
    }
}

add_action("wp_ajax_cffw_save_global_data","customfo_save_ajax_data");
add_action("wp_ajax_nopriv_cffw_save_global_data","customfo_save_ajax_data");