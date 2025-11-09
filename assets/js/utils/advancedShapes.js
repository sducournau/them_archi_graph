import * as d3 from "d3";

/**
 * Utilitaires pour rendre les formes personnalisées et animations avancées
 */

/**
 * Créer un nœud avec la forme spécifiée
 * @param {d3.Selection} selection - Sélection D3 où ajouter la forme
 * @param {Object} data - Données du nœud
 * @returns {d3.Selection} Élément créé
 */
export const createNodeShape = (selection, data) => {
  const params = data.advanced_graph_params || {};
  const shape = params.node_shape || 'circle';
  const size = data.node_size || 60;
  const radius = size / 2;
  
  let shapeElement;
  
  switch (shape) {
    case 'circle':
      shapeElement = selection.append('circle')
        .attr('r', radius);
      break;
      
    case 'square':
      shapeElement = selection.append('rect')
        .attr('width', size)
        .attr('height', size)
        .attr('x', -radius)
        .attr('y', -radius)
        .attr('rx', size * 0.1); // Coins légèrement arrondis
      break;
      
    case 'diamond':
      const diamondPoints = [
        [0, -radius],
        [radius, 0],
        [0, radius],
        [-radius, 0]
      ].map(p => p.join(',')).join(' ');
      
      shapeElement = selection.append('polygon')
        .attr('points', diamondPoints);
      break;
      
    case 'triangle':
      const triangleHeight = radius * Math.sqrt(3);
      const trianglePoints = [
        [0, -triangleHeight * 0.6],
        [radius, triangleHeight * 0.4],
        [-radius, triangleHeight * 0.4]
      ].map(p => p.join(',')).join(' ');
      
      shapeElement = selection.append('polygon')
        .attr('points', trianglePoints);
      break;
      
    case 'star':
      const starPoints = createStarPoints(radius, 5);
      shapeElement = selection.append('polygon')
        .attr('points', starPoints);
      break;
      
    case 'hexagon':
      const hexPoints = createHexagonPoints(radius);
      shapeElement = selection.append('polygon')
        .attr('points', hexPoints);
      break;
      
    default:
      shapeElement = selection.append('circle')
        .attr('r', radius);
  }
  
  return shapeElement;
};

/**
 * Créer les points d'une étoile
 * @param {number} radius - Rayon de l'étoile
 * @param {number} points - Nombre de branches
 * @returns {string} Points SVG
 */
const createStarPoints = (radius, points = 5) => {
  const innerRadius = radius * 0.4;
  const angle = Math.PI / points;
  const starPoints = [];
  
  for (let i = 0; i < points * 2; i++) {
    const r = i % 2 === 0 ? radius : innerRadius;
    const a = i * angle - Math.PI / 2;
    starPoints.push([
      r * Math.cos(a),
      r * Math.sin(a)
    ]);
  }
  
  return starPoints.map(p => p.join(',')).join(' ');
};

/**
 * Créer les points d'un hexagone
 * @param {number} radius - Rayon de l'hexagone
 * @returns {string} Points SVG
 */
const createHexagonPoints = (radius) => {
  const hexPoints = [];
  
  for (let i = 0; i < 6; i++) {
    const angle = (Math.PI / 3) * i - Math.PI / 2;
    hexPoints.push([
      radius * Math.cos(angle),
      radius * Math.sin(angle)
    ]);
  }
  
  return hexPoints.map(p => p.join(',')).join(' ');
};

/**
 * Appliquer le style de bordure au nœud
 * @param {d3.Selection} shapeElement - Élément SVG de la forme
 * @param {Object} data - Données du nœud
 */
export const applyNodeBorder = (shapeElement, data) => {
  const params = data.advanced_graph_params || {};
  const borderStyle = params.node_border || 'none';
  const borderColor = params.border_color || '#ffffff';
  
  if (borderStyle === 'none') {
    shapeElement.attr('stroke', 'none');
    return;
  }
  
  shapeElement
    .attr('stroke', borderColor)
    .attr('stroke-width', 3);
  
  switch (borderStyle) {
    case 'solid':
      shapeElement.attr('stroke-dasharray', 'none');
      break;
      
    case 'dashed':
      shapeElement.attr('stroke-dasharray', '8,4');
      break;
      
    case 'dotted':
      shapeElement.attr('stroke-dasharray', '2,3');
      break;
      
    case 'glow':
      // Créer un filtre de lueur
      const glowId = `glow-${data.id}`;
      createGlowFilter(glowId, borderColor);
      shapeElement.attr('filter', `url(#${glowId})`);
      break;
  }
};

