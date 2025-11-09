/**
 * Arrow Satellites Controller
 * Manages animated arrow GIFs that orbit around graph nodes
 * Number of arrows is determined by node categories instead of size
 * Each category can have its own satellite configuration
 */

import * as d3 from 'd3';

/**
 * Available arrow GIF assets
 */
const ARROW_GIFS = [
  'dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif',
  'red-bouncing-arrow-pointer-transparent-background-usagif.gif',
  'white-arrow-pointing-right-transparent-background-usagif.gif'
];

/**
 * Category-specific satellite configurations
 * Maps category slug to satellite settings
 * Note: count and orbitRadius are now multipliers/modifiers
 */
const CATEGORY_SATELLITE_CONFIG = {
  // Default configuration for uncategorized nodes
  'default': {
    countMultiplier: 1.0,      // Multiplier for base count from node size
    orbitRadiusOffset: 20,     // Additional pixels beyond image radius
    speed: 0.0005,
    arrowSizeMultiplier: 1.0,  // Multiplier for arrow size
    arrowGifs: ARROW_GIFS      // Use all available arrows
  },
  
  // Example configurations per category
  // You can customize these based on your actual category slugs
  'architecture': {
    countMultiplier: 1.2,      // 20% more satellites
    orbitRadiusOffset: 25,
    speed: 0.0006,
    arrowSizeMultiplier: 1.1,
    arrowGifs: ['white-arrow-pointing-right-transparent-background-usagif.gif']
  },
  
  'design': {
    countMultiplier: 1.0,
    orbitRadiusOffset: 20,
    speed: 0.0005,
    arrowSizeMultiplier: 0.9,
    arrowGifs: ['dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif']
  },
  
  'illustration': {
    countMultiplier: 1.3,      // 30% more satellites
    orbitRadiusOffset: 30,
    speed: 0.0007,
    arrowSizeMultiplier: 1.2,
    arrowGifs: ['red-bouncing-arrow-pointer-transparent-background-usagif.gif']
  },
  
  'featured': {
    countMultiplier: 1.5,      // 50% more satellites
    orbitRadiusOffset: 35,
    speed: 0.0008,
    arrowSizeMultiplier: 1.3,
    arrowGifs: ARROW_GIFS
  }
};

/**
 * Get the theme directory URL from WordPress
 * Falls back to relative path if not in WordPress context
 */
const getThemeUrl = () => {
  if (typeof window !== 'undefined' && window.graphConfig?.themeUrl) {
    return window.graphConfig.themeUrl;
  }
  // Fallback pour développement
  return '/wp-content/themes/archi-graph-template';
};

/**
 * Get satellite configuration for a node based on its primary category
 * @param {Object} nodeData - Node data containing categories
 * @returns {Object} - Satellite configuration {count, orbitRadius, speed, arrowGifs}
 */
export const getCategorySatelliteConfig = (nodeData) => {
  // Get primary category (first category in the list)
  let primaryCategory = 'default';
  
  if (nodeData.categories && Array.isArray(nodeData.categories) && nodeData.categories.length > 0) {
    primaryCategory = nodeData.categories[0].slug || nodeData.categories[0].name?.toLowerCase() || 'default';
  }
  
  // Return category-specific config or default
  return CATEGORY_SATELLITE_CONFIG[primaryCategory] || CATEGORY_SATELLITE_CONFIG['default'];
};

/**
 * Calculate number of arrow satellites based on node size and category
 * Base count comes from node size, then modified by category multiplier
 * @param {Object} nodeData - Node data containing node_size and categories
 * @returns {number} - Number of arrows (0-8)
 */
export const calculateArrowCount = (nodeData) => {
  const config = getCategorySatelliteConfig(nodeData);
  const nodeSize = nodeData.node_size || nodeData.nodeSize || 60;
  
  // Base count from node size (similar to image size logic)
  let baseCount = 0;
  if (nodeSize >= 400) baseCount = 6;       // Very large images
  else if (nodeSize >= 300) baseCount = 5;  // Large images
  else if (nodeSize >= 200) baseCount = 4;  // Medium-large images
  else if (nodeSize >= 150) baseCount = 3;  // Medium images
  else if (nodeSize >= 100) baseCount = 2;  // Small-medium images
  else if (nodeSize >= 60) baseCount = 1;   // Small images
  else baseCount = 0;                       // Very small images
  
  // Apply category multiplier
  const multiplier = config.countMultiplier || 1.0;
  const finalCount = Math.round(baseCount * multiplier);
  
  // Cap between 0 and 8
  return Math.max(0, Math.min(8, finalCount));
};

