import * as d3 from "d3";

/**
 * Créer une simulation de force D3.js pour le graphique
 * @param {Array} nodes - Nœuds (articles)
 * @param {Array} categories - Catégories pour clustering
 * @param {Object} options - Options de configuration
 * @returns {d3.Simulation} Simulation D3.js
 */
export const createForceSimulation = (nodes, categories, options = {}) => {
  const {
    width = 1200,
    height = 800,
    nodeSpacing = 100,
    clusterStrength = 0.05, // ⚡ Réduit de 0.1 à 0.05
    linkStrength = 0.1,
    organicMode = false, // ⚡ Désactivé par défaut pour meilleures performances
  } = options;

  // Créer les centres de clusters basés sur les catégories
  const clusterCenters = createClusterCenters(categories, width, height);

  // ⚡ PERFORMANCE: Îles architecturales désactivées par défaut
  const islands = organicMode ? createArchitecturalIslands(nodes) : null;

  // Simulation de force avec paramètres optimisés
  const simulation = d3
    .forceSimulation(nodes)
    // ⚡ Force de répulsion ÉQUILIBRÉE pour espacement visible dans viewBox 1200x800
    .force("charge", d3.forceManyBody()
      .strength((d) => {
        // Force adaptée pour viewBox 1200x800 étiré sur grand écran
        if (organicMode && d.post_type === 'archi_project') {
          return -150; // ⚡ Répulsion modérée pour projets architecturaux
        }
        return -200; // ⚡ Répulsion standard pour articles normaux
      })
      .distanceMax(300) // ⚡ Distance d'effet étendue pour viewBox 1200x800
    )

    // Force de centrage général MODÉRÉE
    .force("center", d3.forceCenter(width / 2, height / 2).strength(0.05)) // ⚡ Force de centrage standard

    // Force anti-collision NORMALE pour éviter chevauchement
    .force(
      "collision",
      d3
        .forceCollide()
        .radius((d) => (d.node_size || 60) / 2 + (organicMode ? 10 : 8)) // ⚡ Padding suffisant
        .strength(organicMode ? 0.7 : 0.8) // ⚡ Force de collision standard
        .iterations(1)
    )

    // Force de clustering NORMALE
    .force(
      "cluster",
      forceCluster().centers(clusterCenters).strength(clusterStrength * 2) // ⚡ Clustering modéré
    )

    // ⚡ PERFORMANCE: Force d'îles désactivée par défaut
    .force(
      "islands",
      organicMode ? forceIslands().islands(islands).strength(0.1) : null // ⚡ Réduit de 0.15
    )

    // ⚡ PERFORMANCE: Gravité désactivée par défaut (une force de moins)
    // .force(
    //   "gravity",
    //   organicMode ? d3.forceY(height / 2).strength(0.01) : null
    // )

    // Force vers les bords (éviter que les nœuds sortent de l'écran)
    .force("boundary", forceBoundary(width, height, 50));

  // ⚡ PERFORMANCE: Configuration optimisée pour stabilisation plus rapide
  simulation
    .alpha(0.8) // ⚡ Réduit de 1 pour démarrage moins violent
    .alphaDecay(0.03) // ⚡ Augmenté de 0.015 pour stabilisation plus rapide
    .velocityDecay(0.5); // ⚡ Augmenté de 0.3/0.4 pour freiner plus vite

  return simulation;
};

/**
 * Créer des îles architecturales basées sur les relations entre projets
 * @param {Array} nodes - Liste des nœuds
 * @returns {Array} Liste des îles avec leurs nœuds membres
 */
const createArchitecturalIslands = (nodes) => {
  const islands = [];
  const projectNodes = nodes.filter(n => n.post_type === 'archi_project');
  const visited = new Set();
  
  // Créer des îles basées sur les relations fortes
  projectNodes.forEach(project => {
    if (visited.has(project.id)) return;
    
    const island = {
      id: `island_${islands.length}`,
      members: [project],
      center: { x: 0, y: 0 },
      radius: 150
    };
    
    visited.add(project.id);
    
    // Trouver les projets fortement liés (même catégories ou tags)
    const relatedProjects = findRelatedProjects(project, projectNodes, visited);
    island.members.push(...relatedProjects);
    
    // Marquer tous les membres comme visités
    relatedProjects.forEach(p => visited.add(p.id));
    
    if (island.members.length > 0) {
      islands.push(island);
    }
  });
  
  return islands;
};

