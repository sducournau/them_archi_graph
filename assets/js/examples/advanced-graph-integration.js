/**
 * Exemple d'intégration des paramètres avancés dans le graphique D3.js
 * 
 * Ce fichier montre comment utiliser les nouvelles fonctionnalités dans votre code existant
 */

import * as d3 from 'd3';
import { fetchGraphData, validateArticleData } from './utils/dataFetcher.js';
import { createForceSimulation } from './utils/graphHelpers.js';
import {
  createNodeShape,
  applyNodeBorder,
  addNodeIcon,
  addNodeBadge,
  addNodeLabel,
  applyEntranceAnimation,
  applyHoverEffect,
  clearHoverEffect,
  applyLinkStyle,
  groupNodesByVisualGroup
} from './utils/advancedShapes.js';

/**
 * Initialiser le graphique avec les nouveaux paramètres avancés
 */
export const initAdvancedGraph = async (containerId, apiEndpoint) => {
  // 1. Récupérer les données
  const data = await fetchGraphData(apiEndpoint);
  const articles = validateArticleData(data.articles || []);
  const categories = data.categories || [];
  
  // 2. Grouper par visual_group si disponible
  const visualGroups = groupNodesByVisualGroup(articles);
  
  // 3. Configuration du conteneur SVG
  const container = d3.select(`#${containerId}`);
  const width = container.node().clientWidth;
  const height = container.node().clientHeight;
  
  const svg = container.append('svg')
    .attr('width', width)
    .attr('height', height)
    .attr('viewBox', [0, 0, width, height]);
  
  // 4. Créer les groupes pour liens et nœuds
  const linkGroup = svg.append('g').attr('class', 'links');
  const nodeGroup = svg.append('g').attr('class', 'nodes');
  
  // 5. Créer la simulation avec paramètres avancés
  const simulation = createForceSimulation(articles, categories, {
    width,
    height,
    organicMode: true,
    clusterStrength: 0.2
  });
  
  // 6. Créer les liens (selon connection_depth et link_style)
  const links = createAdvancedLinks(articles);
  
  const linkElements = linkGroup
    .selectAll('line')
    .data(links)
    .enter()
    .append('line')
    .attr('class', 'link')
    .attr('stroke', '#999')
    .each(function(d) {
      // Appliquer le style de lien personnalisé
      applyLinkStyle(d3.select(this), d.source);
    });
  
  // 7. Créer les nœuds avec toutes les nouvelles fonctionnalités
  const nodes = nodeGroup
    .selectAll('g.node')
    .data(articles)
    .enter()
    .append('g')
    .attr('class', 'node')
    .attr('data-id', d => d.id)
    .call(d3.drag()
      .on('start', dragStarted)
      .on('drag', dragged)
      .on('end', dragEnded));
  
  // 8. Ajouter la forme personnalisée à chaque nœud
  nodes.each(function(d) {
    const nodeG = d3.select(this);
    
    // Créer la forme (cercle, carré, diamant, etc.)
    const shape = createNodeShape(nodeG, d);
    
    shape
      .attr('class', 'node-shape')
      .attr('fill', d.node_color || '#3498db')
      .attr('opacity', d.advanced_graph_params?.node_opacity || 1.0);
    
    // Appliquer la bordure
    applyNodeBorder(shape, d);
    
    // Ajouter l'icône si présente
    addNodeIcon(nodeG, d);
    
    // Ajouter le badge si présent
    addNodeBadge(nodeG, d);
    
    // Ajouter le label
    addNodeLabel(nodeG, d);
  });
  
  // 9. Appliquer les animations d'entrée
  nodes.each(function(d, i) {
    applyEntranceAnimation(d3.select(this), d, i * 50); // Décalage de 50ms entre chaque nœud
  });
  
  // 10. Gérer les événements de survol
  nodes
    .on('mouseenter', function(event, d) {
      const node = d3.select(this);
      applyHoverEffect(node, d, true);
      
      // Mettre en surbrillance les liens connectés
      highlightConnectedLinks(d, linkElements, true);
    })
    .on('mouseleave', function(event, d) {
      const node = d3.select(this);
      clearHoverEffect(node);
      applyHoverEffect(node, d, false);
      
      // Retirer la surbrillance des liens
      highlightConnectedLinks(d, linkElements, false);
    })
    .on('click', function(event, d) {
      // Gérer l'épinglage du nœud
      const params = d.advanced_graph_params || {};
      if (params.pin_node) {
        d.fx = d.x;
        d.fy = d.y;
      } else {
        d.fx = null;
        d.fy = null;
      }
      
      // Action par défaut : ouvrir l'article
      if (d.link) {
        window.location.href = d.link;
      }
    });
  
  // 11. Mettre à jour les positions à chaque tick
  simulation.on('tick', () => {
    linkElements
      .attr('x1', d => d.source.x)
      .attr('y1', d => d.source.y)
      .attr('x2', d => d.target.x)
      .attr('y2', d => d.target.y);
    
    nodes.attr('transform', d => `translate(${d.x},${d.y})`);
  });
  
  // 12. Retourner l'API du graphique
  return {
    simulation,
    nodes,
    links: linkElements,
    svg,
    updateNode: (nodeId, newParams) => updateNodeParams(nodes, nodeId, newParams),
    refreshGraph: () => simulation.alpha(0.3).restart()
  };
};

/**
 * Créer les liens avec prise en compte de connection_depth
 */
