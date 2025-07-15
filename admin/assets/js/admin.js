jQuery(document).ready(function ($) { 

let counter = 0;


$('#add-repeater-field').on('click', function () {
    if (counter < 3) {
        counter++;
        $('#repeater-wrapper').append(`
            <div class="field-wrapper">
                <label for="icon_class_${counter}">Icon Class</label>
                <input type="text" class="icon-picker" name="custom_repeater_field[${counter}][icon_class]" id="icon_class_${counter}" value="" placeholder="Click to Select Icon" />

                <label for="title_${counter}">Title</label>
                <input type="text" name="custom_repeater_field[${counter}][title]" id="title_${counter}" class="title" placeholder="Give title text" />

                <input type="hidden" name="custom_repeater_field[${counter}][image]" class="image" />
                <button type="button" class="upload_image_button button">Upload/Add image</button>
                <button type="button" class="remove_field_button button">Remove</button>
                <div class="image_preview"></div>
            </div><br/>
        `);
        
        if (counter === 3) {
            $('#add-repeater-field').prop('disabled', true).text('Limit Reached');
        }
    }
});


//part two hide add field button
 function toggleAddButton() {
        if ($('.field-wrapper').length >= 3) {
            $('#add-repeater-field').hide();
        } else {
            $('#add-repeater-field').show();
        }
    }

    // Run on page load
    toggleAddButton();

   
     // Uploading files
     jQuery(document).on('click', '.upload_image_button', function(event) {
        event.preventDefault();
        var button = $(this);
        var input = button.prev();
        var imagePreview = button.nextAll('.image_preview');
        
        var file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select an image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            // console.log(attachment.url);
            input.val(attachment.id);
            // imagePreview.html('<img src="' + attachment.url + '" style="max-width:100%;"/>');
            imagePreview.html('<img width="150" height="113" src="' + attachment.url + '" class="attachment-thumbnail size-thumbnail" alt="">');
        });

        // console.log(file_frame);

        file_frame.open();
    });

 

    // Remove field
    $(document).on('click', '.remove_field_button', function() {
        $(this).closest('.field-wrapper').remove();
    });



/*Gobal section start */
  $("#save_change").on("click",function(){

    let cmfw_category_list = $("#cmfw_category_list").val();
    let cmfw_img_height = $("#cmfwc_img_height").val();
    let cmfw_img_width = $("#cmfwc_img_width").val();

    let cmfwc_font_size = $("#cmfwc_font_size").val();
    let cmfwc_bg_color = $("#cmfwc_bg_color").val();
    let cmfwc_font_color = $("#cmfwc_font_color").val();

    let cmfwc_margin = $("#cmfwc_margin").val();
    let cmfwc_padding = $("#cmfwc_padding").val();
    let cmfwc_border_radius = $("#cmfwc_border_radius").val();

    let cmfwc_icon_size = $("#cmfwc_icon_size").val();
    let cmfwc_icon_color = $("#cmfwc_icon_color").val();
    let cmfwc_icon_margin = $("#cmfwc_icon_margin").val();
    let cmfwc_icon_padding = $("#cmfwc_icon_padding").val();



    var repeaterFields = [];
    $('#repeater-wrapper .field-wrapper').each(function (index, element) {
        var fieldData = {
            icon_class: $(this).find('.icon-picker').val(),
            title: $(this).find('.title').val(),
            image: $(this).find('.image').val(),
        };
        repeaterFields.push(fieldData);
    });

    // console.log(repeaterFields); 


    $("#loader_signle_img").show();

    $.ajax({

        type:'POST',
        url: ajax_object.ajax_url,
        data: {
            action:'cffw_save_global_data',
            cmfwc_nonce: ajax_object.cmfwc_nonce,
            repeaterFields: repeaterFields,
            category_list: cmfw_category_list,
            img_height: cmfw_img_height,
            img_width: cmfw_img_width,
            font_size: cmfwc_font_size,
            bg_color: cmfwc_bg_color,
            font_color: cmfwc_font_color,
            margin: cmfwc_margin,
            padding: cmfwc_padding,
            border_radius: cmfwc_border_radius,

            icon_size: cmfwc_icon_size,
            icon_front_color: cmfwc_icon_color,
            icon_margin: cmfwc_icon_margin,
            icon_padding: cmfwc_icon_padding,
        },
        success: function(response){
            console.log(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        },
        complete: function(jqXHR) {
         
            if(jqXHR.readyState === 4) {
                let response = jqXHR.statusText;
                if(response=='OK'){
                    jQuery("#loader_signle_img").hide();
                    
                }
            }   
        } 
    });

  
   

  });

$(".select2-category-dropdown").select2({
    placeholder: "Select categories",
    allowClear: true
});  

 

/*Gobal section end */

});

// colorpicker 
var myPicker = new JSColor('#cmfwc_font_color', {format:'rgba'});

// let's additionally set an option
myPicker.option('previewSize', 40);

// we can also set multiple options at once
myPicker.option({
	'width': 200,
    'height': 200,
	'position': 'top',
    'preset':'light medium',
	'backgroundColor': '#333',
});

// color pick 2 

var myPicker2 = new JSColor('#cmfwc_bg_color', {format:'rgba'});

// let's additionally set an option
myPicker2.option('previewSize', 40);

// we can also set multiple options at once
myPicker2.option({
	'width': 200,
    'height': 200,
	'position': 'top',
    'preset':'light medium',
	'backgroundColor': '#333',
});


// icon color section 



var iconColorPicker = new JSColor('#cmfwc_icon_color', {format:'rgba'});

iconColorPicker.option('previewSize', 40);

iconColorPicker.option({
	'width': 200,
    'height': 200,
	'position': 'top',
    'preset':'light medium',
	'backgroundColor': '#333',
});