/**
 * Trouver les projets liés à un projet donné
 * @param {Object} project - Projet de référence
 * @param {Array} allProjects - Tous les projets
 * @param {Set} visited - Projets déjà visités
 * @returns {Array} Projets liés
 */
const findRelatedProjects = (project, allProjects, visited) => {
  const related = [];
  const maxIslandSize = 5; // Limite de taille d'île pour éviter les méga-îles
  
  allProjects.forEach(other => {
    if (visited.has(other.id) || other.id === project.id) return;
    if (related.length >= maxIslandSize) return;
    
    // Calculer la similarité basée sur catégories et tags
    const sharedCategories = (project.categories || [])
      .filter(c => (other.categories || []).some(oc => oc.id === c.id));
    const sharedTags = (project.tags || [])
      .filter(t => (other.tags || []).some(ot => ot.id === t.id));
    
    // Relations manuelles
    const hasManualLink = (project.related_articles || []).includes(other.id) ||
                         (other.related_articles || []).includes(project.id);
    
    // Créer une île si forte relation
    if (sharedCategories.length >= 2 || sharedTags.length >= 3 || hasManualLink) {
      related.push(other);
    }
  });
  
  return related;
};

/**
 * Force personnalisée pour les îles architecturales
 * @returns {Function} Force d'îles
 */
const forceIslands = () => {
  let nodes = [];
  let islands = [];
  let strength = 0.15;
  let alpha = 1;
  
  const force = () => {
    // Mettre à jour les centres des îles
    islands.forEach(island => {
      if (island.members.length === 0) return;
      
      // Calculer le centre de l'île
      let centerX = 0, centerY = 0;
      island.members.forEach(member => {
        const node = nodes.find(n => n.id === member.id);
        if (node && node.x !== undefined && node.y !== undefined) {
          centerX += node.x;
          centerY += node.y;
        }
      });
      island.center.x = centerX / island.members.length;
      island.center.y = centerY / island.members.length;
      
      // Appliquer une force douce vers le centre de l'île
      island.members.forEach(member => {
        const node = nodes.find(n => n.id === member.id);
        if (!node || node.x === undefined || node.y === undefined) return;
        
        const dx = island.center.x - node.x;
        const dy = island.center.y - node.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        
        if (distance > 0 && distance < island.radius) {
          // Force d'attraction douce au sein de l'île
          const force = strength * alpha * (distance / island.radius);
          node.vx += (dx / distance) * force * 0.5;
          node.vy += (dy / distance) * force * 0.5;
        }
      });
    });
    
    // Répulsion douce entre îles
    for (let i = 0; i < islands.length; i++) {
      for (let j = i + 1; j < islands.length; j++) {
        const islandA = islands[i];
        const islandB = islands[j];
        
        const dx = islandB.center.x - islandA.center.x;
        const dy = islandB.center.y - islandA.center.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        const minDistance = islandA.radius + islandB.radius;
        
        if (distance < minDistance && distance > 0) {
          // Repousser légèrement les îles qui se chevauchent
          const repulsion = (minDistance - distance) * 0.01;
          
          islandA.members.forEach(member => {
            const node = nodes.find(n => n.id === member.id);
            if (node) {
              node.vx -= (dx / distance) * repulsion;
              node.vy -= (dy / distance) * repulsion;
            }
          });
          
          islandB.members.forEach(member => {
            const node = nodes.find(n => n.id === member.id);
            if (node) {
              node.vx += (dx / distance) * repulsion;
              node.vy += (dy / distance) * repulsion;
            }
          });
        }
      }
    }
  };
  
  force.initialize = (newNodes) => {
    nodes = newNodes;
  };
  
  force.islands = (newIslands) => {
    if (!arguments.length) return islands;
    islands = newIslands || [];
    return force;
  };
  
  force.strength = (newStrength) => {
    if (!arguments.length) return strength;
    strength = newStrength;
    return force;
  };
  
  force.alpha = (newAlpha) => {
    alpha = newAlpha;
    return force;
  };
  
  return force;
};

/**
 * Créer les centres de clusters pour chaque catégorie
 * @param {Array} categories - Liste des catégories
 * @param {number} width - Largeur du conteneur
 * @param {number} height - Hauteur du conteneur
 * @returns {Object} Centres de clusters
 */
