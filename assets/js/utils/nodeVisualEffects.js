/**
 * Node Visual Effects - Unified Visual Effect System
 * 
 * Handles all visual effects for graph nodes including:
 * - Pulse effect (continuous animation)
 * - Glow effect (halo)
 * - Hover animations
 * - Entrance animations
 * 
 * @package ArchiGraph
 * @since 1.1.0
 */

import * as d3 from 'd3';

/**
 * Create SVG filters for visual effects
 * @param {d3.Selection} svg - SVG container
 */
export function createVisualEffectFilters(svg) {
  const defs = svg.select('defs').empty() 
    ? svg.insert('defs', ':first-child')
    : svg.select('defs');
  
  // Glow filter
  if (defs.select('#node-glow').empty()) {
    const glowFilter = defs.append('filter')
      .attr('id', 'node-glow')
      .attr('x', '-50%')
      .attr('y', '-50%')
      .attr('width', '200%')
      .attr('height', '200%');
    
    glowFilter.append('feGaussianBlur')
      .attr('in', 'SourceGraphic')
      .attr('stdDeviation', '4')
      .attr('result', 'blur');
    
    glowFilter.append('feFlood')
      .attr('flood-color', '#fff')
      .attr('flood-opacity', '0.8');
    
    glowFilter.append('feComposite')
      .attr('in2', 'blur')
      .attr('operator', 'in')
      .attr('result', 'colorBlur');
    
    const feMerge = glowFilter.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'colorBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');
  }
  
  // Drop shadow filter (already exists in GraphContainer)
  if (defs.select('#drop-shadow').empty()) {
    const dropShadow = defs.append('filter')
      .attr('id', 'drop-shadow')
      .attr('x', '-50%')
      .attr('y', '-50%')
      .attr('width', '200%')
      .attr('height', '200%');
    
    dropShadow.append('feGaussianBlur')
      .attr('in', 'SourceAlpha')
      .attr('stdDeviation', '3')
      .attr('result', 'blur');
    
    dropShadow.append('feOffset')
      .attr('in', 'blur')
      .attr('dx', '2')
      .attr('dy', '2')
      .attr('result', 'offsetBlur');
    
    const feMerge = dropShadow.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'offsetBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');
  }
}

/**
 * Apply continuous pulse effect to a node
 * @param {d3.Selection} imageElement - Node image element
 * @param {Object} nodeData - Node data with size and animation settings
 */
export function applyPulseEffect(imageElement, nodeData) {
  const baseSize = nodeData.node_size || 60;
  const pulseSize = baseSize * 1.1;
  const duration = nodeData.animation_duration || 1000;
  
  // Cancel any existing transitions first
  imageElement.interrupt();
  
  // Reset to base size before starting
  imageElement
    .attr('width', baseSize)
    .attr('height', baseSize)
    .attr('x', -baseSize / 2)
    .attr('y', -baseSize / 2);
  
  const pulse = () => {
    imageElement
      .transition()
      .duration(duration)
      .ease(d3.easeSinInOut)
      .attr('width', pulseSize)
      .attr('height', pulseSize)
      .attr('x', -pulseSize / 2)
      .attr('y', -pulseSize / 2)
      .transition()
      .duration(duration)
      .ease(d3.easeSinInOut)
      .attr('width', baseSize)
      .attr('height', baseSize)
      .attr('x', -baseSize / 2)
      .attr('y', -baseSize / 2)
      .on('end', pulse);
  };
  
  pulse();
}

/**
 * Apply glow effect to a node
 * @param {d3.Selection} imageElement - Node image element
 */
export function applyGlowEffect(imageElement) {
  imageElement.style('filter', 'url(#node-glow) url(#drop-shadow)');
}

/**
 * Remove glow effect from a node
 * @param {d3.Selection} imageElement - Node image element
 */
export function removeGlowEffect(imageElement) {
  imageElement.style('filter', 'url(#drop-shadow)');
}

/**
 * Apply all continuous effects to nodes based on their data
 * @param {d3.Selection} nodeElements - All node group elements
 * @param {d3.Selection} svg - SVG container for filters
 */
export function applyContinuousEffects(nodeElements, svg, settings = {}) {
  // Ensure filters exist
  createVisualEffectFilters(svg);
  
  // 🔥 RÉCUPÉRER LES PARAMÈTRES DU CUSTOMIZER
  const hoverEffect = settings.hoverEffect || 'highlight';
  
  nodeElements.each(function(d) {
    const node = d3.select(this);
    const imageElement = node.select('.node-image');
    
    // 🔥 UTILISER hoverEffect DU CUSTOMIZER au lieu des propriétés individuelles
    // Si hoverEffect est défini dans le Customizer, l'utiliser pour TOUS les nœuds
    let pulseEnabled = false;
    let glowEnabled = false;
    
    if (hoverEffect === 'pulse') {
      pulseEnabled = true;
    } else if (hoverEffect === 'glow') {
      glowEnabled = true;
    } else {
      // Fallback sur les propriétés individuelles du nœud
      pulseEnabled = (d.hover?.pulse === true) || (d.pulse_effect === true || d.pulse_effect === '1');
      glowEnabled = (d.hover?.glow === true) || (d.glow_effect === true || d.glow_effect === '1');
    }
    
    // Apply pulse effect if enabled
    if (pulseEnabled) {
      applyPulseEffect(imageElement, d);
    }
    
    // Apply glow effect if enabled
    if (glowEnabled) {
      applyGlowEffect(imageElement);
    }
  });
}

