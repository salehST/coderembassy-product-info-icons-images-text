(function ($) {
  $(document).ready(function () {
    const isProActive = typeof wooFaqPro !== 'undefined' && wooFaqPro.is_pro;
    const MAX_SINGLE_FAQS = isProActive ? Infinity : 3;
    const MAX_GROUPS_FREE = isProActive ? Infinity : 2;
    const MAX_FAQS_FREE = isProActive ? Infinity : 3;

    // Declare a global counter to track the number of FAQs
    var faqCounter = 1;
    // Disable the add button on page load if limit is reached
    if ($("div.option-group-wrapper .options_group").length >= MAX_SINGLE_FAQS) {
      const $btn = $(".faq-add-question");
      const $newBtn = $('<a href="https://wpbay.com/product/product-faq-for-woocommerce-pro/" target="_blank" class="button fbs-upgrade-button" style="background-color: #ff9800; border-color: #ff9800; color: #fff;">Upgrade</a>');
      $btn.replaceWith($newBtn);
    }

    $(document.body).on("click", ".faq-add-question", function () {
      const $addBtn = $(this);
      const currentFaqs = $("div.option-group-wrapper .options_group").length;
      if (currentFaqs >= MAX_SINGLE_FAQS) {
        const $newBtn = $('<a href="https://wpbay.com/product/product-faq-for-woocommerce-pro/" target="_blank" class="button fbs-upgrade-button" style="background-color: #ff9800; border-color: #ff9800; color: #fff;">Upgrade</a>');
        $addBtn.replaceWith($newBtn);
        alert("Upgrade to the Pro version to add more than 3 FAQs per product.");
        return;
      }

      var lastFaqNumber = $(
        "div.option-group-wrapper .options_group .faq-question-box"
      ).length;
      // Use the counter value directly and increment it for each new FAQ
      var faqNumber = faqCounter + lastFaqNumber;

      // Use template literals for better readability
      var myElement = `
              <div class="options_group">
                  <button type="button" class="faq-remove-question" style="float:right; background:#fff; color:#b32d2e; border-color:#b32d2e; margin-top:5px; padding:0; border-radius: 50%;"><span class="dashicons dashicons-no-alt"></span></button>
                  <p class="form-field faq_${faqNumber}_field">
                      <label for="faq_${faqNumber}">Question</label>
                      <input type="text" class="faq_input" name="faq[question][${faqNumber}]" id="faq_${faqNumber}" value="" placeholder="Add Question">
                  </p>
                  <p class="form-field faq_ans_${faqNumber}_field">
                      <label for="faq_ans_${faqNumber}">Answer</label>
                      <input type="text" class="faq_input" name="faq[answer][${faqNumber}]" id="faq_ans_${faqNumber}" value="" placeholder="Add Answer">
                  </p>
                  
              </div>
          `;

      // Append the new FAQ input fields
      $("div.option-group-wrapper").append(myElement);

      // Increment the counter for the next click
      faqCounter++;

      // Restore the add button if under the limit (in case FAQs are removed)
      if ($("div.option-group-wrapper .options_group").length < MAX_SINGLE_FAQS) {
        const $upgradeBtn = $(".fbs-upgrade-button");
        if ($upgradeBtn.length) {
          const $newBtn = $('<button type="button" class="button faq-add-question">Add Question</button>');
          $upgradeBtn.replaceWith($newBtn);
        }
      }
    });

    // If you have a remove FAQ handler for single product, add this logic there as well:
          // After removing an FAQ, restore the add button if under the limit
      $(document.body).on("click", ".faq-remove-question", function () {
        $(this).closest(".options_group").remove();
        // Restore the add button if under the limit
        if ($("div.option-group-wrapper .options_group").length < MAX_SINGLE_FAQS) {
          const $upgradeBtn = $(".fbs-upgrade-button");
          if ($upgradeBtn.length) {
            const $newBtn = $('<button type="button" class="button faq-add-question">Add Question</button>');
            $upgradeBtn.replaceWith($newBtn);
          }
        }
      });

    // Archive FAQ code start here

    $(document.body).on("click", ".cmfw-add-cm-group", function () {
      const currentGroups = $(
        "#cmfw-groups-container .cmfw-cm-archive-group"
      ).length;

      if (currentGroups >= MAX_GROUPS_FREE) {
        alert("Upgrade to the Pro version to add more than 2 FAQ groups.");
        return;
      }

      const groupIndex = currentGroups;
      let groupHtml = $("#cmfw-cm-group-template")
        .html()
        .replace(/_INDEX_/g, groupIndex);
      $("#cmfw-groups-container").append(groupHtml);

      // Disable the add group button if max reached
      if (groupIndex + 1 >= MAX_GROUPS_FREE) {
        const $btn = $(".cmfw-add-cm-group");
        const $newBtn = $('<a href="https://wpbay.com/product/product-faq-for-woocommerce-pro/" target="_blank" class="button fbs-upgrade-button" style="background-color: #ff9800; border-color: #ff9800; color: #fff;">Upgrade</a>');
        $btn.replaceWith($newBtn);
      }
    });

    // Remove FAQ Group
    $("#cmfw-groups-container").on(
      "click",
      ".cmfw-archive-remove-cm-group",
      function () {
        $(this).closest(".cmfw-cm-archive-group").remove();

        const currentGroups = $(
          "#cmfw-groups-container .cmfw-cm-archive-group"
        ).length;

        if (currentGroups < MAX_GROUPS_FREE) {
          const $upgradeBtn = $(".fbs-upgrade-button");
          if ($upgradeBtn.length) {
            const $newBtn = $('<button type="button" class="button cmfw-add-cm-group">Add CMFW Group</button>');
            $upgradeBtn.replaceWith($newBtn);
          }
        }
      }
    );

    // Add FAQ Item
    $("#cmfw-groups-container").on(
      "click",
      ".cmfw-archive-add-cm-item",
      function () {
        const groupEl = $(this).closest(".cmfw-cm-archive-group");
        const currentFaqs = groupEl.find(".cmfw-archive-cm-item").length;

        if (currentFaqs >= MAX_FAQS_FREE) {
          alert(
            "Upgrade to the Pro version to add more than 3 CMFW per group."
          );
          return;
        }

        const groupIndex = groupEl.index();
        const faqIndex = currentFaqs;
        let faqTemplate = $("#cmfw-archive-cm-item-template").html();
        faqTemplate = faqTemplate
          .replace(/_GROUP_INDEX_/g, groupIndex)
          .replace(/_FAQ_INDEX_/g, faqIndex);

        groupEl.find(".cmfw-archive-cm-items").append(faqTemplate);

        // Disable the button if max reached
        if (faqIndex + 1 >= MAX_FAQS_FREE) {
          const $btn = groupEl.find(".cmfw-archive-add-cm-item");
          const $newBtn = $('<a href="https://wpbay.com/product/product-faq-for-woocommerce-pro/" target="_blank" class="button fbs-upgrade-button" style="background-color: #ff9800; border-color: #ff9800; color: #fff;">Upgrade</a>');
          $btn.replaceWith($newBtn);
        }
      }
    );

    // Remove FAQ Item
    $("#cmfw-groups-container").on(
      "click",
      ".cmfw-archive-remove-cm-item",
      function () {
        const groupEl = $(this).closest(".cmfw-cm-archive-group");
        $(this).closest(".cmfw-archive-cm-item").remove();

        const currentFaqs = groupEl.find(".cmfw-archive-cm-item").length;

        if (currentFaqs < MAX_FAQS_FREE) {
          const $upgradeBtn = groupEl.find(".fbs-upgrade-button");
          if ($upgradeBtn.length) {
            const $newBtn = $('<button type="button" class="button cmfw-archive-add-cm-item">Add New FAQ</button>');
            $upgradeBtn.replaceWith($newBtn);
          }
        }
      }
    );

    $(document.body).on("click", ".fbs-upgrade-button", function (e) {
      e.preventDefault();
      e.stopPropagation();
      window.open("https://wpbay.com/product/product-faq-for-woocommerce-pro/", "_blank");
    });

    // Show/hide archive term row
    $("#cmfw-groups-container").on("change", "select.archive-type", function () {
      const selected = $(this).val();
      const $termRow = $(this).closest("table").find(".archive-term-row");
      if (selected === "product_cat" || selected === "product_tag") {
        $termRow.show();
      } else {
        $termRow.hide();
      }
    });

    // Delegate input event on term field
    $("#cmfw-groups-container").on("focus", ".archive-term", function () {
      const $input = $(this);
      const $group = $input.closest(".cmfw-cm-archive-group");
      const $select = $group.find(".archive-type");
      const taxonomy = $select.val();

      if (!taxonomy) return;

      $input.autocomplete({
        source: function (request, response) {
          if (request.term.length < 3) return;

          $.getJSON(
            faqAjax.ajax_url,
            {
              action: "faq_term_search",
              nonce: faqAjax.nonce,
              taxonomy: taxonomy,
              term: request.term,
            },
            function (data) {
              response(data);
            }
          );
        },
        minLength: 3,
        select: function (event, ui) {
          event.preventDefault();
          $input.val("");

          const $selectedTerms = $group.find(".selected-terms");

          if (
            $selectedTerms.find('input[value="' + ui.item.value + '"]').length
          ) {
            return;
          }

          const selectedHtml = `
                      <span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;">
                          ${ui.item.label}
                          <a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">&times;</a>
                          <input type="hidden" name="faq_groups[${$group.index()}][archive_terms][]" value="${
            ui.item.value
          }">
                      </span>
                  `;
          $selectedTerms.append(selectedHtml);
        },
      });
    });

    // Remove selected term
    $("#cmfw-groups-container").on("click", ".remove-term", function (e) {
      e.preventDefault();
      $(this).closest(".term-pill").remove();
    });

    // Show/hide archive term row + reset inputs
    $("#cmfw-groups-container").on("change", "select.archive-type", function () {
      const $group = $(this).closest(".cmfw-cm-archive-group");
      const selected = $(this).val();
      const $termRow = $group.find(".archive-term-row");

      if (selected === "product_cat" || selected === "product_tag") {
        $termRow.show();
      } else {
        $termRow.hide();
        $termRow.find(".archive-term").val("");
        $termRow.find(".selected-terms").empty();
      }
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
        $preview.find('.dashicons').attr('class', `dashicons dashicons-${selectedIcon}`);
        $input.val(selectedIcon);
        $modal.hide();
      });
    }
  });
})(jQuery);