const createClusterCenters = (categories, width, height) => {
  const centers = {};
  const padding = 150;
  const usableWidth = width - 2 * padding;
  const usableHeight = height - 2 * padding;

  categories.forEach((category, index) => {
    // Disposer les centres en grille ou cercle selon le nombre
    let x, y;

    if (categories.length <= 4) {
      // Grille 2x2 pour 4 catégories ou moins
      const cols = 2;
      const row = Math.floor(index / cols);
      const col = index % cols;
      x = padding + (col + 0.5) * (usableWidth / cols);
      y =
        padding +
        (row + 0.5) * (usableHeight / Math.ceil(categories.length / cols));
    } else {
      // Disposition en cercle pour plus de catégories
      const angle = (index / categories.length) * 2 * Math.PI;
      const radius = Math.min(usableWidth, usableHeight) / 3;
      x = width / 2 + Math.cos(angle) * radius;
      y = height / 2 + Math.sin(angle) * radius;
    }

    centers[category.id] = { x, y, category };
  });

  return centers;
};

/**
 * Force personnalisée pour le clustering par catégories
 * @returns {Function} Force de clustering
 */
const forceCluster = () => {
  let nodes = [];
  let centers = {};
  let strength = 0.1;
  let alpha = 1;

  const force = () => {
    nodes.forEach((node) => {
      if (!node.categories || !node.categories.length) return;

      // Utiliser la première catégorie comme référence principale
      const primaryCategory = node.categories[0];
      const center = centers[primaryCategory.id];

      if (!center) return;

      // Calculer la force vers le centre du cluster
      const dx = center.x - node.x;
      const dy = center.y - node.y;
      const distance = Math.sqrt(dx * dx + dy * dy);

      if (distance > 0) {
        const force = strength * alpha * distance;
        node.vx += (dx / distance) * force;
        node.vy += (dy / distance) * force;
      }
    });
  };

  force.initialize = (newNodes) => {
    nodes = newNodes;
  };

  force.centers = (newCenters) => {
    if (!arguments.length) return centers;
    centers = newCenters;
    return force;
  };

  force.strength = (newStrength) => {
    if (!arguments.length) return strength;
    strength = newStrength;
    return force;
  };

  force.alpha = (newAlpha) => {
    alpha = newAlpha;
    return force;
  };

  return force;
};

/**
 * Force de limite pour garder les nœuds dans l'écran
 * @param {number} width - Largeur
 * @param {number} height - Hauteur
 * @param {number} padding - Espacement des bords
 * @returns {Function} Force de limite
 */
const forceBoundary = (width, height, padding = 50) => {
  let nodes = [];
  let strength = 0.1;

  const force = () => {
    nodes.forEach((node) => {
      const radius = (node.node_size || 60) / 2;

      // Bord gauche
      if (node.x < padding + radius) {
        node.vx += (padding + radius - node.x) * strength;
      }
      // Bord droit
      if (node.x > width - padding - radius) {
        node.vx += (width - padding - radius - node.x) * strength;
      }
      // Bord haut
      if (node.y < padding + radius) {
        node.vy += (padding + radius - node.y) * strength;
      }
      // Bord bas
      if (node.y > height - padding - radius) {
        node.vy += (height - padding - radius - node.y) * strength;
      }
    });
  };

  force.initialize = (newNodes) => {
    nodes = newNodes;
  };

  force.strength = (newStrength) => {
    if (!arguments.length) return strength;
    strength = newStrength;
    return force;
  };

  return force;
};

/**
 * Mettre à jour les positions des nœuds dans le DOM
 * @param {d3.Selection} container - Conteneur D3
 * @param {Array} nodes - Nœuds à positionner
 */
export const updateNodePositions = (container, nodes) => {
  const nodeElements = container.selectAll(".graph-node");

  nodeElements.attr("transform", (d) => {
    const x = d.x || 0;
    const y = d.y || 0;
    if (!d._loggedOnce) {
      d._loggedOnce = true;
    }
    return `translate(${x}, ${y})`;
  });
};

/**
 * Calculer les liens entre les nœuds basés sur les catégories et tags communs
 * Utilise le système de score de proximité
 * @param {Array} nodes - Liste des nœuds
 * @param {Object} options - Options de configuration
 * @returns {Array} Liste des liens avec scores de proximité
 */
