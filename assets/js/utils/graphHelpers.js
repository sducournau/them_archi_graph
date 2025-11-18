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
    width = 1800, // 🎯 ZOOM++ Réduit pour meilleure visibilité
    height = 1400, // 🎯 ZOOM++ Ratio harmonieux ~4:3
    nodeSpacing = 70, // 🎯 RÉDUIT pour proximité accrue
    clusterStrength = 0.10, // 🎯 FORTEMENT RÉDUIT pour éviter dispersion brutale
    linkStrength = 0.25, // 🎯 DOUBLÉ pour rapprocher nodes connectés
    organicMode = false, // ❌ DÉSACTIVÉ - Mode island désactivé
  } = options;

  // Créer les centres de clusters basés sur les catégories
  const clusterCenters = createClusterCenters(categories, width, height);

  // ❌ Mode island DÉSACTIVÉ
  const islands = [];
  
  // 🔗 NOUVEAU: Calculer les liens entre nœuds pour la force d'attraction
  const links = calculateNodeLinks(nodes, {
    minProximityScore: 25, // 🚀 ENCORE RÉDUIT pour créer plus de connexions
    maxLinksPerNode: 15, // 🚀 AUGMENTÉ pour densité maximale de liens
    useProximityScore: true,
  });

  // 🎯 PLACEMENT INITIAL ORGANIQUE: Distribution naturelle au centre
  nodes.forEach((node, index) => {
    // Toujours réinitialiser les positions pour garantir le placement
    if (!node.fx && !node.fy) {
      // 🌟 PLACEMENT CONCENTRÉ AU CENTRE - éviter dispersion
      const centerRadius = Math.min(width, height) * 0.10; // 🚀 ULTRA-RÉDUIT à 10% pour concentration extrême
      
      // Distribution circulaire ULTRA-DENSE autour du centre
      const angle = Math.random() * Math.PI * 2; // Angle aléatoire complet
      const distance = Math.random() * centerRadius; // Distance très limitée
      
      // Position ULTRA-COMPACTE au centre
      node.x = width / 2 + Math.cos(angle) * distance;
      node.y = height / 2 + Math.sin(angle) * distance;
      
      // Vélocité initiale MINIMALE pour stabilité maximale
      node.vx = (Math.random() - 0.5) * 2; // 🚀 RÉDUIT de 3 à 2
      node.vy = (Math.random() - 0.5) * 2;
    }
  });

  // Simulation de force avec paramètres optimisés
  const simulation = d3
    .forceSimulation(nodes)
    // 🚀 Force de répulsion ULTRA-MINIMALE - permet densité maximale
    .force("charge", d3.forceManyBody()
      .strength((d) => {
        // Répulsion ULTRA-FAIBLE pour permettre proximité extrême
        const linkCount = d.linkCount || 0;
        const baseStrength = organicMode && d.post_type === 'archi_project' ? -30 : -38; // 🚀 RÉDUIT encore
        
        // Plus un nœud a de liens, BEAUCOUP MOINS il repousse
        // 0 liens → force complète, 10+ liens → force réduite de 70%
        const linkFactor = 1 - Math.min(linkCount / 12, 0.70); // 🚀 Réduction encore plus forte
        
        return baseStrength * linkFactor;
      })
      .distanceMax(180) // 🚀 FORTEMENT RÉDUIT pour influence ultra-locale
      .distanceMin(25) // 🚀 RÉDUIT pour permettre proximité extrême
    )
    
    // 🚀 Force de liens MAXIMALE pour groupes ultra-cohésifs
    .force("link", d3.forceLink(links)
      .id(d => d.id)
      .distance(d => {
        // Distance ULTRA-COURTE pour cohésion maximale
        const baseDistance = 65; // 🚀 FORTEMENT RÉDUIT à 65px pour rapprochement maximal
        const strengthFactor = d.strength || 1;
        
        // Plus le lien est fort, plus les nœuds sont ULTRA-PROCHES
        // strength de 1-5 → distance de 30-65px (TRÈS COURT)
        return baseDistance - (strengthFactor * 15); // 🚀 AUGMENTÉ l'effet à 15
      })
      .strength(d => {
        // Force de lien ULTRA-FORTE pour rapprochement maximal
        const proximityScore = d.proximity?.score || 50;
        
        // Score 25-100 → strength 0.50-0.85 (ULTRA-FORT)
        const normalizedStrength = 0.50 + (proximityScore / 100) * 0.35; // 🚀 FORTEMENT AUGMENTÉ
        return Math.min(normalizedStrength, 0.85); // 🚀 Max à 0.85
      })
    )

    // Force de centrage MODÉRÉE pour permettre expansion des liens
    .force("center", d3.forceCenter(width / 2, height / 2).strength(0.08)) // 🚀 RÉDUIT pour laisser les liens agir

    // Force anti-collision ULTRA-SOUPLE pour densité maximale
    .force(
      "collision",
      d3
        .forceCollide()
        .radius((d) => {
          // Rayon ULTRA-MINIMAL pour permettre proximité extrême
          const nodeRadius = (d.node_size || 80) / 2; // 40px par défaut
          const safetyMargin = organicMode ? 4 : 3; // 🚀 ULTRA-RÉDUIT pour densité extrême
          return nodeRadius + safetyMargin; // ~43-44px total = proximité maximale
        })
        .strength(0.35) // 🚀 FORTEMENT RÉDUIT pour permettre superposition
        .iterations(1) // 🚀 Minimal pour fluidité
    )

    // Force de clustering ULTRA-MINIMAL pour éviter dispersion
    .force(
      "cluster",
      forceCluster().centers(clusterCenters).strength(clusterStrength * 0.08) // � ULTRA-RÉDUIT à 0.08
    )

    // ❌ Force d'îles DÉSACTIVÉE
    // .force(
    //   "islands",
    //   forceIslands().islands(islands).strength(0.65)
    // )

    // 🎯 BOUNDARY ajustée pour espace 1800x1400
    .force("boundary", forceBoundary(width, height, 50));

  // ⚡ Configuration optimisée pour CONVERGENCE RAPIDE ET STABLE
  simulation
    .alpha(0.8) // 🚀 AUGMENTÉ pour plus d'énergie initiale et convergence efficace
    .alphaDecay(0.035) // 🚀 AUGMENTÉ pour convergence plus rapide
    .alphaMin(0.001) // 🚀 Arrêt ultra-précis
    .velocityDecay(0.75); // 🚀 AUGMENTÉ pour freinage plus fort et stabilité

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
      radius: 100 // ✅ Réduit de 150 à 100 pour îles plus compactes
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
  let strength = 0.4; // 🔥 AUGMENTÉ de 0.3 à 0.4 pour attraction plus forte
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
          // Force d'attraction RENFORCÉE au sein de l'île
          const force = strength * alpha * (distance / island.radius);
          node.vx += (dx / distance) * force * 0.9; // 🔥 AUGMENTÉ de 0.8 à 0.9
          node.vy += (dy / distance) * force * 0.9; // 🔥 AUGMENTÉ de 0.8 à 0.9
        }
      });
    });
    
    // Répulsion RENFORCÉE entre îles
    for (let i = 0; i < islands.length; i++) {
      for (let j = i + 1; j < islands.length; j++) {
        const islandA = islands[i];
        const islandB = islands[j];
        
        const dx = islandB.center.x - islandA.center.x;
        const dy = islandB.center.y - islandA.center.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        const minDistance = islandA.radius + islandB.radius;
        
        if (distance < minDistance && distance > 0) {
          // Repousser FORTEMENT les îles qui se chevauchent
          const repulsion = (minDistance - distance) * 0.015; // 🔥 AUGMENTÉ de 0.01 à 0.015
          
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
  
  force.islands = function(newIslands) {
    if (!arguments.length) return islands;
    islands = newIslands || [];
    return force;
  };
  
  force.strength = function(newStrength) {
    if (!arguments.length) return strength;
    strength = newStrength;
    return force;
  };
  
  force.alpha = function(newAlpha) {
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
  const padding = 100; // 🎯 RÉDUIT de 150 à 100 pour centres plus proches
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
      // Disposition en cercle PLUS COMPACT pour superposition naturelle
      const angle = (index / categories.length) * 2 * Math.PI;
      const radius = Math.min(usableWidth, usableHeight) / 4; // 🎯 RÉDUIT de /3 à /4
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

      // 🎯 Réduire FORTEMENT la force de clustering si le nœud a beaucoup de liens
      // Les nœuds fortement connectés restent près de leurs voisins, pas du centre
      const linkCount = node.linkCount || 0;
      const clusterReduction = Math.min(linkCount / 8, 0.75); // 🎯 Max 75% de réduction
      const adjustedStrength = strength * (1 - clusterReduction);

      // Calculer la force vers le centre du cluster
      const dx = center.x - node.x;
      const dy = center.y - node.y;
      const distance = Math.sqrt(dx * dx + dy * dy);

      if (distance > 0) {
        const force = adjustedStrength * alpha * distance;
        node.vx += (dx / distance) * force;
        node.vy += (dy / distance) * force;
      }
    });
  };

  force.initialize = (newNodes) => {
    nodes = newNodes;
    
    // 🔗 NOUVEAU: Calculer le nombre de liens par nœud pour ajuster le clustering
    nodes.forEach(node => {
      node.linkCount = 0;
    });
    
    // Compter les liens (approximation basée sur les catégories/tags partagés)
    for (let i = 0; i < nodes.length; i++) {
      for (let j = i + 1; j < nodes.length; j++) {
        const nodeA = nodes[i];
        const nodeB = nodes[j];
        
        const sharedCategories = (nodeA.categories || []).filter(catA =>
          (nodeB.categories || []).some(catB => catA.id === catB.id)
        );
        
        const sharedTags = (nodeA.tags || []).filter(tagA =>
          (nodeB.tags || []).some(tagB => tagA.id === tagB.id)
        );
        
        if (sharedCategories.length > 0 || sharedTags.length > 0) {
          nodeA.linkCount++;
          nodeB.linkCount++;
        }
      }
    }
  };

  force.centers = function(newCenters) {
    if (!arguments.length) return centers;
    centers = newCenters;
    return force;
  };

  force.strength = function(newStrength) {
    if (!arguments.length) return strength;
    strength = newStrength;
    return force;
  };

  force.alpha = function(newAlpha) {
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
export const forceBoundary = (width, height, padding = 50) => {
  let nodes = [];
  let strength = 0.6; // 🔥 Force DOUBLÉE de 0.3 à 0.6 pour contenir les nœuds

  const force = () => {
    nodes.forEach((node) => {
      const radius = (node.node_size || 80) / 2;

      // 🔥 Contraintes fermes - empêcher l'échappement
      const minX = padding + radius;
      const maxX = width - padding - radius;
      const minY = padding + radius;
      const maxY = height - padding - radius;

      // Force progressive X - poussée ferme vers l'intérieur
      if (node.x < minX) {
        node.vx += (minX - node.x) * strength * 0.3; // 🔥 Force TRIPLÉE de 0.1 à 0.3
        node.x = Math.max(node.x, minX); // 🔥 Clamping pour empêcher sortie
      } else if (node.x > maxX) {
        node.vx += (maxX - node.x) * strength * 0.3;
        node.x = Math.min(node.x, maxX);
      }

      // Force progressive Y - poussée ferme vers l'intérieur
      if (node.y < minY) {
        node.vy += (minY - node.y) * strength * 0.3;
        node.y = Math.max(node.y, minY);
      } else if (node.y > maxY) {
        node.vy += (maxY - node.y) * strength * 0.3;
        node.y = Math.min(node.y, maxY);
      }

      // Force douce pour éviter les bords (zone de 80px)
      const softBoundary = 80; // 🔥 RÉDUIT de 100 à 80
      if (node.x < padding + radius + softBoundary) {
        node.vx += (padding + radius + softBoundary - node.x) * strength * 0.5; // 🔥 Force augmentée
      }
      if (node.x > width - padding - radius - softBoundary) {
        node.vx += (width - padding - radius - softBoundary - node.x) * strength * 0.5;
      }
      if (node.y < padding + radius + softBoundary) {
        node.vy += (padding + radius + softBoundary - node.y) * strength * 0.5;
      }
      if (node.y > height - padding - radius - softBoundary) {
        node.vy += (height - padding - radius - softBoundary - node.y) * strength * 0.5;
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
};;

/**
 * Mettre à jour les positions des nœuds dans le DOM
 * @param {d3.Selection} container - Conteneur D3
 * @param {Array} nodes - Nœuds à positionner
 */
export const updateNodePositions = (container, nodes) => {
  const nodeElements = container.selectAll(".graph-node");

  nodeElements.attr("transform", (d) => {
    // 🔥 FIX: Contraindre les coordonnées dans les limites du viewBox étendue (16000x11200)
    // Cela empêche les nœuds d'avoir des positions absurdes
    const maxWidth = 16000; // 🔥 Doublé pour nouveau viewBox
    const maxHeight = 11200; // 🔥 Doublé pour nouveau viewBox
    const margin = 800; // 🔥 Doublé pour meilleur espacement
    
    // Si les coordonnées sont invalides ou hors limites, les ramener au centre
    let x = d.x || 0;
    let y = d.y || 0;
    
    // Vérifier si les coordonnées sont valides et dans les limites
    if (!isFinite(x) || !isFinite(y) || 
        Math.abs(x) > maxWidth * 2 || 
        Math.abs(y) > maxHeight * 2) {
      // Coordonnées invalides ou trop grandes : ramener au centre
      x = maxWidth / 2;
      y = maxHeight / 2;
      d.x = x;
      d.y = y;
    } else {
      // Contraindre dans les limites avec marge
      x = Math.max(margin, Math.min(maxWidth - margin, x));
      y = Math.max(margin, Math.min(maxHeight - margin, y));
      d.x = x;
      d.y = y;
    }
    
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
    minProximityScore = 35, // ⬆️ Score minimum augmenté de 20 à 35 pour des liens plus pertinents
    maxLinksPerNode = 10, // ⬆️ Augmenté de 8 à 10 pour plus de connexions
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
    CONTENT_SIMILARITY: 15,      // ⬆️ Augmenté de 5 à 15
    PROJECT_SAME_TYPE: 30,       // ✨ NOUVEAU: Projets de même type
    PROJECT_SAME_CLIENT: 35,     // ✨ NOUVEAU: Même client
    PROJECT_SAME_LOCATION: 25,   // ✨ NOUVEAU: Même localisation
    ILLUSTRATION_SAME_TECHNIQUE: 30, // ✨ NOUVEAU: Même technique
    ILLUSTRATION_SAME_SOFTWARE: 20,  // ✨ NOUVEAU: Même logiciel
    ILLUSTRATION_LINKED_PROJECT: 50, // ✨ NOUVEAU: Lié au même projet
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

  // Similarité de contenu (améliorée)
  if (nodeA.title && nodeB.title && nodeA.excerpt && nodeB.excerpt) {
    const titleA = nodeA.title.toLowerCase();
    const titleB = nodeB.title.toLowerCase();
    const excerptA = nodeA.excerpt.toLowerCase();
    const excerptB = nodeB.excerpt.toLowerCase();
    
    // Extraire les mots-clés significatifs (plus de 4 lettres)
    const getKeywords = (text) => {
      return text.match(/\b\w{4,}\b/g) || [];
    };
    
    const keywordsA = [...getKeywords(titleA), ...getKeywords(excerptA)];
    const keywordsB = [...getKeywords(titleB), ...getKeywords(excerptB)];
    
    // Compter les mots-clés communs
    const commonKeywords = keywordsA.filter(word => keywordsB.includes(word));
    const uniqueCommon = [...new Set(commonKeywords)];
    
    if (uniqueCommon.length >= 3) {
      score += WEIGHTS.CONTENT_SIMILARITY;
      details.factors.contentSimilarity = {
        score: WEIGHTS.CONTENT_SIMILARITY,
        keywords: uniqueCommon.length
      };
    } else if (uniqueCommon.length >= 1) {
      const partialScore = WEIGHTS.CONTENT_SIMILARITY * 0.5;
      score += partialScore;
      details.factors.contentSimilarity = {
        score: partialScore,
        keywords: uniqueCommon.length
      };
    }
  }

  // ✨ NOUVEAU: Liens spécifiques aux PROJETS ARCHITECTURAUX
  if (nodeA.post_type === 'archi_project' && nodeB.post_type === 'archi_project') {
    const metaA = nodeA.project_meta || {};
    const metaB = nodeB.project_meta || {};
    
    // Même type de projet (résidentiel, commercial, etc.)
    if (metaA.project_type && metaB.project_type && 
        metaA.project_type === metaB.project_type) {
      score += WEIGHTS.PROJECT_SAME_TYPE;
      details.factors.projectType = WEIGHTS.PROJECT_SAME_TYPE;
    }
    
    // Même client
    if (metaA.client && metaB.client && 
        metaA.client.toLowerCase() === metaB.client.toLowerCase()) {
      score += WEIGHTS.PROJECT_SAME_CLIENT;
      details.factors.projectClient = WEIGHTS.PROJECT_SAME_CLIENT;
    }
    
    // Même localisation (ville/région)
    if (metaA.location && metaB.location) {
      const locA = metaA.location.toLowerCase();
      const locB = metaB.location.toLowerCase();
      
      // Correspondance exacte ou partielle
      if (locA === locB || locA.includes(locB) || locB.includes(locA)) {
        score += WEIGHTS.PROJECT_SAME_LOCATION;
        details.factors.projectLocation = WEIGHTS.PROJECT_SAME_LOCATION;
      }
    }
    
    // Surface similaire (± 20%)
    if (metaA.surface && metaB.surface) {
      const surfA = parseFloat(metaA.surface);
      const surfB = parseFloat(metaB.surface);
      const ratio = Math.min(surfA, surfB) / Math.max(surfA, surfB);
      
      if (ratio >= 0.8) {
        score += 10;
        details.factors.similarSurface = 10;
      }
    }
  }

  // ✨ NOUVEAU: Liens spécifiques aux ILLUSTRATIONS
  if (nodeA.post_type === 'archi_illustration' && nodeB.post_type === 'archi_illustration') {
    const metaA = nodeA.illustration_meta || {};
    const metaB = nodeB.illustration_meta || {};
    
    // Même technique (dessin, 3D, aquarelle, etc.)
    if (metaA.technique && metaB.technique && 
        metaA.technique.toLowerCase() === metaB.technique.toLowerCase()) {
      score += WEIGHTS.ILLUSTRATION_SAME_TECHNIQUE;
      details.factors.illustrationTechnique = WEIGHTS.ILLUSTRATION_SAME_TECHNIQUE;
    }
    
    // Même logiciel (AutoCAD, SketchUp, etc.)
    if (metaA.software && metaB.software && 
        metaA.software.toLowerCase() === metaB.software.toLowerCase()) {
      score += WEIGHTS.ILLUSTRATION_SAME_SOFTWARE;
      details.factors.illustrationSoftware = WEIGHTS.ILLUSTRATION_SAME_SOFTWARE;
    }
  }

  // ✨ NOUVEAU: Liens PROJET <-> ILLUSTRATION
  if ((nodeA.post_type === 'archi_project' && nodeB.post_type === 'archi_illustration') ||
      (nodeA.post_type === 'archi_illustration' && nodeB.post_type === 'archi_project')) {
    
    const illustration = nodeA.post_type === 'archi_illustration' ? nodeA : nodeB;
    const project = nodeA.post_type === 'archi_project' ? nodeA : nodeB;
    
    // Vérifier si l'illustration est liée au projet
    if (illustration.illustration_meta?.project_link === project.id) {
      score += WEIGHTS.ILLUSTRATION_LINKED_PROJECT;
      details.factors.linkedProject = WEIGHTS.ILLUSTRATION_LINKED_PROJECT;
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
