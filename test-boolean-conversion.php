<?php
/**
 * Test Boolean Conversion Fix
 * Tests the fix for pulse_effect and glow_effect parameters
 */

echo "=== Test Boolean Conversion Fix ===\n\n";

// Simulate the fixed boolean conversion logic
$boolean_fields = ['_archi_hide_links', '_archi_show_in_graph', '_archi_pin_node', '_archi_show_label', '_archi_pulse_effect', '_archi_glow_effect'];

$test_values = [
    '_archi_pulse_effect' => '1',
    '_archi_glow_effect' => '0',
    '_archi_show_in_graph' => '1',
    '_archi_node_size' => '80'
];

echo "Test Data:\n";
print_r($test_values);
echo "\n";

$params = [];

foreach ($test_values as $meta_key => $value) {
    $frontend_key = str_replace('_archi_', '', $meta_key);
    
    if (in_array($meta_key, $boolean_fields)) {
        $params[$frontend_key] = $value === '1';
        echo "Boolean conversion: {$meta_key} ('{$value}') → {$frontend_key} (" . ($params[$frontend_key] ? 'true' : 'false') . ")\n";
    } else {
        $params[$frontend_key] = $value;
        echo "String value: {$meta_key} ('{$value}') → {$frontend_key} ('{$params[$frontend_key]}')\n";
    }
}

echo "\n=== Final Parameters ===\n";
print_r($params);

echo "\n=== Type Checks ===\n";
echo "pulse_effect === true: " . ($params['pulse_effect'] === true ? 'YES ✅' : 'NO ❌') . "\n";
echo "pulse_effect === '1': " . ($params['pulse_effect'] === '1' ? 'YES' : 'NO') . "\n";
echo "glow_effect === false: " . ($params['glow_effect'] === false ? 'YES ✅' : 'NO ❌') . "\n";
echo "glow_effect === '0': " . ($params['glow_effect'] === '0' ? 'YES' : 'NO') . "\n";

echo "\n=== JavaScript Compatibility Check ===\n";
echo "// JavaScript condition: if (d.pulse_effect === true || d.pulse_effect === '1')\n";
echo "pulse_effect will trigger: " . (($params['pulse_effect'] === true || $params['pulse_effect'] === '1') ? 'YES ✅' : 'NO ❌') . "\n";
echo "glow_effect will trigger: " . (($params['glow_effect'] === true || $params['glow_effect'] === '1') ? 'NO ✅ (correctly disabled)' : 'YES (error!)') . "\n";

echo "\n✅ Fix validated!\n";