export const calculateNodeLinks = (nodes, options = {}) => {
  const {
    minProximityScore = 20, // Score minimum pour créer un lien visible
    maxLinksPerNode = 8, // Nombre maximum de liens par nœud
    useProximityScore = true, // Utiliser le nouveau système de score
  } = options;

  const links = [];
  const linksPerNode = new Map();

  // Initialiser le compteur de liens par nœud
  nodes.forEach((node) => {
    linksPerNode.set(node.id, []);
  });

  // ✅ NOUVEAU: Créer les liens manuels pour le livre d'or (guestbook)
  // Ces liens ont priorité et ne sont pas limités par maxLinksPerNode
  nodes.forEach((node) => {
    if (node.post_type === 'archi_guestbook' && node.guestbook_meta?.linked_articles) {
      const linkedArticleIds = node.guestbook_meta.linked_articles;
      
      linkedArticleIds.forEach((linkedId) => {
        const targetNode = nodes.find(n => n.id === linkedId);
        
        if (targetNode) {
          // Créer un lien fort et distinctif pour le livre d'or
          const link = {
            source: node,
            target: targetNode,
            strength: 3, // Force élevée pour les liens manuels
            type: 'guestbook', // Type spécial pour styling différent
            manual: true, // Marqueur de lien manuel
            weight: 100, // Poids élevé pour le calcul de proximité
            id: `guestbook-${node.id}-${targetNode.id}`,
          };
          
          links.push(link);
          linksPerNode.get(node.id).push(link);
          linksPerNode.get(targetNode.id).push(link);
        }
      });
    }
  });

  for (let i = 0; i < nodes.length; i++) {
    for (let j = i + 1; j < nodes.length; j++) {
      const nodeA = nodes[i];
      const nodeB = nodes[j];

      // Ignorer les liens si un des nœuds a hide_links activé
      if (nodeA.hide_links || nodeB.hide_links) {
        continue;
      }

      // NOUVELLE RÈGLE: Ne pas créer de liens entre articles de même catégorie
      // Vérifier si les deux nœuds partagent TOUTES leurs catégories
      const categoriesA = (nodeA.categories || []).map(c => c.id).sort();
      const categoriesB = (nodeB.categories || []).map(c => c.id).sort();
      
      // Si les deux nœuds ont exactement les mêmes catégories, ignorer
      if (categoriesA.length > 0 && 
          categoriesA.length === categoriesB.length &&
          categoriesA.every((catId, idx) => catId === categoriesB[idx])) {
        continue;
      }

      if (useProximityScore) {
        // Nouveau système: calculer le score de proximité
        const proximity = calculateProximity(nodeA, nodeB);

        if (proximity.score >= minProximityScore) {
          const link = {
            source: nodeA,
            target: nodeB,
            strength: proximity.normalizedScore / 20, // Normaliser pour D3 (0-5)
            proximity: proximity,
            weight: proximity.score,
            id: `${nodeA.id}-${nodeB.id}`,
          };

          links.push(link);
          linksPerNode.get(nodeA.id).push(link);
          linksPerNode.get(nodeB.id).push(link);
        }
      } else {
        // Ancien système: seulement catégories communes
        const sharedCategories = nodeA.categories.filter((catA) =>
          nodeB.categories.some((catB) => catA.id === catB.id)
        );

        if (sharedCategories.length > 0) {
          links.push({
            source: nodeA,
            target: nodeB,
            strength: sharedCategories.length,
            categories: sharedCategories,
          });
        }
      }
    }
  }

  // Limiter le nombre de liens par nœud si nécessaire
  if (useProximityScore && maxLinksPerNode > 0) {
    const filteredLinks = new Set();

    nodes.forEach((node) => {
      const nodeLinks = linksPerNode.get(node.id);

      // Trier par score décroissant
      nodeLinks.sort((a, b) => b.proximity.score - a.proximity.score);

      // Garder les N meilleurs
      nodeLinks.slice(0, maxLinksPerNode).forEach((link) => {
        filteredLinks.add(link);
      });
    });

    return Array.from(filteredLinks);
  }

  return links;
};

/**
 * Calcule le score de proximité entre deux nœuds
 * Basé sur catégories, tags, dates, etc.
 * @param {Object} nodeA - Premier nœud
 * @param {Object} nodeB - Deuxième nœud
 * @returns {Object} Score de proximité et détails
 */
