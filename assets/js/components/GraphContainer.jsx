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
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [hoveredNode, setHoveredNode] = useState(null);
  const [selectedNode, setSelectedNode] = useState(null);

  // Références
  const svgRef = useRef(null);
  const simulationRef = useRef(null);
  const transformRef = useRef(d3.zoomIdentity);
  const velocitiesRef = useRef({});
  const animationFrameRef = useRef(null);
  const clickTimerRef = useRef(null); // Timer pour détecter double-clic

  // Paramètres de physique pour la répulsion
  const REPULSION_FORCE = 2000;
  const MIN_DISTANCE = 120;
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
    if (articles.length > 0 && svgRef.current) {
      updateGraph();
    }
  }, [articles, selectedCategory]);

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

    // Filtrer les articles selon la catégorie sélectionnée
    let filteredArticles = articles;
    if (selectedCategory) {
      filteredArticles = articles.filter((article) =>
        article.categories.some((cat) => cat.id === parseInt(selectedCategory))
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

    // Créer la simulation de force
    const simulation = d3
      .forceSimulation(filteredArticles)
      .force("charge", d3.forceManyBody().strength(-300).distanceMax(200))
      .force("center", d3.forceCenter(width / 2, height / 2))
      .force(
        "collision",
        d3
          .forceCollide()
          .radius((d) => (d.node_size || 60) / 2 + 10)
          .strength(0.7)
      )
      .alpha(1)
      .alphaDecay(0.02)
      .velocityDecay(0.3);

    // Ajouter la force des liens seulement si l'option est activée
    if (shouldShowLinks) {
      simulation.force(
        "link",
        d3
          .forceLink(links)
          .id((d) => d.id)
          .distance((d) => {
            // Distance inversement proportionnelle au score
            const baseDistance = 150;
            const scoreFactor = d.proximity?.normalizedScore || 50;
            return baseDistance - (scoreFactor / 100) * 50;
          })
          .strength((d) => {
            // Force proportionnelle au score
            return (d.proximity?.normalizedScore || 50) / 200;
          })
      );
    }

    simulationRef.current = simulation;

    // Créer/Mettre à jour les liens seulement si l'option est activée
    if (shouldShowLinks) {
      updateLinks(g, links);
    } else {
      // Supprimer tous les liens existants
      g.select(".links").selectAll(".graph-link").remove();
    }

    // Créer/Mettre à jour les nœuds
    updateNodes(g, filteredArticles, simulation);

    // Les îles architecturales remplacent les clusters de catégories
    // updateClusters(g, categories, filteredArticles);

    // Îles architecturales par catégories activées
    updateArchitecturalIslands(g, filteredArticles);


    // Démarrer la simulation
    let tickCount = 0;
    simulation.on("tick", () => {
      updateNodePositions(g, filteredArticles);
      // Mettre à jour les liens seulement si l'option est activée
      if (shouldShowLinks) {
        updateLinkPositions(g, links);
      }
      // Les îles architecturales remplacent les clusters de catégories
      // updateClusters(g, categories, filteredArticles);
      // Îles architecturales par catégories activées
      updateArchitecturalIslands(g, filteredArticles);
      
      // Satellites de flèches désactivés
      // const nodeGroups = g.selectAll(".graph-node");
      // animateArrowSatellites(nodeGroups);
      
      if (tickCount === 0) {
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
      filterByCategory: (categoryId) => setSelectedCategory(categoryId),
      resize: resize,
      simulation: simulation,
      data: filteredArticles,
      links: links,
    };
  };

  /**
   * Mise à jour des liens entre les nœuds
   */
  const updateLinks = (container, links) => {
    const linksGroup = container.select(".links");

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
      .style("stroke", (d) => {
        // Couleur basée sur la force du lien
        const strength = d.proximity?.strength || "weak";
        const colors = {
          "very-strong": "#e74c3c",
          strong: "#f39c12",
          medium: "#3498db",
          weak: "#95a5a6",
          "very-weak": "#bdc3c7",
        };
        return colors[strength] || "#95a5a6";
      })
      .style("stroke-width", (d) => {
        // Épaisseur basée sur le score
        const score = d.proximity?.normalizedScore || 25;
        return Math.max(1, (score / 100) * 4);
      })
      .style("stroke-opacity", (d) => {
        // Opacité basée sur le score
        const score = d.proximity?.normalizedScore || 25;
        return Math.max(0.1, (score / 100) * 0.6);
      })
      .style("stroke-dasharray", (d) => {
        // Liens faibles en pointillés
        const strength = d.proximity?.strength || "weak";
        return strength === "weak" || strength === "very-weak" ? "5,5" : "none";
      });

    // Ajouter un titre pour afficher les détails au survol
    linkEnter.append("title").text((d) => {
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
  const updateNodes = (container, data, simulation) => {
    const nodesGroup = container.select(".nodes");

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


    // Image du nœud PNG avec fond transparent (pas de bulle, pas de clip-path)
    // L'image apparaît en entier sans être coupée ni déformée
    nodeEnter
      .append("image")
      .attr("class", "node-image")
      .attr("width", (d) => {
        const size = d.node_size || 60;
        return size;
      })
      .attr("height", (d) => d.node_size || 60)
      .attr("x", (d) => -(d.node_size || 60) / 2)
      .attr("y", (d) => -(d.node_size || 60) / 2)
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
      .attr("r", 8)
      .attr("cx", (d) => (d.node_size || 60) / 2 - 5)
      .attr("cy", (d) => -(d.node_size || 60) / 2 + 5)
      .style("fill", (d) =>
        d.priority_level === "featured" ? "#e74c3c" : "#f39c12"
      )
      .style("stroke", "#ffffff")
      .style("stroke-width", 2);

    // Note: Les labels de titre sont maintenant complètement supprimés
    // Le titre s'affiche dans le panneau latéral au survol

    // Fusionner enter + update
    const nodeUpdate = nodeEnter.merge(nodeElements);

    // Satellites de flèches désactivés
    // updateArrowSatellites(nodeUpdate);

    // ✅ Apply continuous visual effects (pulse, glow)
    const svg = container.select('svg');
    applyContinuousEffects(nodeUpdate, svg);

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
  const updateClusters = (container, categoriesData, articlesData) => {
    const clustersGroup = container.select(".clusters");

    // Calculer les enveloppes convexes pour chaque catégorie
    const clusterData = categoriesData
      .map((category) => {
        const categoryArticles = articlesData.filter((article) =>
          article.categories.some((cat) => cat.id === category.id)
        );

        if (categoryArticles.length === 0) return null;

        // Points des nœuds de cette catégorie
        const nodePoints = categoryArticles.map((article) => ({
          x: article.x || 0,
          y: article.y || 0,
        }));

        // Calculer l'enveloppe convexe
        let hull = convexHull(nodePoints);

        // Si on a moins de 3 points, créer un cercle autour des points
        if (hull.length < 3) {
          const avgX = nodePoints.reduce((sum, p) => sum + p.x, 0) / nodePoints.length;
          const avgY = nodePoints.reduce((sum, p) => sum + p.y, 0) / nodePoints.length;
          const radius = 80;
          
          // Créer un cercle avec 12 points
          hull = Array.from({ length: 12 }, (_, i) => {
            const angle = (i / 12) * Math.PI * 2;
            return {
              x: avgX + Math.cos(angle) * radius,
              y: avgY + Math.sin(angle) * radius,
            };
          });
        } else {
          // Agrandir l'enveloppe pour englober les nœuds avec un padding
          hull = expandHull(hull, 40);
        }

        // Position moyenne pour le label
        const avgX =
          categoryArticles.reduce((sum, a) => sum + (a.x || 0), 0) /
          categoryArticles.length;
        const avgY =
          categoryArticles.reduce((sum, a) => sum + (a.y || 0), 0) /
          categoryArticles.length;

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
      .style("fill-opacity", 0.12)  // Légèrement plus visible
      .style("stroke", (d) => d.color)
      .style("stroke-width", 3)  // Plus épais pour plus de visibilité
      .style("stroke-opacity", 0.35)  // Plus visible
      .style("stroke-dasharray", "none");  // Ligne continue au lieu de pointillés

    // Label du cluster
    clusterEnter
      .append("text")
      .attr("class", "cluster-label")
      .attr("text-anchor", "middle")
      .attr("dy", "0.35em")
      .style("font-size", "14px")
      .style("font-weight", "bold")
      .style("fill", (d) => d.color)
      .style("text-shadow", "2px 2px 4px rgba(255,255,255,0.8)")
      .style("pointer-events", "none")
      .text((d) => d.name.toUpperCase());

    // Nombre d'articles
    clusterEnter
      .append("text")
      .attr("class", "cluster-count")
      .attr("text-anchor", "middle")
      .attr("dy", "1.5em")
      .style("font-size", "11px")
      .style("fill", (d) => d.color)
      .style("opacity", 0.7)
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
  const updateArchitecturalIslands = (container, articlesData) => {
    const islandsGroup = container.select(".islands");
    if (islandsGroup.empty()) return;

    const islandData = [];

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
        // Points des nœuds de cette catégorie
        const points = categoryArticles.map(article => ({
          x: article.x || 0,
          y: article.y || 0
        }));

        // Calculer le centre de la zone
        const centerX = points.reduce((sum, p) => sum + p.x, 0) / points.length;
        const centerY = points.reduce((sum, p) => sum + p.y, 0) / points.length;

        // Créer une enveloppe convexe organique
        let hull = convexHull(points);
        
        if (hull.length < 3) {
          // Si moins de 3 points, créer un cercle
          const radius = 80;
          hull = Array.from({ length: 12 }, (_, i) => {
            const angle = (i / 12) * Math.PI * 2;
            return {
              x: centerX + Math.cos(angle) * radius,
              y: centerY + Math.sin(angle) * radius
            };
          });
        } else {
          // Agrandir l'enveloppe pour un padding généreux
          hull = expandHull(hull, 60);
          // Arrondir les coins pour un effet organique
          hull = smoothHull(hull, 0.3);
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
      const points = pageNodes.map(page => ({
        x: page.x || 0,
        y: page.y || 0
      }));

      const centerX = points.reduce((sum, p) => sum + p.x, 0) / points.length;
      const centerY = points.reduce((sum, p) => sum + p.y, 0) / points.length;

      let hull = convexHull(points);
      
      if (hull.length < 3) {
        const radius = 80;
        hull = Array.from({ length: 12 }, (_, i) => {
          const angle = (i / 12) * Math.PI * 2;
          return {
            x: centerX + Math.cos(angle) * radius,
            y: centerY + Math.sin(angle) * radius
          };
        });
      } else {
        hull = expandHull(hull, 60);
        hull = smoothHull(hull, 0.3);
      }

      const pageIsland = {
        id: 'pages_zone',
        categoryName: 'Pages',
        categorySlug: 'pages',
        color: '#9b59b6', // Violet pour les pages
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
      .style("fill-opacity", 0.12)
      .style("stroke", d => d.color)
      .style("stroke-width", 3)
      .style("stroke-opacity", 0.3)
      .style("stroke-dasharray", "8,4")
      .style("filter", "url(#island-glow)");

    // Texture interne pour effet d'île
    islandEnter
      .append("path")
      .attr("class", "island-texture")
      .style("fill", "none")
      .style("stroke", d => d.color)
      .style("stroke-width", 1)
      .style("stroke-opacity", 0.15)
      .style("stroke-dasharray", "3,3");

    // Label de la catégorie
    islandEnter
      .append("text")
      .attr("class", "island-label")
      .attr("text-anchor", "middle")
      .attr("dy", "0.35em")
      .style("font-size", "14px")
      .style("font-weight", "600")
      .style("fill", d => d.color)
      .style("text-shadow", "2px 2px 6px rgba(255,255,255,0.9)")
      .style("pointer-events", "none")
      .style("opacity", 0.7)
      .text(d => d.categoryName.toUpperCase());

    // Nombre d'éléments dans la catégorie
    islandEnter
      .append("text")
      .attr("class", "island-count")
      .attr("text-anchor", "middle")
      .attr("dy", "1.8em")
      .style("font-size", "11px")
      .style("fill", d => d.color)
      .style("opacity", 0.6)
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
      const innerHull = expandHull(d.hull, -20);
      const pathData = innerHull
        .map((point, i) => `${i === 0 ? 'M' : 'L'}${point.x},${point.y}`)
        .join(" ");
      return pathData + " Z";
    });

    // Mettre à jour les labels
    islandUpdate.select(".island-label")
      .attr("x", d => d.center.x)
      .attr("y", d => d.center.y - 10);

    islandUpdate.select(".island-count")
      .attr("x", d => d.center.x)
      .attr("y", d => d.center.y - 10);
  };

  // smoothHull function is now imported from geometryUtils

  /**
   * Calculer et appliquer les forces de répulsion (wrapper using utility)
   */
  const applyRepulsionForces = () => {
    const nodesList = articles.filter(
      (article) =>
        !selectedCategory ||
        article.categories.some((cat) => cat.id === parseInt(selectedCategory))
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
    }
  };

  /**
   * Gestionnaires d'événements de drag
   */
  const handleDragStart = (event, d, simulation) => {
    if (!event.active) simulation.alphaTarget(0.3).restart();
    d.fx = d.x;
    d.fy = d.y;

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

    if (isEntering) {
      setHoveredNode(d);

      // Activate GIF animation on hover
      activateNodeGif(nodeElement, d);

      // ✅ Use unified hover scale effect
      applyHoverScale(imageElement, d, true);

      // Afficher le tooltip à proximité du nœud
      showNodeTooltip(d, event);
    } else {
      setHoveredNode(null);

      // Deactivate GIF animation when hover ends (only if not selected)
      if (!selectedNode || selectedNode.id !== d.id) {
        deactivateNodeGif(nodeElement, d);
      }

      // ✅ Reset scale using unified function
      if (!selectedNode || selectedNode.id !== d.id) {
        applyHoverScale(imageElement, d, false);
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
    let accentColor = '#3498db'; // Bleu par défaut (fallback)
    
    if (d.categories && d.categories.length > 0) {
      // Utiliser la couleur de la première catégorie
      accentColor = d.categories[0].color || accentColor;
    }

    // Ajouter la classe "active" au node
    nodeElement.classed("active", true);

    // Appliquer la couleur de la catégorie pour l'accentuation du nœud actif
    nodeElement.style("--active-node-color", accentColor);

    // Activate GIF animation when node is selected
    activateNodeGif(nodeElement, d);

    // Agrandir l'image du nœud cliqué
    nodeElement
      .select(".node-image")
      .transition()
      .duration(400)
      .attr("width", (d.node_size || 60) * 1.5)
      .attr("height", (d.node_size || 60) * 1.5)
      .attr("x", (-(d.node_size || 60) * 1.5) / 2)
      .attr("y", (-(d.node_size || 60) * 1.5) / 2);

    // Afficher le panneau latéral avec le lien "Consulter"
    showSideTitlePanel(d, true);
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

        nodeElement
          .select(".node-image")
          .transition()
          .duration(400)
          .attr("width", selectedNode.node_size || 60)
          .attr("height", selectedNode.node_size || 60)
          .attr("x", -(selectedNode.node_size || 60) / 2)
          .attr("y", -(selectedNode.node_size || 60) / 2);
      }
    }

    // Masquer le tooltip quand le node est désélectionné
    hideNodeTooltip();

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
      simulationRef.current
        .force("center", d3.forceCenter(newWidth / 2, newHeight / 2))
        .alpha(0.3)
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
    </div>
  );
};

export default GraphContainer;