const createAdvancedLinks = (articles) => {
  const links = [];
  
  articles.forEach(article => {
    const params = article.advanced_graph_params || {};
    const depth = params.connection_depth || 2;
    const linkStrength = params.link_strength || 1.0;
    
    // Liens manuels
    if (article.related_articles && article.related_articles.length > 0) {
      article.related_articles.forEach(targetId => {
        const target = articles.find(a => a.id === targetId);
        if (target) {
          links.push({
            source: article,
            target: target,
            strength: linkStrength,
            type: 'manual'
          });
        }
      });
    }
    
    // Liens automatiques basés sur catégories/tags (selon depth)
    if (depth > 1) {
      const autoLinks = findAutoLinks(article, articles, depth);
      links.push(...autoLinks);
    }
  });
  
  // Éliminer les doublons
  return links.filter((link, index, self) =>
    index === self.findIndex((l) =>
      (l.source.id === link.source.id && l.target.id === link.target.id) ||
      (l.source.id === link.target.id && l.target.id === link.source.id)
    )
  );
};

/**
 * Trouver les liens automatiques basés sur la profondeur
 */
const findAutoLinks = (article, allArticles, depth) => {
  const links = [];
  const params = article.advanced_graph_params || {};
  const linkStrength = params.link_strength || 1.0;
  
  allArticles.forEach(other => {
    if (article.id === other.id) return;
    
    // Calculer la similarité
    const sharedCategories = (article.categories || [])
      .filter(c => (other.categories || []).some(oc => oc.id === c.id));
    const sharedTags = (article.tags || [])
      .filter(t => (other.tags || []).some(ot => ot.id === t.id));
    
    // Créer un lien selon la profondeur
    let shouldLink = false;
    
    if (depth >= 3 && sharedCategories.length >= 1) {
      shouldLink = true;
    } else if (depth >= 2 && sharedCategories.length >= 2) {
      shouldLink = true;
    } else if (depth >= 1 && sharedTags.length >= 3) {
      shouldLink = true;
    }
    
    if (shouldLink) {
      links.push({
        source: article,
        target: other,
        strength: linkStrength * 0.5, // Liens auto plus faibles
        type: 'auto'
      });
    }
  });
  
  return links;
};

/**
 * Mettre en surbrillance les liens connectés
 */
const highlightConnectedLinks = (node, linkElements, highlight) => {
  linkElements
    .attr('stroke', d => {
      if (d.source.id === node.id || d.target.id === node.id) {
        return highlight ? '#ff6b6b' : '#999';
      }
      return '#999';
    })
    .attr('stroke-width', d => {
      if (d.source.id === node.id || d.target.id === node.id) {
        return highlight ? 3 : (d.strength * 2);
      }
      return d.strength * 2;
    });
};

/**
 * Mettre à jour les paramètres d'un nœud dynamiquement
 */
const updateNodeParams = (nodes, nodeId, newParams) => {
  const node = nodes.filter(d => d.id === nodeId);
  
  if (node.empty()) {
    console.warn(`Node ${nodeId} not found`);
    return;
  }
  
  node.each(function(d) {
    // Mettre à jour les données
    d.advanced_graph_params = {
      ...d.advanced_graph_params,
      ...newParams
    };
    
    const nodeG = d3.select(this);
    
    // Recréer le nœud avec les nouveaux paramètres
    nodeG.selectAll('*').remove();
    
    const shape = createNodeShape(nodeG, d);
    shape
      .attr('class', 'node-shape')
      .attr('fill', d.node_color || '#3498db')
      .attr('opacity', d.advanced_graph_params?.node_opacity || 1.0);
    
    applyNodeBorder(shape, d);
    addNodeIcon(nodeG, d);
    addNodeBadge(nodeG, d);
    addNodeLabel(nodeG, d);
  });
};

/**
 * Gestion du drag avec épinglage
 */
function dragStarted(event, d) {
  const params = d.advanced_graph_params || {};
  
  if (!event.active) {
    this.simulation?.alpha(0.3).restart();
  }
  
  if (!params.pin_node) {
    d.fx = d.x;
    d.fy = d.y;
  }
}

function dragged(event, d) {
  d.fx = event.x;
  d.fy = event.y;
}

function dragEnded(event, d) {
  const params = d.advanced_graph_params || {};
  
  if (!event.active) {
    this.simulation?.alphaTarget(0);
  }
  
  // Ne relâcher que si pas épinglé
  if (!params.pin_node) {
    d.fx = null;
    d.fy = null;
  }
}

/**
 * Exemple d'utilisation
 */
export const exampleUsage = async () => {
  // Initialiser le graphique
  const graph = await initAdvancedGraph(
    'graph-container',
    '/wp-json/archi/v1/articles'
  );
  
  
  // Exemple : Mettre à jour un nœud dynamiquement
  setTimeout(() => {
    graph.updateNode(123, {
      node_shape: 'star',
      node_badge: 'featured',
      hover_effect: 'glow'
    });
    
    graph.refreshGraph();
  }, 3000);
  
  // Exemple : Ajouter un bouton pour basculer les labels
  document.getElementById('toggle-labels')?.addEventListener('click', () => {
    graph.nodes.each(function(d) {
      const params = d.advanced_graph_params;
      params.show_label = !params.show_label;
      
      d3.select(this).selectAll('.node-label, .node-label-bg')
        .transition()
        .duration(300)
        .style('opacity', params.show_label ? 1 : 0);
    });
  });
};
