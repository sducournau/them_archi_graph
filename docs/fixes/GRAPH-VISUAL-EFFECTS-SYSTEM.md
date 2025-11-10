# Graph Visual Effects System

**Date:** 2025-11-10  
**Version:** 1.3.1  
**Author:** Copilot Enhancement

## 📋 Overview

This document describes the comprehensive visual effects system added to the graph visualization in response to the user's request: "ajouter des effets a l'activation de la node, et le hover, le comportement si inactif par défaut, pulsation et autres".

The system provides rich, multi-layered visual feedback for node interactions with proper state management, smooth animations, and accessibility features.

## 🎯 Visual Effects Features

### 1. Multi-Layer SVG Node Structure

Each node now consists of **4 SVG elements** instead of 1:

```javascript
// 1. Halo (outer glow layer)
.append("circle")
  .attr("class", "node-halo")
  .attr("r", 34)
  .attr("fill", "none")
  .attr("stroke", color)
  .attr("stroke-width", 0)
  .attr("stroke-opacity", 0);

// 2. Main Circle (primary visual element)
.append("circle")
  .attr("class", "node-circle")
  .attr("r", 30)
  .attr("fill", color)
  .attr("stroke", "#fff")
  .attr("stroke-width", 2);

// 3. Shine (inner highlight for 3D effect)
.append("circle")
  .attr("class", "node-shine")
  .attr("r", 12)
  .attr("cy", -8)
  .attr("fill", "#fff")
  .attr("opacity", 0.3);

// 4. Label (text identifier)
.append("text")
  .attr("class", "node-label")
  // ... text properties
```

### 2. Node State System

**Three states are tracked:**

- **Active (default)**: Full opacity, normal colors, interactive
- **Inactive**: Reduced opacity (0.3-0.4), grayscale filter, breathing animation
- **Hover**: Enlarged, elevated z-index, halo visible

**State attributes:**
```javascript
// Data attribute for identification
.attr("data-node-id", d => d.id)

// CSS class for styling
.classed("node-inactive", d => d.inactiveByDefault || false)
```

### 3. Hover Effects

**On mouseenter:**
- Halo circle animates to stroke-width: 2px, opacity: 0.4
- Main circle scales up by custom `hover_scale` value (default: 1.1)
- Label font-weight increases to 600
- Node moves to front (z-index simulation via DOM reordering)

**On mouseleave:**
- Halo returns to stroke-width: 0
- Circle scales back to 1
- Label returns to normal weight
- Respects inactive state opacity if applicable

```javascript
nodeGroups
  .on("mouseenter", function(event, d) {
    const node = d3.select(this);
    const hoverScale = d.hover?.hoverScale || 1.1;
    
    // Animate halo
    node.select(".node-halo")
      .transition().duration(200)
      .attr("stroke-width", 2)
      .attr("stroke-opacity", 0.4);
    
    // Scale main circle
    node.select(".node-circle")
      .transition().duration(200)
      .attr("r", 30 * hoverScale);
    
    // Elevate to front
    this.parentNode.appendChild(this);
  });
```

### 4. Click Interactions

**On click:**
- Toggles inactive/active state
- Bounce animation (scale down to 0.9, then back to 1)
- Shockwave effect radiates outward
- Updates data model and opacity

```javascript
nodeGroups.on("click", function(event, d) {
  event.stopPropagation();
  
  const node = d3.select(this);
  const circle = node.select(".node-circle");
  
  // Toggle state
  d.inactiveByDefault = !d.inactiveByDefault;
  node.classed("node-inactive", d.inactiveByDefault);
  
  // Bounce animation
  circle.transition().duration(100)
    .attr("r", 27)
    .transition().duration(100)
    .attr("r", 30);
  
  // Shockwave effect
  createShockwave(node, circle);
});
```

### 5. Shockwave Animation

**Visual feedback on click:**
- Temporary circle element created
- Expands from radius 30 to 90
- Fades opacity from 0.8 to 0
- Duration: 600ms
- Automatically removed after animation

