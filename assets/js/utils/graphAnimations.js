import * as d3 from "d3";

/**
 * Module de gestion des animations du graphique
 * Propose différents effets visuels et transitions
 */

/**
 * Types d'animations disponibles
 */
export const ANIMATION_TYPES = {
  FADE_IN: "fadeIn",
  SCALE_UP: "scaleUp",
  BOUNCE: "bounce",
  SPIRAL: "spiral",
  WAVE: "wave",
  PULSE: "pulse",
  ELASTIC: "elastic",
  STAGGER: "stagger",
  EXPLODE: "explode",
  MORPH: "morph",
};

/**
 * Configuration par défaut des animations
 */
const DEFAULT_CONFIG = {
  duration: 800,
  delay: 0,
  easing: d3.easeCubicInOut,
};

/**
 * Animation Fade In - Apparition progressive avec opacité
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const fadeIn = (selection, config = {}) => {
  const { duration, delay, easing } = { ...DEFAULT_CONFIG, ...config };

  selection
    .style("opacity", 0)
    .transition()
    .duration(duration)
    .delay((d, i) => delay + i * 50)
    .ease(easing)
    .style("opacity", 1);
};

/**
 * Animation Scale Up - Apparition avec zoom
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const scaleUp = (selection, config = {}) => {
  const { duration, delay, easing } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .attr("transform", `translate(${x}, ${y}) scale(0)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * 30)
      .ease(easing)
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Bounce - Rebond élastique
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const bounce = (selection, config = {}) => {
  const { duration, delay } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .attr("transform", `translate(${x}, ${y}) scale(0)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * 40)
      .ease(d3.easeElasticOut.amplitude(1.5).period(0.4))
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Spiral - Apparition en spirale depuis le centre
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const spiral = (selection, config = {}) => {
  const { duration, delay, centerX = 600, centerY = 400 } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const targetX = d.x || 0;
    const targetY = d.y || 0;

    // Calculer position de départ en spirale
    const angle = (i / selection.size()) * Math.PI * 4;
    const startRadius = 50;
    const startX = centerX + Math.cos(angle) * startRadius;
    const startY = centerY + Math.sin(angle) * startRadius;

    node
      .attr("transform", `translate(${startX}, ${startY}) scale(0.1)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * 20)
      .ease(d3.easeCubicOut)
      .attr("transform", `translate(${targetX}, ${targetY}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Wave - Effet de vague
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const wave = (selection, config = {}) => {
  const { duration, delay } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    // Déplacement vertical en vague
    const waveOffset = Math.sin((i / selection.size()) * Math.PI * 2) * 100;

    node
      .attr("transform", `translate(${x}, ${y + waveOffset}) scale(0.5)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * 25)
      .ease(d3.easeBackOut.overshoot(1.5))
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Pulse - Pulsation continue
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const pulse = (selection, config = {}) => {
  const { duration = 1500, scaleMin = 0.95, scaleMax = 1.05 } = config;

  const pulsate = () => {
    selection.each(function (d) {
      const node = d3.select(this);
      const x = d.x || 0;
      const y = d.y || 0;

      node
        .transition()
        .duration(duration / 2)
        .ease(d3.easeSinInOut)
        .attr("transform", `translate(${x}, ${y}) scale(${scaleMax})`)
        .transition()
        .duration(duration / 2)
        .ease(d3.easeSinInOut)
        .attr("transform", `translate(${x}, ${y}) scale(${scaleMin})`)
        .on("end", pulsate);
    });
  };

  pulsate();
};

/**
 * Animation Elastic - Rebond élastique exagéré
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const elastic = (selection, config = {}) => {
  const { duration, delay } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .attr("transform", `translate(${x}, ${y}) scale(0)`)
      .style("opacity", 0)
      .transition()
      .duration(duration * 1.2)
      .delay(delay + i * 35)
      .ease(d3.easeElasticOut.amplitude(2).period(0.6))
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Stagger - Cascade progressive
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const stagger = (selection, config = {}) => {
  const { duration, delay, staggerDelay = 100 } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .attr("transform", `translate(${x}, ${y - 100}) scale(0.3)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * staggerDelay)
      .ease(d3.easeCubicOut)
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Explode - Explosion depuis le centre
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const explode = (selection, config = {}) => {
  const { duration, delay, centerX = 600, centerY = 400, radius = 300 } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const targetX = d.x || 0;
    const targetY = d.y || 0;

    // Tous partent du centre
    node
      .attr("transform", `translate(${centerX}, ${centerY}) scale(0.1)`)
      .style("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + (i * 10))
      .ease(d3.easeCubicOut)
      .attr("transform", `translate(${targetX}, ${targetY}) scale(1)`)
      .style("opacity", 1);
  });
};

/**
 * Animation Morph - Transformation de forme
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration de l'animation
 */
export const morph = (selection, config = {}) => {
  const { duration, delay } = { ...DEFAULT_CONFIG, ...config };

  selection.each(function (d, i) {
    const node = d3.select(this);
    const circle = node.select("circle");

    if (circle.empty()) return;

    const originalRadius = parseFloat(circle.attr("r")) || 30;

    // Animation de transformation du cercle
    circle
      .attr("r", 0)
      .transition()
      .duration(duration / 2)
      .delay(delay + i * 30)
      .ease(d3.easeBackOut)
      .attr("r", originalRadius * 1.3)
      .transition()
      .duration(duration / 2)
      .ease(d3.easeElasticOut)
      .attr("r", originalRadius);

    node.style("opacity", 0)
      .transition()
      .duration(duration / 3)
      .delay(delay + i * 30)
      .style("opacity", 1);
  });
};