/**
 * Calculate satellite positions around a node
 * Distributes arrows evenly in a circle around the node
 * Orbit radius is based on actual image size + category offset
 * @param {Object} nodeData - Node data containing node_size and categories
 * @param {number} count - Number of arrows
 * @returns {Array} - Array of {angle, x, y} positions
 */
export const calculateSatellitePositions = (nodeData, count) => {
  if (count === 0) return [];
  
  // Get category-specific configuration
  const config = getCategorySatelliteConfig(nodeData);
  const nodeSize = nodeData.node_size || nodeData.nodeSize || 60;
  
  // Calculate orbit radius: half of image size (radius of circle) + offset
  // This ensures arrows point AT the image, not over it
  const imageRadius = nodeSize / 2;
  const orbitOffset = config.orbitRadiusOffset || 20;
  const radius = imageRadius + orbitOffset;
  
  const positions = [];
  const angleStep = (Math.PI * 2) / count;
  
  // Start at a random offset for variety
  const startAngle = Math.random() * Math.PI * 2;
  
  for (let i = 0; i < count; i++) {
    const angle = startAngle + (angleStep * i);
    positions.push({
      angle: angle,
      x: Math.cos(angle) * radius,
      y: Math.sin(angle) * radius
    });
  }
  
  return positions;
};

/**
 * Get a random arrow GIF URL from category configuration
 * @param {Object} nodeData - Node data containing categories
 * @returns {string} - URL to arrow GIF
 */
const getRandomArrowGif = (nodeData) => {
  const config = getCategorySatelliteConfig(nodeData);
  const availableGifs = config.arrowGifs || ARROW_GIFS;
  const randomIndex = Math.floor(Math.random() * availableGifs.length);
  const gifName = availableGifs[randomIndex];
  return `${getThemeUrl()}/gif/${gifName}`;
};

/**
 * Calculate rotation angle to point arrow towards node center
 * @param {number} x - Satellite X position relative to node
 * @param {number} y - Satellite Y position relative to node
 * @returns {number} - Rotation angle in degrees
 */
const calculateArrowRotation = (x, y) => {
  // Angle pointing towards center (opposite of position vector)
  const angleToCenter = Math.atan2(-y, -x);
  // Convert to degrees
  return (angleToCenter * 180 / Math.PI) + 90; // +90 to align arrow tip
};

/**
 * Create arrow satellites for a node based on its categories
 * @param {Object} nodeData - Node data with categories
 * @param {d3.Selection} nodeGroup - D3 selection of the node group
 */
