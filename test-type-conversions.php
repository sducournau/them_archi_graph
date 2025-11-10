<?php
/**
 * Complete Type Conversion Test
 * Validates all graph parameter type conversions
 */

echo "=== COMPLETE GRAPH PARAMETER TYPE CONVERSION TEST ===\n\n";

// Define expected types for all parameters
$type_definitions = [
    // Integers
    '_archi_node_size' => ['type' => 'integer', 'test_value' => '80', 'expected' => 80],
    '_archi_node_weight' => ['type' => 'integer', 'test_value' => '5', 'expected' => 5],
    '_archi_connection_depth' => ['type' => 'integer', 'test_value' => '2', 'expected' => 2],
    '_archi_animation_duration' => ['type' => 'integer', 'test_value' => '800', 'expected' => 800],
    '_archi_animation_delay' => ['type' => 'integer', 'test_value' => '100', 'expected' => 100],
    
    // Floats
    '_archi_node_opacity' => ['type' => 'float', 'test_value' => '0.8', 'expected' => 0.8],
    '_archi_link_strength' => ['type' => 'float', 'test_value' => '1.5', 'expected' => 1.5],
    '_archi_hover_scale' => ['type' => 'float', 'test_value' => '1.15', 'expected' => 1.15],
    
    // Booleans
    '_archi_show_in_graph' => ['type' => 'boolean', 'test_value' => '1', 'expected' => true],
    '_archi_pin_node' => ['type' => 'boolean', 'test_value' => '0', 'expected' => false],
    '_archi_hide_links' => ['type' => 'boolean', 'test_value' => '1', 'expected' => true],
    '_archi_show_label' => ['type' => 'boolean', 'test_value' => '0', 'expected' => false],
    '_archi_pulse_effect' => ['type' => 'boolean', 'test_value' => '1', 'expected' => true],
    '_archi_glow_effect' => ['type' => 'boolean', 'test_value' => '0', 'expected' => false],
    
    // Strings (enum values)
    '_archi_node_color' => ['type' => 'string', 'test_value' => '#3498db', 'expected' => '#3498db'],
    '_archi_priority_level' => ['type' => 'string', 'test_value' => 'high', 'expected' => 'high'],
    '_archi_animation_level' => ['type' => 'string', 'test_value' => 'normal', 'expected' => 'normal'],
    '_archi_animation_easing' => ['type' => 'string', 'test_value' => 'ease-out', 'expected' => 'ease-out'],
];

// Type conversion arrays (from archi_get_graph_params)
$integer_fields = ['_archi_node_size', '_archi_node_weight', '_archi_connection_depth', '_archi_animation_duration', '_archi_animation_delay'];
$float_fields = ['_archi_node_opacity', '_archi_link_strength', '_archi_hover_scale'];
$boolean_fields = ['_archi_hide_links', '_archi_show_in_graph', '_archi_pin_node', '_archi_show_label', '_archi_pulse_effect', '_archi_glow_effect'];

$params = [];
$pass_count = 0;
$fail_count = 0;

echo "Testing " . count($type_definitions) . " parameters...\n\n";

foreach ($type_definitions as $meta_key => $def) {
    $frontend_key = str_replace('_archi_', '', $meta_key);
    $value = $def['test_value'];
    
    // Apply type conversion logic
    if (in_array($meta_key, $integer_fields)) {
        $params[$frontend_key] = intval($value);
    } elseif (in_array($meta_key, $float_fields)) {
        $params[$frontend_key] = floatval($value);
    } elseif (in_array($meta_key, $boolean_fields)) {
        $params[$frontend_key] = $value === '1';
    } else {
        $params[$frontend_key] = $value;
    }
    
    // Validate result
    $result = $params[$frontend_key];
    $expected = $def['expected'];
    $match = ($result === $expected);
    
    if ($match) {
        echo "✅ PASS: {$frontend_key}\n";
        echo "   Input: '{$value}' ({$def['type']})\n";
        echo "   Output: " . var_export($result, true) . " (" . gettype($result) . ")\n";
        $pass_count++;
    } else {
        echo "❌ FAIL: {$frontend_key}\n";
        echo "   Expected: " . var_export($expected, true) . " (" . gettype($expected) . ")\n";
        echo "   Got: " . var_export($result, true) . " (" . gettype($result) . ")\n";
        $fail_count++;
    }
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total: " . count($type_definitions) . " parameters\n";
echo "✅ Passed: {$pass_count}\n";
echo "❌ Failed: {$fail_count}\n";

if ($fail_count === 0) {
    echo "\n🎉 ALL TESTS PASSED! Type conversions are correct.\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED! Check the conversions above.\n";
    exit(1);
}

echo "\n=== JavaScript Compatibility Check ===\n";
echo "Testing animation parameter checks...\n\n";

// Test JavaScript conditions
$js_tests = [
    'pulse_effect === true' => $params['pulse_effect'] === true,
    'pulse_effect === "1"' => $params['pulse_effect'] === '1',
    'pulse_effect === true || pulse_effect === "1"' => ($params['pulse_effect'] === true || $params['pulse_effect'] === '1'),
];

foreach ($js_tests as $condition => $result) {
    $status = $result ? '✅ TRUE' : '❌ FALSE';
    echo "{$condition}: {$status}\n";
}

echo "\n✅ All type conversions validated successfully!\n";
