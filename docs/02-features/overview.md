# Fonctionnalités - Thème Archi Graph

## Fonctionnalités Principales

### Visualisation de Graphique Interactif

La fonctionnalité principale est un graphique interactif propulsé par D3.js qui visualise les articles et leurs relations.

#### Caractéristiques du Graphique

- **Disposition force-directed** : Les articles s'organisent naturellement par catégorie
- **Regroupement dynamique** : Les articles se groupent par catégories partagées
- **Connexions basées sur la proximité** : Les articles liés sont connectés avec une force variable
- **Manipulation interactive** : Capacités de glisser, zoomer et panoramique
- **Filtrage en temps réel** : Filtrer par catégorie, termes de recherche
- **Positions persistantes** : Les positions des nœuds peuvent être sauvegardées

#### Système de Score de Relations

Les articles sont connectés en fonction de plusieurs facteurs :

| Facteur                  | Points          | Description                        |
| ------------------------ | --------------- | ---------------------------------- |
| Catégories partagées     | 40 par catégorie| Même affectation de catégorie      |
| Tags communs             | 25 par tag      | Tags partagés                      |
| Concordance catégorie principale | 20 | Même catégorie principale         |
| Proximité temporelle     | 0-10            | Publié à peu près au même moment   |
| Similarité du contenu    | 0-5             | Contenu textuel similaire          |

Le score total détermine la force et la visibilité du lien.

### Types de Publication Personnalisés

#### Projets Architecturaux (\`archi_project\`)

Type de publication personnalisé pour les projets architecturaux avec champs :

- **Surface** : Surface du projet en m²
- **Coût** : Budget estimé
- **Client** : Propriétaire/client du projet
- **Localisation** : Lieu géographique
- **Période** : Dates de début et de fin
- **Bureau Technique** : Consultants en ingénierie
- **Certifications** : Certifications de construction écologique

#### Illustrations Architecturales (\`archi_illustration\`)

Type de publication personnalisé pour les illustrations de projet :

- **Technique** : Technique de dessin/rendu
- **Dimensions** : Taille physique ou numérique
- **Logiciels** : Outils utilisés (AutoCAD, Revit, etc.)
- **Projet Lié** : Lien vers le projet associé
- **Type de Vue** : Plan, élévation, coupe, 3D

### Endpoints API REST

#### \`/wp-json/archi/v1/articles\`

Récupérer tous les articles configurés pour le graphique.

**Paramètres :**

- \`category\` (int) : Filtrer par ID de catégorie
- \`limit\` (int) : Nombre maximum d'articles à retourner
- \`post_type\` (string) : Filtrer par type de publication

**Réponse :**

\`\`\`json
{
  "articles": [
    {
      "id": 123,
      "title": "Titre de l'Article",
      "slug": "slug-article",
      "excerpt": "Brève description",
      "featured_image": "url",
      "categories": [...],
      "tags": [...],
      "graph_settings": {
        "visible": true,
        "color": "#3498db",
        "size": 50,
        "priority": "normal"
      }
    }
  ]
}
\`\`\`

#### \`/wp-json/archi/v1/categories\`

Récupérer toutes les catégories avec configuration de graphique.

**Réponse :**

\`\`\`json
{
  "categories": [
    {
      "id": 1,
      "name": "Architecture",
      "slug": "architecture",
      "color": "#e74c3c",
      "count": 15
    }
  ]
}
\`\`\`

### Blocs Gutenberg

Le thème inclut plus de 11 blocs personnalisés pour afficher du contenu lié à l'architecture et gérer les articles dans le graphique interactif.

Voir la [Documentation des Blocs](blocks.md) pour tous les détails.

### Outils d'Administration

#### Page de Paramètres du Graphique

**Emplacement :** Administration WordPress → Apparence → Paramètres du Graphique

Configurer le comportement global du graphique :

- **Dimensions du canevas** : Largeur et hauteur
- **Paramètres d'animation** : Durée, facilité
- **Simulation de force** : Force de charge, distance de lien
- **Paramètres visuels** : Tailles de nœuds, couleurs, opacité des liens
- **Performance** : Maximum d'articles, chargement différé

#### Outil de Diagnostic

**Emplacement :** Administration WordPress → Apparence → Diagnostic

Vérificateur de santé du système :

- **État de la configuration** : Articles, catégories, templates
- **Fonctionnalité de l'API** : Tester tous les endpoints
- **Métriques de performance** : Temps de chargement, nombre de requêtes
- **Générateur de données de test** : Créer des articles d'exemple

## Documentation Associée

- [Guide de Configuration](setup.md)
- [Référence des Blocs](blocks.md)
- [Référence API](api.md)
- [Journal des Modifications](changelog.md)
