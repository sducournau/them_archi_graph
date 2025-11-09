import * as d3 from "d3";

/**
 * Module de gestion des polygones de catégories dans le graphique
 * Dessine des enveloppes convexes autour des groupes de nœuds
 */

/**
 * Calculer l'enveloppe convexe (convex hull) pour un groupe de points
 * Algorithme de Graham scan
 * @param {Array} points - Tableau de points {x, y}
 * @returns {Array} Points de l'enveloppe convexe
 */
export const calculateConvexHull = (points) => {
  if (!points || points.length < 3) {
    return points || [];
  }

  // Trier les points par x, puis par y
  const sorted = points.slice().sort((a, b) => {
    if (a.x !== b.x) return a.x - b.x;
    return a.y - b.y;
  });

  // Construire la coque inférieure
  const lower = [];
  for (const point of sorted) {
    while (
      lower.length >= 2 &&
      crossProduct(
        lower[lower.length - 2],
        lower[lower.length - 1],
        point
      ) <= 0
    ) {
      lower.pop();
    }
    lower.push(point);
  }

  // Construire la coque supérieure
  const upper = [];
  for (let i = sorted.length - 1; i >= 0; i--) {
    const point = sorted[i];
    while (
      upper.length >= 2 &&
      crossProduct(
        upper[upper.length - 2],
        upper[upper.length - 1],
        point
      ) <= 0
    ) {
      upper.pop();
    }
    upper.push(point);
  }

  // Retirer le dernier point car il est dupliqué
  lower.pop();
  upper.pop();

  return lower.concat(upper);
};

/**
 * Produit vectoriel pour déterminer l'orientation
 * @param {Object} o - Point origine
 * @param {Object} a - Point A
 * @param {Object} b - Point B
 * @returns {number} Produit vectoriel
 */
