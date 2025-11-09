/**
 * Exemple d'intégration des animations et polygones dans le graphique
 * À intégrer dans assets/js/graph-editor.js ou assets/js/graph-admin.js
 */

import * as d3 from "d3";
import { createForceSimulation } from "./utils/graphHelpers.js";
import {
  runAnimation,
  ANIMATION_TYPES,
  applyHoverAnimation,
  animateLinks,
} from "./utils/graphAnimations.js";
import {
  loadPolygonColors,
  createCategoryPolygons,
  drawPolygons,
  updatePolygons,
} from "./utils/polygonRenderer.js";

/**
 * Classe pour gérer le graphique avec animations et polygones
 */
class GraphManager {
  constructor(containerId, options = {}) {
    this.containerId = containerId;
    this.width = options.width || 1200;
    this.height = options.height || 800;

    // Récupérer les settings WordPress
    this.settings = {
      animationType: options.animationType || wp?.archi?.settings?.animation_type || "fadeIn",
      animationDuration: options.animationDuration || wp?.archi?.settings?.animation_duration || 800,
      hoverEffect: options.hoverEffect ?? wp?.archi?.settings?.hover_effect ?? true,
      hoverScale: options.hoverScale || wp?.archi?.settings?.hover_scale || 1.15,
      linkAnimation: options.linkAnimation ?? wp?.archi?.settings?.link_animation ?? true,
      organicMode: options.organicMode ?? wp?.archi?.settings?.organic_mode ?? true,
      clusterStrength: options.clusterStrength || wp?.archi?.settings?.cluster_strength || 0.1,
      showPolygons: options.showPolygons ?? true,
    };

    this.svg = null;
    this.simulation = null;
    this.nodes = [];
    this.links = [];
    this.categories = [];
    this.polygonColors = [];
  }

  /**
   * Initialiser le graphique
   */
  async init() {
    try {
      // Charger les données
      await this.loadData();

      // Créer le SVG
      this.createSVG();

      // Charger les couleurs de polygones
      if (this.settings.showPolygons) {
        this.polygonColors = await loadPolygonColors();
      }

      // Dessiner les éléments dans l'ordre
      this.drawPolygons();
      this.drawLinks();
      this.drawNodes();

      // Créer la simulation
      this.createSimulation();

      // Appliquer les animations
      this.applyAnimations();

    } catch (error) {
      console.error("❌ Erreur lors de l'initialisation du graphique:", error);
    }
  }

  /**
   * Charger les données depuis l'API
   */
  async loadData() {
    try {
      const response = await fetch("/wp-json/archi/v1/articles");
      if (!response.ok) {
        throw new Error("Erreur lors du chargement des articles");
      }

      const data = await response.json();
      this.nodes = data.articles || data.nodes || [];
      this.categories = data.categories || [];

      // ✅ NEW: Generate and integrate comment nodes
      if (typeof window.generateCommentsNodes === 'function') {
        const commentsNodes = window.generateCommentsNodes(this.nodes);
        
        if (commentsNodes.length > 0) {
          // Add comment nodes to the graph
          this.nodes = [...this.nodes, ...commentsNodes];
        }
      }

      // Calculer les liens entre nœuds (includes comment links now)
      this.calculateLinks();

    } catch (error) {
      console.error("Erreur lors du chargement des données:", error);
      throw error;
    }
  }

  /**
   * Créer le conteneur SVG
   */
  createSVG() {
    const container = d3.select(`#${this.containerId}`);

    this.svg = container
      .append("svg")
      .attr("width", this.width)
      .attr("height", this.height)
      .attr("viewBox", [0, 0, this.width, this.height])
      .style("max-width", "100%")
      .style("height", "auto");

    // Ajouter zoom et pan
    const zoom = d3.zoom()
      .scaleExtent([0.5, 3])
      .on("zoom", (event) => {
        this.svg.selectAll("g").attr("transform", event.transform);
      });

    this.svg.call(zoom);
  }

  /**
   * Dessiner les polygones de catégories
   */
  drawPolygons() {
    if (!this.settings.showPolygons || this.polygonColors.length === 0) {
      return;
    }


    const polygons = createCategoryPolygons(
      this.nodes,
      this.categories,
      this.polygonColors
    );

    drawPolygons(this.svg, polygons, {
      animated: true,
      animationDuration: this.settings.animationDuration,
    });
  }