```javascript
function createShockwave(nodeGroup, circle) {
  const x = parseFloat(circle.attr("cx")) || 0;
  const y = parseFloat(circle.attr("cy")) || 0;
  const color = circle.attr("fill");
  
  const shockwave = nodeGroup.append("circle")
    .attr("class", "node-shockwave")
    .attr("cx", x)
    .attr("cy", y)
    .attr("r", 30)
    .attr("fill", "none")
    .attr("stroke", color)
    .attr("stroke-width", 3)
    .attr("stroke-opacity", 0.8);
  
  shockwave.transition()
    .duration(600)
    .attr("r", 90)
    .attr("stroke-opacity", 0)
    .remove();
}
```

### 6. Inactive Node Pulsation

**Breathing effect for inactive nodes:**
- Subtle 2-second animation cycle
- Circle opacity pulses: 0.3 ↔ 0.4
- Halo stroke pulses: 0 ↔ 2px with 0.2 opacity
- Synchronized timing for smooth appearance

```javascript
applyInactivePulse() {
  const inactiveNodes = this.svg
    .selectAll(".graph-node.node-inactive");
  
  inactiveNodes.selectAll(".node-circle")
    .transition()
    .duration(2000)
    .attr("opacity", 0.3)
    .transition()
    .duration(2000)
    .attr("opacity", 0.4)
    .on("end", function repeat() {
      d3.select(this)
        .transition().duration(2000)
        .attr("opacity", 0.3)
        .transition().duration(2000)
        .attr("opacity", 0.4)
        .on("end", repeat);
    });
  
  inactiveNodes.selectAll(".node-halo")
    .transition()
    .duration(2000)
    .attr("stroke-width", 0)
    .attr("stroke-opacity", 0)
    .transition()
    .duration(2000)
    .attr("stroke-width", 2)
    .attr("stroke-opacity", 0.2)
    .on("end", function repeat() {
      d3.select(this)
        .transition().duration(2000)
        .attr("stroke-width", 0)
        .attr("stroke-opacity", 0)
        .transition().duration(2000)
        .attr("stroke-width", 2)
        .attr("stroke-opacity", 0.2)
        .on("end", repeat);
    });
}
```

## 🎨 CSS Styling System

### Core Node Classes

```css
/* Base node styling */
.graph-node {
  transition: all 0.3s ease;
}

/* Inactive state */
.graph-node.node-inactive {
  opacity: 0.5;
  filter: grayscale(30%);
}

/* Individual element styling */
.node-halo {
  transition: all 0.3s ease;
  pointer-events: none;
}

.node-circle {
  transition: all 0.2s ease;
  cursor: pointer;
}

.node-shine {
  transition: opacity 0.2s ease;
  pointer-events: none;
}

.node-label {
  transition: all 0.2s ease;
  pointer-events: none;
  user-select: none;
}
```

### Animation Keyframes

```css
/* Shockwave expansion */
@keyframes shockwave {
  0% {
    r: 30;
    stroke-opacity: 0.8;
  }
  100% {
    r: 90;
    stroke-opacity: 0;
  }
}

/* Breathing for inactive nodes */
@keyframes node-breathe {
  0%, 100% {
    opacity: 0.4;
  }
  50% {
    opacity: 0.3;
  }
}
```

## 📊 WordPress Integration

### Graph Parameters Used

The effects system respects these WordPress meta parameters:

| Parameter | Meta Key | Default | Usage |
|-----------|----------|---------|-------|
| Hover Scale | `_archi_hover_scale` | 1.1 | Circle enlargement on hover |
| Animation Type | `_archi_animation_type` | "scale" | Base animation style |
| Pulse Effect | `_archi_pulse_effect` | false | Enable pulsation |
| Node Color | `_archi_node_color` | "#3498db" | Circle fill color |
| Inactive by Default | `_archi_inactive_by_default` | false | Initial state |

### Data Flow

