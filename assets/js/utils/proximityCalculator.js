/**
 * Calculateur de score de proximité entre les articles
 * Basé sur les catégories, étiquettes (tags), et autres facteurs
 */

/**
 * Poids des différents facteurs de proximité
 */
const PROXIMITY_WEIGHTS = {
  SHARED_CATEGORY: 40, // Catégorie commune
  SHARED_TAG: 25, // Tag commun
  SAME_PRIMARY_CATEGORY: 20, // Même catégorie principale
  DATE_PROXIMITY: 10, // Articles publiés à des dates proches
  CONTENT_LENGTH_SIMILARITY: 5, // Longueur de contenu similaire
  SAME_POST_TYPE: 15, // Même type de post (projet/illustration/post)
  PROJECT_ILLUSTRATION_LINK: 50, // Lien direct illustration->projet
  CUSTOM_RELATIONSHIP: 30, // Relation personnalisée définie manuellement
  SOFTWARE_MATCH: 20, // Même logiciel utilisé (pour illustrations)
  TECHNIQUE_MATCH: 20, // Même technique (pour illustrations)
};

/**
 * Calcule le score de proximité entre deux articles
 * @param {Object} articleA - Premier article
 * @param {Object} articleB - Deuxième article
 * @returns {Object} Score et détails de la proximité
 */