  /**
   * Calculer les liens entre nœuds
   */
  calculateLinks() {
    this.links = [];

    // ✅ NEW: First, add comment node links (they don't use category matching)
    const commentNodes = this.nodes.filter(node => node.is_comment_node === true);
    commentNodes.forEach(commentNode => {
      const parentNode = this.nodes.find(n => n.id === commentNode.parent_article_id);
      if (parentNode) {
        this.links.push({
          source: commentNode,
          target: parentNode,
          strength: 1,
          link_type: 'comment',
          style: 'dashed'
        });
      }
    });

    // Then calculate category-based links for regular nodes
    for (let i = 0; i < this.nodes.length; i++) {
      for (let j = i + 1; j < this.nodes.length; j++) {
        const nodeA = this.nodes[i];
        const nodeB = this.nodes[j];

        // ✅ Skip comment nodes in category-based link calculation
        if (nodeA.is_comment_node || nodeB.is_comment_node) {
          continue;
        }

        // ✅ FIX: Check if either node has hide_links enabled
        if (nodeA.hide_links === '1' || nodeB.hide_links === '1') {
          continue; // Skip this link
        }

        // Vérifier catégories communes
        const sharedCategories = nodeA.categories?.filter((catA) =>
          nodeB.categories?.some((catB) => catA.id === catB.id)
        );

        if (sharedCategories && sharedCategories.length > 0) {
          this.links.push({
            source: nodeA,
            target: nodeB,
            strength: sharedCategories.length,
          });
        }
      }
    }
  }

  /**
   * Dessiner les liens
   */
  drawLinks() {
    const linkGroup = this.svg.append("g").attr("class", "links-layer");

    const links = linkGroup
      .selectAll(".graph-link")
      .data(this.links)
      .enter()
      .append("line")
      .attr("class", "graph-link")
      .attr("stroke", "#999")
      .attr("stroke-opacity", 0.6)
      .attr("stroke-width", (d) => Math.sqrt(d.strength));

    // Animer les liens si activé
    if (this.settings.linkAnimation) {
      animateLinks(links, {
        duration: this.settings.animationDuration * 1.2,
        delay: this.settings.animationDuration,
        staggerDelay: 20,
      });
    }

    this.linkElements = links;
  }

  /**
   * Dessiner les nœuds
   */
  drawNodes() {
    const nodeGroup = this.svg.append("g").attr("class", "nodes-layer");

    const nodes = nodeGroup
      .selectAll(".graph-node")
      .data(this.nodes)
      .enter()
      .append("g")
      .attr("class", "graph-node")
      .attr("cursor", "pointer");

    // Ajouter cercles
    nodes
      .append("circle")
      .attr("r", (d) => (d.node_size || 60) / 2)
      .attr("fill", (d) => d.node_color || "#3498db")
      .attr("stroke", "#fff")
      .attr("stroke-width", 2);

    // Ajouter labels
    nodes
      .append("text")
      .attr("text-anchor", "middle")
      .attr("dy", (d) => (d.node_size || 60) / 2 + 15)
      .attr("font-size", "12px")
      .attr("fill", "#333")
      .text((d) => d.title);

    this.nodeElements = nodes;
  }

  /**
   * Créer la simulation de force
   */
  createSimulation() {
    this.simulation = createForceSimulation(
      this.nodes,
      this.categories,
      {
        width: this.width,
        height: this.height,
        organicMode: this.settings.organicMode,
        clusterStrength: this.settings.clusterStrength,
      }
    );

    // Mettre à jour les positions à chaque tick
    this.simulation.on("tick", () => {
      this.updatePositions();
    });

    // Quand la simulation se stabilise, mettre à jour les polygones
    this.simulation.on("end", () => {
      if (this.settings.showPolygons) {
        updatePolygons(
          this.svg,
          this.nodes,
          this.categories,
          this.polygonColors
        );
      }
    });

    // Ajouter le drag
    this.addDragBehavior();
  }

  /**
   * Mettre à jour les positions des éléments
   */
  updatePositions() {
    // Mettre à jour les liens
    if (this.linkElements) {
      this.linkElements
        .attr("x1", (d) => d.source.x)
        .attr("y1", (d) => d.source.y)
        .attr("x2", (d) => d.target.x)
        .attr("y2", (d) => d.target.y);
    }

    // Mettre à jour les nœuds
    if (this.nodeElements) {
      this.nodeElements.attr("transform", (d) => `translate(${d.x}, ${d.y})`);
    }

    // Mettre à jour les polygones périodiquement
    if (
      this.settings.showPolygons &&
      this.simulation.alpha() > 0.1 &&
      Math.random() < 0.1
    ) {
      updatePolygons(
        this.svg,
        this.nodes,
        this.categories,
        this.polygonColors
      );
    }
  }