export const createArrowSatellites = (nodeData, nodeGroup) => {
  // Get category-specific configuration
  const config = getCategorySatelliteConfig(nodeData);
  const arrowCount = calculateArrowCount(nodeData);
  
  if (arrowCount === 0) {
    // Remove any existing satellites
    nodeGroup.selectAll('.arrow-satellite').remove();
    return;
  }
  
  const positions = calculateSatellitePositions(nodeData, arrowCount);
  
  // Create satellite group if it doesn't exist
  let satelliteGroup = nodeGroup.select('.satellites-group');
  if (satelliteGroup.empty()) {
    satelliteGroup = nodeGroup.append('g')
      .attr('class', 'satellites-group')
      .attr('data-category', () => {
        // Add category data attribute for styling
        if (nodeData.categories && nodeData.categories[0]) {
          return nodeData.categories[0].slug || nodeData.categories[0].name;
        }
        return 'default';
      })
      .style('pointer-events', 'none'); // Make non-clickable
  }
  
  // Bind data to satellites
  const satellites = satelliteGroup
    .selectAll('.arrow-satellite')
    .data(positions, (d, i) => i);
  
  // Remove old satellites
  satellites.exit().remove();
  
  // Create new satellites
  const newSatellites = satellites.enter()
    .append('g')
    .attr('class', 'arrow-satellite')
    .style('pointer-events', 'none'); // Make non-clickable
  
  // Calculate arrow size based on node size and category multiplier
  const nodeSize = nodeData.node_size || nodeData.nodeSize || 60;
  const arrowSizeMultiplier = config.arrowSizeMultiplier || 1.0;
  
  // Base arrow size scales with node size (min 30px, max 80px)
  const baseArrowSize = Math.max(30, Math.min(80, nodeSize * 0.15));
  const arrowSize = baseArrowSize * arrowSizeMultiplier;
  const halfArrowSize = arrowSize / 2;
  
  // Add arrow image with category-specific GIF and adaptive size
  newSatellites.append('image')
    .attr('class', 'arrow-gif')
    .attr('width', arrowSize)
    .attr('height', arrowSize)
    .attr('x', -halfArrowSize) // Center the image
    .attr('y', -halfArrowSize)
    .attr('href', () => getRandomArrowGif(nodeData))
    .style('pointer-events', 'none'); // Make non-clickable
  
  // Update all satellites (new + existing)
  const allSatellites = newSatellites.merge(satellites);
  
  allSatellites
    .attr('transform', d => {
      const rotation = calculateArrowRotation(d.x, d.y);
      return `translate(${d.x}, ${d.y}) rotate(${rotation})`;
    });
  
  // Store positions and config for animation
  nodeData._satellitePositions = positions;
  // Store the actual calculated orbit radius (image radius + offset)
  const imageRadius = (nodeData.node_size || nodeData.nodeSize || 60) / 2;
  const orbitOffset = config.orbitRadiusOffset || 20;
  nodeData._satelliteOrbitRadius = imageRadius + orbitOffset;
  nodeData._satelliteSpeed = config.speed || 0.0005;
  nodeData._satelliteArrowSize = arrowSize; // Store for potential future use
};

/**
 * Animate arrow satellites in orbit
 * Uses category-specific rotation speed
 * Call this in the simulation tick or animation loop
 * @param {d3.Selection} nodeGroup - D3 selection of node groups
 * @param {number} time - Current animation time (optional, uses Date.now())
 */
export const animateArrowSatellites = (nodeGroups, time = null) => {
  const currentTime = time || Date.now();
  
  nodeGroups.each(function(d) {
    const node = d3.select(this);
    const satelliteGroup = node.select('.satellites-group');
    
    if (satelliteGroup.empty() || !d._satellitePositions) return;
    
    // Use category-specific configuration stored during creation
    const orbitRadius = d._satelliteOrbitRadius || 45;
    const rotationSpeed = d._satelliteSpeed || 0.0005; // Radians per millisecond
    const timeOffset = d.id * 1000; // Offset based on node ID for variety
    
    // Update satellite positions
    satelliteGroup.selectAll('.arrow-satellite')
      .data(d._satellitePositions)
      .attr('transform', (pos, i) => {
        // Calculate animated angle
        const baseAngle = pos.angle;
        const animationOffset = (currentTime + timeOffset + (i * 500)) * rotationSpeed;
        const animatedAngle = baseAngle + animationOffset;
        
        // Calculate new position
        const x = Math.cos(animatedAngle) * orbitRadius;
        const y = Math.sin(animatedAngle) * orbitRadius;
        
        // Calculate rotation to point towards center
        const rotation = calculateArrowRotation(x, y);
        
        return `translate(${x}, ${y}) rotate(${rotation})`;
      });
  });
};

/**
 * Update arrow satellites when node data changes
 * @param {d3.Selection} nodeGroups - D3 selection of all node groups
 */
export const updateArrowSatellites = (nodeGroups) => {
  nodeGroups.each(function(d) {
    createArrowSatellites(d, d3.select(this));
  });
};

/**
 * Remove arrow satellites from a node
 * @param {d3.Selection} nodeGroup - D3 selection of the node group
 */
export const removeArrowSatellites = (nodeGroup) => {
  nodeGroup.select('.satellites-group').remove();
};

/**
 * Toggle arrow satellites visibility
 * @param {d3.Selection} nodeGroups - D3 selection of all node groups
 * @param {boolean} visible - Show or hide satellites
 */
export const toggleArrowSatellites = (nodeGroups, visible) => {
  nodeGroups.selectAll('.satellites-group')
    .style('opacity', visible ? 1 : 0);
};