/**
 * Créer un filtre de lueur SVG
 * @param {string} id - ID du filtre
 * @param {string} color - Couleur de la lueur
 */
const createGlowFilter = (id, color) => {
  // Vérifier si le filtre existe déjà
  if (document.getElementById(id)) return;
  
  const svg = d3.select('svg');
  let defs = svg.select('defs');
  
  if (defs.empty()) {
    defs = svg.append('defs');
  }
  
  const filter = defs.append('filter')
    .attr('id', id)
    .attr('x', '-50%')
    .attr('y', '-50%')
    .attr('width', '200%')
    .attr('height', '200%');
  
  filter.append('feGaussianBlur')
    .attr('in', 'SourceGraphic')
    .attr('stdDeviation', 5)
    .attr('result', 'blur');
  
  filter.append('feFlood')
    .attr('flood-color', color)
    .attr('flood-opacity', 0.8)
    .attr('result', 'color');
  
  filter.append('feComposite')
    .attr('in', 'color')
    .attr('in2', 'blur')
    .attr('operator', 'in')
    .attr('result', 'glow');
  
  const feMerge = filter.append('feMerge');
  feMerge.append('feMergeNode').attr('in', 'glow');
  feMerge.append('feMergeNode').attr('in', 'SourceGraphic');
};

/**
 * Ajouter une icône au centre du nœud
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 * @param {Object} data - Données du nœud
 */
export const addNodeIcon = (nodeGroup, data) => {
  const params = data.advanced_graph_params || {};
  const icon = params.node_icon;
  
  if (!icon) return;
  
  const size = data.node_size || 60;
  const fontSize = size * 0.5;
  
  nodeGroup.append('text')
    .attr('class', 'node-icon')
    .attr('text-anchor', 'middle')
    .attr('dominant-baseline', 'central')
    .attr('font-size', `${fontSize}px`)
    .attr('pointer-events', 'none')
    .text(icon);
};

/**
 * Ajouter un badge au nœud
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 * @param {Object} data - Données du nœud
 */
export const addNodeBadge = (nodeGroup, data) => {
  const params = data.advanced_graph_params || {};
  const badge = params.node_badge;
  
  if (!badge) return;
  
  const size = data.node_size || 60;
  const radius = size / 2;
  
  // Mapping des badges vers emojis
  const badgeEmojis = {
    'new': '🆕',
    'featured': '⭐',
    'hot': '🔥',
    'updated': '🔄',
    'popular': '💎'
  };
  
  const badgeGroup = nodeGroup.append('g')
    .attr('class', 'node-badge')
    .attr('transform', `translate(${radius * 0.7}, ${-radius * 0.7})`);
  
  // Cercle de fond pour le badge
  badgeGroup.append('circle')
    .attr('r', size * 0.2)
    .attr('fill', '#ffffff')
    .attr('stroke', '#333')
    .attr('stroke-width', 1);
  
  // Emoji du badge
  badgeGroup.append('text')
    .attr('text-anchor', 'middle')
    .attr('dominant-baseline', 'central')
    .attr('font-size', `${size * 0.25}px`)
    .text(badgeEmojis[badge] || badge);
};

/**
 * Ajouter un label au nœud
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 * @param {Object} data - Données du nœud
 */
export const addNodeLabel = (nodeGroup, data) => {
  const params = data.advanced_graph_params || {};
  const label = params.node_label || data.title;
  const showLabel = params.show_label;
  
  if (!label) return;
  
  const size = data.node_size || 60;
  const radius = size / 2;
  
  const labelElement = nodeGroup.append('text')
    .attr('class', 'node-label')
    .attr('text-anchor', 'middle')
    .attr('y', radius + 15)
    .attr('font-size', '11px')
    .attr('font-weight', '600')
    .attr('fill', '#333')
    .attr('pointer-events', 'none')
    .style('opacity', showLabel ? 1 : 0)
    .text(label.length > 20 ? label.substring(0, 20) + '...' : label);
  
  // Fond blanc pour lisibilité
  const bbox = labelElement.node().getBBox();
  nodeGroup.insert('rect', '.node-label')
    .attr('class', 'node-label-bg')
    .attr('x', bbox.x - 4)
    .attr('y', bbox.y - 2)
    .attr('width', bbox.width + 8)
    .attr('height', bbox.height + 4)
    .attr('fill', 'rgba(255, 255, 255, 0.9)')
    .attr('rx', 3)
    .style('opacity', showLabel ? 1 : 0);
};

