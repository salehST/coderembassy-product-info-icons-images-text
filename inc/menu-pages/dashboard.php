<?php
if (isset($_POST['save_woo_afaq']) && check_admin_referer('save_woo_afaq_data', 'woo_afaq_nonce')) {
    $raw_faq_groups = $_POST['faq_groups'] ?? [];

    $faq_groups = [];

    foreach ($raw_faq_groups as $group) {
        if (empty($group['archive_type']) || empty($group['archive_terms'])) continue;

        $archive_type = sanitize_text_field($group['archive_type']);
        $archive_terms = array_map('intval', (array) $group['archive_terms']);

        $faqs = [];
        if (!empty($group['faqs']) && is_array($group['faqs'])) {
            foreach ($group['faqs'] as $faq) {
                $question = sanitize_text_field($faq['question'] ?? '');
                $answer = sanitize_textarea_field($faq['answer'] ?? '');
                $icon = sanitize_text_field($faq['icon'] ?? '');

                if ($question && $answer) {
                    $faqs[] = compact('question', 'answer', 'icon');
                }
            }
        }

        $faq_groups[] = [
            'archive_type' => $archive_type,
            'archive_terms' => $archive_terms,
            'faqs' => $faqs,
        ];
    }

    update_option('woo_afaq_global_groups', $faq_groups);

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Custom Meta groups saved successfully!', 'custom-meta-for-woocommerce') . '</p></div>';
}

?>

