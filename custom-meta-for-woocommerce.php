<?php 

/*
* Plugin Name: Custom Meta for WooCommerce
* Description: Add Additional information to WooCommerce Single product.
* Plugin URI: https://coderembassy.com/
* Author: codersaleh
* Version: 1.0.0
* License: GPLv2 or later
* Author URI: https://github.com/coderembassy
* Text Domain: customfo
*/


if( !defined('ABSPATH')){
    exit();
}

/*make a constant for plugin directory*/

define('CUSTOMFO_DIR',  plugin_dir_url(__FILE__) );

/*checking if plugin is not active*/
if( !function_exists('customfo_admin_notice')){
    function customfo_admin_notice(){
        $current_screen = get_current_screen();
        if ($current_screen->id === 'plugins') {
            if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                echo '<div class="notice notice-error"><p>';
                echo 'Please install and activate WooCommerce to use this plugin.';
                echo '</p></div>';
            }
        }
    }
}
add_action("admin_notices","customfo_admin_notice");

/*load text domain*/
function customfo_load_textdomain() {
    load_plugin_textdomain( 'customfo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'customfo_load_textdomain' );

/*load text domain end*/

/*enqueue admin assets file section*/
if(!function_exists('customfo_enqueue_admin_assets')){
    function customfo_enqueue_admin_assets(){
          //add font Font-Awesome
         wp_enqueue_style("custom-meta-admin-font-awesome-css",'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
        
         wp_enqueue_script('fontawesome-iconpicker-js', 'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.min.js', array('jquery'), '3.2.0', true);
         wp_enqueue_style('fontawesome-iconpicker-css', 'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css');
        
         //select2
        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);
         
          // admin section 
          wp_enqueue_style("cmfwc-admin-css", CUSTOMFO_DIR.'/admin/assets/css/admin-style.css',null,'1.0.0','all');
         
          // color picker
          wp_enqueue_script("cmfwc-jscolor-js",CUSTOMFO_DIR.'/admin/assets/js/jscolor/jscolor.js',null,'1.0.0',true);
          wp_enqueue_script("cmfwc-admin-js",CUSTOMFO_DIR.'/admin/assets/js/admin.js',array('jquery'),'1.0.0',true);
          
  

          wp_localize_script("cmfwc-admin-js","ajax_object",array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
         
          wp_enqueue_script('jquery');
          if (is_admin()) {
            wp_enqueue_media();
         }
        }
}
add_action("admin_enqueue_scripts","customfo_enqueue_admin_assets");
/*enqueue admin assets file section end*/

/*enqueue frontend assets file section*/
if(!function_exists("customfo_enqueue_frontend_assets")){
    function customfo_enqueue_frontend_assets(){

        //add font Font-Awesome
        wp_enqueue_style("cmfwc-font-awesome-css",'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

        // //select2
        //  wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        //  wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);
        
       
        wp_enqueue_style("cmfwc-frontend-css", CUSTOMFO_DIR.'/frontend/assets/css/frontend-style.css',null,'1.0.0','all');
        wp_enqueue_script("cmfwc-frontend-js",CUSTOMFO_DIR."/frontend/assets/js/frontend.js",array('jquery'),'1.0.0',true);


    }
}
add_action("wp_enqueue_scripts","customfo_enqueue_frontend_assets");
/*enqueue frontend assets file section end*/

/*Support svg image*/
function add_svg_to_upload_mimes( $upload_mimes ) {
    $upload_mimes['svg'] = 'image/svg+xml';
    return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes' );
/*Support svg image*/



/* ==================Admin menu section=============*/
if( ! function_exists( 'customfo_add_menu_under_woocommerce ')){
    function customfo_add_menu_under_woocommerce(){
       add_submenu_page(
           'woocommerce',
           'Custom Meta for WooCommerce',
           'Custom Meta for WooCommerce',
           'manage_options',
           'customfo-settings',
           'customfo_add_submenu_page'
       ); 
    }
}
register_activation_hook(__FILE__, 'customfo_add_menu_under_woocommerce');
add_action( 'admin_menu', 'customfo_add_menu_under_woocommerce' );


 function customfo_add_submenu_page(){
    ?>

    <div class="cmfwc_main_wrap">
          <div class='global_options_group'>
            <div id="setting_title">
                <h3 class="global-options"><?php esc_html_e( 'Global Options', 'customfo' ); ?></h3>
            </div>
             <div id="repeater-wrapper">

                <?php
                    $custom_fields = get_option("cmfwc_global_repeater_fields");

                    if (!empty($custom_fields)) {

                        $count = 0;
                        foreach ($custom_fields as $field) {
                            ?>
                            <div class="field-wrapper">
                                <input type="text" class="icon-picker" name="custom_repeater_field[<?php echo esc_attr($count);?>][icon_class]" value="<?php echo esc_attr($field['icon_class']) ? esc_attr($field['icon_class']) : '' ?>"  placeholder="Click to Select Icon" />
                               
                                <input type="text" class="title" name="custom_repeater_field[<?php echo esc_attr($count);?>][title]" value="<?php echo esc_attr($field['title']) ?  esc_attr($field['title']) : '' ?>" placeholder="give title text" />
                                <input type="hidden" name="custom_repeater_field[<?php echo esc_attr($count);?>][image]" value=" <?php echo isset($field['image']) ?  esc_attr($field['image']) : '' ?>" />
                               
                                <button type="button" class="upload_image_button button"><?php  echo esc_html('Upload/Add image', 'customfo') ?></button>
                                <button type="button" class="remove_field_button button"><?php  echo esc_html('Remove', 'customfo')  ?> </button>
                                <div class="image_preview">
                                    <?php
                                    if (isset($field['image']) && !empty($field['image'])) {
                                        $image_url = wp_get_attachment_url($field['image']);
                                        
                                        // Check if the image is an SVG
                                        if (strpos($image_url, '.svg') !== false) {
                                            echo '<img src="' . esc_url($image_url) . '" alt="" class="custom-svg-class"/>';
                                        } else {
                                            // Use wp_get_attachment_image for non-SVG images
                                            echo wp_get_attachment_image($field['image'], 'thumbnail');
                                        }
                                    }
                                    ?>
                                </div>
                        
                            </div>
                            <?php

                            $count++;
                        }
                    } else{
                        ?>
                         <div class="field-wrapper">
                            <input type="text" class="icon-picker" name="custom_repeater_field[0][icon_class]" value=""  placeholder="Click to Select Icon" />
                            <label for="icon_title"><?php esc_html_e('Title','customfo');?></label>
                            <input type="text" name="custom_repeater_field[0][title]"  class="title" placeholder="give title text" />
                            
                            <input type="hidden" name="custom_repeater_field[0][image]" class="image" />
                            <button type="button" class="upload_image_button button"><?php  echo esc_html_e('Upload/Add image', 'customfo') ?> </button>
                            <button type="button" class="remove_field_button button"><?php echo esc_html_e('Remove', 'customfo') ?> </button>  
                            <div class="image_preview"></div>
                        </div>
                        <?php
                    }
                    ?>

                </div>
                <button type="button" id="add-repeater-field" class="button"><?php esc_html_e('Add Field', 'customfo'); ?></button>
                
             </div>

             <!--- Category based hide price section --->
             <?php if ( false ) : ?>
            <div class="category_based_cmfw_section">
                <label for="cmfw_category_based_hide_price"> <?php echo esc_html_e('Display custom fields on Category Based ','customfo');?> </label>
    
                <?php 
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false,
                    ));


                    if( !empty($categories) && !is_wp_error($categories)){
                        
                        ?>
                           <select class="select2-category-dropdown" name="cmfw_category_list"  id="cmfw_category_list" multiple="multiple"  style="width: 40%;">
                           <option value=""><?php esc_html_e('Select a category','hpabfw');?></option>
                            <?php
                                foreach ($categories as $category) {
                                ?>
                                <option value="<?php echo esc_attr($category->term_id);?>"><?php echo esc_html($category->name); ?> </option>
                                <?php
                                }
                                ?>
                          </select>
                         <?php
                       
                    }
                ?>
            </div>
            <?php endif; ?>
            <!--- Category based hide price section end --->

            <?php
            $cmfwc_bg_color      = get_option( "cmfwc_bg_color" );
            $cmfwc_font_color    = get_option( "cmfwc_font_color");
            $cmfwc_img_height    = get_option( "cmfwc_img_height");
            $cmfwc_img_width     = get_option( "cmfwc_img_width");
            $cmfwc_font_size     = get_option( "cmfwc_font_size");
            $cmfwc_margin        = get_option( "cmfwc_margin");
            $cmfwc_padding       = get_option( "cmfwc_padding");
            $cmfwc_border_radius = get_option( "cmfwc_border_radius");
            ?>

            <!---Style area start --->
                    <div id="cmfwc_style_area_wrap">
                            <h3><?php echo esc_html_e('Style Area', 'customfo'); ?></h3>

                        <div class="cmfwc_img_size_setup">
                            <div class="img_height">
                                <label for="cmfwc_img_height"> <?php echo esc_html_e('Image Height', 'customfo'); ?></label>
                                <input type="number" name="cmfwc_img_height" id="cmfwc_img_height" value="<?php echo esc_attr($cmfwc_img_height);?>">
                            </div>
                            <div class="img_width">
                                <label for="cmfwc_img_height"> <?php echo esc_html_e('Image Width', 'customfo'); ?></label>
                                <input type="number" name="cmfwc_img_width" id="cmfwc_img_width" value="<?php echo esc_attr($cmfwc_img_width); ?>">
                            </div>
                        </div>
                
                        <div class="cmfwc_font_size_setup">
                            <label for="cmfwc_font_size"> <?php echo esc_html_e('Font Size', 'customfo'); ?></label>
                            <input type="number" name="cmfwc_font_size" id="cmfwc_font_size" value="<?php echo esc_attr($cmfwc_font_size); ?>">
                        </div>

                        <div class="cmfwc_bg_color_setup">
                            <label for="cmfwc_style_area_color"> <?php echo esc_html_e('Background Color', 'customfo'); ?></label>
                            <input  name="cmfwc_style_area_color" id="cmfwc_bg_color"  value="<?php echo esc_attr($cmfwc_bg_color);?>">
                        </div>
                        <div class="cmfwc_font_color_setup">
                            <label for="cmfwc_font_color"> <?php echo esc_html_e('Font Color', 'customfo'); ?></label>
                            <input name="cmfwc_font_color"  id="cmfwc_font_color" value="<?php echo esc_attr($cmfwc_font_color);?>">
                        </div>

                        <div class="cmfwc_margin_setup">
                            <label for="cmfwc_margin"> <?php echo esc_html_e('Set Margin', 'customfo'); ?></label>
                            <input type="number" name="cmfwc_margin" id="cmfwc_margin" value="<?php echo esc_attr($cmfwc_margin);?>">
                        </div>

                        <div class="cmfwc_padding_setup">
                            <label for="cmfwc_padding"> <?php echo esc_html_e('Set Padding', 'customfo'); ?></label>
                            <input type="number" name="cmfwc_padding" id="cmfwc_padding" value="<?php echo esc_attr($cmfwc_padding);?>">
                        </div>
                        <div class="cmfwc_border_radius_setup">
                            <label for="cmfwc_border_radius"> <?php echo esc_html_e('Set Border Radius', 'customfo'); ?></label>
                            <input type="number" name="cmfwc_border_radius" id="cmfwc_border_radius" value="<?php echo esc_attr($cmfwc_border_radius);?>">
                        </div>
                    </div>
            <!---Style area end --->
            
            <!---Icon Style area --->
                    <div id="cmfwc_icon_style_area_wrap">
                                <h3><?php echo esc_html_e('Icon Design', 'customfo'); ?></h3>
                        <div class="icon_setup">
                            <div class="icon_size">
                                <label for="cmfwc_icon_size"> <?php echo esc_html_e('Icon Font Size', 'customfo'); ?></label>
                                <input type="number" name="cmfwc_icon_size" id="cmfwc_icon_size" value="">
                            </div>

                            <div class="icon_color">
                                <label for="cmfwc_icon_color"> <?php echo esc_html_e('Icon Font Color', 'customfo'); ?></label>
                                <input type="text" name="cmfwc_icon_color" id="cmfwc_icon_color" value="">
                            </div>

                            <div class="icon_margin">
                                <label for="cmfwc_icon_margin"> <?php echo esc_html_e('Icon Margin', 'customfo'); ?></label>
                                <input type="number" name="cmfwc_icon_margin" id="cmfwc_icon_margin" value="">
                            </div>

                            <div class="icon_padding">
                                <label for="cmfwc_icon_padding"> <?php echo esc_html_e('Icon Padding', 'customfo'); ?></label>
                                <input type="number" name="cmfwc_icon_padding" id="cmfwc_icon_padding" value="">
                            </div>
                        </div>

                    </div>
            <!---Icon Style area end --->



            <div id="loading_img">
                <?php 
                    echo '<img src="' . esc_url( plugins_url( 'admin/assets/img/loader.gif', __FILE__ ) ) . '" id="loader_signle_img"> ';
                ?>
            </div>                  

            <button type="button" id="save_change" class="button"><?php esc_html_e('Save Change', 'customfo'); ?></button>


        </div>
    <?php

    
    
}

/* ==================Admin menu section end=============*/

/* =====================Style area==================*/
function customfo_custom_style() {
        
    // Get options with defaults if necessary
    $cmfwc_bg_color      = get_option( "cmfwc_bg_color", "#ffffff" );
    $cmfwc_font_color    = get_option( "cmfwc_font_color", "#000000" );
    $cmfwc_img_height    = get_option( "cmfwc_img_height", "25" );
    $cmfwc_img_width     = get_option( "cmfwc_img_width", "25" );
    $cmfwc_font_size     = get_option( "cmfwc_font_size", "16" );
    $cmfwc_margin        = get_option( "cmfwc_margin", "10" );
    $cmfwc_padding       = get_option( "cmfwc_padding", "10" );
    $cmfwc_border_radius = get_option( "cmfwc_border_radius", "5" );

    $cmfwc_icon_size = get_option( "cmfwc_icon_size", "13" );
    $cmfwc_icon_color = get_option( "cmfwc_icon_color", "#000000" );
    $cmfwc_icon_margin = get_option( "cmfwc_icon_margin", "2" );
    $cmfwc_icon_padding = get_option( "cmfwc_icon_padding", "2" );

    
    echo "<style type='text/css'>
        .meta_box {
            background: " . esc_attr( $cmfwc_bg_color ) . ";
            color: " . esc_attr( $cmfwc_font_color ) . ";
            margin: " . esc_attr( $cmfwc_margin ) . "px !important;
            padding: " . esc_attr( $cmfwc_padding ) . "px !important;
            border-radius: " . esc_attr( $cmfwc_border_radius ) . "px !important;
        }

        .meta_box img {
            height: " . esc_attr( $cmfwc_img_height ) . "px;
            width: " . esc_attr( $cmfwc_img_width ) . "px;
        }

        .meta_box .title {
            font-size: " . esc_attr( $cmfwc_font_size ) . "px;
        }

        .meta_box i {
            font-size: " . esc_attr( $cmfwc_icon_size ) . "px;
            color: " . esc_attr( $cmfwc_icon_color ) . ";
            margin: " . esc_attr( $cmfwc_icon_margin ) . "px !important;
            padding: " . esc_attr( $cmfwc_icon_padding ) . "px !important;
        }


    </style>";

}
add_action( "wp_head", "customfo_custom_style");
/* =====================Style area end==================*/


/* ===================== Fontawesome area Start==================*/
function my_plugin_icon_picker_init() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $(document).on( "click",".icon-picker", function() {
                $('.icon-picker').iconpicker();
            });
        });
    </script>
    <?php
}

add_action('admin_head', 'my_plugin_icon_picker_init');

/* ===================== Fontawesome area end==================*/




/*include backend and frontend files*/

include_once('admin/admin.php');
include_once('admin/repeater-field-edit-category.php');
include_once('admin/save-global-settings-data.php');
include_once('frontend/frontend.php');
include_once('frontend/display-custom-fields-categroy-based.php');

