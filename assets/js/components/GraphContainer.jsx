import React, { useState, useEffect, useRef } from "react";
import * as d3 from "d3";
import Node from "./Node";
import CategoryCluster from "./CategoryCluster";
import { fetchGraphData, saveNodePositions } from "../utils/dataFetcher";
import {
  createForceSimulation,
  updateNodePositions,
  calculateNodeLinks,
} from "../utils/graphHelpers";
import {
  preprocessArticleImages,
  activateNodeGif,
  deactivateNodeGif,
} from "../utils/gifController";
import {
  convexHull,
  expandHull,
  smoothHull,
  hullToPath,
  createCircularHull,
  calculateCentroid,
} from "../utils/geometryUtils";
import {
  showNodeTooltip as showNodeTooltipUtil,
  hideNodeTooltip as hideNodeTooltipUtil,
  showSideTitlePanel as showSideTitlePanelUtil,
  hideSideTitlePanel as hideSideTitlePanelUtil,
  getNodeColor,
} from "../utils/nodeInteractions";
import {
  applyRepulsionForces as applyRepulsionForcesUtil,
  initializeNodePositions,
} from "../utils/physicsUtils";
import {
  applyContinuousEffects,
  applyHoverScale,
  createVisualEffectFilters,
} from "../utils/nodeVisualEffects";
import {
  showInfoPanel,
  hideInfoPanel,
} from "../utils/sidebarUtils";
import {
  applyEntranceAnimation,
} from "../utils/entranceAnimations";
import {
  applyLinkAnimation,
  updateLinkAnimations,
} from "../utils/linkAnimations";
import CategoryLegend from "./CategoryLegend";
// Arrow satellites désactivés
// import {
//   createArrowSatellites,
//   animateArrowSatellites,
//   updateArrowSatellites,
// } from "../utils/arrowSatellites";

/**
 * Composant principal du graphique interactif
 */
