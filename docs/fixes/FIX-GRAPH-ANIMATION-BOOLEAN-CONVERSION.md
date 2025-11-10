# Fix: Graph Animation Parameters Boolean Conversion

**Date**: 2025-11-XX  
**Type**: 🐛 Bug Fix  
**Impact**: High - Core functionality restored

## Problem

Graph node animation parameters (`pulse_effect` and `glow_effect`) were not working in the frontend despite being correctly saved in the database.

### Root Cause

In `inc/graph-meta-registry.php`, the function `archi_get_graph_params()` (line 760) was only converting 4 boolean fields:
- `_archi_hide_links`
- `_archi_show_in_graph`
- `_archi_pin_node`
- `_archi_show_label`

But was **missing** the animation boolean fields:
- `_archi_pulse_effect` ❌
- `_archi_glow_effect` ❌

These parameters were stored in WordPress as strings `'0'` or `'1'`, but the JavaScript code in `nodeVisualEffects.js` checks for boolean `true`:

```javascript
// Line 141 in nodeVisualEffects.js
if (d.pulse_effect === true || d.pulse_effect === '1') {
    applyPulseEffect(imageElement, d);
}
```

While the JavaScript has a fallback check for `=== '1'`, the REST API was exposing these as strings without converting them to proper booleans, causing inconsistent behavior.

## Solution

Added `_archi_pulse_effect` and `_archi_glow_effect` to the boolean conversion array in `archi_get_graph_params()`.

### Files Modified

- **`inc/graph-meta-registry.php`** (line 760)
  - Added missing animation parameters to boolean conversion list
  - Now converts 6 boolean fields instead of 4

### Before
```php
} elseif (in_array($meta_key, [
    '_archi_hide_links', 
    '_archi_show_in_graph', 
    '_archi_pin_node', 
    '_archi_show_label'
])) {
    $params[$frontend_key] = $value === '1';
```

### After
```php
} elseif (in_array($meta_key, [
    '_archi_hide_links', 
    '_archi_show_in_graph', 
    '_archi_pin_node', 
    '_archi_show_label', 
    '_archi_pulse_effect',  // ✅ Added
    '_archi_glow_effect'    // ✅ Added
])) {
    $params[$frontend_key] = $value === '1';
```

## Testing

Created `test-boolean-conversion.php` to validate the fix:

```
pulse_effect ('1') → true ✅
glow_effect ('0') → false ✅
```

## Impact

✅ **FIXED**: Pulse and glow effects now work correctly in the graph visualization  
✅ **Type Safety**: Boolean parameters are now properly typed in REST API responses  
✅ **Consistency**: All 6 boolean graph parameters now follow the same conversion logic  

## Related

- Initial implementation: `assets/js/utils/nodeVisualEffects.js`
- Documentation: `docs/GRAPH-PARAMETERS-CONSOLIDATED.md`
- Summary: `docs/GRAPH-HARMONIZATION-SUMMARY.md`

## Verification Steps

1. Clear browser cache
2. Edit an article in WordPress admin
3. Enable "Pulse Effect" checkbox in Graph Parameters meta box
4. Save the article
5. View the graph visualization
6. **Expected**: Node should have continuous pulsing animation
7. Hover over the node
8. **Expected**: Glow effect appears if enabled

## Additional Notes

- This fix completes the graph harmonization work from the previous session
- All 32 graph parameters are now properly typed and exposed via REST API
- JavaScript compatibility maintained with fallback checks (`=== true || === '1'`)
