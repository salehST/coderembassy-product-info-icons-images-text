(function ($) {
  $(document).ready(function () {
    // Groups UI (dashboard)
    function reindexAll() {
      $('#cmfw-groups-container .cmfw-group').each(function (gIdx) {
        const $group = $(this);
        $group.attr('data-group-index', gIdx);
        $group.find('.cmfw-item').each(function (iIdx) {
          const $item = $(this);
          $item.find('input[name$="[title]"]').attr('name', `cmfw_groups[${gIdx}][items][${iIdx}][title]`);
          $item.find('.cmfw-icon-value').attr('name', `cmfw_groups[${gIdx}][items][${iIdx}][icon]`);
          $item.find('.cmfw-image-value').attr('name', `cmfw_groups[${gIdx}][items][${iIdx}][image_id]`);
        });
        // Ensure taxonomy and term hidden inputs are properly named
        $group.find('select.taxonomy-select').attr('name', `cmfw_groups[${gIdx}][taxonomy]`);
        $group.find('.selected-terms input[type="hidden"]').each(function(){
          $(this).attr('name', `cmfw_groups[${gIdx}][terms][]`);
        });
      });
    }

    $(document.body).on('click', '.cmfw-add-group', function () {
      const groupIndex = $('#cmfw-groups-container .cmfw-group').length;
      let groupHtml = $('#cmfw-group-template').html().replace(/_INDEX_/g, groupIndex);
      $('#cmfw-groups-container').append(groupHtml);
      reindexAll();
    });

    $('#cmfw-groups-container').on('click', '.cmfw-remove-group', function () {
      $(this).closest('.cmfw-group').remove();
      reindexAll();
    });

    $('#cmfw-groups-container').on('click', '.cmfw-add-item', function () {
      const $group = $(this).closest('.cmfw-group');
      const groupIndex = $('#cmfw-groups-container .cmfw-group').index($group);
      const itemIndex = $group.find('.cmfw-item').length;
      let itemHtml = $('#cmfw-item-template').html()
        .replace(/_GROUP_INDEX_/g, groupIndex)
        .replace(/_ITEM_INDEX_/g, itemIndex);
      $group.find('.cmfw-items').append(itemHtml);
      reindexAll();
    });

    $('#cmfw-groups-container').on('click', '.cmfw-remove-item', function () {
      $(this).closest('.cmfw-item').remove();
      reindexAll();
    });

    // Taxonomy show/hide terms row
    $('#cmfw-groups-container').on('change', 'select.taxonomy-select', function(){
      const $group = $(this).closest('.cmfw-group');
      const val = $(this).val();
      const $row = $group.find('.term-row');
      if (val === 'product_cat' || val === 'product_tag') {
        $row.show();
      } else {
        $row.hide();
        $group.find('.term-search').val('');
        $group.find('.selected-terms').empty();
      }
    });

    // Term autocomplete
    $('#cmfw-groups-container').on('focus', '.term-search', function(){
      const $input = $(this);
      const $group = $input.closest('.cmfw-group');
      const taxonomy = $group.find('.taxonomy-select').val();
      if (!taxonomy) return;

      if (typeof $input.autocomplete !== 'function') return; // jQuery UI may not be present

      $input.autocomplete({
        source: function(request, response){
          if ((request.term || '').length < 2) return;
          $.getJSON(
            cmfwAjax.ajax_url,
            {
              action: 'cmfw_term_search',
              nonce: cmfwAjax.nonce,
              taxonomy: taxonomy,
              term: request.term
            },
            function(data){ response(data || []); }
          );
        },
        minLength: 2,
        select: function(event, ui){
          event.preventDefault();
          $input.val('');
          const $selected = $group.find('.selected-terms');
          if ($selected.find('input[value="' + ui.item.value + '"]').length) return;
          const pill = `
                      <span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;">
                          ${ui.item.label}
                          <a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">&times;</a>
              <input type="hidden" name="cmfw_groups[${$('#cmfw-groups-container .cmfw-group').index($group)}][terms][]" value="${ui.item.value}">
            </span>`;
          $selected.append(pill);
          reindexAll();
        }
      });
    });

    // Remove selected term pill
    $('#cmfw-groups-container').on('click', '.remove-term', function(e){
      e.preventDefault();
      $(this).closest('.term-pill').remove();
      reindexAll();
    });

    // Enforce exclusivity: selecting an icon clears image and disables image picker; selecting an image clears icon and disables icon picker
    function updateExclusivity($item){
      const hasIcon = ($item.find('.cmfw-icon-value').val() || '').trim() !== '';
      const hasImage = ($item.find('.cmfw-image-value').val() || '').trim() !== '';

      // Icon section controls
      const $iconPickerBtn = $item.find('.cmfw-open-icon-picker');
      const $iconRemoveBtn = $item.find('.cmfw-remove-icon');
      // Image section controls
      const $imgSelectBtn = $item.find('.cmfw-select-image');
      const $imgRemoveBtn = $item.find('.cmfw-remove-image');
      const $noImage = $item.find('.cmfw-no-image');
      const $img = $item.find('.cmfw-image-preview img');

      if (hasIcon) {
        // Disable image controls
        $imgSelectBtn.prop('disabled', true).addClass('is-disabled');
        if (!$img.is(':visible')) { $noImage.show(); }
        $item.find('.cmfw-no-icon').hide();
        $item.find('.cmfw-icon-preview .dashicons').show();
        $iconRemoveBtn.show();
      } else {
        $imgSelectBtn.prop('disabled', false).removeClass('is-disabled');
        $iconRemoveBtn.hide();
        $item.find('.cmfw-icon-preview .dashicons').hide();
        $item.find('.cmfw-no-icon').show();
      }

      if (hasImage) {
        // Disable icon controls
        $iconPickerBtn.prop('disabled', true).addClass('is-disabled');
      } else {
        $iconPickerBtn.prop('disabled', false).removeClass('is-disabled');
      }
    }

    // After adding item, ensure exclusivity state
    $('#cmfw-groups-container').on('click', '.cmfw-add-item', function(){
      const $item = $(this).closest('.cmfw-group').find('.cmfw-item').last();
      updateExclusivity($item);
    });

    // Click icon area to open picker
    $("#cmfw-groups-container").on('click', '.cmfw-icon-preview.cmfw-clickable', function(e){
      e.preventDefault();
      const $container = $(this).closest('.cmfw-icon-picker-container');
      const $preview = $container.find('.cmfw-icon-preview');
      const $input = $container.find('.cmfw-icon-value');
      showDashiconPicker($preview, $input);
    });

    // Dashicon picker functionality
    $("#cmfw-groups-container").on("click", ".cmfw-open-icon-picker", function(e) {
      e.preventDefault();
      const $button = $(this);
      const $container = $button.closest('.cmfw-icon-picker-container');
      const $preview = $container.find('.cmfw-icon-preview');
      const $input = $container.find('.cmfw-icon-value');
      
      // Create or show the icon picker modal
      showDashiconPicker($preview, $input);
    });

    // Function to show the Dashicon picker modal
    function showDashiconPicker($preview, $input) {
      // Check if modal already exists
      let $modal = $('#cmfw-dashicon-picker-modal');
      if ($modal.length === 0) {
        // Create the modal HTML
        const modalHtml = `
          <div id="cmfw-dashicon-picker-modal" class="cmfw-modal" style="display: none;">
            <div class="cmfw-modal-overlay"></div>
            <div class="cmfw-modal-content">
              <div class="cmfw-modal-header">
                <h2>Select Dashicon</h2>
                <button type="button" class="cmfw-modal-close">&times;</button>
              </div>
              <div class="cmfw-modal-body">
                <div class="cmfw-icon-search">
                  <input type="text" id="cmfw-icon-search-input" placeholder="Search icons..." />
                </div>
                <div class="cmfw-icon-grid"></div>
              </div>
            </div>
          </div>
        `;
        $('body').append(modalHtml);
        $modal = $('#cmfw-dashicon-picker-modal');
      }

      // Populate the icon grid
      populateIconGrid($modal, $preview, $input);

      // Show the modal
      $modal.show();

      // Close modal handler
      $modal.find('.cmfw-modal-close, .cmfw-modal-overlay').on('click', function() {
        $modal.hide();
      });

      // Search functionality
      $modal.find('#cmfw-icon-search-input').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $modal.find('.cmfw-icon-item').each(function() {
          const iconName = $(this).data('icon-name');
          if (iconName.includes(searchTerm)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });
    }

    // Function to populate the icon grid with Dashicons
    function populateIconGrid($modal, $preview, $input) {
      // List of common Dashicons (this is not exhaustive but includes many commonly used ones)
      const dashicons = [
        'admin-appearance', 'admin-collapse', 'admin-comments', 'admin-customizer', 'admin-dashboard',
        'admin-generic', 'admin-home', 'admin-links', 'admin-media', 'admin-multisite', 'admin-network',
        'admin-page', 'admin-plugins', 'admin-post', 'admin-settings', 'admin-site', 'admin-site-alt',
        'admin-site-alt2', 'admin-site-alt3', 'admin-tools', 'admin-users', 'airplane', 'album', 'align-center',
        'align-full-width', 'align-left', 'align-none', 'align-pull-left', 'align-pull-right', 'align-right',
        'align-wide', 'amazon', 'analytics', 'archive', 'arrow-down', 'arrow-down-alt', 'arrow-down-alt2',
        'arrow-left', 'arrow-left-alt', 'arrow-left-alt2', 'arrow-right', 'arrow-right-alt', 'arrow-right-alt2',
        'arrow-up', 'arrow-up-alt', 'arrow-up-alt2', 'art', 'awards', 'backup', 'beer', 'bell', 'block-default',
        'book', 'book-alt', 'buddicons-activity', 'buddicons-bbpress-logo', 'buddicons-buddypress-logo',
        'buddicons-community', 'buddicons-forums', 'buddicons-friends', 'buddicons-groups', 'buddicons-pm',
        'buddicons-replies', 'buddicons-topics', 'buddicons-tracking', 'building', 'building-42', 'businessman',
        'businessperson', 'businesswoman', 'button', 'calculator', 'calendar', 'calendar-alt', 'camera', 'camera-alt',
        'car', 'category', 'chart-area', 'chart-bar', 'chart-line', 'chart-pie', 'clipboard', 'clock', 'cloud',
        'controls-back', 'controls-forward', 'controls-pause', 'controls-play', 'controls-repeat', 'controls-skipback',
        'controls-skipforward', 'controls-volumeoff', 'controls-volumeon', 'cover-image', 'dashboard', 'database',
        'database-add', 'database-export', 'database-import', 'database-remove', 'database-view', 'desktop',
        'dismiss', 'download', 'drumstick', 'edit', 'edit-large', 'edit-page', 'editor-aligncenter', 'editor-alignleft',
        'editor-alignright', 'editor-bold', 'editor-break', 'editor-code', 'editor-code-duplicate', 'editor-contract',
        'editor-customchar', 'editor-expand', 'editor-help', 'editor-indent', 'editor-insertmore', 'editor-italic',
        'editor-justify', 'editor-kitchensink', 'editor-ltr', 'editor-ol', 'editor-ol-rtl', 'editor-outdent',
        'editor-paragraph', 'editor-paste-text', 'editor-paste-word', 'editor-quote', 'editor-removeformatting',
        'editor-rtl', 'editor-spellcheck', 'editor-strikethrough', 'editor-table', 'editor-textcolor', 'editor-underline',
        'editor-unlink', 'editor-ul', 'email', 'email-alt', 'email-alt2', 'excerpt-view', 'external', 'facebook',
        'facebook-alt', 'feedback', 'filter', 'flag', 'food', 'format-aside', 'format-audio', 'format-chat', 'format-gallery',
        'format-image', 'format-quote', 'format-status', 'format-video', 'forms', 'fullscreen-alt', 'fullscreen-exit-alt',
        'games', 'google', 'grid-view', 'groups', 'hammer', 'heading', 'heart', 'hidden', 'hourglass', 'html', 'id',
        'id-alt', 'image-crop', 'image-filter', 'image-flip', 'image-rotate', 'image-rotate-left', 'image-rotate-right',
        'images-alt', 'images-alt2', 'index-card', 'info', 'info-outline', 'insert-after', 'insert-before', 'insert',
        'instagram', 'keyboard-hide', 'laptop', 'layout', 'leftright', 'lightbulb', 'list-view', 'location', 'location-alt',
        'lock', 'marker', 'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document', 'media-interactive',
        'media-spreadsheet', 'media-text', 'media-video', 'megaphone', 'menu', 'menu-alt', 'menu-alt2', 'menu-alt3',
        'microphone', 'migrate', 'minus', 'money', 'move', 'nametag', 'networking', 'no', 'no-alt', 'palmtree', 'paperclip',
        'pdf', 'performance', 'pets', 'phone', 'pinterest', 'playlist-audio', 'playlist-video', 'plus', 'plus-alt',
        'plus-alt2', 'portfolio', 'post-status', 'pressthis', 'products', 'publish', 'randomize', 'redo', 'remove',
        'rest-api', 'rss', 'saved', 'schedule', 'screenoptions', 'search', 'share', 'share-alt', 'share-alt2', 'shield',
        'shield-alt', 'shortcode', 'slides', 'smartphone', 'smiley', 'sort', 'sos', 'spotify', 'star-empty', 'star-filled',
        'star-half', 'sticky', 'store', 'tablet', 'tag', 'tagcloud', 'testimonial', 'text', 'text-page', 'thumbs-down',
        'thumbs-up', 'tickets', 'tickets-alt', 'tide', 'translation', 'trash', 'twitch', 'twitter', 'undo', 'universal-access',
        'universal-access-alt', 'unlock', 'update', 'update-alt', 'upload', 'vault', 'video-alt', 'video-alt2', 'video-alt3',
        'visibility', 'warning', 'welcome-add-page', 'welcome-comments', 'welcome-learn-more', 'welcome-view-site',
        'welcome-widgets-menus', 'wordpress', 'wordpress-alt', 'yes', 'yes-alt'
      ];

      const $grid = $modal.find('.cmfw-icon-grid');
      $grid.empty();

      dashicons.forEach(function(iconName) {
        const iconHtml = `
          <div class="cmfw-icon-item" data-icon-name="${iconName}">
            <span class="dashicons dashicons-${iconName}" style="font-size: 24px; width: 24px; height: 24px;"></span>
            <span class="cmfw-icon-name">${iconName}</span>
          </div>
        `;
        $grid.append(iconHtml);
      });

      // Handle icon selection
      $grid.find('.cmfw-icon-item').on('click', function() {
        const selectedIcon = $(this).data('icon-name');
        $preview.find('.dashicons').attr('class', `dashicons dashicons-${selectedIcon}`).show();
        $preview.closest('.cmfw-icon-preview').find('.cmfw-no-icon').hide();
        $input.val(selectedIcon);
        // Clear image if any and update exclusivity in the parent item
        const $item = $input.closest('.cmfw-item');
        const $img = $item.find('.cmfw-image-preview img');
        const $noImage = $item.find('.cmfw-no-image');
        const $imgRemoveBtn = $item.find('.cmfw-remove-image');
        const $imgInp = $item.find('.cmfw-image-value');
        if ($imgInp.val()) {
          $imgInp.val('');
          $img.hide().attr('src', '');
          $noImage.show();
          $imgRemoveBtn.hide();
        }
        updateExclusivity($item);
        $modal.hide();
      });
    }

    // WordPress Media Library Integration for Image Upload
    var cmfwMediaFrame;

    // Click image area to open media frame
    $("#cmfw-groups-container").on("click", ".cmfw-image-preview.cmfw-clickable", function(e) {
      e.preventDefault();
      
      const $container = $(this).closest('.cmfw-image-picker-container');
      const $preview = $container.find('.cmfw-image-preview');
      const $input = $container.find('.cmfw-image-value');
      const $removeBtn = $container.find('.cmfw-remove-image');
      const $noImage = $container.find('.cmfw-no-image');
      const $img = $preview.find('img');

      // If the media frame already exists, reopen it
      if (cmfwMediaFrame) {
        cmfwMediaFrame.open();
        return;
      }

      // Create the media frame
      cmfwMediaFrame = wp.media({
        title: cmfwAjax.media_title,
        button: {
          text: cmfwAjax.media_button
        },
        multiple: false,
        library: {
          type: 'image'
        }
      });

      // When an image is selected, run a callback
      cmfwMediaFrame.on('select', function() {
        const attachment = cmfwMediaFrame.state().get('selection').first().toJSON();
        
        // Set the attachment ID
        $input.val(attachment.id);
        
        // Set the image preview
        const imageUrl = attachment.sizes && attachment.sizes.thumbnail
          ? attachment.sizes.thumbnail.url
          : attachment.url;
        
        $img.attr('src', imageUrl).show();
        $noImage.hide();
        $removeBtn.show();
        // Clear icon and update exclusivity
        const $item = $container.closest('.cmfw-item');
        $item.find('.cmfw-icon-value').val('');
        $item.find('.cmfw-icon-preview .dashicons').attr('class', 'dashicons dashicons-admin-generic');
        updateExclusivity($item);
      });

      // Open the media frame
      cmfwMediaFrame.open();
    });

    // Handle image removal
    $("#cmfw-groups-container").on("click", ".cmfw-remove-image", function(e) {
      e.preventDefault();
      
      const $button = $(this);
      const $container = $button.closest('.cmfw-image-picker-container');
      const $preview = $container.find('.cmfw-image-preview');
      const $input = $container.find('.cmfw-image-value');
      const $noImage = $container.find('.cmfw-no-image');
      const $img = $preview.find('img');

      // Clear the input value
      $input.val('');
      
      // Hide image preview and show no-image placeholder
      $img.hide().attr('src', '');
      $noImage.show();
      $button.hide();
      const $item = $button.closest('.cmfw-item');
      updateExclusivity($item);
    });

    // Handle icon removal
    $("#cmfw-groups-container").on('click', '.cmfw-remove-icon', function(e){
      e.preventDefault();
      const $btn = $(this);
      const $item = $btn.closest('.cmfw-item');
      $item.find('.cmfw-icon-value').val('');
      $item.find('.cmfw-icon-preview .dashicons').attr('class', 'dashicons').hide();
      $item.find('.cmfw-no-icon').show();
      $btn.hide();
      updateExclusivity($item);
    });

    // Load existing images on page load
    function loadExistingImages() {
      $('.cmfw-image-picker-container[data-image-id]').each(function() {
        const $container = $(this);
        const imageId = $container.attr('data-image-id');
        
        if (imageId && imageId > 0) {
          // Make AJAX call to get image URL
          $.ajax({
            url: cmfwAjax.ajax_url,
            type: 'POST',
            data: {
              action: 'cmfw_get_image_url',
              nonce: cmfwAjax.nonce,
              image_id: imageId
            },
            success: function(response) {
              if (response.success && response.data.url) {
                const $preview = $container.find('.cmfw-image-preview');
                const $img = $preview.find('img');
                const $noImage = $container.find('.cmfw-no-image');
                const $removeBtn = $container.find('.cmfw-remove-image');
                
                $img.attr('src', response.data.url).show();
                $noImage.hide();
                $removeBtn.show();
              }
            }
          });
        }
      });
    }

    // Load existing images when page is ready
    $(document).ready(function() {
      setTimeout(loadExistingImages, 500); // Small delay to ensure DOM is fully ready
      // Initialize exclusivity for existing items
      $('#cmfw-groups-container .cmfw-item').each(function(){ updateExclusivity($(this)); });
    });

  });
})(jQuery);
