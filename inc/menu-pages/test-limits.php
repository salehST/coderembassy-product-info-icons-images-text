<?php
// Test page to verify free version limits
if (!current_user_can('manage_options')) {
    return;
}

$pro_active = cmfw_is_pro_active();
$current_groups = get_option('cmfw_groups', []);
$group_count = count($current_groups);
$max_groups = $pro_active ? 'Unlimited' : '2';
$max_items = $pro_active ? 'Unlimited' : '3';

// Count total items across all groups
$total_items = 0;
foreach ($current_groups as $group) {
    $total_items += count($group['items'] ?? []);
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('Free Version Limits Test', 'coderembassy-product-info-icons-images-text'); ?></h1>
    
    <div class="cmfw-test-limits">
        <h2><?php echo esc_html__('Current Status', 'coderembassy-product-info-icons-images-text'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php echo esc_html__('PRO Version Active', 'coderembassy-product-info-icons-images-text'); ?></th>
                <td>
                    <span style="color: <?php echo $pro_active ? 'green' : 'red'; ?>; font-weight: bold;">
                        <?php echo $pro_active ? 'Yes' : 'No'; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Current Groups', 'coderembassy-product-info-icons-images-text'); ?></th>
                <td>
                    <?php echo esc_html($group_count); ?> / <?php echo esc_html($max_groups); ?>
                    <?php if (!$pro_active && $group_count >= 2): ?>
                        <span style="color: red; margin-left: 10px;">⚠️ Limit reached</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Total Product Info Items', 'coderembassy-product-info-icons-images-text'); ?></th>
                <td>
                    <?php echo esc_html($total_items); ?> / <?php echo esc_html($max_items); ?>
                    <?php if (!$pro_active && $total_items >= 6): ?>
                        <span style="color: red; margin-left: 10px;">⚠️ Limit reached</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if (!$pro_active): ?>
            <div class="cmfw-limits-notice notice notice-info" style="margin: 20px 0;">
                <h3><?php echo esc_html__('Free Version Limits', 'coderembassy-product-info-icons-images-text'); ?></h3>
                <ul>
                    <li><?php echo esc_html__('Maximum 2 groups allowed', 'coderembassy-product-info-icons-images-text'); ?></li>
                    <li><?php echo esc_html__('Maximum 3 Product Info items per group', 'coderembassy-product-info-icons-images-text'); ?></li>
                    <li><?php echo esc_html__('Total maximum: 6 Product Info items across all groups', 'coderembassy-product-info-icons-images-text'); ?></li>
                </ul>
                <p><em><?php echo esc_html__('Upgrade to PRO version to remove these limits!', 'coderembassy-product-info-icons-images-text'); ?></em></p>
            </div>
        <?php else: ?>
            <div class="notice notice-success" style="margin: 20px 0;">
                <p><strong><?php echo esc_html__('PRO Version Active - No Limits!', 'coderembassy-product-info-icons-images-text'); ?></strong></p>
                <p><?php echo esc_html__('You can add unlimited groups and Product Info items.', 'coderembassy-product-info-icons-images-text'); ?></p>
            </div>
        <?php endif; ?>
        
        <h3><?php echo esc_html__('Test Results', 'coderembassy-product-info-icons-images-text'); ?></h3>
        <p><?php echo esc_html__('This page helps verify that the free version limits are working correctly:', 'coderembassy-product-info-icons-images-text'); ?></p>
        <ul>
            <li><?php echo esc_html__('JavaScript prevents adding more than 2 groups in free version', 'coderembassy-product-info-icons-images-text'); ?></li>
            <li><?php echo esc_html__('JavaScript prevents adding more than 3 items per group in free version', 'coderembassy-product-info-icons-images-text'); ?></li>
            <li><?php echo esc_html__('Server-side validation enforces limits even if JavaScript is bypassed', 'coderembassy-product-info-icons-images-text'); ?></li>
            <li><?php echo esc_html__('PRO version removes all limits', 'coderembassy-product-info-icons-images-text'); ?></li>
        </ul>
    </div>
</div>
