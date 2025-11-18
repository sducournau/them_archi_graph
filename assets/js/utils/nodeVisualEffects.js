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
 * @param {Object} settings - Customizer settings
 */
export function applyPulseEffect(imageElement, nodeData, settings = {}) {
  const baseSize = nodeData.node_size || 80;
  
  // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER
  const pulseDuration = settings.nodePulseDuration ?? 2500;
  const pulseIntensity = settings.nodePulseIntensity ?? 0.85;
  
  // Calcul de la taille de pulse basé sur l'intensité
  // pulseIntensity = opacité minimale (0.5-1.0)
  // On convertit en facteur de scale: intensity 0.85 → scale 1.08
  const pulseScale = 1 + (1 - pulseIntensity) * 0.5;
  const pulseSize = baseSize * pulseScale;
  
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
      .duration(pulseDuration)
      .ease(d3.easeSinInOut)
      .attr('width', pulseSize)
      .attr('height', pulseSize)
      .attr('x', -pulseSize / 2)
      .attr('y', -pulseSize / 2)
      .style('opacity', pulseIntensity) // 🔥 Opacité minimale paramétrable
      .transition()
      .duration(pulseDuration)
      .ease(d3.easeSinInOut)
      .attr('width', baseSize)
      .attr('height', baseSize)
      .attr('x', -baseSize / 2)
      .attr('y', -baseSize / 2)
      .style('opacity', 1) // Retour à opacité maximale
      .on('end', pulse);
  };
  
  pulse();
}

/**
 * Apply glow effect to a node
 * @param {d3.Selection} imageElement - Node image element
 * @param {Object} settings - Customizer settings
 */
export function applyGlowEffect(imageElement, settings = {}) {
  // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER
  const glowEnabled = settings.activeNodeGlowEnabled ?? true;
  
  if (glowEnabled) {
    imageElement.style('filter', 'url(#glow)');
  } else {
    // Si désactivé, utiliser drop-shadow classique
    const shadowEnabled = settings.nodeShadowEnabled ?? true;
    imageElement.style('filter', shadowEnabled ? 'url(#drop-shadow)' : 'none');
  }
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
 * @param {Object} settings - Customizer settings
 */
export function applyContinuousEffects(nodeElements, svg, settings = {}) {
  // Ensure filters exist
  createVisualEffectFilters(svg);
  
  // 🔥 RÉCUPÉRER LES NOUVEAUX PARAMÈTRES DU CUSTOMIZER
  const hoverEffect = settings.hoverEffect || 'highlight';
  const nodePulseEnabled = settings.nodePulseEnabled ?? true;
  const activeGlowEnabled = settings.activeNodeGlowEnabled ?? true;
  
  // ⚡ PERFORMANCE: Désactiver les animations continues par défaut
  // Elles consomment trop de ressources avec requestAnimationFrame
  // Utiliser uniquement des effets CSS ou au hover
  const enableContinuousAnimations = settings.enableContinuousAnimations === true;
  
  if (!enableContinuousAnimations && !nodePulseEnabled && !activeGlowEnabled) {
    // Aucun effet continu à appliquer
    return;
  }
  
  nodeElements.each(function(d) {
    const node = d3.select(this);
    const imageElement = node.select('.node-image');
    
    // 🔥 UTILISER LES NOUVEAUX PARAMÈTRES
    let pulseEnabled = false;
    let glowEnabled = false;
    
    if (nodePulseEnabled && hoverEffect === 'pulse') {
      pulseEnabled = true;
    } else if (activeGlowEnabled && hoverEffect === 'glow') {
      glowEnabled = true;
    } else {
      // Fallback sur les propriétés individuelles du nœud
      pulseEnabled = nodePulseEnabled && ((d.hover?.pulse === true) || (d.pulse_effect === true || d.pulse_effect === '1'));
      glowEnabled = activeGlowEnabled && ((d.hover?.glow === true) || (d.glow_effect === true || d.glow_effect === '1'));
    }
    
    // Apply pulse effect if enabled
    if (pulseEnabled) {
      applyPulseEffect(imageElement, d, settings); // 🔥 Passer les settings
    }
    
    // Apply glow effect if enabled
    if (glowEnabled) {
      applyGlowEffect(imageElement, settings); // 🔥 Passer les settings
    }
  });
}

/**
 * Apply hover scale effect based on animation level
 * @param {d3.Selection} imageElement - Node image element
 * @param {Object} nodeData - Node data
 * @param {boolean} isHovering - Whether mouse is hovering
 * @param {Object} settings - Customizer settings
 */
export function applyHoverScale(imageElement, nodeData, isHovering, settings = {}) {
  const baseSize = nodeData.node_size || 80;
  
  // 🔥 UTILISER LES NOUVEAUX PARAMÈTRES DU CUSTOMIZER
  const hoverScale = settings.hoverScale ?? nodeData.hover_scale ?? 1.2;
  const hoverTransitionDuration = settings.hoverTransitionDuration ?? 300;
  const hoverBrightness = settings.hoverBrightness ?? 1.15;
  const hoverEffect = settings.hoverEffect || 'scale';
  const animationLevel = nodeData.animation_level || 'normal';
  
  // Si hoverEffect n'est pas 'scale' ou 'highlight', ne pas appliquer le scale
  if (hoverEffect !== 'scale' && hoverEffect !== 'highlight' && hoverEffect !== 'none') {
    // Pour 'glow' et 'pulse', le scale est géré par applyContinuousEffects
    return;
  }
  
  // Determine duration based on animation level
  let duration = hoverTransitionDuration;
  if (animationLevel === 'none') {
    duration = 0;
  } else if (animationLevel === 'subtle') {
    duration = hoverTransitionDuration * 1.5;
  } else if (animationLevel === 'intense') {
    duration = hoverTransitionDuration * 0.75;
  }
  
  if (isHovering && hoverEffect !== 'none') {
    const scaledSize = baseSize * hoverScale;
    imageElement
      .transition()
      .duration(duration)
      .attr('width', scaledSize)
      .attr('height', scaledSize)
      .attr('x', -scaledSize / 2)
      .attr('y', -scaledSize / 2)
      .style('filter', `brightness(${hoverBrightness})`); // 🔥 NOUVEAU: luminosité paramétrable
  } else {
    imageElement
      .transition()
      .duration(duration)
      .attr('width', baseSize)
      .attr('height', baseSize)
      .attr('x', -baseSize / 2)
      .attr('y', -baseSize / 2)
      .style('filter', settings?.nodeShadowEnabled ? 'url(#drop-shadow)' : 'none'); // 🔥 Reset au filtre par défaut
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