export const calculateProximityScore = (articleA, articleB) => {
  let score = 0;
  const details = {
    sharedCategories: [],
    sharedTags: [],
    samePrimaryCategory: false,
    dateProximityDays: null,
    factors: {},
  };

  // 1. Catégories partagées
  const sharedCategories = findSharedItems(
    articleA.categories,
    articleB.categories,
    "id"
  );
  if (sharedCategories.length > 0) {
    const categoryScore =
      PROXIMITY_WEIGHTS.SHARED_CATEGORY * sharedCategories.length;
    score += categoryScore;
    details.sharedCategories = sharedCategories;
    details.factors.categories = {
      count: sharedCategories.length,
      score: categoryScore,
    };
  }

  // 2. Catégorie principale identique (bonus)
  if (
    articleA.categories.length > 0 &&
    articleB.categories.length > 0 &&
    articleA.categories[0].id === articleB.categories[0].id
  ) {
    score += PROXIMITY_WEIGHTS.SAME_PRIMARY_CATEGORY;
    details.samePrimaryCategory = true;
    details.factors.primaryCategory = PROXIMITY_WEIGHTS.SAME_PRIMARY_CATEGORY;
  }

  // 3. Tags partagés (étiquettes)
  const sharedTags = findSharedItems(articleA.tags, articleB.tags, "id");
  if (sharedTags.length > 0) {
    const tagScore = PROXIMITY_WEIGHTS.SHARED_TAG * sharedTags.length;
    score += tagScore;
    details.sharedTags = sharedTags;
    details.factors.tags = {
      count: sharedTags.length,
      score: tagScore,
    };
  }

  // 4. Proximité temporelle (articles publiés à des dates proches)
  if (articleA.date && articleB.date) {
    const dateA = new Date(articleA.date);
    const dateB = new Date(articleB.date);
    const daysDiff = Math.abs((dateA - dateB) / (1000 * 60 * 60 * 24));

    details.dateProximityDays = Math.round(daysDiff);

    // Score diminue avec le temps (max score si moins de 7 jours)
    if (daysDiff <= 7) {
      const dateScore = PROXIMITY_WEIGHTS.DATE_PROXIMITY;
      score += dateScore;
      details.factors.dateProximity = dateScore;
    } else if (daysDiff <= 30) {
      const dateScore = PROXIMITY_WEIGHTS.DATE_PROXIMITY * 0.5;
      score += dateScore;
      details.factors.dateProximity = dateScore;
    }
  }

  // 5. Similarité de longueur de contenu
  if (articleA.excerpt && articleB.excerpt) {
    const lengthA = articleA.excerpt.length;
    const lengthB = articleB.excerpt.length;
    const lengthRatio = Math.min(lengthA, lengthB) / Math.max(lengthA, lengthB);

    // Si les longueurs sont similaires (ratio > 0.7)
    if (lengthRatio > 0.7) {
      const lengthScore = PROXIMITY_WEIGHTS.CONTENT_LENGTH_SIMILARITY;
      score += lengthScore;
      details.factors.contentSimilarity = lengthScore;
    }
  }

  // 6. Même type de post (bonus pour articles du même type)
  if (articleA.post_type && articleB.post_type && 
      articleA.post_type === articleB.post_type) {
    score += PROXIMITY_WEIGHTS.SAME_POST_TYPE;
    details.factors.samePostType = PROXIMITY_WEIGHTS.SAME_POST_TYPE;
  }

  // 7. Lien direct illustration -> projet (relation spéciale)
  if (articleA.post_type === 'archi_illustration' && 
      articleB.post_type === 'archi_project' &&
      articleA.illustration_meta?.project_link) {
    // Extraire l'ID du projet depuis le lien
    const projectIdFromLink = extractProjectIdFromUrl(articleA.illustration_meta.project_link);
    if (projectIdFromLink === articleB.id) {
      score += PROXIMITY_WEIGHTS.PROJECT_ILLUSTRATION_LINK;
      details.factors.illustrationToProject = PROXIMITY_WEIGHTS.PROJECT_ILLUSTRATION_LINK;
      details.directLink = true;
    }
  }
  
  // Inverse: projet -> illustration
  if (articleB.post_type === 'archi_illustration' && 
      articleA.post_type === 'archi_project' &&
      articleB.illustration_meta?.project_link) {
    const projectIdFromLink = extractProjectIdFromUrl(articleB.illustration_meta.project_link);
    if (projectIdFromLink === articleA.id) {
      score += PROXIMITY_WEIGHTS.PROJECT_ILLUSTRATION_LINK;
      details.factors.projectToIllustration = PROXIMITY_WEIGHTS.PROJECT_ILLUSTRATION_LINK;
      details.directLink = true;
    }
  }

  // 8. Même logiciel utilisé (pour illustrations)
  if (articleA.post_type === 'archi_illustration' && 
      articleB.post_type === 'archi_illustration' &&
      articleA.illustration_meta?.software && 
      articleB.illustration_meta?.software) {
    const softwareA = articleA.illustration_meta.software.toLowerCase();
    const softwareB = articleB.illustration_meta.software.toLowerCase();
    
    // Vérifier si les logiciels contiennent des mots communs
    const commonSoftware = findCommonWords(softwareA, softwareB);
    if (commonSoftware.length > 0) {
      score += PROXIMITY_WEIGHTS.SOFTWARE_MATCH;
      details.factors.softwareMatch = {
        score: PROXIMITY_WEIGHTS.SOFTWARE_MATCH,
        common: commonSoftware
      };
    }
  }

  // 9. Même technique utilisée (pour illustrations)
  if (articleA.post_type === 'archi_illustration' && 
      articleB.post_type === 'archi_illustration' &&
      articleA.illustration_meta?.technique && 
      articleB.illustration_meta?.technique) {
    const techniqueA = articleA.illustration_meta.technique.toLowerCase();
    const techniqueB = articleB.illustration_meta.technique.toLowerCase();
    
    if (techniqueA === techniqueB || 
        techniqueA.includes(techniqueB) || 
        techniqueB.includes(techniqueA)) {
      score += PROXIMITY_WEIGHTS.TECHNIQUE_MATCH;
      details.factors.techniqueMatch = PROXIMITY_WEIGHTS.TECHNIQUE_MATCH;
    }
  }

  // 10. Relations personnalisées (si définies dans les métadonnées)
  if (articleA.related_articles && Array.isArray(articleA.related_articles)) {
    if (articleA.related_articles.includes(articleB.id)) {
      score += PROXIMITY_WEIGHTS.CUSTOM_RELATIONSHIP;
      details.factors.customRelationship = PROXIMITY_WEIGHTS.CUSTOM_RELATIONSHIP;
      details.customRelation = true;
    }
  }

  return {
    score: Math.round(score),
    maxPossibleScore: calculateMaxPossibleScore(articleA, articleB),
    normalizedScore: 0, // Calculé après
    strength: getStrengthCategory(score),
    details,
  };
};

/**
 * Extrait l'ID d'un projet depuis une URL
 * @param {string} url - URL du projet
 * @returns {number|null} ID du projet ou null
 */