/**
 * Apply hover scale effect based on animation level
 * @param {d3.Selection} imageElement - Node image element
 * @param {Object} nodeData - Node data
 * @param {boolean} isHovering - Whether mouse is hovering
 */
export function applyHoverScale(imageElement, nodeData, isHovering, settings = {}) {
  const baseSize = nodeData.node_size || 60;
  const hoverScale = nodeData.hover_scale || 1.15;
  const animationLevel = nodeData.animation_level || 'normal';
  
  // 🔥 UTILISER transitionSpeed et hoverEffect DU CUSTOMIZER
  const transitionSpeed = settings.transitionSpeed || 200;
  const hoverEffect = settings.hoverEffect || 'scale';
  
  // Si hoverEffect n'est pas 'scale' ou 'highlight', ne pas appliquer le scale
  if (hoverEffect !== 'scale' && hoverEffect !== 'highlight' && hoverEffect !== 'none') {
    // Pour 'glow' et 'pulse', le scale est géré par applyContinuousEffects
    return;
  }
  
  // Determine duration based on animation level, ou utiliser transitionSpeed du Customizer
  let duration = transitionSpeed;
  if (animationLevel === 'none') {
    duration = 0;
  } else if (animationLevel === 'subtle') {
    duration = transitionSpeed * 1.5;
  } else if (animationLevel === 'intense') {
    duration = transitionSpeed * 0.75;
  }
  
  if (isHovering && hoverEffect !== 'none') {
    const scaledSize = baseSize * hoverScale;
    imageElement
      .transition()
      .duration(duration)
      .attr('width', scaledSize)
      .attr('height', scaledSize)
      .attr('x', -scaledSize / 2)
      .attr('y', -scaledSize / 2);
  } else {
    imageElement
      .transition()
      .duration(duration)
      .attr('width', baseSize)
      .attr('height', baseSize)
      .attr('x', -baseSize / 2)
      .attr('y', -baseSize / 2);
  }
}

/**
 * Get entrance animation settings based on node data
 * @param {Object} nodeData - Node data
 * @returns {Object} Animation settings
 */
export function getEntranceAnimationSettings(nodeData) {
  const enterFrom = nodeData.enter_from || 'center';
  const duration = nodeData.animation_duration || 800;
  const delay = nodeData.animation_delay || 0;
  const easing = nodeData.animation_easing || 'ease-out';
  
  // Map easing string to D3 easing function
  const easingMap = {
    'linear': d3.easeLinear,
    'ease': d3.easeCubic,
    'ease-in': d3.easeCubicIn,
    'ease-out': d3.easeCubicOut,
    'ease-in-out': d3.easeCubicInOut,
    'elastic': d3.easeElastic,
    'bounce': d3.easeBounce
  };
  
  return {
    enterFrom,
    duration,
    delay,
    easing: easingMap[easing] || d3.easeCubicOut
  };
}

/**
 * Apply entrance animation to a node
 * @param {d3.Selection} nodeElement - Node group element
 * @param {Object} nodeData - Node data
 * @param {Object} centerPosition - {x, y} center of canvas
 */
export function applyEntranceAnimation(nodeElement, nodeData, centerPosition) {
  const settings = getEntranceAnimationSettings(nodeData);
  const { enterFrom, duration, delay, easing } = settings;
  
  // Determine start position based on enter_from
  let startX = nodeData.x;
  let startY = nodeData.y;
  
  switch (enterFrom) {
    case 'top':
      startY = -100;
      break;
    case 'bottom':
      startY = centerPosition.y * 2 + 100;
      break;
    case 'left':
      startX = -100;
      break;
    case 'right':
      startX = centerPosition.x * 2 + 100;
      break;
    case 'center':
    default:
      startX = centerPosition.x;
      startY = centerPosition.y;
      break;
  }
  
  // Set initial position
  nodeElement.attr('transform', `translate(${startX}, ${startY})`);
  
  // Animate to final position
  nodeElement
    .transition()
    .delay(delay)
    .duration(duration)
    .ease(easing)
    .attr('transform', `translate(${nodeData.x}, ${nodeData.y})`);
}

/**
 * Update node visual effects based on animation level
 * @param {Object} nodeData - Node data
 * @returns {Object} Effect configuration
 */
export function getEffectConfiguration(nodeData) {
  const level = nodeData.animation_level || 'normal';
  
  // Check both nested and flat structures for backward compatibility
  const pulseEnabled = (nodeData.hover?.pulse === true) || (nodeData.pulse_effect === true || nodeData.pulse_effect === '1');
  const glowEnabled = (nodeData.hover?.glow === true) || (nodeData.glow_effect === true || nodeData.glow_effect === '1');
  const hoverScale = nodeData.hover?.scale || nodeData.hover_scale || 1.15;
  
  const configs = {
    none: {
      enablePulse: false,
      enableGlow: false,
      hoverDuration: 0,
      hoverScale: 1.0
    },
    subtle: {
      enablePulse: false,
      enableGlow: false,
      hoverDuration: 300,
      hoverScale: 1.05
    },
    normal: {
      enablePulse: pulseEnabled,
      enableGlow: glowEnabled,
      hoverDuration: 200,
      hoverScale: hoverScale
    },
    intense: {
      enablePulse: true,
      enableGlow: true,
      hoverDuration: 150,
      hoverScale: nodeData.hover_scale || 1.3
    }
  };
  
  return configs[level] || configs.normal;
}

export default {
  createVisualEffectFilters,
  applyPulseEffect,
  applyGlowEffect,
  removeGlowEffect,
  applyContinuousEffects,
  applyHoverScale,
  getEntranceAnimationSettings,
  applyEntranceAnimation,
  getEffectConfiguration
};
