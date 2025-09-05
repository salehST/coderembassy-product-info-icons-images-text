<?php
/**
 * Test script to verify free/pro version integration
 * This file should be deleted after testing
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>CMFW Plugin Integration Test</h1>";

// Test 1: Check if free version functions exist
echo "<h2>Test 1: Free Version Functions</h2>";
$functions_to_test = [
    'cmfw_is_pro_active',
    'cmfw_get_groups',
    'cmfw_save_groups',
    'cmfw_get_max_groups',
    'cmfw_get_max_items_per_group',
    'cmfw_can_add_groups',
    'cmfw_can_add_items',
    'cmfw_can_remove_groups',
    'cmfw_can_remove_items',
    'cmfw_get_dashboard_config'
];

foreach ($functions_to_test as $function) {
    if (function_exists($function)) {
        echo "✓ Function {$function} exists<br>";
    } else {
        echo "✗ Function {$function} missing<br>";
    }
}

// Test 2: Check free version structure
echo "<h2>Test 2: Free Version Structure</h2>";
$groups = cmfw_get_groups();
echo "Groups count: " . count($groups) . "<br>";
echo "Max groups allowed: " . cmfw_get_max_groups() . "<br>";
echo "Max items per group: " . cmfw_get_max_items_per_group() . "<br>";
echo "Can add groups: " . (cmfw_can_add_groups() ? 'Yes' : 'No') . "<br>";
echo "Can remove groups: " . (cmfw_can_remove_groups() ? 'Yes' : 'No') . "<br>";

// Test 3: Check dashboard config
echo "<h2>Test 3: Dashboard Configuration</h2>";
$config = cmfw_get_dashboard_config();
echo "<pre>";
print_r($config);
echo "</pre>";

// Test 4: Check if pro version is active
echo "<h2>Test 4: Pro Version Status</h2>";
$pro_active = cmfw_is_pro_active();
echo "Pro version active: " . ($pro_active ? 'Yes' : 'No') . "<br>";

if ($pro_active) {
    echo "Pro functions available:<br>";
    $pro_functions = [
        'cmfw_pro_is_active',
        'cmfw_pro_get_version',
        'cmfw_pro_get_url'
    ];
    
    foreach ($pro_functions as $function) {
        if (function_exists($function)) {
            echo "✓ Function {$function} exists<br>";
        } else {
            echo "✗ Function {$function} missing<br>";
        }
    }
}

// Test 5: Test free version structure enforcement
echo "<h2>Test 5: Free Version Structure Enforcement</h2>";
$test_groups = [
    [
        'taxonomy' => 'product_cat',
        'terms' => [1, 2],
        'items' => [
            ['title' => 'Item 1', 'icon' => 'star', 'image_id' => 0],
            ['title' => 'Item 2', 'icon' => 'heart', 'image_id' => 0],
            ['title' => 'Item 3', 'icon' => 'check', 'image_id' => 0],
            ['title' => 'Item 4', 'icon' => 'plus', 'image_id' => 0] // This should be removed
        ]
    ],
    [
        'taxonomy' => 'product_tag',
        'terms' => [3, 4],
        'items' => [
            ['title' => 'Extra Item', 'icon' => 'star', 'image_id' => 0]
        ]
    ]
];

echo "Before applying free version structure:<br>";
echo "Groups: " . count($test_groups) . "<br>";
echo "Items in first group: " . count($test_groups[0]['items']) . "<br>";

$processed_groups = cmfw_apply_free_version_structure($test_groups);

echo "After applying free version structure:<br>";
echo "Groups: " . count($processed_groups) . "<br>";
echo "Items in first group: " . count($processed_groups[0]['items']) . "<br>";

echo "<h2>Test Complete!</h2>";
echo "<p>If all tests pass, the integration is working correctly.</p>";
echo "<p><strong>Remember to delete this test file after testing!</strong></p>";
?>