const extractProjectIdFromUrl = (url) => {
  if (!url) return null;
  
  // Essayer de trouver un paramètre ?p=123
  const pMatch = url.match(/[?&]p=(\d+)/);
  if (pMatch) return parseInt(pMatch[1], 10);
  
  // Essayer de trouver /projet/123
  const pathMatch = url.match(/\/projet\/(\d+)/);
  if (pathMatch) return parseInt(pathMatch[1], 10);
  
  return null;
};

/**
 * Trouve les mots communs entre deux chaînes
 * @param {string} strA - Première chaîne
 * @param {string} strB - Deuxième chaîne
 * @returns {Array} Mots communs
 */
const findCommonWords = (strA, strB) => {
  const wordsA = strA.split(/[\s,;]+/).filter(w => w.length > 2);
  const wordsB = strB.split(/[\s,;]+/).filter(w => w.length > 2);
  
  return wordsA.filter(wordA => 
    wordsB.some(wordB => 
      wordA === wordB || 
      wordA.includes(wordB) || 
      wordB.includes(wordA)
    )
  );
};

/**
 * Trouve les éléments partagés entre deux tableaux
 * @param {Array} arrayA - Premier tableau
 * @param {Array} arrayB - Deuxième tableau
 * @param {string} key - Clé pour comparer les objets
 * @returns {Array} Éléments partagés
 */
const findSharedItems = (arrayA, arrayB, key) => {
  if (!arrayA || !arrayB) return [];

  const idsB = new Set(arrayB.map((item) => item[key]));
  return arrayA.filter((item) => idsB.has(item[key]));
};

/**
 * Calcule le score maximum possible entre deux articles
 * @param {Object} articleA - Premier article
 * @param {Object} articleB - Deuxième article
 * @returns {number} Score maximum possible
 */
