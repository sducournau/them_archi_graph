/**
 * Utilitaires pour récupérer et sauvegarder les données du graphique
 */

/**
 * Récupérer les données des articles pour le graphique
 * @param {string} apiEndpoint - URL de l'API REST
 * @param {Object} options - Options de requête
 * @returns {Promise<Object>} Données des articles
 */
export const fetchGraphData = async (apiEndpoint, options = {}) => {
  try {
    const url = new URL(apiEndpoint);

    // Ajouter les paramètres de requête
    if (options.category) {
      url.searchParams.append("category", options.category);
    }
    if (options.limit) {
      url.searchParams.append("limit", options.limit);
    }

    const response = await fetch(url.toString(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": window.archiGraph?.nonce || "",
      },
      credentials: "same-origin",
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status} ${response.statusText}`);
    }

    const data = await response.json();

    // Ajouter des positions initiales si elles n'existent pas
    if (data.articles) {
      data.articles = data.articles.map((article) => ({
        ...article,
        x: article.position?.x || Math.random() * 800 + 200,
        y: article.position?.y || Math.random() * 600 + 100,
        vx: 0,
        vy: 0,
      }));
    }

    return data;
  } catch (error) {
    console.error("Erreur lors de la récupération des données:", error);
    throw error;
  }
};

/**
 * Récupérer les données des catégories
 * @param {string} apiEndpoint - URL de l'API REST
 * @returns {Promise<Array>} Données des catégories
 */
export const fetchCategoriesData = async (apiEndpoint) => {
  try {
    const categoriesUrl = apiEndpoint.replace("/articles", "/categories");

    const response = await fetch(categoriesUrl, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": window.archiGraph?.nonce || "",
      },
      credentials: "same-origin",
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error("Erreur lors de la récupération des catégories:", error);
    throw error;
  }
};

/**
 * Sauvegarder les positions des nœuds
 * @param {Array} positions - Array d'objets {id, x, y}
 * @returns {Promise<Object>} Réponse de l'API
 */
export const saveNodePositions = async (positions) => {
  try {
    const apiUrl = window.archiGraph?.apiUrl || "/wp-json/archi/v1/";
    const saveUrl = apiUrl + "save-positions";

    const response = await fetch(saveUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": window.archiGraph?.nonce || "",
      },
      credentials: "same-origin",
      body: JSON.stringify(positions),
    });

    if (!response.ok) {
      throw new Error(`Erreur lors de la sauvegarde: ${response.status}`);
    }

    const result = await response.json();

    return result;
  } catch (error) {
    console.error("Erreur lors de la sauvegarde des positions:", error);
    throw error;
  }
};

/**
 * Fonction de debounce pour éviter trop de sauvegardes
 * @param {Function} func - Fonction à debouncer
 * @param {number} wait - Délai en millisecondes
 * @returns {Function} Fonction debouncée
 */
export const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

/**
 * Sauvegarder les positions avec debounce
 */
export const debouncedSavePositions = debounce((positions) => {
  saveNodePositions(positions);
}, 2000); // Attendre 2 secondes après la dernière modification

/**
 * Récupérer les données depuis le cache localStorage
 * @param {string} key - Clé de cache
 * @param {number} maxAge - Âge maximum en millisecondes
 * @returns {Object|null} Données cachées ou null
 */
export const getCachedData = (key, maxAge = 5 * 60 * 1000) => {
  // 5 minutes par défaut
  try {
    const cached = localStorage.getItem(key);
    if (!cached) return null;

    const data = JSON.parse(cached);
    const now = Date.now();

    if (now - data.timestamp > maxAge) {
      localStorage.removeItem(key);
      return null;
    }

    return data.value;
  } catch (error) {
    console.warn("Erreur lors de la lecture du cache:", error);
    return null;
  }
};

/**
 * Mettre en cache les données dans localStorage
 * @param {string} key - Clé de cache
 * @param {*} value - Données à cacher
 */
export const setCachedData = (key, value) => {
  try {
    const data = {
      value,
      timestamp: Date.now(),
    };

    localStorage.setItem(key, JSON.stringify(data));
  } catch (error) {
    console.warn("Erreur lors de la mise en cache:", error);
  }
};

/**
 * Récupérer les données avec cache
 * @param {string} apiEndpoint - URL de l'API
 * @param {Object} options - Options
 * @returns {Promise<Object>} Données
 */
export const fetchGraphDataWithCache = async (apiEndpoint, options = {}) => {
  const cacheKey = `archi_graph_${apiEndpoint}_${JSON.stringify(options)}`;

  // Essayer de récupérer depuis le cache
  const cachedData = getCachedData(cacheKey);
  if (cachedData) {
    return cachedData;
  }

  // Récupérer depuis l'API
  const freshData = await fetchGraphData(apiEndpoint, options);

  // Mettre en cache
  setCachedData(cacheKey, freshData);

  return freshData;
};

/**
 * Précharger les images des nœuds
 * @param {Array} articles - Liste des articles
 * @returns {Promise<Array>} Promesses de chargement des images
 */
export const preloadImages = (articles) => {
  const imagePromises = articles.map((article) => {
    return new Promise((resolve, reject) => {
      if (!article.thumbnail) {
        resolve();
        return;
      }

      const img = new Image();
      img.onload = () => resolve();
      img.onerror = () => resolve(); // Ne pas bloquer sur une erreur d'image
      img.src = article.thumbnail;
    });
  });

  return Promise.all(imagePromises);
};

/**
 * Valider les données d'articles
 * @param {Array} articles - Données à valider
 * @returns {Array} Articles validés
 */
export const validateArticleData = (articles) => {
  if (!Array.isArray(articles)) {
    console.warn("Les données d'articles doivent être un tableau");
    return [];
  }

  return articles.filter((article) => {
    // Vérifications basiques
    if (!article.id || !article.title) {
      console.warn("Article invalide:", article);
      return false;
    }

    // Assurer que les catégories sont un tableau
    if (!Array.isArray(article.categories)) {
      article.categories = [];
    }

    // Assurer que les tags sont un tableau
    if (!Array.isArray(article.tags)) {
      article.tags = [];
    }

    // Valeurs par défaut pour les paramètres de base
    article.node_size = article.node_size || 80;
    article.node_color = article.node_color || "#3498db";
    article.priority_level = article.priority_level || "normal";

    // Valeurs par défaut pour les paramètres avancés
    if (!article.advanced_graph_params) {
      article.advanced_graph_params = {};
    }
    
    const params = article.advanced_graph_params;
    params.node_shape = params.node_shape || 'circle';
    params.node_icon = params.node_icon || '';
    params.visual_group = params.visual_group || '';
    params.node_opacity = params.node_opacity !== undefined ? params.node_opacity : 1.0;
    params.node_border = params.node_border || 'none';
    params.border_color = params.border_color || '';
    params.node_weight = params.node_weight || 1;
    params.hover_effect = params.hover_effect || 'zoom';
    params.entrance_animation = params.entrance_animation || 'fade';
    params.pin_node = params.pin_node || false;
    params.node_label = params.node_label || '';
    params.show_label = params.show_label || false;
    params.node_badge = params.node_badge || '';
    params.connection_depth = params.connection_depth || 2;
    params.link_strength = params.link_strength !== undefined ? params.link_strength : 1.0;
    params.link_style = params.link_style || 'curve';

    return true;
  });
};

/**
 * Exporter les données du graphique
 * @param {Array} articles - Données des articles
 * @param {Array} categories - Données des catégories
 * @returns {string} JSON stringifié
 */
export const exportGraphData = (articles, categories) => {
  const exportData = {
    timestamp: new Date().toISOString(),
    version: "1.0.0",
    articles: articles.map((article) => ({
      id: article.id,
      title: article.title,
      position: { x: article.x, y: article.y },
      categories: article.categories,
      node_color: article.node_color,
      node_size: article.node_size,
      priority_level: article.priority_level,
    })),
    categories: categories,
  };

  return JSON.stringify(exportData, null, 2);
};

/**
 * Import data from JSON
 */
export const importGraphData = (jsonData) => {
  try {
    const data = JSON.parse(jsonData);
    return {
      articles: data.articles || [],
      categories: data.categories || [],
      metadata: data.metadata || {},
    };
  } catch (error) {
    console.error("Error importing graph data:", error);
    return null;
  }
};
