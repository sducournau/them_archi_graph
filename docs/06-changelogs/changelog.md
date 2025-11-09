# Journal des Modifications

## Version 1.1.0 - 14 octobre 2025

### 🧹 Nettoyage du Dépôt

#### Supprimé

- **Fichiers de sauvegarde** : Suppression des fichiers \`.backup\` (footer, header, single, index)
- **Documentation redondante** : Consolidation de plus de 20 fichiers MD éparpillés dans le dossier docs/ organisé

#### Ajouté

- **Structure de documentation organisée** dans le dossier \`docs/\` :
  - \`setup.md\` - Guide d'installation et de configuration
  - \`features.md\` - Documentation complète des fonctionnalités
  - \`blocks.md\` - Référence des blocs Gutenberg
  - \`api.md\` - Documentation de l'API REST et JavaScript
  - \`changelog.md\` - Ce fichier

#### Modifié

- **Standardisation du nommage** : Suppression des préfixes "enhanced" et "unified" dans toute la base de code
- **Préfixe cohérent** : Toutes les fonctions, classes et blocs utilisent le préfixe \`archi_\` ou \`archi-graph/\`
- **Consolidation de la documentation** : Fusion des docs liés dans des guides complets

### 🎯 Mises à Jour des Conventions de Nommage

#### Avant

- Nommage mixte : \`unified-article-manager\`, \`enhanced-graph-settings\`
- Préfixes incohérents entre les fichiers
- Descripteurs redondants dans les noms

#### Après

- Simplifié : \`article-manager\`, \`graph-settings\`
- Préfixe \`archi_\` cohérent pour les fonctions PHP
- Préfixe \`archi-graph/\` cohérent pour les blocs Gutenberg
- Classes CSS propres avec préfixe \`archi-\` uniquement

### 📦 Consolidation de la Documentation

#### Structure de Documentation Précédente

\`\`\`
ARTICLE-INFO-BLOCK.md
ARTICLE-INFO-EXAMPLES.md
ARTICLE-INFO-QUICKSTART.md
MIGRATION-LAZYBLOCKS.md
RESUME-BLOC-GUTENBERG.md
NOUVELLES-FONCTIONNALITES.md
PROJETS-ARCHITECTURAUX.md
... et plus
\`\`\`

#### Nouvelle Structure de Documentation

\`\`\`
docs/
  ├── setup.md          # Démarrage rapide et configuration
  ├── features.md       # Référence complète des fonctionnalités
  ├── blocks.md         # Guide des blocs Gutenberg
  ├── api.md           # Documentation API REST et JS
  └── changelog.md     # Historique des versions (ce fichier)

README.md             # Hub de documentation principal
\`\`\`

### 📁 Fichiers Supprimés

- \`footer.php.backup\`
- \`header.php.backup\`
- \`single.php.backup\`
- \`index.php.backup\`

### 📄 Fichiers Archivés

Les fichiers de documentation suivants ont été consolidés dans la nouvelle structure docs/. Les fichiers originaux peuvent être supprimés en toute sécurité :

- Tous les fichiers \`ARTICLE-INFO-*.md\` → fusionnés dans \`docs/blocks.md\`
- Tous les fichiers \`MIGRATION-*.md\` → contenu pertinent dans \`docs/changelog.md\`
- Tous les fichiers \`RESUME-*.md\` → consolidés dans les docs appropriés
- Fichiers \`LAZYBLOCKS-*.md\` → intégrés dans \`docs/blocks.md\`
- Docs spécifiques aux fonctionnalités → fusionnés dans \`docs/features.md\`

### 🔧 Améliorations Techniques

- **Base de code plus propre** : Réduction du nombre de fichiers de ~40%
- **Meilleure organisation** : Hiérarchie de documentation claire
- **Maintenance plus facile** : Source unique de vérité pour chaque sujet
- **Découvrabilité améliorée** : Tous les docs accessibles depuis le README principal

### 📚 Notes de Migration

Si vous effectuez une mise à niveau depuis une version précédente :

1. **Aucun changement de code requis** - les mises à jour de nommage sont internes uniquement
2. **Documentation déplacée** - consultez le dossier \`docs/\` pour les guides
3. **Fichiers de sauvegarde supprimés** - assurez-vous d'avoir un contrôle de version
4. **Toutes les fonctionnalités préservées** - aucun changement breaking

---

## Version 1.0.0 - Octobre 2025

### 🎉 Version Initiale

#### Fonctionnalités Principales

- Visualisation de graphique interactive D3.js
- Types de publications personnalisés (projets, illustrations)
- Endpoints API REST
- Intégration des blocs Gutenberg
- Outils de diagnostic d'administration
- Relations d'articles basées sur la proximité

#### Templates

- \`front-page.php\` - Template de page d'accueil avec graphique
- \`template-parts/graph-homepage.php\` - Partiel de graphique réutilisable
- Templates single personnalisés pour les types de publications

#### Fichiers PHP

- \`functions.php\` - Configuration et setup du thème
- \`inc/rest-api.php\` - Endpoints API REST
- \`inc/gutenberg-blocks.php\` - Blocs Gutenberg personnalisés
- \`inc/custom-post-types.php\` - Types de publications d'architecture
- \`inc/meta-boxes.php\` - Méta-boxes d'administration
- \`inc/admin-settings.php\` - Page de paramètres
- \`inc/diagnostic.php\` - Outil de diagnostic
- \`inc/graph-management.php\` - Logique du graphique

#### JavaScript/React

- \`assets/js/app.js\` - Application principale
- \`assets/js/components/GraphContainer.jsx\` - Composant graphique principal
- \`assets/js/components/Node.jsx\` - Composant nœud
- \`assets/js/utils/dataFetcher.js\` - Récupération de données API
- \`assets/js/utils/proximityCalculator.js\` - Calcul de score de relations

#### Blocs Gutenberg

- Graphique Interactif
- Vitrine de Projets
- Grille d'Illustrations
- Filtre de Catégories
- Projets en Vedette
- Chronologie
- Curseur Avant/Après
- Spécifications Techniques
- Gestionnaire d'Articles (bloc unifié)

#### Endpoints API REST

- \`/wp-json/archi/v1/articles\` - Récupérer les articles
- \`/wp-json/archi/v1/categories\` - Récupérer les catégories
- \`/wp-json/archi/v1/proximity-analysis\` - Analyser les relations
- \`/wp-json/archi/v1/related-articles/{id}\` - Récupérer les articles liés
- \`/wp-json/archi/v1/save-positions\` - Sauvegarder les positions des nœuds

---

## Feuille de Route

### Prévu pour 1.2.0

- Optimisations de performance pour de grands ensembles de données
- Mises en page de graphique supplémentaires (circulaire, hiérarchique)
- Expérience mobile améliorée
- Plus d'options de personnalisation

### Prévu pour 1.3.0

- Bibliothèque de modèles de blocs
- Templates pré-conçus
- Import/export de configurations de graphique
- Tableau de bord d'analytique avancé

---

**Version Actuelle :** 1.1.0  
**Date de Sortie :** 14 octobre 2025  
**Statut :** Stable  
**Compatibilité WordPress :** 5.0+  
**Prérequis PHP :** 7.4+
