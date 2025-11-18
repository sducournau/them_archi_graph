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
    this.width = options.width || 8000; // 🔥 Espace raisonnable (réduit de 20000)
    this.height = options.height || 6000; // 🔥 Ratio 4:3 (réduit de 14000)

    // Get configuration from WordPress (simplified and centralized)
    const globalConfig = window.archiGraph?.config || {};
    
    // Merge with options, prioritizing: options > globalConfig > defaults
    this.config = {
      // Visual
      nodeColor: options.nodeColor || globalConfig.nodeColor || '#3498db',
      nodeSize: options.nodeSize || globalConfig.nodeSize || 80, // 🔥 FIX: Changed from 30 to 80 for consistency with PHP config
      nodeOpacity: options.nodeOpacity || globalConfig.nodeOpacity || 1.0,
      showLabels: options.showLabels ?? globalConfig.showLabels ?? true,
      showPolygons: options.showPolygons ?? globalConfig.showPolygons ?? true,
      
      // Animation
      animationEnabled: options.animationEnabled ?? globalConfig.animationEnabled ?? true,
      animationType: options.animationType || globalConfig.animationType || 'slide',
      animationDuration: options.animationDuration || globalConfig.animationDuration || 800,
      animationEasing: options.animationEasing || globalConfig.animationEasing || 'ease-out',
      staggerDelay: options.staggerDelay || globalConfig.staggerDelay || 50,
      
      // Hover
      hoverEnabled: options.hoverEnabled ?? globalConfig.hoverEnabled ?? true,
      hoverEffect: options.hoverEffect || globalConfig.hoverEffect || 'scale',
      hoverScale: options.hoverScale || globalConfig.hoverScale || 1.15,
      showHalo: options.showHalo ?? globalConfig.showHalo ?? true,
      elevateOnHover: options.elevateOnHover ?? globalConfig.elevateOnHover ?? true,
      
      // Inactive nodes
      inactiveEnabled: options.inactiveEnabled ?? globalConfig.inactiveEnabled ?? true,
      pulseInactive: options.pulseInactive ?? globalConfig.pulseInactive ?? true,
      pulseSpeed: options.pulseSpeed || globalConfig.pulseSpeed || 2000,
      inactiveOpacityMin: options.inactiveOpacityMin || globalConfig.inactiveOpacityMin || 0.3,
      inactiveOpacityMax: options.inactiveOpacityMax || globalConfig.inactiveOpacityMax || 0.4,
      inactiveGrayscale: options.inactiveGrayscale || globalConfig.inactiveGrayscale || 30,
      
      // Click interactions
      clickToggle: options.clickToggle ?? globalConfig.clickToggle ?? true,
      shockwaveEnabled: options.shockwaveEnabled ?? globalConfig.shockwaveEnabled ?? true,
      shockwaveDuration: options.shockwaveDuration || globalConfig.shockwaveDuration || 600,
      bounceOnClick: options.bounceOnClick ?? globalConfig.bounceOnClick ?? true,
      
      // Links
      linkAnimation: options.linkAnimation ?? globalConfig.linkAnimation ?? true,
      highlightLinksOnHover: options.highlightLinksOnHover ?? globalConfig.highlightLinksOnHover ?? true,
      linkStyle: options.linkStyle || globalConfig.linkStyle || 'curve',
      linkOpacity: options.linkOpacity || globalConfig.linkOpacity || 0.3,
      linkHoverOpacity: options.linkHoverOpacity || globalConfig.linkHoverOpacity || 1.0,
      
      // Physics
      chargeStrength: options.chargeStrength || globalConfig.chargeStrength || -300,
      linkDistance: options.linkDistance || globalConfig.linkDistance || 100,
      collisionRadius: options.collisionRadius || globalConfig.collisionRadius || 40,
      centerStrength: options.centerStrength || globalConfig.centerStrength || 0.05,
      clusterStrength: options.clusterStrength || globalConfig.clusterStrength || 0.1,
      
      // Performance
      lazyLoad: options.lazyLoad ?? globalConfig.lazyLoad ?? true,
      maxVisibleNodes: options.maxVisibleNodes || globalConfig.maxVisibleNodes || 100,
      respectReducedMotion: options.respectReducedMotion ?? globalConfig.respectReducedMotion ?? true,
    };

    // Check for reduced motion preference
    if (this.config.respectReducedMotion && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.config.animationEnabled = false;
      this.config.pulseInactive = false;
      this.config.linkAnimation = false;
    }

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
      if (this.config.showPolygons) {
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

      // ✅ Transform flat structure to nested structure for effects
      this.nodes = this.nodes.map(node => {
        // Create animation object from flat parameters
        const animation = {
          type: node.animation_type || "fadeIn",
          duration: node.animation_duration || this.config.animationDuration,
          delay: node.animation_delay || 0,
          easing: node.animation_easing || "ease-out",
          enterFrom: node.enter_from || "center"
        };

        // Create hover object from flat parameters
        const hover = {
          scale: node.hover_scale || 1.15,
          pulse: node.pulse_effect || false,
          glow: node.glow_effect || false
        };

        // Return node with nested structures
        return {
          ...node,
          animation,
          hover
        };
      });

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
    const containerNode = container.node();

    // 🔥 IMPORTANT: Le SVG doit s'adapter au conteneur, pas avoir une taille fixe
    this.svg = container
      .append("svg")
      .attr("viewBox", [0, 0, this.width, this.height]) // ViewBox sur l'espace virtuel
      .attr("preserveAspectRatio", "xMidYMid meet")
      .style("width", "100%")  // 🔥 Prend toute la largeur du conteneur
      .style("height", "100%") // 🔥 Prend toute la hauteur du conteneur
      .style("display", "block");

    // Ajouter zoom et pan avec plage étendue
    const zoom = d3.zoom()
      .scaleExtent([0.1, 4]) // Zoom de 10% à 400%
      .on("zoom", (event) => {
        this.svg.selectAll("g").attr("transform", event.transform);
      });

    this.svg.call(zoom);
    
    // 🔥 Zoom initial optimisé pour voir l'ensemble du graphique dispersé
    const containerWidth = containerNode.clientWidth;
    const containerHeight = containerNode.clientHeight;
    
    const initialScale = 0.5; // 🔥 FIX: Reduced from 0.7 to 0.5 to show more area and dispersion
    
    // Calculer la translation pour centrer le graphique scalé dans le viewport
    const scaledWidth = this.width * initialScale;
    const scaledHeight = this.height * initialScale;
    const translateX = (containerWidth - scaledWidth) / 2;
    const translateY = (containerHeight - scaledHeight) / 2;
    
    const initialTransform = d3.zoomIdentity
      .translate(translateX, translateY)
      .scale(initialScale);
    
    this.svg.call(zoom.transform, initialTransform);
  }

  /**
   * Dessiner les polygones de catégories
   */
  drawPolygons() {
    if (!this.config.showPolygons || this.polygonColors.length === 0) {
      return;
    }


    const polygons = createCategoryPolygons(
      this.nodes,
      this.categories,
      this.polygonColors
    );

    drawPolygons(this.svg, polygons, {
      animated: true,
      animationDuration: this.config.animationDuration,
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
    if (this.config.linkAnimation) {
      animateLinks(links, {
        duration: this.config.animationDuration * 1.2,
        delay: this.config.animationDuration,
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
      .attr("cursor", "pointer")
      .attr("data-node-id", (d) => d.id)
      .classed("node-inactive", (d) => d.inactive || false);

    // Ajouter cercle externe pour effet de halo
    nodes
      .append("circle")
      .attr("class", "node-halo")
      .attr("r", (d) => (d.node_size || 80) / 2 + 4)
      .attr("fill", "none")
      .attr("stroke", (d) => d.node_color || "#3498db")
      .attr("stroke-width", 0)
      .attr("stroke-opacity", 0);

    // Ajouter cercles principaux
    nodes
      .append("circle")
      .attr("class", "node-circle")
      .attr("r", (d) => (d.node_size || 80) / 2)
      .attr("fill", (d) => d.node_color || "#3498db")
      .attr("stroke", "#fff")
      .attr("stroke-width", 2)
      .style("opacity", (d) => d.inactive ? 0.4 : 1);

    // Ajouter cercle intérieur pour effet de brillance
    nodes
      .append("circle")
      .attr("class", "node-shine")
      .attr("r", (d) => (d.node_size || 80) / 4)
      .attr("cx", -5)
      .attr("cy", -5)
      .attr("fill", "white")
      .attr("opacity", 0.3);

    // Ajouter labels
    nodes
      .append("text")
      .attr("class", "node-label")
      .attr("text-anchor", "middle")
      .attr("dy", (d) => (d.node_size || 80) / 2 + 15)
      .attr("font-size", "12px")
      .attr("fill", "#333")
      .style("opacity", (d) => d.inactive ? 0.5 : 1)
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
        organicMode: this.config.organicMode,
        clusterStrength: this.config.clusterStrength,
      }
    );

    // Mettre à jour les positions à chaque tick
    this.simulation.on("tick", () => {
      this.updatePositions();
    });

    // Quand la simulation se stabilise, mettre à jour les polygones
    this.simulation.on("end", () => {
      if (this.config.showPolygons) {
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
      this.config.showPolygons &&
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
        if (this.config.showPolygons) {
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

    // Gestion des clics
    this.handleNodeClick();

    // Effets continus (pulse/glow)
    this.applyContinuousEffects();
  }

  /**
   * Appliquer les animations d'entrée personnalisées par nœud
   */
  applyPerNodeAnimations() {
    // Si les animations sont désactivées globalement, ne rien faire
    if (!this.config.animationEnabled) {
      this.nodeElements.style("opacity", 1);
      return;
    }

    this.nodeElements.each((d, i, nodes) => {
      const node = d3.select(nodes[i]);
      const circle = node.select(".node-circle");
      const text = node.select(".node-label");

      // Récupérer les paramètres d'animation (node ou global)
      const animation = d.animation || {};
      const animationType = animation.type || this.config.animationType || 'slide';
      const duration = animation.duration || this.config.animationDuration;
      const delay = animation.delay || (i * (this.config.staggerDelay || 50));
      
      // Choisir l'easing selon le type d'animation
      let easingName = animation.easing || this.config.animationEasing || "ease-out";
      if (!animation.easing && animationType === 'bounce') {
        easingName = 'bounce'; // Force bounce easing pour le type bounce
      }
      const easing = this.getEasingFunction(easingName);
      
      const enterFrom = animation.enterFrom || "center";

      // Définir l'état initial selon le type d'animation
      const initialState = this.getInitialStateByType(animationType, enterFrom, d);
      
      // Appliquer l'état initial
      node.attr("transform", initialState.transform)
          .style("opacity", 0);
      
      if (circle.size() > 0) {
        circle.attr("r", initialState.scale ? 0 : circle.attr("r"));
      }

      // Animation d'entrée
      const targetX = d.x || this.width / 2;
      const targetY = d.y || this.height / 2;
      
      node.transition()
          .delay(delay)
          .duration(duration)
          .ease(easing)
          .attr("transform", `translate(${targetX}, ${targetY})`)
          .style("opacity", 1);
      
      // Animation spécifique du cercle pour certains types
      if (circle.size() > 0 && initialState.scale) {
        const targetRadius = parseFloat(circle.attr("data-r") || 30);
        circle.transition()
            .delay(delay)
            .duration(duration)
            .ease(easing)
            .attr("r", targetRadius);
      }
    });
  }

  /**
   * Obtenir l'état initial selon la direction d'entrée
   */
  /**
   * Déterminer l'état initial d'un nœud selon le type d'animation et la direction
   * @param {string} animationType - Type d'animation : fade, slide, bounce, zoom
   * @param {string} enterFrom - Direction d'entrée : top, bottom, left, right, center
   * @param {object} nodeData - Données du nœud (avec d.x, d.y)
   * @returns {object} État initial {transform, scale}
   */
  getInitialStateByType(animationType, enterFrom, nodeData) {
    const targetX = nodeData.x || this.width / 2;
    const targetY = nodeData.y || this.height / 2;
    
    let startX = targetX;
    let startY = targetY;
    let scale = false; // Indique si on doit animer le scale
    
    switch (animationType) {
      case 'fade':
        // Fade in place : pas de mouvement, juste l'opacité
        return { transform: `translate(${targetX}, ${targetY})`, scale: false };
        
      case 'slide':
        // Glissement depuis un bord
        const offset = Math.max(this.width, this.height) * 0.3;
        switch (enterFrom) {
          case 'top':
            startY = -offset;
            break;
          case 'bottom':
            startY = this.height + offset;
            break;
          case 'left':
            startX = -offset;
            break;
          case 'right':
            startX = this.width + offset;
            break;
          default: // center ou random
            const directions = ['top', 'bottom', 'left', 'right'];
            const randomDir = directions[Math.floor(Math.random() * directions.length)];
            return this.getInitialStateByType('slide', randomDir, nodeData);
        }
        return { transform: `translate(${startX}, ${startY})`, scale: false };
        
      case 'bounce':
        // Bounce : commence au centre avec scale 0, puis bounce sur la position
        return { transform: `translate(${targetX}, ${targetY})`, scale: true };
        
      case 'zoom':
        // Zoom : apparait sur place avec scale 0
        return { transform: `translate(${targetX}, ${targetY})`, scale: true };
        
      default:
        // Fallback vers slide/center
        return { transform: `translate(${targetX}, ${targetY})`, scale: false };
    }
  }

  /**
   * [DEPRECATED] Ancienne méthode - utiliser getInitialStateByType
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
    // Si hover désactivé globalement, ne rien faire
    if (!this.config.hoverEnabled) {
      return;
    }

    this.nodeElements
      .on("mouseenter", (event, d) => {
        const node = d3.select(event.currentTarget);
        const circle = node.select(".node-circle");
        const halo = node.select(".node-halo");
        const text = node.select(".node-label");

        // Récupérer les paramètres de survol (node ou global)
        const hover = d.hover || {};
        const scale = hover.scale || this.config.hoverScale || 1.15;
        const hoverEffect = hover.effect || this.config.hoverEffect || 'scale';
        const glowEnabled = hover.glow !== undefined ? hover.glow : (hoverEffect === 'glow' || hoverEffect === 'multi');

        // Effet d'activation - retirer l'état inactif
        node.classed("node-inactive", false);

        // Animer le cercle principal
        circle.transition()
          .duration(200)
          .attr("r", (d.node_size || 80) / 2 * scale)
          .style("opacity", 1);

        // Effet de halo (configurable via Customizer)
        const settings = window.archiGraphSettings || {};
        const haloEnabled = settings.nodeHaloEnabled !== undefined ? settings.nodeHaloEnabled : true;
        const haloWidth = settings.nodeHaloWidth || 3;
        const haloOpacity = settings.nodeHaloOpacity || 0.5;
        
        if (haloEnabled) {
          halo.transition()
            .duration(300)
            .attr("stroke-width", haloWidth)
            .attr("stroke-opacity", haloOpacity);
        }

        // Animer le label
        text.transition()
          .duration(200)
          .style("font-weight", "bold")
          .style("font-size", scale > 1.2 ? "15px" : "14px")
          .style("opacity", 1);

        // Ajouter l'effet glow si activé
        if (glowEnabled) {
          circle.attr("filter", "url(#node-glow)");
        }

        // Effet de brillance renforcé
        node.select(".node-shine")
          .transition()
          .duration(200)
          .attr("opacity", scale > 1.2 ? 0.7 : 0.6);

        // Élever le node au-dessus des autres (z-index simulation)
        try {
          const nodeElement = node.node();
          const parent = nodeElement ? nodeElement.parentNode : null;
          if (parent && nodeElement) {
            parent.appendChild(nodeElement);
          }
        } catch (e) {
          console.warn('Could not reorder node for z-index:', e);
        }
      })
      .on("mouseleave", (event, d) => {
        const node = d3.select(event.currentTarget);
        const circle = node.select(".node-circle");
        const halo = node.select(".node-halo");
        const text = node.select(".node-label");

        // Restaurer état inactif si nécessaire (vérification plus robuste)
        const isInactive = d.inactive || d.inactiveByDefault || false;
        if (isInactive) {
          node.classed("node-inactive", true);
        }

        // Retour à l'état normal
        const defaultSize = (d.node_size || 80) / 2;
        circle.transition()
          .duration(200)
          .attr("r", defaultSize)
          .style("opacity", isInactive ? 0.4 : 1);

        // Retirer le halo
        halo.transition()
          .duration(300)
          .attr("stroke-width", 0)
          .attr("stroke-opacity", 0);

        // Retour label normal
        text.transition()
          .duration(200)
          .style("font-weight", "normal")
          .style("font-size", "12px")
          .style("opacity", isInactive ? 0.5 : 1);

        // Retirer le glow
        circle.attr("filter", null);

        // Brillance normale
        node.select(".node-shine")
          .transition()
          .duration(200)
          .attr("opacity", 0.3);
      });
  }

  /**
   * Gérer les clics sur les nodes
   */
  handleNodeClick() {
    this.nodeElements
      .on("click", function(event, d) {
        // Empêcher la propagation de l'événement
        event.stopPropagation();
        
        // Effet de clic - onde de choc
        const node = d3.select(this);
        const circle = node.select(".node-circle");
        
        // Taille du node
        const nodeSize = d.node_size || 80;
        const nodeRadius = nodeSize / 2;
        
        // Créer une onde de choc temporaire
        const shockwave = node.insert("circle", ":first-child")
          .attr("class", "node-shockwave")
          .attr("r", nodeRadius)
          .attr("fill", "none")
          .attr("stroke", d.node_color || "#3498db")
          .attr("stroke-width", 3)
          .attr("stroke-opacity", 0.8);

        // Animer l'onde de choc
        shockwave.transition()
          .duration(600)
          .ease(d3.easeQuadOut)
          .attr("r", nodeSize * 1.5)
          .attr("stroke-opacity", 0)
          .remove();

        // Petit effet de rebond sur le node
        circle.transition()
          .duration(100)
          .ease(d3.easeBackOut)
          .attr("r", nodeRadius * 1.1)
          .transition()
          .duration(100)
          .ease(d3.easeBackIn)
          .attr("r", nodeRadius);

        // Toggle état actif/inactif avec synchronisation
        const wasInactive = d.inactive || d.inactiveByDefault || false;
        d.inactive = !wasInactive;
        d.inactiveByDefault = d.inactive;
        
        node.classed("node-inactive", d.inactive);
        
        // Mettre à jour l'opacité
        circle.transition()
          .duration(300)
          .style("opacity", d.inactive ? 0.4 : 1);
        
        node.select(".node-label")
          .transition()
          .duration(300)
          .style("opacity", d.inactive ? 0.5 : 1);
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
      const circle = node.select(".node-circle");
      const halo = node.select(".node-halo");
      const hover = d.hover || {};

      // Effet pulse continu pour nodes avec pulse activé
      if (hover.pulse) {
        this.applyPulseEffect(circle, d);
      }

      // Effet glow permanent
      if (hover.glow) {
        circle.attr("filter", "url(#node-glow)");
      }

      // Pulsation subtile pour les nodes inactifs
      if (d.inactive) {
        this.applyInactivePulse(circle, halo, d);
      }
    });
  }

  /**
   * Appliquer une pulsation subtile pour les nodes inactifs
   */
  applyInactivePulse(circle, halo, nodeData) {
    const baseRadius = (nodeData.node_size || 80) / 2;
    const pulseRadius = baseRadius * 1.05;

    const pulse = () => {
      circle.transition()
        .duration(2000)
        .ease(d3.easeSinInOut)
        .style("opacity", 0.3)
        .transition()
        .duration(2000)
        .ease(d3.easeSinInOut)
        .style("opacity", 0.4)
        .on("end", pulse);
    };

    const haloPulse = () => {
      halo.transition()
        .duration(2000)
        .ease(d3.easeSinInOut)
        .attr("stroke-width", 2)
        .attr("stroke-opacity", 0.2)
        .transition()
        .duration(2000)
        .ease(d3.easeSinInOut)
        .attr("stroke-width", 0)
        .attr("stroke-opacity", 0)
        .on("end", haloPulse);
    };

    pulse();
    haloPulse();
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
    const baseRadius = (nodeData.node_size || 80) / 2;
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
      this.config.animationType = animationType;
      runAnimation(animationType, this.nodeElements, {
        duration: this.config.animationDuration,
        centerX: this.width / 2,
        centerY: this.height / 2,
      });
    }
  }

  /**
   * Toggle la visibilité des polygones
   */
  togglePolygons(visible) {
    this.config.showPolygons = visible;

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
  width: 8000,
  height: 5600,
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
