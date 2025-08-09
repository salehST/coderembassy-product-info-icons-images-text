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
            defaultColor: false,
            change: function(event, ui){
                // Color picker change event
            },
            clear: function() {
                // Color picker clear event
            },
            hide: true,
            palettes: true
        });
    }

    // Add some visual feedback for form elements
    $('.form-table input[type="text"], .form-table input[type="number"], .form-table select').on('focus', function() {
        $(this).closest('tr').addClass('highlight');
    }).on('blur', function() {
        $(this).closest('tr').removeClass('highlight');
    });
}); 