const GraphContainer = ({ config, onGraphReady, onError }) => {
  // États React
  const [articles, setArticles] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedCategories, setSelectedCategories] = useState(new Set());
  const [hoveredNode, setHoveredNode] = useState(null);
  const [selectedNode, setSelectedNode] = useState(null);

  // Références
  const svgRef = useRef(null);
  const simulationRef = useRef(null);
  const transformRef = useRef(d3.zoomIdentity);
  const velocitiesRef = useRef({});
  const animationFrameRef = useRef(null);
  const clickTimerRef = useRef(null); // Timer pour détecter double-clic
  const customizerSettingsRef = useRef({}); // 🔥 STOCKER LES SETTINGS DU CUSTOMIZER
  
  // ⚡ PERFORMANCE: Contrôles pour applyRepulsionForces
  const repulsionStartTimeRef = useRef(null);
  const repulsionIterationsRef = useRef(0);
  const MAX_REPULSION_DURATION = 3000; // 3 secondes max
  const MAX_REPULSION_ITERATIONS = 180; // ~3s à 60fps
  
  // ⚡ PERFORMANCE: Debounce pour updateGraph
  const updateGraphTimeoutRef = useRef(null);

  // ✅ Paramètres de physique MODÉRÉS pour nœuds visibles DANS le viewBox 1200x800
  const REPULSION_FORCE = 1500;  // ✅ Force raisonnable pour séparer sans sortir du viewBox
  const MIN_DISTANCE = 120;      // ✅ Distance adaptée à la taille 120px des nœuds
  const DAMPING = 0.8;

  // Configuration
  const width = config.width || 1200;
  const height = config.height || 800;
  const options = config.options || {};

  /**
   * Initialisation du composant
   */
  useEffect(() => {
    initializeGraph();

    // Gestionnaire de clic global pour désélectionner
    const handleDocumentClick = (e) => {
      if (
        !e.target.closest(".graph-node") &&
        !e.target.closest(".control-btn") &&
        !e.target.closest(".graph-node-tooltip")
      ) {
        resetSelectedNode();
      }
    };
    document.addEventListener("click", handleDocumentClick);

    // Cleanup
    return () => {
      if (simulationRef.current) {
        simulationRef.current.stop();
      }
      if (animationFrameRef.current) {
        cancelAnimationFrame(animationFrameRef.current);
      }
      document.removeEventListener("click", handleDocumentClick);
    };
  }, []);

  /**
   * Masquer le loader quand le composant est monté
   */
  useEffect(() => {
    if (!loading) {
      // Masquer tous les loaders possibles
      const loaders = document.querySelectorAll(
        ".graph-loading-inline, #graph-loading"
      );
      loaders.forEach((loader) => {
        loader.classList.add("hidden");
        loader.style.display = "none";
      });

      // Marquer le conteneur comme chargé
      const container = document.getElementById("graph-container");
      if (container) {
        container.classList.add("loaded");
      }

      // Force le SVG à être visible
      const svg = document.querySelector(".graph-svg");
      if (svg) {
        svg.style.display = "block";
        svg.style.visibility = "visible";
        svg.style.opacity = "1";
      }
    }
  }, [loading]);

  /**
   * Initialiser le SVG une fois qu'il est rendu
   */
  useEffect(() => {
    if (svgRef.current && !loading) {
      setupSVG();
    }
  }, [loading]);

  /**
   * Mise à jour quand les articles changent
   */
  useEffect(() => {
    // ⚡ PERFORMANCE: Debounce updateGraph pour éviter appels multiples
    if (articles.length > 0 && svgRef.current) {
      if (updateGraphTimeoutRef.current) {
        clearTimeout(updateGraphTimeoutRef.current);
      }
      
      updateGraphTimeoutRef.current = setTimeout(() => {
        updateGraph();
        updateGraphTimeoutRef.current = null;
      }, 150); // 150ms de debounce
    }
    
    // Cleanup
    return () => {
      if (updateGraphTimeoutRef.current) {
        clearTimeout(updateGraphTimeoutRef.current);
      }
    };
  }, [articles, selectedCategories]);

  /**
   * Écouter les changements de paramètres du Customizer
   */
  useEffect(() => {
    const handleSettingsUpdate = (event) => {
      const newSettings = event.detail;
      console.log('Customizer settings updated:', newSettings);

      // Mettre à jour window.archiGraphSettings
      if (typeof window.archiGraphSettings === 'object') {
        Object.assign(window.archiGraphSettings, newSettings);
      }

      // Mettre à jour customizerSettingsRef
      customizerSettingsRef.current = window.archiGraphSettings || {};

      // Redessiner le graphe avec les nouveaux paramètres
      if (articles.length > 0 && svgRef.current) {
        updateGraph();
      }
    };

    // Écouter l'événement personnalisé
    window.addEventListener('graphSettingsUpdated', handleSettingsUpdate);
    
    // 🔥 Exposer la fonction updateGraphSettings globalement pour le Customizer
    // Cette fonction doit persister même si le composant se recharge
    if (!window.updateGraphSettings) {
      console.log('🎨 Exposing window.updateGraphSettings for Customizer');
      
      window.updateGraphSettings = (newSettings) => {
        console.log('🎨 Graph settings update requested:', newSettings);
        
        // Mettre à jour window.archiGraphSettings
        if (typeof window.archiGraphSettings === 'object') {
          Object.assign(window.archiGraphSettings, newSettings);
        } else {
          window.archiGraphSettings = newSettings;
        }
        
        // Déclencher l'événement pour que le composant se mette à jour
        const event = new CustomEvent('graphSettingsUpdated', { 
          detail: newSettings 
        });
        window.dispatchEvent(event);
      };
    }

    // Cleanup
    return () => {
      window.removeEventListener('graphSettingsUpdated', handleSettingsUpdate);
      // NE PAS supprimer window.updateGraphSettings car customizer-preview.js en a besoin
    };
  }, [articles]); // Dépend de articles pour pouvoir redessiner

  /**
   * Initialiser le graphique
   */
  const initializeGraph = async () => {
    try {
      setLoading(true);

      // Get API endpoint from config or window.graphConfig
      const apiEndpoint = config.apiEndpoint || window.graphConfig?.apiEndpoint;
      
      if (!apiEndpoint) {
        throw new Error("API endpoint not configured");
      }


      // Récupérer les données
      const [articlesData, categoriesData] = await Promise.all([
        fetchGraphData(apiEndpoint),
        fetch(`${apiEndpoint.replace("/articles", "/categories")}`).then(
          (res) => res.json()
        ),
      ]);

      // Preprocess images to extract static frames for GIFs
      const rawArticles = articlesData.articles || [];
      const processedArticles = await preprocessArticleImages(rawArticles);
      
      // Normalize related_articles to ensure it's always an array
      processedArticles.forEach(article => {
        if (!Array.isArray(article.related_articles)) {
          article.related_articles = [];
        }
      });

      setArticles(processedArticles);
      setCategories(categoriesData || []);

      // Ne pas appeler setupSVG ici - le SVG n'existe pas encore!
      // Il sera initialisé via useEffect après le rendu

      setLoading(false);
      onGraphReady && onGraphReady();
    } catch (error) {
      console.error("Erreur lors de l'initialisation:", error);
      setLoading(false);
      onError && onError(error);
    }
  };

  /**
   * Configuration du SVG et des interactions
   */
  const setupSVG = () => {
    const svg = d3.select(svgRef.current);

    // Nettoyer le contenu existant
    svg.selectAll("*").remove();

    // S'assurer que le SVG est visible
    svg
      .style("display", "block")
      .style("visibility", "visible")
      .style("opacity", "1");

    // Container principal avec zoom
    const container = svg
      .attr("width", width)
      .attr("height", height)
      .attr("viewBox", `0 0 ${width} ${height}`)
      .attr("preserveAspectRatio", "xMidYMid meet")
      .style("background", "#ffffff")
      .call(d3.zoom().scaleExtent([0.1, 4]).on("zoom", handleZoom));

    // Groupe principal pour les transformations
    const g = container.append("g").attr("class", "graph-group");

      // Rectangle invisible en arrière-plan pour capturer les clics
    g.append("rect")
      .attr("class", "graph-background")
      .attr("width", width)
      .attr("height", height)
      .attr("fill", "transparent")
      .style("cursor", "default")
      .on("click", (event) => {
        event.stopPropagation();
        resetSelectedNode();
        hideNodeTooltip();
      });    // Groupes pour l'ordre des éléments (les îles en arrière-plan)
    g.append("g").attr("class", "islands");
    g.append("g").attr("class", "links");
    g.append("g").attr("class", "clusters");
    g.append("g").attr("class", "nodes");

    // Dégradés pour les nœuds
    const defs = svg.append("defs");

    // Gradient pour le glow des nœuds
    const nodeGlow = defs
      .append("radialGradient")
      .attr("id", "nodeGlow")
      .attr("cx", "50%")
      .attr("cy", "50%")
      .attr("r", "50%");

    nodeGlow
      .append("stop")
      .attr("offset", "0%")
      .attr("style", "stop-color: rgba(255, 255, 255, 0.3); stop-opacity: 1");

    nodeGlow
      .append("stop")
      .attr("offset", "100%")
      .attr("style", "stop-color: rgba(255, 255, 255, 0); stop-opacity: 1");

    // Filtre d'ombre
    const filter = defs
      .append("filter")
      .attr("id", "drop-shadow")
      .attr("x", "-50%")
      .attr("y", "-50%")
      .attr("width", "200%")
      .attr("height", "200%");

    filter
      .append("feDropShadow")
      .attr("dx", 2)
      .attr("dy", 2)
      .attr("stdDeviation", 3)
      .attr("flood-color", "rgba(0,0,0,0.3)");

    // Filtre de glow
    const glowFilter = defs
      .append("filter")
      .attr("id", "glow")
      .attr("x", "-50%")
      .attr("y", "-50%")
      .attr("width", "200%")
      .attr("height", "200%");

    glowFilter
      .append("feGaussianBlur")
      .attr("stdDeviation", "3")
      .attr("result", "coloredBlur");

    const glowMerge = glowFilter.append("feMerge");
    glowMerge.append("feMergeNode").attr("in", "coloredBlur");
    glowMerge.append("feMergeNode").attr("in", "SourceGraphic");

    // Filtre de lueur pour les îles architecturales
    const islandGlowFilter = defs
      .append("filter")
      .attr("id", "island-glow")
      .attr("x", "-50%")
      .attr("y", "-50%")
      .attr("width", "200%")
      .attr("height", "200%");

    islandGlowFilter
      .append("feGaussianBlur")
      .attr("stdDeviation", "5")
      .attr("result", "islandBlur");

    const islandMerge = islandGlowFilter.append("feMerge");
    islandMerge.append("feMergeNode").attr("in", "islandBlur");
    islandMerge.append("feMergeNode").attr("in", "islandBlur");
    islandMerge.append("feMergeNode").attr("in", "SourceGraphic");

    // Créer les particules flottantes
    createFloatingParticles();
  };

  /**
   * Créer les particules flottantes en arrière-plan
   */
  const createFloatingParticles = () => {
    const container =
      document.querySelector(".graph-wrapper") ||
      document.querySelector(".graph-container");
    if (!container) return;

    // Vérifier si le conteneur existe déjà
    let particlesContainer = container.querySelector(".floating-particles");
    if (!particlesContainer) {
      particlesContainer = document.createElement("div");
      particlesContainer.className = "floating-particles";
      container.insertBefore(particlesContainer, container.firstChild);
    }

    // Nettoyer les anciennes particules
    particlesContainer.innerHTML = "";

    // Créer 20 particules
    for (let i = 0; i < 20; i++) {
      const particle = document.createElement("div");
      particle.className = "particle";

      const size = Math.random() * 4 + 2;
      particle.style.width = `${size}px`;
      particle.style.height = `${size}px`;
      particle.style.left = `${Math.random() * 100}%`;
      particle.style.animationDelay = `${Math.random() * 15}s`;
      particle.style.animationDuration = `${15 + Math.random() * 10}s`;

      particlesContainer.appendChild(particle);
    }
  };

  /**
   * Gestion du zoom
   */
  const handleZoom = (event) => {
    const { transform } = event;
    transformRef.current = transform;

    d3.select(svgRef.current)
      .select(".graph-group")
      .attr("transform", transform);
  };

  /**
   * Mise à jour du graphique avec les données
   */
  const updateGraph = () => {
    if (!articles.length) {
      console.warn("No articles to display in graph");
      return;
    }

    const svg = d3.select(svgRef.current);
    const g = svg.select(".graph-group");

    if (g.empty()) {
      console.error("Graph group is empty! SVG setup may have failed.");
      return;
    }

    // 🔥 RÉCUPÉRER LES PARAMÈTRES DU CUSTOMIZER
    const customizerSettings = window.archiGraphSettings || {};
    console.log('🎨 Using Customizer settings:', customizerSettings);
    
    // 🔥 STOCKER DANS LA REF POUR L'ACCÈS GLOBAL
    customizerSettingsRef.current = customizerSettings;

    // Filtrer les articles selon les catégories sélectionnées
    let filteredArticles = articles;
    if (selectedCategories.size > 0) {
      filteredArticles = articles.filter((article) =>
        article.categories.some((cat) => selectedCategories.has(cat.id))
      );
    }


    // Initialiser les positions si elles n'existent pas
    filteredArticles.forEach((article) => {
      if (article.x === undefined || article.x === null) {
        article.x = width / 2 + (Math.random() - 0.5) * 100;
      }
      if (article.y === undefined || article.y === null) {
        article.y = height / 2 + (Math.random() - 0.5) * 100;
      }
    });


    // Calculer les liens de proximité entre les articles
    const links = calculateNodeLinks(filteredArticles, {
      minProximityScore: 20,
      maxLinksPerNode: 8,
      useProximityScore: true,
    });


    // Vérifier si l'affichage des liens est activé
    const shouldShowLinks = options.showLinks !== false;

    // 🔥 UTILISER LA FORCE DE REGROUPEMENT DU CUSTOMIZER
    const clusterStrength = customizerSettings.clusterStrength !== undefined 
      ? customizerSettings.clusterStrength 
      : 0.1;

    // 🔥 TAILLE PAR DÉFAUT DOUBLÉE pour visibilité dans viewBox 1200x800
    const defaultNodeSize = customizerSettings.defaultNodeSize || 120; // 🔥 Doublé de 60 à 120px
    
    // 🔥 UTILISER LES FORCES DE SIMULATION DU CUSTOMIZER
    const chargeStrength = customizerSettings.chargeStrength || -300;
    const chargeDistance = customizerSettings.chargeDistance || 200;
    const collisionPadding = customizerSettings.collisionPadding || 10;
    const alphaValue = customizerSettings.simulationAlpha || 1;
    const alphaDecayValue = customizerSettings.simulationAlphaDecay || 0.02;
    const velocityDecayValue = customizerSettings.simulationVelocityDecay || 0.3;

    console.log('🎯 Cluster strength:', clusterStrength, 'Node size:', defaultNodeSize);

    // Créer la simulation de force
    const simulation = d3
      .forceSimulation(filteredArticles)
      .force("charge", d3.forceManyBody().strength(chargeStrength).distanceMax(chargeDistance))
      .force("center", d3.forceCenter(width / 2, height / 2))
      .force(
        "collision",
        d3
          .forceCollide()
          .radius((d) => (d.node_size || defaultNodeSize) / 2 + collisionPadding)
          .strength(clusterStrength)
      )
      .alpha(alphaValue)
      .alphaDecay(alphaDecayValue)
      .velocityDecay(velocityDecayValue);

    // Ajouter la force des liens seulement si l'option est activée
    if (shouldShowLinks) {
      const linkDistance = customizerSettings.linkDistance || 150;
      const linkDistanceVariation = customizerSettings.linkDistanceVariation || 50;
      const linkStrengthDivisor = customizerSettings.linkStrengthDivisor || 200;
      
      simulation.force(
        "link",
        d3
          .forceLink(links)
          .id((d) => d.id)
          .distance((d) => {
            // Distance inversement proportionnelle au score
            const scoreFactor = d.proximity?.normalizedScore || 50;
            return linkDistance - (scoreFactor / 100) * linkDistanceVariation;
          })
          .strength((d) => {
            // Force proportionnelle au score
            return (d.proximity?.normalizedScore || 50) / linkStrengthDivisor;
          })
      );
    }

    simulationRef.current = simulation;

    // Créer/Mettre à jour les liens seulement si l'option est activée
    if (shouldShowLinks) {
      updateLinks(g, links, customizerSettings);
    } else {
      // Supprimer tous les liens existants
      g.select(".links").selectAll(".graph-link").remove();
    }

    // Créer/Mettre à jour les nœuds
    updateNodes(g, filteredArticles, simulation, customizerSettings);

    // Les îles architecturales remplacent les clusters de catégories
    // updateClusters(g, categories, filteredArticles);

    // Îles architecturales par catégories activées
    updateArchitecturalIslands(g, filteredArticles, customizerSettings);


    // Démarrer la simulation
    let tickCount = 0;
    simulation.on("tick", () => {
      updateNodePositions(g, filteredArticles);
      // Mettre à jour les liens seulement si l'option est activée
      if (shouldShowLinks) {
        updateLinkPositions(g, links);
      }
      
      // ⚡ PERFORMANCE FIX: Ne mettre à jour les îles architecturales que périodiquement
      // au lieu de chaque tick (60 fois par seconde !)
      // Mettre à jour uniquement tous les 30 ticks (~0.5 secondes) ou quand la simulation ralentit
      if (tickCount % 30 === 0 || simulation.alpha() < 0.1) {
        updateArchitecturalIslands(g, filteredArticles, customizerSettings);
      }
      
      tickCount++;
    });

    // IMPORTANT: La simulation est déjà configurée avec alpha(1) lors de sa création
    // On doit juste s'assurer qu'elle démarre
    try {
      if (typeof simulation.restart === "function") {
        simulation.restart();
      }
    } catch (error) {
      console.error("Error starting simulation:", error);
    }


    // Exposer l'instance globalement avec la méthode resize
    window.graphInstance = {
      resetZoom: () => resetZoom(),
      toggleCategoryFilter: (categoryId) => {
        setSelectedCategories(prev => {
          const newSet = new Set(prev);
          if (newSet.has(categoryId)) {
            newSet.delete(categoryId);
          } else {
            newSet.add(categoryId);
          }
          return newSet;
        });
      },
      clearCategoryFilters: () => setSelectedCategories(new Set()),
      resize: resize,
      simulation: simulation,
      data: filteredArticles,
      links: links,
    };
  };

  /**
   * Mise à jour des liens entre les nœuds
   */
  const updateLinks = (container, links, settings = {}) => {
    const linksGroup = container.select(".links");

    // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER
    const linkColor = settings.linkColor || settings.defaultNodeColor || '#999999';
    const linkWidth = settings.linkWidth || 1.5;
    const linkOpacity = settings.linkOpacity || 0.6;
    const linkStyle = settings.linkStyle || 'solid';
    const showArrows = settings.showArrows !== undefined ? settings.showArrows : false;
    const guestbookLinkColor = settings.guestbookLinkColor || '#2ecc71';
    const guestbookLinkWidth = settings.guestbookLinkWidth || 3;
    const guestbookLinkOpacity = settings.guestbookLinkOpacity || 0.8;
    const dashedLinePattern = settings.dashedLinePattern || "5,5";
    const dottedLinePattern = settings.dottedLinePattern || "2,2";
    const guestbookDashPattern = settings.guestbookDashPattern || "10,5";

    console.log('🔗 Link settings:', { linkColor, linkWidth, linkOpacity, linkStyle, showArrows });

    const linkElements = linksGroup
      .selectAll(".graph-link")
      .data(links, (d) => d.id);

    // Supprimer les anciens liens
    linkElements.exit().remove();

    // Créer les nouveaux liens
    const linkEnter = linkElements
      .enter()
      .append("line")
      .attr("class", "graph-link")
      .attr("data-link-id", (d) => d.id)
      .attr("data-link-type", (d) => d.type || "proximity")
      .style("stroke", (d) => {
        // ✅ Lien de livre d'or : couleur distinctive
        if (d.type === 'guestbook') {
          return guestbookLinkColor;
        }
        
        // 🔥 UTILISER LA COULEUR DU CUSTOMIZER
        return linkColor;
      })
      .style("stroke-width", (d) => {
        // ✅ Lien de livre d'or : plus épais
        if (d.type === 'guestbook') {
          return guestbookLinkWidth;
        }
        
        // 🔥 UTILISER L'ÉPAISSEUR DU CUSTOMIZER
        return linkWidth;
      })
      .style("stroke-opacity", (d) => {
        // ✅ Lien de livre d'or : bien visible
        if (d.type === 'guestbook') {
          return guestbookLinkOpacity;
        }
        
        // 🔥 UTILISER L'OPACITÉ DU CUSTOMIZER
        return linkOpacity;
      })
      .style("stroke-dasharray", (d) => {
        // ✅ Lien de livre d'or : tirets longs pour distinction
        if (d.type === 'guestbook') {
          return guestbookDashPattern;
        }
        
        // 🔥 UTILISER LE STYLE DU CUSTOMIZER
        if (linkStyle === 'dashed') {
          return dashedLinePattern;
        } else if (linkStyle === 'dotted') {
          return dottedLinePattern;
        }
        return "none"; // solid
      });

    // Ajouter un titre pour afficher les détails au survol
    linkEnter.append("title").text((d) => {
      // ✅ Tooltip spécial pour les liens du livre d'or
      if (d.type === 'guestbook') {
        return `Lien du Livre d'Or\n\nArticle lié manuellement depuis une entrée du livre d'or.`;
      }
      
      const prox = d.proximity;
      if (!prox) return "Lien";

      let details = `Score de proximité: ${prox.score}\nForce: ${prox.strength}\n\n`;

      if (prox.details?.shared_categories?.length > 0) {
        details += `Catégories communes: ${prox.details.shared_categories
          .map((c) => c.name)
          .join(", ")}\n`;
      }

      if (prox.details?.shared_tags?.length > 0) {
        details += `Tags communs: ${prox.details.shared_tags
          .map((t) => t.name)
          .join(", ")}\n`;
      }

      if (prox.details?.samePrimaryCategory) {
        details += `Même catégorie principale\n`;
      }

      return details;
    });

    // Fusionner enter + update
    const linkUpdate = linkEnter.merge(linkElements);

    // 🔥 Apply link animations based on settings
    applyLinkAnimation(linkUpdate, settings);

    return linkUpdate;
  };

  /**
   * Mise à jour des positions des liens
   */
  const updateLinkPositions = (container, links) => {
    const linkElements = container.selectAll(".graph-link");

    linkElements
      .attr("x1", (d) => d.source.x)
      .attr("y1", (d) => d.source.y)
      .attr("x2", (d) => d.target.x)
      .attr("y2", (d) => d.target.y);
  };

  /**
   * Mise à jour des nœuds
   */
  const updateNodes = (container, data, simulation, settings = {}) => {
    const nodesGroup = container.select(".nodes");

    // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER (taille doublée pour visibilité)
    const defaultNodeColor = settings.defaultNodeColor || '#3498db';
    const defaultNodeSize = settings.defaultNodeSize || 120; // 🔥 Doublé de 60 à 120px
    const priorityBadgeSize = settings.priorityBadgeSize || 8;
    const priorityBadgeOffset = settings.priorityBadgeOffset || 5;
    const priorityBadgeStrokeColor = settings.priorityBadgeStrokeColor || '#ffffff';
    const priorityBadgeStrokeWidth = settings.priorityBadgeStrokeWidth || 2;

    console.log('⭕ Node settings:', { defaultNodeColor, defaultNodeSize });

    const nodeElements = nodesGroup
      .selectAll(".graph-node")
      .data(data, (d) => d.id);


    // Réinitialiser l'opacité de tous les labels existants à 0
    nodeElements.selectAll(".node-title-label").style("opacity", 0);

    // Supprimer les anciens nœuds
    nodeElements.exit().remove();

    // Créer les nouveaux nœuds
    const nodeEnter = nodeElements
      .enter()
      .append("g")
      .attr("class", "graph-node")
      .attr("data-node-id", (d) => d.id)
      .attr("data-link", (d) => d.permalink || "")
      .attr("data-animation-level", (d) => d.animation_level || "normal")
      .style("cursor", "pointer")
      .call(
        d3
          .drag()
          .on("start", (event, d) => handleDragStart(event, d, simulation))
          .on("drag", (event, d) => handleDrag(event, d))
          .on("end", (event, d) => handleDragEnd(event, d, simulation))
      );


    // 🔥 NOUVEAU: Ajouter symbole de fond selon le paramètre nodeSymbolType
    const symbolType = settings.nodeSymbolType || 'none';
    console.log('🎯 Symbol Type:', symbolType, 'Settings:', settings);
    
    if (symbolType !== 'none') {
      const symbolGroup = nodeEnter.append("g").attr("class", "node-symbol");
      
      const applySymbol = (selection, d) => {
        const size = d.node_size || defaultNodeSize;
        const radius = size / 2;
        const color = d.node_color || defaultNodeColor;
        
        switch(symbolType) {
          case 'circle':
            selection.append("circle")
              .attr("class", "node-shape")
              .attr("r", radius * 0.95)
              .attr("fill", color)
              .attr("fill-opacity", 0.2)
              .attr("stroke", color)
              .attr("stroke-width", 2)
              .attr("stroke-opacity", 0.5);
            break;
            
          case 'square':
            selection.append("rect")
              .attr("class", "node-shape")
              .attr("width", size * 0.95)
              .attr("height", size * 0.95)
              .attr("x", -(size * 0.95) / 2)
              .attr("y", -(size * 0.95) / 2)
              .attr("rx", size * 0.1)
              .attr("fill", color)
              .attr("fill-opacity", 0.2)
              .attr("stroke", color)
              .attr("stroke-width", 2)
              .attr("stroke-opacity", 0.5);
            break;
            
          case 'diamond':
            const diamondPoints = [
              [0, -radius * 0.95],
              [radius * 0.95, 0],
              [0, radius * 0.95],
              [-radius * 0.95, 0]
            ].map(p => p.join(',')).join(' ');
            
            selection.append("polygon")
              .attr("class", "node-shape")
              .attr("points", diamondPoints)
              .attr("fill", color)
              .attr("fill-opacity", 0.2)
              .attr("stroke", color)
              .attr("stroke-width", 2)
              .attr("stroke-opacity", 0.5);
            break;
            
          case 'triangle':
            const triangleHeight = radius * 0.95 * Math.sqrt(3);
            const trianglePoints = [
              [0, -triangleHeight * 0.6],
              [radius * 0.95, triangleHeight * 0.4],
              [-radius * 0.95, triangleHeight * 0.4]
            ].map(p => p.join(',')).join(' ');
            
            selection.append("polygon")
              .attr("class", "node-shape")
              .attr("points", trianglePoints)
              .attr("fill", color)
              .attr("fill-opacity", 0.2)
              .attr("stroke", color)
              .attr("stroke-width", 2)
              .attr("stroke-opacity", 0.5);
            break;
        }
      };
      
      symbolGroup.each(function(d) {
        applySymbol(d3.select(this), d);
      });
    }

    // Image du nœud PNG avec fond transparent (pas de bulle, pas de clip-path)
    // L'image apparaît en entier sans être coupée ni déformée
    nodeEnter
      .append("image")
      .attr("class", "node-image")
      .attr("width", (d) => {
        const size = d.node_size || defaultNodeSize;
        return size;
      })
      .attr("height", (d) => d.node_size || defaultNodeSize)
      .attr("x", (d) => -(d.node_size || defaultNodeSize) / 2)
      .attr("y", (d) => -(d.node_size || defaultNodeSize) / 2)
      .attr("href", (d) => d.thumbnail || "")
      .attr("preserveAspectRatio", "xMidYMid meet")
      .style("filter", "url(#drop-shadow)")
      .style("transition", "all 0.3s ease")
      .style("overflow", "visible");

    // Badge de priorité (petit cercle en haut à droite de l'image)
    nodeEnter
      .filter(
        (d) => d.priority_level === "featured" || d.priority_level === "high"
      )
      .append("circle")
      .attr("class", "priority-badge")
      .attr("r", priorityBadgeSize)
      .attr("cx", (d) => (d.node_size || defaultNodeSize) / 2 - priorityBadgeOffset)
      .attr("cy", (d) => -(d.node_size || defaultNodeSize) / 2 + priorityBadgeOffset)
      .style("fill", (d) =>
        d.priority_level === "featured" ? (settings.priorityFeaturedColor || defaultNodeColor) : (settings.priorityHighColor || defaultNodeColor)
      )
      .style("stroke", priorityBadgeStrokeColor)
      .style("stroke-width", priorityBadgeStrokeWidth);

    // Note: Les labels de titre sont maintenant complètement supprimés
    // Le titre s'affiche dans le panneau latéral au survol

    // Fusionner enter + update
    const nodeUpdate = nodeEnter.merge(nodeElements);

    // Satellites de flèches désactivés
    // updateArrowSatellites(nodeUpdate);

    // 🔥 Apply entrance animation to new nodes
    const centerPosition = { x: width / 2, y: height / 2 };
    applyEntranceAnimation(nodeEnter, settings, centerPosition);

    // ✅ Apply continuous visual effects (pulse, glow)
    const svg = container.select('svg');
    applyContinuousEffects(nodeUpdate, svg, settings);

    // Événements
    nodeUpdate
      .on("mouseover", (event, d) => handleNodeHover(event, d, true))
      .on("mouseout", (event, d) => handleNodeHover(event, d, false))
      .on("click", (event, d) => handleNodeClick(event, d))
      .on("dblclick", (event, d) => handleNodeDoubleClick(event, d));

    // DIAGNOSTIC: Vérifier que les nœuds sont réellement dans le DOM
    setTimeout(() => {
      const allNodes = container.selectAll(".graph-node");
      allNodes.each(function (d, i) {
        if (i === 0) {
          const nodeElement = this;
          const bbox = nodeElement.getBBox();
        }
      });
    }, 100);
  };

  // Geometry functions (convexHull, cross, expandHull) are now imported from geometryUtils

  /**
   * Mise à jour des polygones par type de post
   * Crée un polygone englobant pour chaque type de contenu
   */
  /**
   * Mise à jour des clusters de catégories avec enveloppes convexes
   */
  const updateClusters = (container, categoriesData, articlesData, settings = {}) => {
    const clustersGroup = container.select(".clusters");
    
    // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER POUR LES CLUSTERS
    const clusterFillOpacity = settings.clusterFillOpacity || 0.12;
    const clusterStrokeWidth = settings.clusterStrokeWidth || 3;
    const clusterStrokeOpacity = settings.clusterStrokeOpacity || 0.35;
    const clusterLabelFontSize = settings.clusterLabelFontSize || 14;
    const clusterLabelFontWeight = settings.clusterLabelFontWeight || 'bold';
    const clusterCountFontSize = settings.clusterCountFontSize || 11;
    const clusterCountOpacity = settings.clusterCountOpacity || 0.7;
    const clusterTextShadow = settings.clusterTextShadow || '2px 2px 4px rgba(255,255,255,0.8)';
    const clusterHullPadding = settings.clusterHullPadding || 40;
    const clusterCircleRadius = settings.clusterCircleRadius || 80;
    const clusterCirclePoints = settings.clusterCirclePoints || 12;

    // Calculer les enveloppes convexes pour chaque catégorie
    const clusterData = categoriesData
      .map((category) => {
        const categoryArticles = articlesData.filter((article) =>
          article.categories.some((cat) => cat.id === category.id)
        );

        if (categoryArticles.length === 0) return null;

        // Points des nœuds de cette catégorie - VALIDER LES COORDONNÉES
        const nodePoints = categoryArticles
          .filter(article => {
            return typeof article.x === 'number' && isFinite(article.x) && 
                   typeof article.y === 'number' && isFinite(article.y);
          })
          .map((article) => ({
            x: article.x,
            y: article.y,
          }));
        
        // Si pas de points valides, skip ce cluster
        if (nodePoints.length === 0) {
          return null;
        }

        // Calculer l'enveloppe convexe
        let hull = convexHull(nodePoints);

        // Si on a moins de 3 points, créer un cercle autour des points
        if (hull.length < 3) {
          const avgX = nodePoints.reduce((sum, p) => sum + p.x, 0) / nodePoints.length;
          const avgY = nodePoints.reduce((sum, p) => sum + p.y, 0) / nodePoints.length;
          
          // Valider les moyennes avant de créer le cercle
          if (!isFinite(avgX) || !isFinite(avgY)) {
            return null;
          }
          
          // Créer un cercle avec N points
          hull = Array.from({ length: clusterCirclePoints }, (_, i) => {
            const angle = (i / clusterCirclePoints) * Math.PI * 2;
            return {
              x: avgX + Math.cos(angle) * clusterCircleRadius,
              y: avgY + Math.sin(angle) * clusterCircleRadius,
            };
          });
        } else {
          // Agrandir l'enveloppe pour englober les nœuds avec un padding
          hull = expandHull(hull, clusterHullPadding);
        }

        // Position moyenne pour le label - VALIDER les coordonnées
        const validArticles = categoryArticles.filter(a => {
          return typeof a.x === 'number' && isFinite(a.x) && 
                 typeof a.y === 'number' && isFinite(a.y);
        });
        
        // Si pas d'articles valides, skip ce cluster
        if (validArticles.length === 0) {
          return null;
        }
        
        const avgX = validArticles.reduce((sum, a) => sum + a.x, 0) / validArticles.length;
        const avgY = validArticles.reduce((sum, a) => sum + a.y, 0) / validArticles.length;
        
        // Dernière validation des moyennes
        if (!isFinite(avgX) || !isFinite(avgY)) {
          return null;
        }

        return {
          ...category,
          hull: hull,
          labelX: avgX,
          labelY: avgY,
          count: categoryArticles.length,
        };
      })
      .filter(Boolean);

    // Mettre à jour les éléments du cluster
    const clusterElements = clustersGroup
      .selectAll(".category-cluster")
      .data(clusterData, (d) => d.id);

    // Supprimer les anciens clusters
    clusterElements.exit().remove();

    // Créer les nouveaux clusters
    const clusterEnter = clusterElements
      .enter()
      .append("g")
      .attr("class", "category-cluster");

    // Ajouter le chemin de l'enveloppe - UN SEUL POLYGONE PAR CATÉGORIE
    clusterEnter
      .append("path")
      .attr("class", "cluster-hull")
      .style("fill", (d) => d.color)
      .style("fill-opacity", clusterFillOpacity)
      .style("stroke", (d) => d.color)
      .style("stroke-width", clusterStrokeWidth)
      .style("stroke-opacity", clusterStrokeOpacity)
      .style("stroke-dasharray", "none");  // Ligne continue au lieu de pointillés

    // Label du cluster
    clusterEnter
      .append("text")
      .attr("class", "cluster-label")
      .attr("text-anchor", "middle")
      .attr("dy", "0.35em")
      .style("font-size", `${clusterLabelFontSize}px`)
      .style("font-weight", clusterLabelFontWeight)
      .style("fill", (d) => d.color)
      .style("text-shadow", clusterTextShadow)
      .style("pointer-events", "none")
      .text((d) => d.name.toUpperCase());

    // Nombre d'articles
    clusterEnter
      .append("text")
      .attr("class", "cluster-count")
      .attr("text-anchor", "middle")
      .attr("dy", "1.5em")
      .style("font-size", `${clusterCountFontSize}px`)
      .style("fill", (d) => d.color)
      .style("opacity", clusterCountOpacity)
      .style("pointer-events", "none")
      .text((d) => `${d.count} ${d.count > 1 ? 'projets' : 'projet'}`);

    // Fusionner enter + update
    const clusterUpdate = clusterEnter.merge(clusterElements);

    // Mettre à jour le chemin de l'enveloppe
    clusterUpdate.select(".cluster-hull").attr("d", (d) => {
      if (!d.hull || d.hull.length === 0) return "";

      // Créer un chemin SVG à partir des points de l'enveloppe
      const pathData = d.hull
        .map((point, i) => {
          const command = i === 0 ? "M" : "L";
          return `${command}${point.x},${point.y}`;
        })
        .join(" ");

      return pathData + " Z"; // Z pour fermer le chemin
    });

    // Mettre à jour la position des labels
    clusterUpdate
      .select(".cluster-label")
      .attr("x", (d) => d.labelX)
      .attr("y", (d) => d.labelY);

    clusterUpdate
      .select(".cluster-count")
      .attr("x", (d) => d.labelX)
      .attr("y", (d) => d.labelY);
  };

  /**
   * Mise à jour des îles architecturales
   * Visualise les groupes de projets connectés comme des îles organiques
   */
  const updateArchitecturalIslands = (container, articlesData, settings = {}) => {
    const islandsGroup = container.select(".islands");
    if (islandsGroup.empty()) return;

    const islandData = [];
    
    // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER POUR LES ÎLES
    const islandHullPadding = settings.islandHullPadding || 60;
    const islandSmoothFactor = settings.islandSmoothFactor || 0.3;
    const islandCircleRadius = settings.islandCircleRadius || 80;
    const islandCirclePoints = settings.islandCirclePoints || 12;
    const islandInnerPadding = settings.islandInnerPadding || -20;
    const islandFillOpacity = settings.clusterFillOpacity || 0.12;
    const islandStrokeWidth = settings.clusterStrokeWidth || 3;
    const islandStrokeOpacity = settings.clusterStrokeOpacity || 0.3;
    const islandStrokeDashArray = settings.islandStrokeDashArray || "8,4";
    const islandLabelFontSize = settings.islandLabelFontSize || 14;
    const islandLabelFontWeight = settings.islandLabelFontWeight || '600';
    const islandLabelOpacity = settings.islandLabelOpacity || 0.7;
    const islandLabelYOffset = settings.islandLabelYOffset || -10;
    const islandTextShadow = settings.islandTextShadow || '2px 2px 6px rgba(255,255,255,0.9)';
    const islandCountFontSize = settings.islandCountFontSize || 11;
    const islandCountOpacity = settings.islandCountOpacity || 0.6;
    const islandTextureOpacity = settings.islandTextureOpacity || 0.15;
    const islandTextureDashArray = settings.islandTextureDashArray || "3,3";

    // 1. Récupérer toutes les catégories uniques présentes dans les articles
    const categoriesMap = new Map();
    
    articlesData.forEach(article => {
      if (article.categories && Array.isArray(article.categories)) {
        article.categories.forEach(cat => {
          if (!categoriesMap.has(cat.id)) {
            categoriesMap.set(cat.id, {
              id: cat.id,
              name: cat.name,
              slug: cat.slug,
              color: cat.color || '#3498db', // Couleur de la catégorie ou bleu par défaut
              articles: []
            });
          }
          categoriesMap.get(cat.id).articles.push(article);
        });
      }
    });

    // 2. Créer une zone pour chaque catégorie qui a au moins 2 articles
    categoriesMap.forEach((categoryInfo, catId) => {
      const categoryArticles = categoryInfo.articles;
      
      // Ne créer une zone que s'il y a au moins 2 articles
      if (categoryArticles.length >= 2) {
        // Points des nœuds de cette catégorie - VALIDER que x et y sont des nombres
        const points = categoryArticles
          .filter(article => {
            const hasValidX = typeof article.x === 'number' && isFinite(article.x);
            const hasValidY = typeof article.y === 'number' && isFinite(article.y);
            return hasValidX && hasValidY;
          })
          .map(article => ({
            x: article.x,
            y: article.y
          }));

        // Si pas assez de points valides, skip cette catégorie
        if (points.length < 2) {
          return;
        }

        // Calculer le centre de la zone
        const centerX = points.reduce((sum, p) => sum + p.x, 0) / points.length;
        const centerY = points.reduce((sum, p) => sum + p.y, 0) / points.length;
        
        // Valider que le centre est un nombre fini
        if (!isFinite(centerX) || !isFinite(centerY)) {
          return;
        }

        // Créer une enveloppe convexe organique
        let hull = convexHull(points);
        
        if (hull.length < 3) {
          // Si moins de 3 points, créer un cercle
          hull = Array.from({ length: islandCirclePoints }, (_, i) => {
            const angle = (i / islandCirclePoints) * Math.PI * 2;
            return {
              x: centerX + Math.cos(angle) * islandCircleRadius,
              y: centerY + Math.sin(angle) * islandCircleRadius
            };
          });
        } else {
          // Agrandir l'enveloppe pour un padding généreux
          hull = expandHull(hull, islandHullPadding);
          // Arrondir les coins pour un effet organique
          hull = smoothHull(hull, islandSmoothFactor);
        }

        const island = {
          id: `category_${catId}`,
          categoryId: catId,
          categoryName: categoryInfo.name,
          categorySlug: categoryInfo.slug,
          color: categoryInfo.color,
          members: categoryArticles,
          center: { x: centerX, y: centerY },
          hull: hull,
          count: categoryArticles.length,
          type: 'category'
        };

        islandData.push(island);
      }
    });

    // 3. Créer une zone spéciale pour les PAGES (post_type = 'page')
    const pageNodes = articlesData.filter(a => a.post_type === 'page');
    
    if (pageNodes.length >= 2) {
      const points = pageNodes
        .filter(page => {
          const hasValidX = typeof page.x === 'number' && isFinite(page.x);
          const hasValidY = typeof page.y === 'number' && isFinite(page.y);
          return hasValidX && hasValidY;
        })
        .map(page => ({
          x: page.x,
          y: page.y
        }));

      // Si pas assez de points valides, skip
      if (points.length < 2) {
        return;
      }

      const centerX = points.reduce((sum, p) => sum + p.x, 0) / points.length;
      const centerY = points.reduce((sum, p) => sum + p.y, 0) / points.length;
      
      // Valider que le centre est un nombre fini
      if (!isFinite(centerX) || !isFinite(centerY)) {
        return;
      }

      let hull = convexHull(points);
      
      if (hull.length < 3) {
        hull = Array.from({ length: islandCirclePoints }, (_, i) => {
          const angle = (i / islandCirclePoints) * Math.PI * 2;
          return {
            x: centerX + Math.cos(angle) * islandCircleRadius,
            y: centerY + Math.sin(angle) * islandCircleRadius
          };
        });
      } else {
        hull = expandHull(hull, islandHullPadding);
        hull = smoothHull(hull, islandSmoothFactor);
      }

      const pageIsland = {
        id: 'pages_zone',
        categoryName: 'Pages',
        categorySlug: 'pages',
        color: settings.pagesZoneColor || settings.defaultNodeColor || '#9b59b6',
        members: pageNodes,
        center: { x: centerX, y: centerY },
        hull: hull,
        count: pageNodes.length,
        type: 'pages'
      };

      islandData.push(pageIsland);
    }

    // Mettre à jour les îles
    const islandElements = islandsGroup
      .selectAll(".architectural-island")
      .data(islandData, d => d.id);

    // Supprimer les anciennes îles
    islandElements.exit().remove();

    // Créer les nouvelles îles
    const islandEnter = islandElements
      .enter()
      .append("g")
      .attr("class", "architectural-island")
      .attr("data-category-id", d => d.categoryId || 'pages')
      .attr("data-category-slug", d => d.categorySlug);

    // Fond d'île avec dégradé radial pour effet organique
    islandEnter
      .append("path")
      .attr("class", "island-background")
      .style("fill", d => d.color)
      .style("fill-opacity", islandFillOpacity)
      .style("stroke", d => d.color)
      .style("stroke-width", islandStrokeWidth)
      .style("stroke-opacity", islandStrokeOpacity)
      .style("stroke-dasharray", islandStrokeDashArray)
      .style("filter", "url(#island-glow)");

    // Texture interne pour effet d'île
    islandEnter
      .append("path")
      .attr("class", "island-texture")
      .style("fill", "none")
      .style("stroke", d => d.color)
      .style("stroke-width", 1)
      .style("stroke-opacity", islandTextureOpacity)
      .style("stroke-dasharray", islandTextureDashArray);

    // Label de la catégorie
    islandEnter
      .append("text")
      .attr("class", "island-label")
      .attr("text-anchor", "middle")
      .attr("dy", "0.35em")
      .style("font-size", `${islandLabelFontSize}px`)
      .style("font-weight", islandLabelFontWeight)
      .style("fill", d => d.color)
      .style("text-shadow", islandTextShadow)
      .style("pointer-events", "none")
      .style("opacity", islandLabelOpacity)
      .text(d => d.categoryName.toUpperCase());

    // Nombre d'éléments dans la catégorie
    islandEnter
      .append("text")
      .attr("class", "island-count")
      .attr("text-anchor", "middle")
      .attr("dy", "1.8em")
      .style("font-size", `${islandCountFontSize}px`)
      .style("fill", d => d.color)
      .style("opacity", islandCountOpacity)
      .style("pointer-events", "none")
      .text(d => `${d.count} ${d.count > 1 ? 'éléments' : 'élément'}`);

    // Fusionner enter + update
    const islandUpdate = islandEnter.merge(islandElements);

    // Mettre à jour les enveloppes
    islandUpdate.select(".island-background").attr("d", d => {
      if (!d.hull || d.hull.length < 3) return "";
      const pathData = d.hull
        .map((point, i) => `${i === 0 ? 'M' : 'L'}${point.x},${point.y}`)
        .join(" ");
      return pathData + " Z";
    });

    // Texture interne (enveloppe réduite)
    islandUpdate.select(".island-texture").attr("d", d => {
      if (!d.hull || d.hull.length < 3) return "";
      const innerHull = expandHull(d.hull, islandInnerPadding);
      const pathData = innerHull
        .map((point, i) => `${i === 0 ? 'M' : 'L'}${point.x},${point.y}`)
        .join(" ");
      return pathData + " Z";
    });

    // Mettre à jour les labels
    islandUpdate.select(".island-label")
      .attr("x", d => d.center.x)
      .attr("y", d => d.center.y + islandLabelYOffset);

    islandUpdate.select(".island-count")
      .attr("x", d => d.center.x)
      .attr("y", d => d.center.y + islandLabelYOffset);
  };

  // smoothHull function is now imported from geometryUtils

  /**
   * Calculer et appliquer les forces de répulsion (wrapper using utility)
   */
  const applyRepulsionForces = () => {
    // ⚡ PERFORMANCE: Vérifier les limites de temps et d'itérations
    if (!repulsionStartTimeRef.current) {
      repulsionStartTimeRef.current = Date.now();
      repulsionIterationsRef.current = 0;
    }
    
    const elapsed = Date.now() - repulsionStartTimeRef.current;
    repulsionIterationsRef.current++;
    
    // Arrêter si dépassement des limites
    if (elapsed > MAX_REPULSION_DURATION || repulsionIterationsRef.current > MAX_REPULSION_ITERATIONS) {
      console.log(`⚡ Repulsion stopped: ${elapsed}ms, ${repulsionIterationsRef.current} iterations`);
      repulsionStartTimeRef.current = null;
      repulsionIterationsRef.current = 0;
      if (animationFrameRef.current) {
        cancelAnimationFrame(animationFrameRef.current);
        animationFrameRef.current = null;
      }
      return;
    }
    
    const nodesList = articles.filter(
      (article) =>
        selectedCategories.size === 0 ||
        article.categories.some((cat) => selectedCategories.has(cat.id))
    );

    const hasMovement = applyRepulsionForcesUtil(
      nodesList,
      velocitiesRef,
      {
        repulsionForce: REPULSION_FORCE,
        minDistance: MIN_DISTANCE,
        damping: DAMPING,
      },
      width,
      height
    );

    // Mettre à jour les positions visuelles
    if (hasMovement) {
      updateNodePositions(
        d3.select(svgRef.current).select(".graph-group"),
        nodesList
      );
    }

    // Continuer l'animation si au moins un nœud est en mouvement
    if (hasMovement) {
      animationFrameRef.current = requestAnimationFrame(applyRepulsionForces);
    } else {
      // Plus de mouvement, réinitialiser les compteurs
      repulsionStartTimeRef.current = null;
      repulsionIterationsRef.current = 0;
      animationFrameRef.current = null;
    }
  };

  /**
   * Gestionnaires d'événements de drag
   */
  const handleDragStart = (event, d, simulation) => {
    if (!event.active) simulation.alphaTarget(0.3).restart();
    d.fx = d.x;
    d.fy = d.y;

    // ⚡ PERFORMANCE: Réinitialiser les compteurs avant de démarrer la répulsion
    repulsionStartTimeRef.current = null;
    repulsionIterationsRef.current = 0;
    
    // Déclencher la répulsion
    if (animationFrameRef.current) {
      cancelAnimationFrame(animationFrameRef.current);
    }
    animationFrameRef.current = requestAnimationFrame(applyRepulsionForces);
  };

  const handleDrag = (event, d) => {
    d.fx = event.x;
    d.fy = event.y;
  };

  const handleDragEnd = (event, d, simulation) => {
    if (!event.active) simulation.alphaTarget(0);

    // Sauvegarder la position si activé
    if (options.autoSavePositions) {
      const positions = [
        {
          id: d.id,
          x: d.x,
          y: d.y,
        },
      ];
      saveNodePositions(positions);
    }

    d.fx = null;
    d.fy = null;

    // ⚡ PERFORMANCE: Réinitialiser les compteurs avant de continuer la répulsion
    repulsionStartTimeRef.current = null;
    repulsionIterationsRef.current = 0;
    
    // Continuer la répulsion après le drag
    if (animationFrameRef.current) {
      cancelAnimationFrame(animationFrameRef.current);
    }
    animationFrameRef.current = requestAnimationFrame(applyRepulsionForces);
  };

  /**
   * Get animation intensity multipliers based on node's animation level
   * @param {Object} node - Node data
   * @returns {Object} - Multipliers for scale and duration
   */
  const getAnimationIntensity = (node) => {
    const level = node.animation_level || 'normal';
    
    const intensities = {
      'none': { scale: 1.0, duration: 0 },
      'subtle': { scale: 1.05, duration: 300 },
      'normal': { scale: 1.2, duration: 200 },
      'intense': { scale: 1.4, duration: 150 }
    };
    
    return intensities[level] || intensities['normal'];
  };

  /**
   * Gestion du survol des nœuds
   */
  const handleNodeHover = (event, d, isEntering) => {
    const nodeElement = d3.select(event.currentTarget);
    const imageElement = nodeElement.select(".node-image");
    const intensity = getAnimationIntensity(d);

    // 🔥 RÉCUPÉRER LES SETTINGS DU CUSTOMIZER
    const settings = customizerSettingsRef.current;

    if (isEntering) {
      setHoveredNode(d);

      // Activate GIF animation on hover
      activateNodeGif(nodeElement, d);

      // ✅ Use unified hover scale effect AVEC LES SETTINGS
      applyHoverScale(imageElement, d, true, settings);

      // Afficher le tooltip à proximité du nœud
      showNodeTooltip(d, event);
    } else {
      setHoveredNode(null);

      // Deactivate GIF animation when hover ends (only if not selected)
      if (!selectedNode || selectedNode.id !== d.id) {
        deactivateNodeGif(nodeElement, d);
      }

      // ✅ Reset scale using unified function AVEC LES SETTINGS
      if (!selectedNode || selectedNode.id !== d.id) {
        applyHoverScale(imageElement, d, false, settings);
      }

      // Masquer le tooltip
      hideNodeTooltip();
    }
  };

  /**
   * Afficher le tooltip à proximité du nœud (utilise nodeInteractions.js)
   */
  const showNodeTooltip = (node, event) => {
    showNodeTooltipUtil(node, event, svgRef, transformRef, options);
  };

  /**
   * Masquer le tooltip (utilise nodeInteractions.js)
   */
  const hideNodeTooltip = () => {
    hideNodeTooltipUtil();
  };

  /**
   * Afficher le panneau latéral avec animation d'écriture (utilise nodeInteractions.js)
   */
  const showSideTitlePanel = (node, showLink = false) => {
    showSideTitlePanelUtil(node, showLink, options);
  };

  /**
   * Masquer le panneau latéral (utilise nodeInteractions.js)
   */
  const hideSideTitlePanel = () => {
    hideSideTitlePanelUtil();
  };

  /**
   * Gestion du clic sur les nœuds
   */
  const handleNodeClick = (event, d) => {
    event.stopPropagation();
    const nodeElement = d3.select(event.currentTarget);

    // Si le nœud est déjà sélectionné (actif), ouvrir l'article
    if (selectedNode && selectedNode.id === d.id) {
      const link = nodeElement.attr("data-link");
      if (link) {
        window.location.href = link;
      }
      return;
    }

    // Premier clic : activer le nœud et permettre le drag & drop
    // Réinitialiser le nœud précédemment sélectionné
    if (selectedNode) {
      resetSelectedNode();
    }

    // Sécurité : enlever la classe "active" de tous les nodes
    const svg = d3.select(svgRef.current);
    svg.selectAll(".graph-node").classed("active", false);

    // Sélectionner le nouveau nœud
    setSelectedNode(d);

    // Utiliser la couleur de la catégorie principale du nœud pour l'accentuation
    // Cela assure la cohérence visuelle entre le polygone et le nœud actif
    const settings = customizerSettingsRef.current;
    let accentColor = settings.defaultNodeColor || '#3498db'; // Couleur par défaut du Customizer
    
    if (d.categories && d.categories.length > 0) {
      // Utiliser la couleur de la première catégorie
      accentColor = d.categories[0].color || accentColor;
    } else if (d.node_color) {
      // Sinon utiliser la couleur personnalisée du nœud
      accentColor = d.node_color;
    }

    // Ajouter la classe "active" au node
    nodeElement.classed("active", true);

    // Appliquer la couleur de la catégorie pour l'accentuation du nœud actif
    nodeElement.style("--active-node-color", accentColor);

    // Activate GIF animation when node is selected
    activateNodeGif(nodeElement, d);

    // Agrandir l'image du nœud cliqué
    const imageElement = nodeElement.select(".node-image");
    
    // Cancel any ongoing pulse effect transition
    imageElement.interrupt();
    
    // 🔥 Use settings from Customizer
    const graphSettings = customizerSettingsRef.current;
    const scale = graphSettings.activeNodeScale || 1.5;
    
    imageElement
      .transition()
      .duration(400)
      .attr("width", (d.node_size || 60) * scale)
      .attr("height", (d.node_size || 60) * scale)
      .attr("x", (-(d.node_size || 60) * scale) / 2)
      .attr("y", (-(d.node_size || 60) * scale) / 2);

    // Afficher le panneau latéral avec le lien "Consulter"
    showSideTitlePanel(d, true);

    // Afficher le panneau d'informations avec résumé, titre et thumbnail
    showInfoPanel(d);
  };

  /**
   * Gestion du double-clic sur les nœuds (ouvre directement l'article)
   */
  const handleNodeDoubleClick = (event, d) => {
    event.stopPropagation();
    const nodeElement = d3.select(event.currentTarget);
    
    // Annuler le timer du simple clic pour éviter l'ouverture en double
    if (clickTimerRef.current) {
      clearTimeout(clickTimerRef.current);
      clickTimerRef.current = null;
    }
    
    // Double-clic : ouvrir directement l'article en utilisant data-link
    const link = nodeElement.attr("data-link");
    if (link) {
      window.location.href = link;
    }
  };

  /**
   * Réinitialiser le nœud sélectionné
   * Désactive TOUS les nœuds actifs, même si selectedNode est null
   */
  const resetSelectedNode = () => {
    const svg = d3.select(svgRef.current);
    
    // Par sécurité, retirer la classe "active" de TOUS les nœuds
    svg.selectAll(".graph-node").classed("active", false);
    
    // Si un nœud spécifique était sélectionné, le réinitialiser proprement
    if (selectedNode) {
      const nodeElement = svg.select(`[data-node-id="${selectedNode.id}"]`);

      if (!nodeElement.empty()) {
        // Retirer la variable CSS personnalisée
        nodeElement.style("--active-node-color", null);

        // Deactivate GIF animation when node is deselected
        deactivateNodeGif(nodeElement, selectedNode);

        const imageElement = nodeElement.select(".node-image");
        
        // Cancel any ongoing transitions
        imageElement.interrupt();
        
        imageElement
          .transition()
          .duration(400)
          .attr("width", selectedNode.node_size || 60)
          .attr("height", selectedNode.node_size || 60)
          .attr("x", -(selectedNode.node_size || 60) / 2)
          .attr("y", -(selectedNode.node_size || 60) / 2)
          .on("end", () => {
            // Restart pulse effect after transition if enabled
            const svg = d3.select(svgRef.current);
            applyContinuousEffects(nodeElement, svg);
          });
      }
    }

    // Masquer le tooltip quand le node est désélectionné
    hideNodeTooltip();

    // Masquer le panneau d'informations
    hideInfoPanel();

    setSelectedNode(null);
  };

  /**
   * Réinitialiser le zoom
   */
  const resetZoom = () => {
    const svg = d3.select(svgRef.current);
    svg.transition().duration(750).call(d3.zoom().transform, d3.zoomIdentity);
  };

  /**
   * Redimensionnement
   */
  const resize = (newWidth, newHeight) => {
    const svg = d3.select(svgRef.current);
    svg
      .attr("width", newWidth)
      .attr("height", newHeight)
      .attr("viewBox", `0 0 ${newWidth} ${newHeight}`);

    if (simulationRef.current) {
      const settings = customizerSettingsRef.current;
      const alphaValue = settings.resizeAlpha || 0.3;
      
      simulationRef.current
        .force("center", d3.forceCenter(newWidth / 2, newHeight / 2))
        .alpha(alphaValue)
        .restart();
    }
  };

  // Note: La méthode resize est maintenant exposée directement dans updateGraph()
  // via window.graphInstance

  if (loading) {
    return (
      <div className="graph-loading">
        <div className="loading-spinner">
          <div className="spinner"></div>
          <p>Chargement des articles...</p>
        </div>
      </div>
    );
  }

  if (articles.length === 0) {
    return (
      <div className="graph-error">
        <h3>Aucun article à afficher</h3>
        <p>Il n'y a pas encore d'articles avec le graphique activé.</p>
        <a href="/wordpress/wp-admin/edit.php" className="btn btn-primary">
          Créer des articles
        </a>
      </div>
    );
  }

  return (
    <div
      className="graph-wrapper"
      style={{
        display: "block",
        visibility: "visible",
        opacity: 1,
        position: "relative",
        width: "100%",
        height: "100%",
        minHeight: "800px",
      }}
    >
      {/* Titre en arrière-plan */}
      <div className="title-overlay">
        <h1>ARCHI GRAPH</h1>
        <p>Exploration Interactive des Concepts</p>
      </div>

      {/* SVG principal */}
      <svg
        ref={svgRef}
        className="graph-svg"
        width={width}
        height={height}
        viewBox={`0 0 ${width} ${height}`}
        preserveAspectRatio="xMidYMid meet"
        style={{
          display: "block",
          visibility: "visible",
          opacity: 1,
          width: "100%",
          height: "100%",
          minHeight: "800px",
          backgroundColor: "#ffffff",
        }}
      />

      {/* Plus de popup NodeTooltip - le titre est maintenant intégré dans le SVG */}

      {/* Category Legend */}
      <CategoryLegend
        articles={articles}
        settings={customizerSettingsRef.current}
        selectedCategories={selectedCategories}
        onCategoryToggle={(categoryId) => {
          setSelectedCategories(prev => {
            const newSet = new Set(prev);
            if (newSet.has(categoryId)) {
              newSet.delete(categoryId);
            } else {
              newSet.add(categoryId);
            }
            return newSet;
          });
        }}
        onClearFilters={() => setSelectedCategories(new Set())}
      />
    </div>
  );
};

export default GraphContainer;
