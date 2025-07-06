<?php

if( !function_exists("cmfwc_save_ajax_data")){
    function cmfwc_save_ajax_data(){

        $cmfwc_img_height = sanitize_text_field($_POST["img_height"]);
        $cmfwc_img_width = sanitize_text_field($_POST["img_width"]);
        $cmfwc_font_size = sanitize_text_field($_POST["font_size"]);
        $cmfwc_font_color = sanitize_text_field($_POST["font_color"]);
        $cmfwc_bg_color = sanitize_text_field($_POST["bg_color"]);

        $cmfwc_margin = sanitize_text_field($_POST["margin"]);
        $cmfwc_padding = sanitize_text_field($_POST["padding"]);
        $cmfwc_border_radius = sanitize_text_field($_POST["border_radius"]);


        $cmfwc_icon_size = sanitize_text_field($_POST["icon_size"]);
        $cmfwc_icon_color = sanitize_text_field($_POST["icon_front_color"]);
        $cmfwc_icon_margin = sanitize_text_field($_POST["icon_margin"]);
        $cmfwc_icon_padding = sanitize_text_field($_POST["icon_padding"]);

        
       
        if ( isset($_POST['repeaterFields']) && is_array($_POST['repeaterFields']) ) {
            update_option('cmfwc_global_repeater_fields', $_POST['repeaterFields']);
        }


        if ( isset($_POST['category_list']) && is_array($_POST['category_list']) ) {
            update_option('cmfwc_global_category_list', $_POST['category_list']);
        }

        if(isset($cmfwc_bg_color)){
            update_option("cmfwc_bg_color", $cmfwc_bg_color);
        }

        if(isset($cmfwc_font_color)){
            update_option("cmfwc_font_color", $cmfwc_font_color);
        }

        if(isset($cmfwc_img_height)){
            update_option("cmfwc_img_height", $cmfwc_img_height);
        }

        if(isset($cmfwc_img_width)){
            update_option("cmfwc_img_width", $cmfwc_img_width);
        }

        if(isset($cmfwc_font_size)){
            update_option("cmfwc_font_size", $cmfwc_font_size);
        }

        if(isset($cmfwc_margin)){
            update_option("cmfwc_margin", $cmfwc_margin);
        }
       
        if(isset($cmfwc_padding)){
            update_option("cmfwc_padding", $cmfwc_padding);
        }

        if(isset($cmfwc_border_radius)){
            update_option("cmfwc_border_radius", $cmfwc_border_radius);
        }

        if(isset($cmfwc_icon_size)){
            update_option("cmfwc_icon_size", $cmfwc_icon_size);
        }

        if(isset($cmfwc_icon_color)){
            update_option("cmfwc_icon_color", $cmfwc_icon_color);
        }

        if(isset($cmfwc_icon_margin)){
            update_option("cmfwc_icon_margin", $cmfwc_icon_margin);
        }

        if(isset($cmfwc_icon_padding)){
            update_option("cmfwc_icon_padding", $cmfwc_icon_padding);
        }
        
        
        wp_die();
    }
}

add_action("wp_ajax_cffw_save_global_data","cmfwc_save_ajax_data");
add_action("wp_ajax_nopriv_cffw_save_global_data","cmfwc_save_ajax_data");