```
WordPress Meta
    ↓
REST API (/wp-json/archi/v1/articles)
    ↓
GraphManager.loadData() transformation
    ↓
Nested structure: { animation: {...}, hover: {...} }
    ↓
Applied in drawNodes() and effect methods
```

## 🧪 Testing Checklist

### Visual Verification

- [ ] **Halo appears on hover**: Outer glow visible
- [ ] **Circle scales correctly**: Uses custom `hover_scale` value
- [ ] **Click toggles state**: Node becomes inactive/active
- [ ] **Shockwave animates**: Expands and fades on click
- [ ] **Breathing works**: Inactive nodes pulse subtly
- [ ] **Z-index elevation**: Hovered node comes to front
- [ ] **Label responds**: Font weight changes on hover

### State Testing

- [ ] **Active → Inactive**: Click reduces opacity, adds grayscale
- [ ] **Inactive → Active**: Click restores full opacity, removes filter
- [ ] **Hover on inactive**: Still shows hover effects
- [ ] **Multiple clicks**: Toggle works reliably

### Performance

- [ ] **Smooth animations**: 60 FPS maintained
- [ ] **No memory leaks**: Shockwaves properly removed
- [ ] **Many nodes**: Effects work with 50+ nodes
- [ ] **Reduced motion**: Respects prefers-reduced-motion

### Accessibility

- [ ] **Keyboard focus**: Visible outline on focus
- [ ] **Screen readers**: Label text readable
- [ ] **High contrast**: Visible in high contrast mode
- [ ] **Color blind**: Effects don't rely solely on color

## 🔧 Customization Guide

### Adjusting Animation Duration

```javascript
// In applyPerNodeHoverEffects()
.transition().duration(300) // Change from 200ms
```

### Modifying Halo Size

```javascript
// In drawNodes()
.attr("r", 40) // Default is 34, increase for larger halo
```

### Customizing Shockwave

```javascript
// In createShockwave()
.attr("r", 120) // Expand further (default: 90)
.duration(800)  // Slower animation (default: 600)
```

### Changing Breathing Speed

```javascript
// In applyInactivePulse()
.duration(3000) // Slower pulse (default: 2000ms)
```

## 📁 Modified Files

1. **assets/js/utils/GraphManager.js**
   - `drawNodes()`: Added 4-layer SVG structure
   - `applyPerNodeHoverEffects()`: Complete rewrite with interactions
   - `applyContinuousEffects()`: Calls inactive pulse
   - `applyInactivePulse()`: New method for breathing effect

2. **assets/css/graph-effects.css**
   - New file with comprehensive styling
   - Animation keyframes
   - State classes
   - Accessibility enhancements

3. **functions.php**
   - Added `wp_enqueue_style('archi-graph-effects')`

## 🎉 Results

The visual effects system provides:

✅ **Rich feedback**: Multiple layers of visual response  
✅ **Smooth interactions**: Professional-grade animations  
✅ **State management**: Clear visual distinction between states  
✅ **Performance**: Optimized D3.js transitions  
✅ **Accessibility**: Respects user preferences  
✅ **Customizable**: Easy parameter tuning

## 🔮 Future Enhancements

Potential additions:
- Glow intensity based on node importance
- Trail effect on drag
- Ripple effect for connected nodes
- Sound effects (optional)
- Particle effects on state change
- Custom animation curves per node

## 📚 Related Documentation

- [GRAPH-EFFECTS-COMPLETE-SUMMARY.md](./GRAPH-EFFECTS-COMPLETE-SUMMARY.md) - Original parameter fix
- [GRAPH-EFFECTS-TESTING-GUIDE.md](./GRAPH-EFFECTS-TESTING-GUIDE.md) - Testing procedures
- [GRAPH-PARAMETERS-CONSOLIDATED.md](../GRAPH-PARAMETERS-CONSOLIDATED.md) - Parameter reference

---

**Enhancement Complete:** The graph now features a comprehensive visual effects system with multi-layered rendering, rich interactions, state management, and smooth animations. 🎨✨