/**
 * Appliquer l'animation d'entrée
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 * @param {Object} data - Données du nœud
 * @param {number} delay - Délai avant animation (ms)
 */
export const applyEntranceAnimation = (nodeGroup, data, delay = 0) => {
  const params = data.advanced_graph_params || {};
  const animation = params.entrance_animation || 'fade';
  const duration = 600;
  
  switch (animation) {
    case 'fade':
      nodeGroup
        .style('opacity', 0)
        .transition()
        .delay(delay)
        .duration(duration)
        .style('opacity', 1);
      break;
      
    case 'scale':
      nodeGroup
        .attr('transform', d => `translate(${d.x},${d.y}) scale(0)`)
        .transition()
        .delay(delay)
        .duration(duration)
        .attr('transform', d => `translate(${d.x},${d.y}) scale(1)`)
        .ease(d3.easeElasticOut.amplitude(1).period(0.5));
      break;
      
    case 'slide':
      const startY = data.y - 100;
      nodeGroup
        .attr('transform', `translate(${data.x},${startY})`)
        .style('opacity', 0)
        .transition()
        .delay(delay)
        .duration(duration)
        .attr('transform', `translate(${data.x},${data.y})`)
        .style('opacity', 1)
        .ease(d3.easeCubicOut);
      break;
      
    case 'bounce':
      nodeGroup
        .attr('transform', d => `translate(${d.x},${d.y - 200})`)
        .transition()
        .delay(delay)
        .duration(duration)
        .attr('transform', d => `translate(${d.x},${d.y})`)
        .ease(d3.easeBounceOut);
      break;
      
    case 'none':
    default:
      // Pas d'animation
      break;
  }
};

/**
 * Appliquer l'effet au survol
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 * @param {Object} data - Données du nœud
 * @param {boolean} isHovering - État du survol
 */
export const applyHoverEffect = (nodeGroup, data, isHovering) => {
  const params = data.advanced_graph_params || {};
  const effect = params.hover_effect || 'zoom';
  const duration = 200;
  
  // Afficher/cacher le label si non permanent
  if (!params.show_label) {
    nodeGroup.selectAll('.node-label, .node-label-bg')
      .transition()
      .duration(duration)
      .style('opacity', isHovering ? 1 : 0);
  }
  
  if (!isHovering) {
    // Réinitialiser tous les effets
    nodeGroup
      .transition()
      .duration(duration)
      .attr('transform', d => `translate(${d.x},${d.y})`);
    
    return;
  }
  
  switch (effect) {
    case 'zoom':
      nodeGroup
        .transition()
        .duration(duration)
        .attr('transform', d => `translate(${d.x},${d.y}) scale(1.2)`);
      break;
      
    case 'pulse':
      const pulseAnimation = () => {
        nodeGroup
          .transition()
          .duration(300)
          .attr('transform', d => `translate(${d.x},${d.y}) scale(1.15)`)
          .transition()
          .duration(300)
          .attr('transform', d => `translate(${d.x},${d.y}) scale(1)`)
          .on('end', function() {
            if (nodeGroup.classed('hovering')) {
              pulseAnimation();
            }
          });
      };
      nodeGroup.classed('hovering', true);
      pulseAnimation();
      break;
      
    case 'glow':
      nodeGroup.select('.node-shape')
        .transition()
        .duration(duration)
        .attr('filter', 'brightness(1.3)');
      break;
      
    case 'rotate':
      nodeGroup
        .transition()
        .duration(800)
        .attrTween('transform', function(d) {
          return t => `translate(${d.x},${d.y}) rotate(${t * 360})`;
        });
      break;
      
    case 'bounce':
      nodeGroup
        .transition()
        .duration(150)
        .attr('transform', d => `translate(${d.x},${d.y - 10})`)
        .transition()
        .duration(150)
        .attr('transform', d => `translate(${d.x},${d.y})`)
        .ease(d3.easeBounceOut);
      break;
      
    case 'none':
    default:
      // Pas d'effet
      break;
  }
};