const calculateProximity = (nodeA, nodeB) => {
  const WEIGHTS = {
    SHARED_CATEGORY: 40,
    SHARED_TAG: 25,
    SAME_PRIMARY_CATEGORY: 20,
    DATE_PROXIMITY: 10,
    CONTENT_SIMILARITY: 5,
  };

  let score = 0;
  const details = {
    sharedCategories: [],
    sharedTags: [],
    samePrimaryCategory: false,
    factors: {},
  };

  // Catégories partagées
  const sharedCategories =
    nodeA.categories?.filter((catA) =>
      nodeB.categories?.some((catB) => catA.id === catB.id)
    ) || [];

  if (sharedCategories.length > 0) {
    const categoryScore = WEIGHTS.SHARED_CATEGORY * sharedCategories.length;
    score += categoryScore;
    details.sharedCategories = sharedCategories;
    details.factors.categories = {
      count: sharedCategories.length,
      score: categoryScore,
    };
  }

  // Catégorie principale identique
  if (
    nodeA.categories?.length > 0 &&
    nodeB.categories?.length > 0 &&
    nodeA.categories[0].id === nodeB.categories[0].id
  ) {
    score += WEIGHTS.SAME_PRIMARY_CATEGORY;
    details.samePrimaryCategory = true;
    details.factors.primaryCategory = WEIGHTS.SAME_PRIMARY_CATEGORY;
  }

  // Tags partagés
  const sharedTags =
    nodeA.tags?.filter((tagA) =>
      nodeB.tags?.some((tagB) => tagA.id === tagB.id)
    ) || [];

  if (sharedTags.length > 0) {
    const tagScore = WEIGHTS.SHARED_TAG * sharedTags.length;
    score += tagScore;
    details.sharedTags = sharedTags;
    details.factors.tags = {
      count: sharedTags.length,
      score: tagScore,
    };
  }

  // Proximité temporelle
  if (nodeA.date && nodeB.date) {
    const dateA = new Date(nodeA.date);
    const dateB = new Date(nodeB.date);
    const daysDiff = Math.abs((dateA - dateB) / (1000 * 60 * 60 * 24));

    if (daysDiff <= 7) {
      score += WEIGHTS.DATE_PROXIMITY;
      details.factors.dateProximity = WEIGHTS.DATE_PROXIMITY;
    } else if (daysDiff <= 30) {
      const dateScore = WEIGHTS.DATE_PROXIMITY * 0.5;
      score += dateScore;
      details.factors.dateProximity = dateScore;
    }
  }

  // Similarité de contenu
  if (nodeA.excerpt && nodeB.excerpt) {
    const lengthA = nodeA.excerpt.length;
    const lengthB = nodeB.excerpt.length;
    const lengthRatio = Math.min(lengthA, lengthB) / Math.max(lengthA, lengthB);

    if (lengthRatio > 0.7) {
      score += WEIGHTS.CONTENT_SIMILARITY;
      details.factors.contentSimilarity = WEIGHTS.CONTENT_SIMILARITY;
    }
  }

  // Calculer score normalisé (0-100)
  const maxPossible =
    WEIGHTS.SHARED_CATEGORY *
      Math.min(nodeA.categories?.length || 0, nodeB.categories?.length || 0) +
    WEIGHTS.SHARED_TAG *
      Math.min(nodeA.tags?.length || 0, nodeB.tags?.length || 0) +
    WEIGHTS.SAME_PRIMARY_CATEGORY +
    WEIGHTS.DATE_PROXIMITY +
    WEIGHTS.CONTENT_SIMILARITY;

  const normalizedScore = maxPossible > 0 ? (score / maxPossible) * 100 : 0;

  return {
    score: Math.round(score),
    normalizedScore: Math.round(normalizedScore),
    strength: getStrengthCategory(score),
    details,
  };
};

/**
 * Détermine la catégorie de force du lien
 * @param {number} score - Score de proximité
 * @returns {string} Catégorie de force
 */
const getStrengthCategory = (score) => {
  if (score >= 100) return "very-strong";
  if (score >= 70) return "strong";
  if (score >= 40) return "medium";
  if (score >= 20) return "weak";
  return "very-weak";
};

/**
 * Appliquer un filtre d'animation sur les nœuds
 * @param {d3.Selection} nodes - Sélection des nœuds
 * @param {Function} filterFn - Fonction de filtre
 * @param {number} duration - Durée de l'animation
 */
export const animateNodeFilter = (nodes, filterFn, duration = 500) => {
  nodes
    .transition()
    .duration(duration)
    .style("opacity", (d) => (filterFn(d) ? 1 : 0.2))
    .style("transform", (d) => {
      const scale = filterFn(d) ? 1 : 0.8;
      return `translate(${d.x}px, ${d.y}px) scale(${scale})`;
    });
};