<div class="wrap">
    <div class="cmfw-product-archive">
        <h1><?php echo esc_html__('Settings for Product Archive CMFW', 'custom-meta-for-woocommerce'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('save_woo_afaq_data', 'woo_afaq_nonce'); ?>
            <div id="cmfw-groups-container"></div>
            <p><button type="button" class="button cmfw-add-cm-group"><?php echo esc_html__('Add New Custom Meta Group', 'custom-meta-for-woocommerce'); ?></button></p>
            <hr>
            <input type="submit" name="save_woo_afaq" class="button button-primary" value="<?php echo esc_attr__('Save', 'custom-meta-for-woocommerce'); ?>">
        </form>
    </div>
</div>

<!-- Templates -->
<script type="text/html" id="cmfw-cm-group-template">
    <div class="cmfw-cm-archive-group">
        <button type="button" class="button cmfw-archive-remove-cm-group"><span class="dashicons dashicons-no-alt"></span></button>
        <h2><?php echo esc_html__('Custom Meta Group', 'custom-meta-for-woocommerce'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label><?php echo esc_html__('Archive Type', 'custom-meta-for-woocommerce'); ?></label></th>
                <td>
                    <select class="archive-type" name="faq_groups[_INDEX_][archive_type]">
                        <option value=""><?php echo esc_html__('Select Archive Type', 'custom-meta-for-woocommerce'); ?></option>
                        <option value="product_cat"><?php echo esc_html__('Category', 'custom-meta-for-woocommerce'); ?></option>
                        <option value="product_tag"><?php echo esc_html__('Tag', 'custom-meta-for-woocommerce'); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="archive-term-row" style="display:none;">
                <th scope="row"><label><?php echo esc_html__('Term', 'custom-meta-for-woocommerce'); ?></label></th>
                <td>
                    <input type="text" class="archive-term regular-text" name="" placeholder="<?php echo esc_attr__('Search...', 'custom-meta-for-woocommerce'); ?>" />
                    <div class="term-suggestions"></div>
                    <div class="selected-terms"></div>
                </td>
            </tr>
        </table>

        <div class="cmfw-archive-cm-items"></div>
        <p><button type="button" class="button cmfw-archive-add-cm-item"><?php echo esc_html__('Add New Custom Meta', 'custom-meta-for-woocommerce'); ?></button></p>
    </div>
</script>

<script type="text/html" id="cmfw-archive-cm-item-template">
    <div class="cmfw-archive-cm-item">
        <button type="button" class="button cmfw-archive-remove-cm-item"><span class="dashicons dashicons-no-alt"></span></button>
        <p>
            <label><?php echo esc_html__('Custom meta title', 'custom-meta-for-woocommerce'); ?><br>
                <input type="text" name="faq_groups[_GROUP_INDEX_][faqs][_FAQ_INDEX_][question]" class="regular-text" />
            </label>
        </p>
        <p>
            <label><?php echo esc_html__('Custom meta icons', 'custom-meta-for-woocommerce'); ?> <br>
                <div class="cmfw-icon-picker-container">
                    <input type="hidden" name="faq_groups[_GROUP_INDEX_][faqs][_FAQ_INDEX_][icon]" class="cmfw-icon-value" />
                    <div class="cmfw-icon-preview" style="display: inline-block; margin-right: 10px;">
                        <span class="dashicons dashicons-admin-generic" style="font-size: 24px; width: 24px; height: 24px;"></span>
                    </div>
                    <button type="button" class="button cmfw-open-icon-picker"><?php echo esc_html__('Select Icon', 'custom-meta-for-woocommerce'); ?></button>
                </div>
            </label>
        </p>
        <p>
            <!-- <label><?php echo esc_html__('Answer', 'custom-meta-for-woocommerce'); ?><br>
                <textarea name="faq_groups[_GROUP_INDEX_][faqs][_FAQ_INDEX_][answer]" rows="3" class="large-text"></textarea>
            </label> -->
        </p>
    </div>
</script>

<?php
$saved_data = get_option('woo_afaq_global_groups', []);

if (!empty($saved_data)) {
    // Add term names to each group before sending to JS
    foreach ($saved_data as $g_index => &$group) {
        $archive_type = $group['archive_type'] ?? '';
        $term_names = [];

        if (!empty($group['archive_terms']) && taxonomy_exists($archive_type)) {
            foreach ($group['archive_terms'] as $term_id) {
                $term = get_term($term_id, $archive_type);
                if ($term && !is_wp_error($term)) {
                    $term_names[$term_id] = esc_html($term->name);
                }
            }
        }

        $group['term_names'] = $term_names;
    }
    unset($group); // Break reference
    ?>

    <script>
        jQuery(document).ready(function ($) {
            const groupTemplate = $('#cmfw-cm-group-template').html();
            const faqTemplate = $('#cmfw-archive-cm-item-template').html();

            const savedGroups = <?php echo wp_json_encode($saved_data); ?>;

            $.each(savedGroups, function (gIndex, group) {
                let groupHtml = groupTemplate.replace(/_INDEX_/g, gIndex);
                const $group = $(groupHtml);

                // Set archive type
                $group.find('select.archive-type').val(group.archive_type);
                $group.find('.archive-term-row').show();

                // Populate selected terms with name
                const selectedContainer = $group.find('.selected-terms');
                const termNames = group.term_names || {};
                if (Array.isArray(group.archive_terms)) {
                    group.archive_terms.forEach(function (termId) {
                        const termName = termNames[termId] || <?php echo wp_json_encode(__('Term #', 'custom-meta-for-woocommerce')); ?> + termId;
                        const termHtml = `<span class="term-pill" style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f1f1; border:1px solid #ccc; border-radius:20px;" data-id="${termId}">
                            ${$('<div>').text(termName).html()}
                            <a href="#" class="remove-term" style="margin-left:5px; color:red; text-decoration:none;">×</a>
                            <input type="hidden" name="faq_groups[${gIndex}][archive_terms][]" value="${termId}">
                        </span>`;

                        selectedContainer.append(termHtml);
                    });
                }

                // Add FAQs
                const faqs = group.faqs || [];
                const $faqContainer = $group.find('.cmfw-archive-cm-items');
                $.each(faqs, function (faqIndex, faq) {
                    let faqHtml = faqTemplate
                        .replace(/_GROUP_INDEX_/g, gIndex)
                        .replace(/_FAQ_INDEX_/g, faqIndex);

                    const $faq = $(faqHtml);
                    $faq.find('input[name$="[question]"]').val(faq.question);
                    $faq.find('textarea[name$="[answer]"]').val(faq.answer);
                    
                    // Handle icon field
                    if (faq.icon) {
                        $faq.find('.cmfw-icon-value').val(faq.icon);
                        $faq.find('.cmfw-icon-preview .dashicons').attr('class', 'dashicons dashicons-' + faq.icon);
                    }
                    
                    $faqContainer.append($faq);
                });

                $('#cmfw-groups-container').append($group);
            });
        });
    </script>

    <script>
    jQuery(document).ready(function($) {
        $('form').on('submit', function(e) {
            var hasError = false;
            $('.cmfw-cm-archive-group').each(function() {
                var $group = $(this);
                var archiveType = $group.find('select.archive-type').val();
                var selectedTerms = $group.find('.selected-terms input[type="hidden"]');
                if ((archiveType === 'product_cat' || archiveType === 'product_tag') && selectedTerms.length === 0) {
                    hasError = true;
                    // Show error message (only once per group)
                    if ($group.find('.fbs-term-error').length === 0) {
                        $group.find('.selected-terms').after('<div class="fbs-term-error">' + <?php echo wp_json_encode(__('Please select at least one term.', 'custom-meta-for-woocommerce')); ?> + '</div>');
                    }
                } else {
                    $group.find('.fbs-term-error').remove();
                }
            });
            if (hasError) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.fbs-term-error').first().offset().top - 100
                }, 300);
            }
        });
    });
    </script>

    <style>
    .fbs-term-error {
      color: #b32d2e;
      font-size: 14px;
      margin-top: 6px;
    }
    </style>
<?php } ?>