  /**
   * Ajouter le comportement de drag
   */
  addDragBehavior() {
    const drag = d3
      .drag()
      .on("start", (event, d) => {
        if (!event.active) this.simulation.alphaTarget(0.3).restart();
        d.fx = d.x;
        d.fy = d.y;
      })
      .on("drag", (event, d) => {
        d.fx = event.x;
        d.fy = event.y;
      })
      .on("end", (event, d) => {
        if (!event.active) this.simulation.alphaTarget(0);
        d.fx = null;
        d.fy = null;

        // Mettre à jour les polygones après le drag
        if (this.settings.showPolygons) {
          setTimeout(() => {
            updatePolygons(
              this.svg,
              this.nodes,
              this.categories,
              this.polygonColors
            );
          }, 500);
        }
      });

    this.nodeElements.call(drag);
  }

  /**
   * Appliquer les animations
   */
  applyAnimations() {

    // Appliquer animations personnalisées par nœud
    this.applyPerNodeAnimations();

    // Effets de survol personnalisés par nœud
    this.applyPerNodeHoverEffects();

    // Effets continus (pulse/glow)
    this.applyContinuousEffects();
  }

  /**
   * Appliquer les animations d'entrée personnalisées par nœud
   */
  applyPerNodeAnimations() {
    this.nodeElements.each((d, i, nodes) => {
      const node = d3.select(nodes[i]);
      const circle = node.select("circle");
      const text = node.select("text");

      // Récupérer les paramètres d'animation personnalisés
      const animation = d.animation || {};
      const duration = animation.duration || this.settings.animationDuration;
      const delay = animation.delay || i * 50; // Délai par défaut échelonné
      const easing = this.getEasingFunction(animation.easing || "ease-out");
      const enterFrom = animation.enterFrom || "center";

      // Définir l'état initial selon la direction
      const initialState = this.getInitialState(enterFrom, d);
      
      // Appliquer l'état initial
      node.attr("transform", initialState.transform)
          .style("opacity", 0);

      // Animation d'entrée
      node.transition()
          .delay(delay)
          .duration(duration)
          .ease(easing)
          .attr("transform", `translate(${d.x || this.width / 2}, ${d.y || this.height / 2})`)
          .style("opacity", 1);
    });
  }

  /**
   * Obtenir l'état initial selon la direction d'entrée
   */
  getInitialState(enterFrom, nodeData) {
    const x = nodeData.x || this.width / 2;
    const y = nodeData.y || this.height / 2;

    switch (enterFrom) {
      case "top":
        return { transform: `translate(${x}, ${-100})` };
      case "bottom":
        return { transform: `translate(${x}, ${this.height + 100})` };
      case "left":
        return { transform: `translate(${-100}, ${y})` };
      case "right":
        return { transform: `translate(${this.width + 100}, ${y})` };
      case "center":
      default:
        return { transform: `translate(${this.width / 2}, ${this.height / 2}) scale(0)` };
    }
  }

  /**
   * Obtenir la fonction d'easing D3
   */
  getEasingFunction(easingName) {
    const easingMap = {
      "linear": d3.easeLinear,
      "ease": d3.easeCubic,
      "ease-in": d3.easeCubicIn,
      "ease-out": d3.easeCubicOut,
      "ease-in-out": d3.easeCubicInOut,
      "bounce": d3.easeBounceOut,
      "elastic": d3.easeElasticOut,
      "back": d3.easeBackOut,
      "circle": d3.easeCircleOut,
      "sin": d3.easeSinOut,
      "exp": d3.easeExpOut,
    };

    return easingMap[easingName] || d3.easeCubicOut;
  }

