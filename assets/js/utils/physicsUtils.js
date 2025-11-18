/**
 * Physics and Simulation Utilities
 * 
 * Helper functions for force-directed graph physics:
 * - Repulsion forces
 * - Velocity damping
 * - Node collision
 * 
 * Extracted from GraphContainer.jsx for better code organization
 */

/**
 * Apply repulsion forces between nodes
 * Prevents nodes from overlapping by pushing them apart
 * 
 * @param {Array} nodes - Array of node objects with x, y positions
 * @param {Object} velocitiesRef - Reference to node velocities
 * @param {Object} config - Physics configuration
 * @param {number} width - Canvas width for boundary checking
 * @param {number} height - Canvas height for boundary checking
 * @returns {boolean} True if any node moved
 */
export const applyRepulsionForces = (nodes, velocitiesRef, config = {}, width = 16000, height = 11200) => {
  const {
    repulsionForce = 3000, // 🔥 Augmenté pour grand espace
    minDistance = 200, // 🔥 Augmenté pour meilleur espacement
    damping = 0.85 // 🔥 Légèrement augmenté
  } = config;

  let hasMovement = false;

  nodes.forEach((node) => {
    let forceX = 0;
    let forceY = 0;

    // Calculate repulsion with all other nodes
    nodes.forEach((otherNode) => {
      if (node.id === otherNode.id) return;

      const dx = node.x - otherNode.x;
      const dy = node.y - otherNode.y;
      const distance = Math.sqrt(dx * dx + dy * dy);

      // Apply repulsion force if nodes are too close
      if (distance < minDistance && distance > 0) {
        const force = repulsionForce / (distance * distance);
        forceX += (dx / distance) * force;
        forceY += (dy / distance) * force;
      }
    });

    // Don't apply force to fixed nodes
    if (node.fx !== undefined && node.fy !== undefined) {
      velocitiesRef.current[node.id] = { x: 0, y: 0 };
      return;
    }

    // Initialize velocity if needed
    if (!velocitiesRef.current[node.id]) {
      velocitiesRef.current[node.id] = { x: 0, y: 0 };
    }

    // Apply damping
    velocitiesRef.current[node.id].x =
      (velocitiesRef.current[node.id].x + forceX) * damping;
    velocitiesRef.current[node.id].y =
      (velocitiesRef.current[node.id].y + forceY) * damping;

    // Update position if velocity is significant
    if (
      Math.abs(velocitiesRef.current[node.id].x) > 0.1 ||
      Math.abs(velocitiesRef.current[node.id].y) > 0.1
    ) {
      node.x += velocitiesRef.current[node.id].x;
      node.y += velocitiesRef.current[node.id].y;

      // Keep within canvas bounds
      node.x = Math.max(100, Math.min(width - 100, node.x));
      node.y = Math.max(100, Math.min(height - 100, node.y));

      hasMovement = true;
    } else {
      // Stop movement if velocity too low
      velocitiesRef.current[node.id] = { x: 0, y: 0 };
    }
  });

  return hasMovement;
};

/**
 * Initialize node positions with intelligent clustering by category
 * Les nœuds de la même catégorie sont positionnés plus proches
 * Places nodes randomly near the center
 * 
 * @param {Array} nodes - Array of node objects
 * @param {number} width - Canvas width
 * @param {number} height - Canvas height
 * @param {number} spread - Random spread distance (default: 150 pour équilibre optimal)
 */
