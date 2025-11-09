# Archi Graph Template - Documentation

## 📋 Vue d'ensemble

![Theme Version](https://img.shields.io/badge/version-1.2.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-green)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/license-GPL%20v3-orange)

Un thème WordPress interactif avec une visualisation de graphique propulsée par D3.js qui affiche les articles et leurs relations basées sur les catégories, tags et similarité de contenu.

## ✨ Fonctionnalités

- **Visualisation de Graphique Interactif** - Graphique force-directed D3.js avec drag, zoom et panoramique
- **Relations Intelligentes** - Les articles se connectent selon les catégories, tags et similarité de contenu
- **Types de Publication Personnalisés** - Projets architecturaux et illustrations avec champs spécialisés
- **Blocs Gutenberg Complets** - 78+ blocs disponibles (60 WordPress Core + 18 personnalisés)
- **Éditeur Gutenberg Complet** - Tous les blocs WordPress Core autorisés pour une création de contenu flexible
- **API REST** - API complète pour les données du graphique et les relations d'articles
- **Outils d'Administration** - Panel de diagnostic et générateur de contenu de test
- **Design Responsive** - Optimisé pour mobile avec gestes tactiles
- **Performance** - Chargement différé, mise en cache et requêtes optimisées

### 🆕 Nouveautés v1.2.0

- ✅ **Tous les blocs WordPress Core** maintenant disponibles dans l'éditeur
- ✅ **78+ blocs au total** pour articles, projets et illustrations
- ✅ **Flexibilité maximale** pour créer du contenu riche (images, galeries, colonnes, embeds, etc.)
- ✅ **Template lock désactivé** permettant l'ajout libre de blocs

## 🚀 Démarrage Rapide

### Installation

1. Téléchargez et extrayez le thème dans `/wp-content/themes/`
2. Activez via **Administration WordPress → Apparence → Thèmes**
3. Lancez le diagnostic : **Apparence → 🔍 Diagnostic**
4. Créez du contenu de test ou configurez vos articles existants

### Configuration en 5 Minutes

\`\`\`bash
1. Activer le thème
2. Aller dans Apparence → Diagnostic
3. Cliquer sur "Créer des articles de test"
4. Visiter votre page d'accueil
5. Profiter du graphique interactif ! 🎉
\`\`\`

## 📚 Documentation

Une documentation complète est disponible dans le dossier \`docs/\` :

- **[Guide de Configuration](docs/setup.md)** - Installation, configuration et dépannage
- **[Fonctionnalités](docs/features.md)** - Référence complète des fonctionnalités
- **[Référence des Blocs](docs/blocks.md)** - Guide des blocs Gutenberg avec exemples
- **[Documentation API](docs/api.md)** - Référence de l'API REST et JavaScript
- **[Journal des Modifications](docs/changelog.md)** - Historique des versions et notes de mise à jour

## 🛠️ Outils de Développement

Des utilitaires de test, maintenance et débogage sont disponibles dans `utilities/` :

- **[Testing](utilities/README.md#-testing)** - Scripts de test HTML, PHP et shell
- **[Maintenance](utilities/README.md#-maintenance)** - Outils de flush cache et réparation
- **[Debug](utilities/README.md#-debug)** - Diagnostics et outils de débogage

**⚠️ Ces outils sont réservés au développement et ne doivent PAS être déployés en production.**

## 🎯 Concepts Fondamentaux

### Visualisation du Graphique

Les articles apparaissent comme des nœuds dans un graphique force-directed, automatiquement positionnés et regroupés par catégorie. Les connexions entre nœuds représentent les relations basées sur :

| Facteur                          | Poids      | Description                       |
| -------------------------------- | ---------- | --------------------------------- |
| Catégories Partagées             | 40 pts     | Même affectation de catégorie     |
| Tags Communs                     | 25 pts     | Tags partagés                     |
| Concordance Catégorie Principale | 20 pts     | Catégorie principale identique    |
| Proximité Temporelle             | 0-10 pts   | Publié à peu près au même moment  |
| Similarité du Contenu            | 0-5 pts    | Contenu similaire                 |

### Types de Publication Personnalisés

**Projets Architecturaux** (\`archi_project\`)
- Surface, coût, localisation
- Client, période, bureau technique
- Certifications, métadonnées du projet

**Illustrations Architecturales** (\`archi_illustration\`)
- Technique, dimensions, logiciels
- Association au projet lié
- Type de vue (plan, élévation, 3D, etc.)

### Blocs Gutenberg

Le thème inclut plus de 11 blocs personnalisés :

- **Gestionnaire d'Articles** - Afficher les informations et métadonnées d'articles
- **Graphique Interactif** - Intégrer la visualisation du graphique n'importe où
- **Vitrine de Projets** - Grille de projets en vedette
- **Filtre de Catégories** - Filtrage dynamique de contenu
- **Chronologie** - Vue chronologique des projets
- **Avant/Après** - Curseur de comparaison d'images
- Et plus encore...

Voir la [Documentation des Blocs](docs/blocks.md) pour les détails complets.

### API REST

API REST complète pour accéder aux données du graphique :

- \`/wp-json/archi/v1/articles\` - Récupérer les articles
- \`/wp-json/archi/v1/categories\` - Récupérer les catégories avec couleurs
- \`/wp-json/archi/v1/proximity-analysis\` - Analyser les relations
- \`/wp-json/archi/v1/related-articles/{id}\` - Récupérer le contenu lié
- \`/wp-json/archi/v1/save-positions\` - Sauvegarder les positions des nœuds

Voir la [Documentation API](docs/api.md) pour les endpoints et l'utilisation.

## 🛠️ Configuration

### Paramètres des Articles

Chaque article peut être configuré avec des paramètres de graphique via la méta-box :

\`\`\`
☑ Afficher dans le graphique
🎨 Couleur du nœud : [sélecteur de couleur]
�� Taille du nœud : [curseur 20-100px]
⭐ Priorité : [faible | normale | élevée]
\`\`\`

### Paramètres des Catégories

Chaque catégorie peut avoir :

- Couleur personnalisée pour l'organisation visuelle
- Description et métadonnées
- Icône optionnelle

### Paramètres du Graphique

Configuration via **Apparence → Paramètres du Graphique** :

- Dimensions du canevas (largeur/hauteur)
- Vitesse et facilité d'animation
- Paramètres de simulation de force
- Style visuel (couleurs, opacité)
- Options de performance

## 🔧 Détails Techniques

### Prérequis

- **WordPress** : 5.0 ou supérieur
- **PHP** : 7.4 ou supérieur
- **MySQL** : 5.6 ou supérieur
- **Navigateur** : Navigateur moderne avec support ES6

### Structure des Fichiers

\`\`\`
archi-graph-template/
├── docs/                    # Documentation
├── assets/                  # Ressources frontend
│   ├── css/                # Feuilles de style
│   ├── js/                 # JavaScript/React
│   │   ├── components/    # Composants React
│   │   ├── utils/         # Fonctions utilitaires
│   │   └── blocks/        # JavaScript des blocs Gutenberg
├── inc/                    # Includes PHP
│   ├── rest-api.php       # Endpoints API REST
│   ├── gutenberg-blocks.php # Enregistrement des blocs
│   ├── custom-post-types.php # Définitions CPT
│   ├── meta-boxes.php     # Méta-boxes admin
│   ├── diagnostic.php     # Outil de diagnostic
│   └── ...
├── template-parts/         # Partiels de template
├── functions.php           # Configuration du thème
├── front-page.php         # Template de page d'accueil
├── single.php             # Template d'article unique
└── style.css              # Feuille de style du thème
\`\`\`

### Développement

#### Compiler les Ressources

\`\`\`bash
# Installer les dépendances
npm install

# Build de développement avec surveillance
npm run dev

# Build de production (minifié)
npm run build
\`\`\`

#### Mode Debug

Activer dans \`wp-config.php\` :

\`\`\`php
define('ARCHI_DEBUG', true);
\`\`\`

Fournit la journalisation console, les métriques de performance et les détails de requête API.

## 🎨 Personnalisation

### Variables CSS

Personnaliser facilement l'apparence :

\`\`\`css
:root {
  --archi-primary-color: #3498db;
  --archi-secondary-color: #2ecc71;
  --archi-accent-color: #e74c3c;
  --archi-node-size: 50px;
  --archi-link-opacity: 0.6;
}
\`\`\`

### Hooks PHP

Étendre les fonctionnalités avec des hooks :

\`\`\`php
// Modifier le score de proximité
add_filter('archi_proximity_score', function($score, $post_a, $post_b) {
  // Logique personnalisée
  return $score;
}, 10, 3);

// Avant le rendu du graphique
add_action('archi_before_graph_render', function() {
  // Code personnalisé
});
\`\`\`

### Thème Enfant

Remplacer les templates dans un thème enfant :

\`\`\`
child-theme/
  archi-graph/
    templates/
      graph-homepage.php
      single-project.php
\`\`\`

## 🐛 Dépannage

### Le Graphique ne s'Affiche pas

1. Lancez **Apparence → Diagnostic**
2. Vérifiez que les articles sont configurés pour le graphique
3. Vérifiez la console JavaScript (F12) pour les erreurs
4. Videz le cache du site

### Graphique Vide

1. Assurez-vous que les articles ont "Afficher dans le graphique" coché
2. Essayez de créer des articles de test via l'outil de diagnostic
3. Vérifiez que les catégories sont affectées

### Erreurs API

1. Allez dans **Réglages → Permaliens**
2. Sélectionnez la structure "Nom de l'article"
3. Sauvegardez les paramètres pour vider les règles de réécriture

Voir le [Guide de Configuration](docs/setup.md) pour plus d'étapes de dépannage.

## 📖 Ressources d'Apprentissage

- [Codex WordPress](https://codex.wordpress.org/)
- [Documentation D3.js](https://d3js.org/)
- [Documentation React](https://react.dev/)
- [Manuel de l'Éditeur de Blocs Gutenberg](https://developer.wordpress.org/block-editor/)

## 🤝 Contribution

Les contributions sont les bienvenues ! Domaines d'intérêt :

- Améliorations de performance
- Corrections de bugs
- Améliorations de documentation
- Nouveaux designs de blocs
- Améliorations des traductions

## 📝 Licence

Ce thème est sous licence GPL v3 ou ultérieure.

\`\`\`
Copyright (C) 2025

Ce programme est un logiciel libre : vous pouvez le redistribuer et/ou le modifier
selon les termes de la GNU General Public License publiée par
la Free Software Foundation, soit la version 3 de la Licence, soit
(à votre choix) toute version ultérieure.
\`\`\`

## 🆘 Support

- **Documentation** : Consultez le dossier \`docs/\`
- **Outil de Diagnostic** : **Apparence → Diagnostic**
- **Journal de Debug** : \`wp-content/debug.log\` (si activé)
- **Console du Navigateur** : Appuyez sur F12 pour les messages d'erreur

## 🗺️ Feuille de Route

### Version 1.2.0 (Planifiée)

- Optimisations de performance pour de grands ensembles de données
- Mises en page de graphique supplémentaires
- Expérience mobile améliorée
- Plus d'options de personnalisation

### Version 1.3.0 (Planifiée)

- Bibliothèque de modèles de blocs
- Templates pré-conçus
- Import/export de configurations
- Tableau de bord d'analytique

## ⭐ Crédits

- **D3.js** - Bibliothèque de visualisation de données
- **React** - Bibliothèque de composants UI
- **WordPress** - Système de gestion de contenu

---

**Version Actuelle** : 1.1.0  
**Date de Sortie** : 14 octobre 2025  
**Statut** : Stable  

Fait avec ❤️ pour les professionnels de l'architecture et du design.