  /**
   * Appliquer les effets de survol personnalisés par nœud
   */
  applyPerNodeHoverEffects() {
    this.nodeElements
      .on("mouseenter", function(event, d) {
        const node = d3.select(this);
        const circle = node.select("circle");
        const text = node.select("text");

        // Récupérer les paramètres de survol
        const hover = d.hover || {};
        const scale = hover.scale || 1.15;
        const pulse = hover.pulse || false;
        const glow = hover.glow || false;

        // Appliquer l'échelle
        circle.transition()
          .duration(200)
          .attr("r", (d.node_size || 60) / 2 * scale);

        text.transition()
          .duration(200)
          .style("font-weight", "bold")
          .style("font-size", "14px");

        // Ajouter l'effet glow si activé
        if (glow) {
          circle.attr("filter", "url(#node-glow)");
        }
      })
      .on("mouseleave", function(event, d) {
        const node = d3.select(this);
        const circle = node.select("circle");
        const text = node.select("text");

        // Retour à l'état normal
        circle.transition()
          .duration(200)
          .attr("r", (d.node_size || 60) / 2);

        text.transition()
          .duration(200)
          .style("font-weight", "normal")
          .style("font-size", "12px");

        // Retirer le glow
        circle.attr("filter", null);
      });
  }

  /**
   * Appliquer les effets continus (pulse/glow)
   */
  applyContinuousEffects() {
    // Créer le filtre SVG pour l'effet glow
    this.createGlowFilter();

    this.nodeElements.each((d, i, nodes) => {
      const node = d3.select(nodes[i]);
      const circle = node.select("circle");
      const hover = d.hover || {};

      // Effet pulse continu
      if (hover.pulse) {
        this.applyPulseEffect(circle, d);
      }

      // Effet glow permanent
      if (hover.glow) {
        circle.attr("filter", "url(#node-glow)");
      }
    });
  }

  /**
   * Créer le filtre SVG pour l'effet glow
   */
  createGlowFilter() {
    // Vérifier si le filtre existe déjà
    if (this.svg.select("#node-glow").size() > 0) {
      return;
    }

    const defs = this.svg.append("defs");
    const filter = defs.append("filter")
      .attr("id", "node-glow")
      .attr("x", "-50%")
      .attr("y", "-50%")
      .attr("width", "200%")
      .attr("height", "200%");

    filter.append("feGaussianBlur")
      .attr("in", "SourceGraphic")
      .attr("stdDeviation", "4")
      .attr("result", "blur");

    filter.append("feFlood")
      .attr("flood-color", "#fff")
      .attr("flood-opacity", "0.8");

    filter.append("feComposite")
      .attr("in2", "blur")
      .attr("operator", "in")
      .attr("result", "colorBlur");

    const feMerge = filter.append("feMerge");
    feMerge.append("feMergeNode").attr("in", "colorBlur");
    feMerge.append("feMergeNode").attr("in", "SourceGraphic");
  }

  /**
   * Appliquer l'effet de pulsation
   */
  applyPulseEffect(circle, nodeData) {
    const baseRadius = (nodeData.node_size || 60) / 2;
    const pulseRadius = baseRadius * 1.1;

    const pulse = () => {
      circle.transition()
        .duration(1000)
        .ease(d3.easeSinInOut)
        .attr("r", pulseRadius)
        .transition()
        .duration(1000)
        .ease(d3.easeSinInOut)
        .attr("r", baseRadius)
        .on("end", pulse);
    };

    pulse();
  }

  /**
   * Changer le type d'animation et rejouer
   */
  changeAnimation(animationType) {
    if (ANIMATION_TYPES[animationType.toUpperCase()]) {
      this.settings.animationType = animationType;
      runAnimation(animationType, this.nodeElements, {
        duration: this.settings.animationDuration,
        centerX: this.width / 2,
        centerY: this.height / 2,
      });
    }
  }

  /**
   * Toggle la visibilité des polygones
   */
  togglePolygons(visible) {
    this.settings.showPolygons = visible;

    if (visible) {
      this.drawPolygons();
    } else {
      this.svg.selectAll(".category-polygon").remove();
    }
  }

  /**
   * Nettoyer et détruire le graphique
   */
  destroy() {
    if (this.simulation) {
      this.simulation.stop();
    }

    if (this.svg) {
      this.svg.remove();
    }

  }
}

// Export de la classe
export default GraphManager;

// Exemple d'utilisation
/*
import GraphManager from './utils/GraphManager.js';

// Créer et initialiser le graphique
const graph = new GraphManager('graph-container', {
  width: 1200,
  height: 800,
  animationType: 'bounce',
  animationDuration: 800,
  hoverEffect: true,
  hoverScale: 1.15,
  showPolygons: true
});

await graph.init();

// Changer l'animation
document.getElementById('animation-select').addEventListener('change', (e) => {
  graph.changeAnimation(e.target.value);
});

// Toggle polygones
document.getElementById('toggle-polygons').addEventListener('change', (e) => {
  graph.togglePolygons(e.target.checked);
});
*/
