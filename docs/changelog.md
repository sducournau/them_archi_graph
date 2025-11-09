# Journal des Modifications

## Version 1.2.0 - Janvier 2025

### 🎨 Intégration complète du WordPress Customizer

#### Ajouté

- **WordPress Customizer API** : Interface utilisateur complète pour la personnalisation du thème
  - **6 sections** : Header, Graph, Typography, Colors, Social Media, Footer
  - **20+ paramètres personnalisables** avec aperçu en temps réel
  - **Live preview** : Changements instantanés sans rechargement de page
  
- **Options du Header** (`archi_header_options`)
  - Temps avant disparition du header (0-5000ms, défaut: 500ms)
  - Type d'animation (6 options: linear, ease, ease-in, ease-out, ease-in-out, cubic-bezier)
  - Durée de l'animation (0.1-2s, défaut: 0.3s)
  - Hauteur de la zone de déclenchement (20-150px, défaut: 50px)
  
- **Options du Graphique** (`archi_graph_options`)
  - Couleur par défaut des nœuds (défaut: #3498db)
  - Taille par défaut des nœuds (40-120px, défaut: 60px)
  - Force de clustering (0-1, défaut: 0.3)
  - Durée des animations (500-5000ms, défaut: 1500ms)
  
- **Typographie** (`archi_typography`)
  - Famille de police (système, Google Fonts, personnalisée)
  - Taille de base (12-24px, défaut: 16px)
  
- **Couleurs** (`archi_colors`)
  - Couleur primaire (défaut: #3498db)
  - Couleur secondaire (défaut: #2ecc71)
  
- **Réseaux sociaux** (`archi_social_media`)
  - URLs pour Facebook, Twitter, Instagram, LinkedIn, YouTube, GitHub
  
- **Pied de page** (`archi_footer`)
  - Texte de copyright personnalisable
  - Affichage/masquage des liens sociaux

#### Fichiers ajoutés

- **inc/customizer.php** : Enregistrement des paramètres et sections du Customizer
  - `archi_customize_register()` : Enregistrement de tous les settings/controls
  - `archi_customizer_css()` : Génération CSS dynamique
  - Fonctions de sanitization : `archi_sanitize_float()`, `archi_sanitize_checkbox()`
  - Helper : `archi_adjust_color_brightness()` pour manipulation de couleurs
  
- **assets/js/customizer-preview.js** : Bindings pour aperçu en temps réel
  - Liaison `wp.customize` pour tous les paramètres
  - Mise à jour CSS dynamique
  - Ré-initialisation du comportement du header
  
- **assets/js/customizer-controls.js** : Améliorations UX du panneau de contrôle
  - Messages d'aide contextuels
  - Indicateurs d'aperçu en direct (⚡)
  - Affichage des valeurs pour les sliders
  - Style amélioré des color pickers

#### Modifié

- **functions.php** : Ajout de `require_once ARCHI_THEME_DIR . '/inc/customizer.php';`
- **front-page.php** : Remplacement des valeurs codées en dur
  - Délai du header : `500` → `get_theme_mod('archi_header_hide_delay', 500)`
  - Type d'animation : `'ease-in-out'` → `get_theme_mod('archi_header_animation_type', 'ease-in-out')`
  - Durée d'animation : `0.3` → `get_theme_mod('archi_header_animation_duration', 0.3)`
  - Hauteur zone trigger : `50px` → `get_theme_mod('archi_header_trigger_height', 50)`
  
- **page-home.php** : Mêmes modifications que front-page.php pour cohérence

#### Documentation

- **docs/CUSTOMIZER-INTEGRATION.md** : Guide complet de l'intégration du Customizer
  - Vue d'ensemble des fonctionnalités
  - Guide d'utilisation pour administrateurs
  - Documentation technique pour développeurs
  - Exemples de code PHP/JavaScript
  - Instructions pour étendre le Customizer
  - Troubleshooting

#### Impact technique

- **Rétrocompatibilité** : Les valeurs par défaut correspondent aux anciennes valeurs codées en dur
- **Performance** : CSS inline dans `<head>`, JavaScript chargé uniquement en contexte Customizer
- **Sécurité** : Tous les paramètres utilisent des callbacks de sanitization appropriés
- **Extensibilité** : Architecture modulaire facilitant l'ajout de nouveaux paramètres

#### Actions hooks

- `customize_register` : Enregistrement des options du Customizer
- `wp_head` : Sortie du CSS dynamique
- `customize_preview_init` : Chargement du JavaScript d'aperçu
- `customize_controls_enqueue_scripts` : Chargement du JavaScript de contrôles

---

### ✨ Autorisation complète des blocs Gutenberg

#### Ajouté

- **Tous les blocs WordPress Core** : Autorisation de l'intégralité des blocs natifs Gutenberg (texte, média, design, widgets, thème, embed)
- **Flexibilité éditoriale accrue** : Les utilisateurs peuvent désormais utiliser tous les blocs disponibles dans WordPress pour créer du contenu riche

#### Modifié

- **Fonction `archi_allowed_block_types()`** : Extension de la liste des blocs autorisés dans `inc/block-templates.php`
  - **Avant** : Seuls quelques blocs essentiels (paragraphe + blocs personnalisés spécifiques)
  - **Après** : Tous les blocs WordPress Core + tous les blocs personnalisés du thème
- **Template lock pour les articles** : Ajout de `template_lock = false` pour les posts, cohérent avec les projets et illustrations

#### Impact

- **Articles** (`post`) : Accès à tous les blocs (70+ blocs disponibles)
- **Projets** (`archi_project`) : Accès à tous les blocs
- **Illustrations** (`archi_illustration`) : Accès à tous les blocs
- **Pages** : Comportement par défaut maintenu (tous les blocs)

#### Blocs maintenant disponibles

**Texte** : paragraph, heading, list, quote, code, preformatted, pullquote, table, verse

**Média** : image, gallery, audio, video, file, media-text, cover

**Design** : button, buttons, columns, group, row, stack, separator, spacer

**Widgets** : shortcode, archives, calendar, categories, html, latest-comments, latest-posts, page-list, rss, search, social-links, tag-cloud

**Thème** : navigation, query, post-title, post-content, post-date, post-excerpt, post-featured-image, post-terms, et plus

**Embed** : YouTube, Vimeo, Twitter, Instagram, Facebook, Spotify, SoundCloud

#### Documentation

- Nouveau fichier : `docs/07-fixes-updates/2025-01-04-tous-blocs-autorises.md` - Documentation détaillée des changements

---

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