const calculateMaxPossibleScore = (articleA, articleB) => {
  let maxScore = 0;

  // Max pour les catégories
  const maxSharedCategories = Math.min(
    articleA.categories?.length || 0,
    articleB.categories?.length || 0
  );
  maxScore += PROXIMITY_WEIGHTS.SHARED_CATEGORY * maxSharedCategories;

  // Max pour les tags
  const maxSharedTags = Math.min(
    articleA.tags?.length || 0,
    articleB.tags?.length || 0
  );
  maxScore += PROXIMITY_WEIGHTS.SHARED_TAG * maxSharedTags;

  // Bonus catégorie principale
  maxScore += PROXIMITY_WEIGHTS.SAME_PRIMARY_CATEGORY;

  // Max pour proximité temporelle
  maxScore += PROXIMITY_WEIGHTS.DATE_PROXIMITY;

  // Max pour similarité de contenu
  maxScore += PROXIMITY_WEIGHTS.CONTENT_LENGTH_SIMILARITY;

  return maxScore;
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
 * Calcule tous les liens de proximité entre tous les articles
 * @param {Array} articles - Liste des articles
 * @param {Object} options - Options de filtrage
 * @returns {Array} Liste des liens avec scores
 */
export const calculateAllProximityLinks = (articles, options = {}) => {
  const {
    minScore = 20, // Score minimum pour créer un lien
    maxLinksPerNode = 10, // Nombre maximum de liens par nœud
    includeWeakLinks = true, // Inclure les liens faibles
  } = options;

  const links = [];
  const linksPerNode = new Map();

  // Initialiser le compteur de liens par nœud
  articles.forEach((article) => {
    linksPerNode.set(article.id, []);
  });

  // Calculer les scores pour toutes les paires
  for (let i = 0; i < articles.length; i++) {
    for (let j = i + 1; j < articles.length; j++) {
      const articleA = articles[i];
      const articleB = articles[j];

      const proximity = calculateProximityScore(articleA, articleB);

      // Calculer le score normalisé (0-100)
      proximity.normalizedScore = Math.round(
        (proximity.score / proximity.maxPossibleScore) * 100
      );

      // Filtrer selon le score minimum
      if (proximity.score >= minScore) {
        const link = {
          source: articleA,
          target: articleB,
          proximity,
          id: `${articleA.id}-${articleB.id}`,
        };

        links.push(link);

        // Ajouter aux liens des nœuds
        linksPerNode.get(articleA.id).push(link);
        linksPerNode.get(articleB.id).push(link);
      }
    }
  }

  // Limiter le nombre de liens par nœud (garder les plus forts)
  if (maxLinksPerNode > 0) {
    const filteredLinks = new Set();

    articles.forEach((article) => {
      const nodeLinks = linksPerNode.get(article.id);

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
 * Obtient les articles les plus proches d'un article donné
 * @param {Object} article - Article de référence
 * @param {Array} allArticles - Tous les articles
 * @param {number} limit - Nombre d'articles à retourner
 * @returns {Array} Articles les plus proches avec leurs scores
 */
export const getClosestArticles = (article, allArticles, limit = 5) => {
  const proximities = allArticles
    .filter((a) => a.id !== article.id)
    .map((otherArticle) => ({
      article: otherArticle,
      proximity: calculateProximityScore(article, otherArticle),
    }))
    .sort((a, b) => b.proximity.score - a.proximity.score);

  return proximities.slice(0, limit);
};

/**
 * Analyse la distribution des scores de proximité
 * @param {Array} articles - Liste des articles
 * @returns {Object} Statistiques de proximité
 */
export const analyzeProximityDistribution = (articles) => {
  const links = calculateAllProximityLinks(articles, {
    minScore: 0,
    maxLinksPerNode: 0,
  });

  const scores = links.map((link) => link.proximity.score);
  const normalizedScores = links.map((link) => link.proximity.normalizedScore);

  const stats = {
    totalLinks: links.length,
    averageScore: average(scores),
    medianScore: median(scores),
    maxScore: Math.max(...scores),
    minScore: Math.min(...scores),
    averageNormalizedScore: average(normalizedScores),
    distribution: {
      veryStrong: links.filter((l) => l.proximity.strength === "very-strong")
        .length,
      strong: links.filter((l) => l.proximity.strength === "strong").length,
      medium: links.filter((l) => l.proximity.strength === "medium").length,
      weak: links.filter((l) => l.proximity.strength === "weak").length,
      veryWeak: links.filter((l) => l.proximity.strength === "very-weak")
        .length,
    },
    topFactors: analyzeTopFactors(links),
  };

  return stats;
};

/**
 * Analyse les facteurs les plus influents
 * @param {Array} links - Liste des liens
 * @returns {Object} Facteurs principaux
 */
const analyzeTopFactors = (links) => {
  const factorCounts = {
    categories: 0,
    tags: 0,
    primaryCategory: 0,
    dateProximity: 0,
    contentSimilarity: 0,
  };

  const factorScores = {
    categories: 0,
    tags: 0,
    primaryCategory: 0,
    dateProximity: 0,
    contentSimilarity: 0,
  };

  links.forEach((link) => {
    const { factors } = link.proximity.details;

    Object.keys(factors).forEach((key) => {
      if (factorCounts[key] !== undefined) {
        factorCounts[key]++;
        factorScores[key] +=
          typeof factors[key] === "object" ? factors[key].score : factors[key];
      }
    });
  });

  return {
    counts: factorCounts,
    averageScores: Object.keys(factorScores).reduce((acc, key) => {
      acc[key] =
        factorCounts[key] > 0
          ? Math.round(factorScores[key] / factorCounts[key])
          : 0;
      return acc;
    }, {}),
  };
};

/**
 * Utilitaires mathématiques
 */
const average = (arr) =>
  arr.length > 0 ? arr.reduce((sum, val) => sum + val, 0) / arr.length : 0;

const median = (arr) => {
  if (arr.length === 0) return 0;
  const sorted = [...arr].sort((a, b) => a - b);
  const mid = Math.floor(sorted.length / 2);
  return sorted.length % 2 === 0
    ? (sorted[mid - 1] + sorted[mid]) / 2
    : sorted[mid];
};

/**
 * Crée une matrice de proximité entre tous les articles
 * @param {Array} articles - Liste des articles
 * @returns {Map} Matrice (Map de Maps) avec les scores
 */
export const createProximityMatrix = (articles) => {
  const matrix = new Map();

  articles.forEach((articleA) => {
    const row = new Map();

    articles.forEach((articleB) => {
      if (articleA.id === articleB.id) {
        row.set(articleB.id, { score: 100, isSelf: true });
      } else {
        const proximity = calculateProximityScore(articleA, articleB);
        row.set(articleB.id, proximity);
      }
    });

    matrix.set(articleA.id, row);
  });

  return matrix;
};

export default {
  calculateProximityScore,
  calculateAllProximityLinks,
  getClosestArticles,
  analyzeProximityDistribution,
  createProximityMatrix,
};