/**
 * Nettoyer les effets de survol
 * @param {d3.Selection} nodeGroup - Groupe du nœud
 */
export const clearHoverEffect = (nodeGroup) => {
  nodeGroup.classed('hovering', false);
  nodeGroup.interrupt(); // Arrêter toutes les transitions en cours
};

/**
 * Créer un style de lien personnalisé
 * @param {d3.Selection} linkSelection - Sélection des liens
 * @param {Object} sourceData - Données du nœud source
 * @returns {Function} Fonction de tracé du lien
 */
export const createLinkPath = (sourceData) => {
  const params = sourceData.advanced_graph_params || {};
  const linkStyle = params.link_style || 'curve';
  const linkStrength = params.link_strength || 1.0;
  
  switch (linkStyle) {
    case 'straight':
      return d3.linkHorizontal()
        .x(d => d.x)
        .y(d => d.y);
      
    case 'curve':
      return d3.linkRadial()
        .angle(d => Math.atan2(d.y, d.x))
        .radius(d => Math.sqrt(d.x * d.x + d.y * d.y));
      
    case 'wave':
      return (d) => {
        const dx = d.target.x - d.source.x;
        const dy = d.target.y - d.source.y;
        const dr = Math.sqrt(dx * dx + dy * dy);
        const waveAmplitude = 20 * linkStrength;
        
        return `M${d.source.x},${d.source.y} Q${d.source.x + dx/2 + waveAmplitude},${d.source.y + dy/2 - waveAmplitude} ${d.target.x},${d.target.y}`;
      };
      
    case 'dotted':
    case 'dashed':
      // Ces styles sont appliqués via stroke-dasharray dans applyLinkStyle
      return d3.linkHorizontal()
        .x(d => d.x)
        .y(d => d.y);
      
    default:
      return d3.linkHorizontal()
        .x(d => d.x)
        .y(d => d.y);
  }
};

/**
 * Appliquer le style visuel aux liens
 * @param {d3.Selection} linkElement - Élément du lien
 * @param {Object} sourceData - Données du nœud source
 */
export const applyLinkStyle = (linkElement, sourceData) => {
  const params = sourceData.advanced_graph_params || {};
  const linkStyle = params.link_style || 'curve';
  const linkStrength = params.link_strength || 1.0;
  
  const strokeWidth = Math.max(1, linkStrength * 2);
  
  linkElement
    .attr('stroke-width', strokeWidth)
    .attr('opacity', 0.6);
  
  switch (linkStyle) {
    case 'dotted':
      linkElement.attr('stroke-dasharray', '2,4');
      break;
      
    case 'dashed':
      linkElement.attr('stroke-dasharray', '8,4');
      break;
      
    default:
      linkElement.attr('stroke-dasharray', 'none');
  }
};

/**
 * Grouper les nœuds par groupe visuel
 * @param {Array} nodes - Liste des nœuds
 * @returns {Object} Nœuds groupés par visual_group
 */
export const groupNodesByVisualGroup = (nodes) => {
  const groups = {};
  
  nodes.forEach(node => {
    const params = node.advanced_graph_params || {};
    const group = params.visual_group || 'default';
    
    if (!groups[group]) {
      groups[group] = [];
    }
    
    groups[group].push(node);
  });
  
  return groups;
};

/**
 * Calculer la position optimale d'un groupe visuel
 * @param {Array} groupNodes - Nœuds du groupe
 * @param {number} width - Largeur du conteneur
 * @param {number} height - Hauteur du conteneur
 * @returns {Object} Position {x, y}
 */
export const calculateGroupCenter = (groupNodes, width, height) => {
  if (groupNodes.length === 0) {
    return { x: width / 2, y: height / 2 };
  }
  
  const sumX = groupNodes.reduce((sum, node) => sum + (node.x || 0), 0);
  const sumY = groupNodes.reduce((sum, node) => sum + (node.y || 0), 0);
  
  return {
    x: sumX / groupNodes.length,
    y: sumY / groupNodes.length
  };
};