/**
 * Animation de hover - Effet au survol
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration
 */
export const applyHoverAnimation = (selection, config = {}) => {
  const {
    scaleFactor = 1.15,
    duration = 200,
    shadowBlur = 20,
    shadowColor = "rgba(0, 0, 0, 0.3)",
  } = config;

  selection
    .on("mouseenter", function (event, d) {
      const node = d3.select(this);
      const x = d.x || 0;
      const y = d.y || 0;

      node
        .raise()
        .transition()
        .duration(duration)
        .ease(d3.easeBackOut.overshoot(1.2))
        .attr("transform", `translate(${x}, ${y}) scale(${scaleFactor})`)
        .style("filter", `drop-shadow(0 ${shadowBlur}px ${shadowBlur}px ${shadowColor})`);
    })
    .on("mouseleave", function (event, d) {
      const node = d3.select(this);
      const x = d.x || 0;
      const y = d.y || 0;

      node
        .transition()
        .duration(duration)
        .ease(d3.easeCubicOut)
        .attr("transform", `translate(${x}, ${y}) scale(1)`)
        .style("filter", "none");
    });
};

/**
 * Animation de click - Effet lors du clic
 * @param {d3.Selection} selection - Sélection D3 des éléments
 * @param {Object} config - Configuration
 */
export const applyClickAnimation = (selection, config = {}) => {
  const { duration = 300, scaleFactor = 0.9 } = config;

  selection.on("click", function (event, d) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .transition()
      .duration(duration / 2)
      .ease(d3.easeBackIn)
      .attr("transform", `translate(${x}, ${y}) scale(${scaleFactor})`)
      .transition()
      .duration(duration / 2)
      .ease(d3.easeBackOut)
      .attr("transform", `translate(${x}, ${y}) scale(1)`);
  });
};

/**
 * Animation des liens - Effet de tracé progressif
 * @param {d3.Selection} linkSelection - Sélection D3 des liens
 * @param {Object} config - Configuration
 */
export const animateLinks = (linkSelection, config = {}) => {
  const { duration = 1000, delay = 0, staggerDelay = 20 } = config;

  linkSelection.each(function (d, i) {
    const link = d3.select(this);
    const totalLength = link.node().getTotalLength();

    link
      .attr("stroke-dasharray", `${totalLength} ${totalLength}`)
      .attr("stroke-dashoffset", totalLength)
      .attr("opacity", 0)
      .transition()
      .duration(duration)
      .delay(delay + i * staggerDelay)
      .ease(d3.easeQuadInOut)
      .attr("stroke-dashoffset", 0)
      .attr("opacity", 1);
  });
};

/**
 * Réinitialiser toutes les animations
 * @param {d3.Selection} selection - Sélection D3 des éléments
 */
export const resetAnimations = (selection) => {
  selection.interrupt().each(function (d) {
    const node = d3.select(this);
    const x = d.x || 0;
    const y = d.y || 0;

    node
      .attr("transform", `translate(${x}, ${y}) scale(1)`)
      .style("opacity", 1)
      .style("filter", "none");
  });
};

/**
 * Exécuter une animation selon son type
 * @param {string} type - Type d'animation
 * @param {d3.Selection} selection - Sélection D3
 * @param {Object} config - Configuration
 */
export const runAnimation = (type, selection, config = {}) => {
  const animations = {
    [ANIMATION_TYPES.FADE_IN]: fadeIn,
    [ANIMATION_TYPES.SCALE_UP]: scaleUp,
    [ANIMATION_TYPES.BOUNCE]: bounce,
    [ANIMATION_TYPES.SPIRAL]: spiral,
    [ANIMATION_TYPES.WAVE]: wave,
    [ANIMATION_TYPES.PULSE]: pulse,
    [ANIMATION_TYPES.ELASTIC]: elastic,
    [ANIMATION_TYPES.STAGGER]: stagger,
    [ANIMATION_TYPES.EXPLODE]: explode,
    [ANIMATION_TYPES.MORPH]: morph,
  };

  const animationFn = animations[type];

  if (animationFn) {
    animationFn(selection, config);
  } else {
    console.warn(`Animation type "${type}" not found`);
  }
};

/**
 * Animation de transition entre états du graphique
 * @param {d3.Selection} selection - Sélection D3
 * @param {Array} newPositions - Nouvelles positions
 * @param {Object} config - Configuration
 */
export const transitionToNewState = (selection, newPositions, config = {}) => {
  const { duration = 800, easing = d3.easeCubicInOut } = config;

  selection
    .transition()
    .duration(duration)
    .ease(easing)
    .attr("transform", (d) => {
      const newPos = newPositions.find((p) => p.id === d.id);
      if (newPos) {
        d.x = newPos.x;
        d.y = newPos.y;
        return `translate(${newPos.x}, ${newPos.y})`;
      }
      return `translate(${d.x || 0}, ${d.y || 0})`;
    });
};

export default {
  ANIMATION_TYPES,
  fadeIn,
  scaleUp,
  bounce,
  spiral,
  wave,
  pulse,
  elastic,
  stagger,
  explode,
  morph,
  applyHoverAnimation,
  applyClickAnimation,
  animateLinks,
  resetAnimations,
  runAnimation,
  transitionToNewState,
};