export const initializeNodePositions = (nodes, width, height, spread = 150) => {
  // 🔥 NOUVELLE LOGIQUE: Grouper par catégorie pour positionnement intelligent
  const categoryGroups = {};
  
  // Regrouper les nœuds par catégorie principale
  nodes.forEach((node) => {
    // Utiliser la première catégorie ou "uncategorized"
    const categoryId = node.categories && node.categories.length > 0 
      ? node.categories[0].id 
      : 'uncategorized';
    
    if (!categoryGroups[categoryId]) {
      categoryGroups[categoryId] = [];
    }
    categoryGroups[categoryId].push(node);
  });
  
  // Calculer le nombre de groupes et créer une disposition en cercle
  const categoryIds = Object.keys(categoryGroups);
  const numCategories = categoryIds.length;
  const centerX = width / 2;
  const centerY = height / 2;
  
  // Rayon du cercle principal (distance du centre)
  // Équilibre entre visibilité et compacité
  const mainRadius = Math.min(width, height) * 0.22; // 🔥 Ajusté à 0.22 pour meilleur équilibre
  
  categoryIds.forEach((categoryId, index) => {
    const categoryNodes = categoryGroups[categoryId];
    
    // Position du centre de ce groupe (disposition circulaire)
    const angle = (index / numCategories) * 2 * Math.PI;
    const groupCenterX = centerX + mainRadius * Math.cos(angle);
    const groupCenterY = centerY + mainRadius * Math.sin(angle);
    
    // 🔥 Spread équilibré pour les nœuds d'un même groupe
    const groupSpread = spread * 0.5; // 50% du spread global pour bonne visibilité
    
    // Positionner chaque nœud du groupe autour du centre du groupe
    categoryNodes.forEach((node) => {
      if (node.x === undefined || node.x === null) {
        // Position aléatoire autour du centre du groupe
        node.x = groupCenterX + (Math.random() - 0.5) * groupSpread;
      }
      if (node.y === undefined || node.y === null) {
        node.y = groupCenterY + (Math.random() - 0.5) * groupSpread;
      }
    });
  });
};

/**
 * Check if two nodes are colliding
 * 
 * @param {Object} node1 - First node
 * @param {Object} node2 - Second node
 * @param {number} padding - Extra padding distance (default: 10)
 * @returns {boolean} True if nodes are colliding
 */
export const areNodesColliding = (node1, node2, padding = 10) => {
  const dx = node1.x - node2.x;
  const dy = node1.y - node2.y;
  const distance = Math.sqrt(dx * dx + dy * dy);
  
  const radius1 = (node1.node_size || 80) / 2 + padding;
  const radius2 = (node2.node_size || 80) / 2 + padding;
  
  return distance < (radius1 + radius2);
};

/**
 * Get distance between two nodes
 * 
 * @param {Object} node1 - First node with x, y
 * @param {Object} node2 - Second node with x, y
 * @returns {number} Euclidean distance
 */
export const getNodeDistance = (node1, node2) => {
  const dx = node1.x - node2.x;
  const dy = node1.y - node2.y;
  return Math.sqrt(dx * dx + dy * dy);
};

/**
 * Calculate center of mass for a group of nodes
 * 
 * @param {Array} nodes - Array of nodes
 * @returns {Object} Center point {x, y}
 */
export const calculateCenterOfMass = (nodes) => {
  if (nodes.length === 0) return { x: 0, y: 0 };
  
  const totalMass = nodes.reduce((sum, node) => sum + (node.node_weight || 1), 0);
  
  const centerX = nodes.reduce((sum, node) => {
    return sum + (node.x * (node.node_weight || 1));
  }, 0) / totalMass;
  
  const centerY = nodes.reduce((sum, node) => {
    return sum + (node.y * (node.node_weight || 1));
  }, 0) / totalMass;
  
  return { x: centerX, y: centerY };
};

/**
 * Apply gravity force towards a point
 * Useful for keeping nodes centered
 * 
 * @param {Object} node - Node to apply gravity to
 * @param {Object} center - Center point {x, y}
 * @param {number} strength - Gravity strength (default: 0.1)
 */
export const applyGravity = (node, center, strength = 0.1) => {
  const dx = center.x - node.x;
  const dy = center.y - node.y;
  
  node.vx = (node.vx || 0) + dx * strength;
  node.vy = (node.vy || 0) + dy * strength;
};

/**
 * Limit node velocity to prevent excessive speeds
 * 
 * @param {Object} node - Node with vx, vy velocities
 * @param {number} maxSpeed - Maximum speed (default: 10)
 */
export const limitVelocity = (node, maxSpeed = 10) => {
  const vx = node.vx || 0;
  const vy = node.vy || 0;
  const speed = Math.sqrt(vx * vx + vy * vy);
  
  if (speed > maxSpeed) {
    const scale = maxSpeed / speed;
    node.vx = vx * scale;
    node.vy = vy * scale;
  }
};
