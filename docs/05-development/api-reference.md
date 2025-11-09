# Référence API - Thème Archi Graph

## Endpoints API REST

URL de base : \`/wp-json/archi/v1/\`

### Authentification

La plupart des endpoints de lecture sont publics. Les endpoints d'écriture nécessitent une authentification :

\`\`\`javascript
// Inclure le nonce dans la requête
fetch("/wp-json/archi/v1/save-positions", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-WP-Nonce": archiGraph.nonce,
  },
  body: JSON.stringify(data),
});
\`\`\`

## Endpoints

### GET \`/articles\`

Récupérer les articles configurés pour le graphique.

#### Paramètres

| Paramètre   | Type   | Défaut | Description                        |
| ----------- | ------ | ------ | ---------------------------------- |
| \`category\`  | int    | -      | Filtrer par ID de catégorie        |
| \`limit\`     | int    | 100    | Maximum d'articles à retourner     |
| \`post_type\` | string | all    | Filtrer par type de publication    |
| \`offset\`    | int    | 0      | Décalage de pagination             |
| \`orderby\`   | string | date   | Champ de tri                       |
| \`order\`     | string | DESC   | Direction de tri                   |

#### Réponse

\`\`\`json
{
  "success": true,
  "articles": [
    {
      "id": 123,
      "title": "Immeuble de Bureaux Moderne",
      "slug": "immeuble-bureaux-moderne",
      "excerpt": "Une conception d'espace de travail contemporain...",
      "featured_image": "https://example.com/image.jpg",
      "categories": [
        {
          "id": 5,
          "name": "Commercial",
          "slug": "commercial",
          "color": "#3498db"
        }
      ],
      "tags": [...],
      "graph_settings": {
        "visible": true,
        "color": "#3498db",
        "size": 60,
        "priority": "high"
      }
    }
  ],
  "total": 42,
  "pages": 1
}
\`\`\`

### GET \`/categories\`

Récupérer toutes les catégories avec configuration du graphique.

#### Paramètres

| Paramètre    | Type  | Défaut | Description                            |
| ------------ | ----- | ------ | -------------------------------------- |
| \`hide_empty\` | bool  | true   | Masquer les catégories sans articles   |
| \`include\`    | array | -      | IDs de catégories spécifiques à inclure|

#### Réponse

\`\`\`json
{
  "success": true,
  "categories": [
    {
      "id": 5,
      "name": "Commercial",
      "slug": "commercial",
      "description": "Bâtiments et espaces commerciaux",
      "color": "#3498db",
      "count": 15,
      "parent": 0
    }
  ]
}
\`\`\`

### GET \`/proximity-analysis\`

Analyser les relations entre articles en fonction des attributs partagés.

#### Paramètres

| Paramètre   | Type   | Défaut | Description                             |
| ----------- | ------ | ------ | --------------------------------------- |
| \`min_score\` | int    | 10     | Score de proximité minimum à inclure    |
| \`post_type\` | string | all    | Filtrer par type de publication         |

#### Réponse

\`\`\`json
{
  "success": true,
  "connections": [
    {
      "source": 123,
      "target": 456,
      "strength": 85,
      "factors": {
        "shared_categories": 40,
        "common_tags": 25,
        "main_category_match": 20,
        "temporal_proximity": 8,
        "content_similarity": 3
      }
    }
  ],
  "total_connections": 156,
  "avg_strength": 47.2
}
\`\`\`

### GET \`/related-articles/{id}\`

Récupérer les articles liés à un article spécifique.

#### Paramètres

| Paramètre   | Type | Défaut | Description                        |
| ----------- | ---- | ------ | ---------------------------------- |
| \`limit\`     | int  | 5      | Maximum d'articles liés            |
| \`min_score\` | int  | 20     | Score de relation minimum          |

#### Réponse

\`\`\`json
{
  "success": true,
  "article_id": 123,
  "related": [
    {
      "id": 456,
      "title": "Conception de Bureau Écologique",
      "score": 85,
      "excerpt": "Un autre projet durable...",
      "featured_image": "https://...",
      "shared_categories": ["Commercial", "Durable"]
    }
  ]
}
\`\`\`

### POST \`/save-positions\`

Sauvegarder les positions des nœuds du graphique.

**Authentification Requise**

#### Corps de la Requête

\`\`\`json
{
  "positions": {
    "123": { "x": 150, "y": 200 },
    "456": { "x": 350, "y": 180 }
  }
}
\`\`\`

#### Réponse

\`\`\`json
{
  "success": true,
  "message": "Positions sauvegardées avec succès",
  "saved_count": 2
}
\`\`\`

## API JavaScript

### Objet Global

Le thème expose un objet global \`ArchiGraph\` :

\`\`\`javascript
// Configuration passée depuis PHP
window.archiGraph = {
  apiUrl: '/wp-json/archi/v1/',
  nonce: 'abc123...',
  settings: {...}
};
\`\`\`

### Instance du Graphique

Initialiser un graphique :

\`\`\`javascript
const graph = new ArchiGraph("graph-container", {
  width: 1200,
  height: 800,
  categories: [1, 2, 3],
  postTypes: ["post", "archi_project"],
  maxArticles: 50,
});

graph.init();
\`\`\`

#### Méthodes

##### \`graph.loadArticles(params)\`

Charger les articles depuis l'API.

\`\`\`javascript
graph.loadArticles({
  category: 5,
  limit: 20,
}).then((articles) => {
  console.log("Chargés:", articles);
});
\`\`\`

##### \`graph.filterByCategory(categoryId)\`

Filtrer les articles affichés par catégorie.

\`\`\`javascript
graph.filterByCategory(5); // Afficher uniquement la catégorie 5
graph.filterByCategory(null); // Afficher tous
\`\`\`

##### \`graph.search(query)\`

Rechercher des articles par terme.

\`\`\`javascript
graph.search("durable"); // Filtrer par terme de recherche
graph.search(""); // Effacer la recherche
\`\`\`

##### \`graph.savePositions()\`

Sauvegarder les positions actuelles des nœuds.

\`\`\`javascript
graph.savePositions().then((response) => {
  console.log("Positions sauvegardées");
});
\`\`\`

##### \`graph.zoom(level)\`

Définir le niveau de zoom.

\`\`\`javascript
graph.zoom(1.5); // Zoom avant
graph.zoom(0.5); // Zoom arrière
graph.zoom(1); // Réinitialiser
\`\`\`

### Événements

Le graphique émet des événements personnalisés :

\`\`\`javascript
// Nœud cliqué
document.addEventListener("archi:node:click", (e) => {
  console.log("Nœud cliqué:", e.detail);
});

// Survol de nœud
document.addEventListener("archi:node:hover", (e) => {
  console.log("Nœud survolé:", e.detail);
});

// Filtre de catégorie changé
document.addEventListener("archi:filter:change", (e) => {
  console.log("Filtre:", e.detail.category);
});

// Positions sauvegardées
document.addEventListener("archi:positions:saved", (e) => {
  console.log("Sauvegardées:", e.detail.count);
});
\`\`\`

## API PHP

### Fonctions

#### \`archi_get_graph_articles($args)\`

Récupérer les articles pour le graphique.

\`\`\`php
$articles = archi_get_graph_articles([
  'category' => 5,
  'limit' => 20,
  'post_type' => 'archi_project'
]);
\`\`\`

#### \`archi_calculate_proximity($post_a, $post_b)\`

Calculer le score de proximité entre deux articles.

\`\`\`php
$score = archi_calculate_proximity(123, 456);
// Retourne: 85
\`\`\`

#### \`archi_get_related_articles($post_id, $limit)\`

Récupérer les articles liés.

\`\`\`php
$related = archi_get_related_articles(123, 5);
\`\`\`

#### \`archi_is_graph_visible($post_id)\`

Vérifier si l'article est visible dans le graphique.

\`\`\`php
if (archi_is_graph_visible(123)) {
  // L'article est visible
}
\`\`\`

### Filtres

Modifier le comportement avec des filtres :

\`\`\`php
// Modifier le score de proximité
add_filter('archi_proximity_score', function($score, $post_a, $post_b) {
  // Logique de score personnalisée
  return $score;
}, 10, 3);

// Modifier les articles du graphique
add_filter('archi_graph_articles', function($articles) {
  // Filtrer ou modifier les articles
  return $articles;
});

// Modifier les paramètres du graphique
add_filter('archi_graph_settings', function($settings) {
  $settings['canvas']['width'] = 1400;
  return $settings;
});
\`\`\`

### Actions

Se connecter aux événements :

\`\`\`php
// Avant le rendu du graphique
add_action('archi_before_graph_render', function() {
  // Logique personnalisée
});

// Après le chargement des articles
add_action('archi_articles_loaded', function($articles) {
  // Traiter les articles
});

// Quand les positions sont sauvegardées
add_action('archi_positions_saved', function($positions) {
  // Journaliser ou traiter les positions sauvegardées
});
\`\`\`

## Limitation de Taux

Les endpoints de l'API REST sont limités en taux :

- **Endpoints de lecture** : 100 requêtes par minute par IP
- **Endpoints d'écriture** : 20 requêtes par minute par utilisateur

Le dépassement des limites retourne HTTP 429.

## Réponses d'Erreur

Format d'erreur standard :

\`\`\`json
{
  "success": false,
  "code": "error_code",
  "message": "Message d'erreur lisible",
  "data": {
    "status": 400
  }
}
\`\`\`

Codes d'erreur courants :

- \`rest_no_route\` : Endpoint introuvable (404)
- \`rest_forbidden\` : Permission refusée (403)
- \`rest_invalid_param\` : Paramètre invalide (400)
- \`rate_limit_exceeded\` : Trop de requêtes (429)

## Documentation Associée

- [Guide de Configuration](setup.md)
- [Fonctionnalités](features.md)
- [Référence des Blocs](blocks.md)