/**
 * Rechercher des nœuds par texte
 * @param {Array} nodes - Liste des nœuds
 * @param {string} searchTerm - Terme de recherche
 * @returns {Array} Nœuds correspondants
 */
export const searchNodes = (nodes, searchTerm) => {
  if (!searchTerm || searchTerm.trim() === "") {
    return nodes;
  }

  const term = searchTerm.toLowerCase().trim();

  return nodes.filter((node) => {
    // Recherche dans le titre
    if (node.title && node.title.toLowerCase().includes(term)) {
      return true;
    }

    // Recherche dans l'extrait
    if (node.excerpt && node.excerpt.toLowerCase().includes(term)) {
      return true;
    }

    // Recherche dans les catégories
    if (
      node.categories &&
      node.categories.some((cat) => cat.name.toLowerCase().includes(term))
    ) {
      return true;
    }

    // Recherche dans les tags
    if (
      node.tags &&
      node.tags.some((tag) => tag.name.toLowerCase().includes(term))
    ) {
      return true;
    }

    return false;
  });
};

/**
 * Calculer les statistiques du graphique
 * @param {Array} nodes - Nœuds du graphique
 * @param {Array} categories - Catégories
 * @returns {Object} Statistiques
 */
export const calculateGraphStats = (nodes, categories) => {
  const stats = {
    totalNodes: nodes.length,
    totalCategories: categories.length,
    nodesPerCategory: {},
    averageConnections: 0,
    density: 0,
  };

  // Compter les nœuds par catégorie
  categories.forEach((category) => {
    stats.nodesPerCategory[category.id] = nodes.filter((node) =>
      node.categories.some((cat) => cat.id === category.id)
    ).length;
  });

  // Calculer les connexions moyennes et la densité
  const links = calculateNodeLinks(nodes);
  stats.averageConnections =
    nodes.length > 0 ? (links.length * 2) / nodes.length : 0;

  const maxPossibleLinks = (nodes.length * (nodes.length - 1)) / 2;
  stats.density = maxPossibleLinks > 0 ? links.length / maxPossibleLinks : 0;

  return stats;
};

/**
 * Optimiser les performances de la simulation
 * @param {d3.Simulation} simulation - Simulation D3
 * @param {number} nodeCount - Nombre de nœuds
 */
export const optimizeSimulationPerformance = (simulation, nodeCount) => {
  // Ajuster les paramètres selon le nombre de nœuds
  if (nodeCount > 100) {
    // Pour de gros graphiques, réduire la précision mais accélérer
    simulation
      .alphaDecay(0.05) // Plus rapide
      .velocityDecay(0.4); // Plus de friction

    // Réduire la force de répulsion pour éviter les calculs coûteux
    simulation.force("charge")?.strength(-200);
  } else if (nodeCount > 50) {
    simulation.alphaDecay(0.03).velocityDecay(0.3);
  } else {
    // Pour de petits graphiques, privilégier la qualité
    simulation.alphaDecay(0.02).velocityDecay(0.2);
  }
};

/**
 * Sauvegarder l'état actuel du graphique
 * @param {Array} nodes - Nœuds avec positions
 * @param {Object} viewState - État de la vue (zoom, pan)
 * @returns {Object} État sérialisé
 */
export const saveGraphState = (nodes, viewState = {}) => {
  return {
    timestamp: Date.now(),
    nodes: nodes.map((node) => ({
      id: node.id,
      x: node.x,
      y: node.y,
    })),
    viewState: {
      scale: viewState.scale || 1,
      translateX: viewState.translateX || 0,
      translateY: viewState.translateY || 0,
    },
  };
};

/**
 * Restaurer l'état du graphique
 * @param {Object} state - État sauvegardé
 * @param {Array} currentNodes - Nœuds actuels
 * @returns {Array} Nœuds avec positions restaurées
 */
export const restoreGraphState = (state, currentNodes) => {
  if (!state || !state.nodes) return currentNodes;

  const savedPositions = new Map(
    state.nodes.map((node) => [node.id, { x: node.x, y: node.y }])
  );

  return currentNodes.map((node) => {
    const savedPos = savedPositions.get(node.id);
    if (savedPos) {
      return { ...node, x: savedPos.x, y: savedPos.y };
    }
    return node;
  });
};