const crossProduct = (o, a, b) => {
  return (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
};

/**
 * Agrandir l'enveloppe convexe avec un padding
 * @param {Array} hull - Points de l'enveloppe
 * @param {number} padding - Padding en pixels
 * @returns {Array} Points de l'enveloppe agrandie
 */
export const expandHull = (hull, padding = 20) => {
  if (!hull || hull.length < 3) {
    return hull || [];
  }

  // Calculer le centre
  const center = {
    x: hull.reduce((sum, p) => sum + p.x, 0) / hull.length,
    y: hull.reduce((sum, p) => sum + p.y, 0) / hull.length,
  };

  // Agrandir chaque point vers l'extérieur
  return hull.map((point) => {
    const dx = point.x - center.x;
    const dy = point.y - center.y;
    const distance = Math.sqrt(dx * dx + dy * dy);

    if (distance === 0) return point;

    const ratio = (distance + padding) / distance;

    return {
      x: center.x + dx * ratio,
      y: center.y + dy * ratio,
    };
  });
};

/**
 * Lisser l'enveloppe convexe avec des courbes de Bézier
 * @param {Array} hull - Points de l'enveloppe
 * @param {number} tension - Tension des courbes (0-1)
 * @returns {string} Chemin SVG avec courbes
 */
export const smoothHull = (hull, tension = 0.5) => {
  if (!hull || hull.length < 3) {
    return "";
  }

  const points = [...hull, hull[0]]; // Fermer le polygone
  let path = `M${points[0].x},${points[0].y}`;

  for (let i = 0; i < points.length - 1; i++) {
    const p0 = points[i > 0 ? i - 1 : points.length - 2];
    const p1 = points[i];
    const p2 = points[i + 1];
    const p3 = points[i + 2 < points.length ? i + 2 : 1];

    // Points de contrôle pour la courbe de Bézier cubique
    const cp1x = p1.x + ((p2.x - p0.x) * tension) / 6;
    const cp1y = p1.y + ((p2.y - p0.y) * tension) / 6;
    const cp2x = p2.x - ((p3.x - p1.x) * tension) / 6;
    const cp2y = p2.y - ((p3.y - p1.y) * tension) / 6;

    path += ` C${cp1x},${cp1y} ${cp2x},${cp2y} ${p2.x},${p2.y}`;
  }

  return path + "Z";
};

/**
 * Créer les polygones pour toutes les catégories
 * @param {Array} nodes - Tous les nœuds du graphique
 * @param {Array} categories - Toutes les catégories
 * @param {Array} polygonColors - Configuration des polygones (enabled status)
 * @returns {Array} Données des polygones à dessiner
 */
export const createCategoryPolygons = (nodes, categories, polygonColors = []) => {
  const polygons = [];

  categories.forEach((category) => {
    // Trouver la configuration pour cette catégorie
    const colorConfig = polygonColors.find(
      (pc) => pc.category_id === category.id
    );

    // Si les polygones sont désactivés pour cette catégorie, ignorer
    if (colorConfig && !colorConfig.enabled) {
      return;
    }

    // Filtrer les nœuds de cette catégorie
    const categoryNodes = nodes.filter((node) =>
      node.categories?.some((cat) => cat.id === category.id)
    );

    // Besoin d'au moins 3 nœuds pour un polygone
    if (categoryNodes.length < 3) {
      return;
    }

    // Extraire les positions
    const points = categoryNodes.map((node) => ({
      x: node.x || 0,
      y: node.y || 0,
    }));

    // Calculer l'enveloppe convexe
    let hull = calculateConvexHull(points);

    // Agrandir avec padding
    hull = expandHull(hull, 30);

    // Générer le chemin lissé
    const path = smoothHull(hull, 0.5);

    // Utiliser la couleur native de la catégorie pour le polygone
    polygons.push({
      category,
      path,
      color: category.color || "#3498db",
      opacity: 0.2, // Opacité fixe et cohérente
      nodeCount: categoryNodes.length,
    });
  });

  return polygons;
};

/**
 * Dessiner les polygones sur le SVG
 * @param {d3.Selection} svg - Sélection D3 du SVG
 * @param {Array} polygons - Données des polygones
 * @param {Object} options - Options de rendu
 */
export const drawPolygons = (svg, polygons, options = {}) => {
  const {
    className = "category-polygon",
    animated = true,
    animationDuration = 800,
  } = options;

  // Créer ou récupérer le groupe pour les polygones
  let polygonGroup = svg.select(".polygons-layer");

  if (polygonGroup.empty()) {
    polygonGroup = svg.insert("g", ":first-child").attr("class", "polygons-layer");
  }

  // Bind des données
  const polygonPaths = polygonGroup
    .selectAll(`.${className}`)
    .data(polygons, (d) => d.category.id);

  // Enter
  const enter = polygonPaths
    .enter()
    .append("path")
    .attr("class", className)
    .attr("d", (d) => d.path)
    .attr("fill", (d) => d.color)
    .attr("stroke", (d) => d.color)
    .attr("stroke-width", 2)
    .attr("stroke-opacity", 0.5)
    .attr("fill-opacity", 0);

  if (animated) {
    enter
      .transition()
      .duration(animationDuration)
      .attr("fill-opacity", (d) => d.opacity);
  } else {
    enter.attr("fill-opacity", (d) => d.opacity);
  }

  // Update
  const update = polygonPaths
    .transition()
    .duration(animated ? animationDuration : 0)
    .attr("d", (d) => d.path)
    .attr("fill", (d) => d.color)
    .attr("fill-opacity", (d) => d.opacity)
    .attr("stroke", (d) => d.color);

  // Exit
  polygonPaths
    .exit()
    .transition()
    .duration(animated ? animationDuration / 2 : 0)
    .attr("fill-opacity", 0)
    .attr("stroke-opacity", 0)
    .remove();

  // Ajouter des tooltips
  svg
    .selectAll(`.${className}`)
    .on("mouseenter", function (event, d) {
      d3.select(this)
        .transition()
        .duration(200)
        .attr("fill-opacity", Math.min(d.opacity * 1.5, 0.5))
        .attr("stroke-width", 3);

      // Afficher le nom de la catégorie
      showPolygonTooltip(event, d);
    })
    .on("mouseleave", function (event, d) {
      d3.select(this)
        .transition()
        .duration(200)
        .attr("fill-opacity", d.opacity)
        .attr("stroke-width", 2);

      hidePolygonTooltip();
    });
};

/**
 * Afficher un tooltip pour le polygone
 * @param {Event} event - Événement souris
 * @param {Object} polygonData - Données du polygone
 */
const showPolygonTooltip = (event, polygonData) => {
  // Retirer l'ancien tooltip s'il existe
  hidePolygonTooltip();

  const tooltip = d3
    .select("body")
    .append("div")
    .attr("class", "archi-polygon-tooltip")
    .style("position", "absolute")
    .style("background", "rgba(0, 0, 0, 0.8)")
    .style("color", "white")
    .style("padding", "8px 12px")
    .style("border-radius", "4px")
    .style("font-size", "12px")
    .style("pointer-events", "none")
    .style("z-index", "10000")
    .style("left", event.pageX + 10 + "px")
    .style("top", event.pageY - 10 + "px")
    .html(
      `<strong>${polygonData.category.name}</strong><br>` +
        `${polygonData.nodeCount} article${polygonData.nodeCount > 1 ? "s" : ""}`
    );
};

/**
 * Cacher le tooltip
 */
const hidePolygonTooltip = () => {
  d3.selectAll(".archi-polygon-tooltip").remove();
};

/**
 * Mettre à jour les polygones quand les nœuds bougent
 * @param {d3.Selection} svg - Sélection D3 du SVG
 * @param {Array} nodes - Nœuds mis à jour
 * @param {Array} categories - Catégories
 * @param {Array} polygonColors - Configuration des couleurs
 */
export const updatePolygons = (svg, nodes, categories, polygonColors) => {
  const polygons = createCategoryPolygons(nodes, categories, polygonColors);
  drawPolygons(svg, polygons, { animated: false });
};

/**
 * Activer/désactiver l'affichage des polygones
 * @param {d3.Selection} svg - Sélection D3 du SVG
 * @param {boolean} visible - Visibilité
 * @param {number} duration - Durée de la transition
 */
export const togglePolygonsVisibility = (svg, visible, duration = 300) => {
  svg
    .selectAll(".category-polygon")
    .transition()
    .duration(duration)
    .attr("fill-opacity", visible ? (d) => d.opacity : 0)
    .attr("stroke-opacity", visible ? 0.5 : 0);
};

/**
 * Charger les configurations de couleur depuis l'API
 * @returns {Promise<Array>} Configuration des couleurs de polygone
 */
export const loadPolygonColors = async () => {
  try {
    const response = await fetch("/wp-json/archi/v1/polygon-colors");
    if (!response.ok) {
      throw new Error("Failed to fetch polygon colors");
    }
    return await response.json();
  } catch (error) {
    console.error("Error loading polygon colors:", error);
    return [];
  }
};

export default {
  calculateConvexHull,
  expandHull,
  smoothHull,
  createCategoryPolygons,
  drawPolygons,
  updatePolygons,
  togglePolygonsVisibility,
  loadPolygonColors,
};
