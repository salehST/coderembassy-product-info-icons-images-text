jQuery(document).ready(function($) {
    // Tab functionality
    $('.woo-cmfw-settings-nav .nav-tab').on('click', function(e) {
        e.preventDefault();

        // Deactivate all tabs and content
        $('.woo-cmfw-settings-nav .nav-tab').removeClass('nav-tab-active');
        $('.woo-cmfw-settings-content .tab-content').removeClass('active');

        // Activate the clicked tab and its content
        $(this).addClass('nav-tab-active');
        var targetContent = $(this).data('target');
        $('#' + targetContent).addClass('active');

        // Save active tab to localStorage
        localStorage.setItem('cmfwAdminActiveTab', targetContent);
    });

    // Show the last active tab, or the first tab by default
    var lastTab = localStorage.getItem('cmfwAdminActiveTab');
    if (lastTab && $('.woo-cmfw-settings-nav .nav-tab[data-target="' + lastTab + '"]').length) {
        $('.woo-cmfw-settings-nav .nav-tab[data-target="' + lastTab + '"]').trigger('click');
    } else {
        // If no saved tab or saved tab doesn't exist, activate the first tab
        $('.woo-cmfw-settings-nav .nav-tab').first().addClass('nav-tab-active');
        $('.woo-cmfw-settings-content .tab-content').first().addClass('active');
    }

    // Initialize WordPress color picker
    if ($.fn.wpColorPicker) {
        $('.cmfw-color-picker').wpColorPicker({
            defaultColor: '#333333',
            create: function(event, ui) {
             setTimeout(function() {

                 $('.iris-picker').css({
                     'height': '240px',
                 });
                 $('.iris-palette').css({
                     'width': '15px',
                     'height': '15px',
                     'margin': '1px'
                 });

           }, 0);
         },
            change: function(event, ui){
                // Color picker change event - you can add custom logic here
                console.log('Color changed to: ' + ui.color.toString());
            },
            clear: function() {
                // Color picker clear event - reset to default
                $(this).val('#333333').trigger('change');
            },
            hide: true,
            palettes: [
                '#333333', '#000000', '#ffffff', '#f44336', '#e91e63', '#9c27b0',
                '#673ab7', '#3f51b5', '#2196f3', '#03a9f4', '#00bcd4', '#009688',
                '#4caf50', '#8bc34a', '#cddc39', '#ffeb3b', '#ffc107', '#ff9800',
                '#ff5722', '#795548', '#9e9e9e', '#607d8b'
            ]
        });
    }

    // Add some visual feedback for form elements
    $('.form-table input[type="text"], .form-table input[type="number"], .form-table select').on('focus', function() {
        $(this).closest('tr').addClass('highlight');
    }).on('blur', function() {
        $(this).closest('tr').removeClass('highlight');
    });
}); 
