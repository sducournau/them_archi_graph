# Simplification des Blocs de l'Éditeur

## 📋 Résumé des Modifications

Les modèles de blocs par défaut pour les articles, projets et illustrations ont été simplifiés pour ne conserver que les blocs essentiels du thème Archi-Graph.

## 🎯 Objectif

Retirer tous les blocs WordPress par défaut (paragraphes, images, galeries, etc.) et ne garder que :
- **Bloc de liens** (`core/paragraph`) - pour permettre l'ajout de texte et liens
- **Paramètres Graph** (via `article-manager`)
- **Fiches techniques/identité** (blocs spécifiques selon le type de contenu)

## 🔧 Modifications Apportées

### Fichier Modifié
`inc/block-templates.php`

### 1. Articles Standards (post)

**Avant :** 10+ blocs (images, paragraphes, galeries, séparateurs, etc.)

**Après :** 2 blocs essentiels uniquement
```php
$post_type_object->template = [
    ['archi-graph/article-manager', [...]],  // Paramètres graph + métadonnées
    ['archi-graph/article-specs', []],        // Fiche identité de l'article
];
```

### 2. Projets Architecturaux (archi_project)

**Avant :** 15+ blocs (cover, colonnes, galeries, timelines, etc.)

**Après :** 2 blocs essentiels uniquement
```php
$post_type_object->template = [
    ['archi-graph/article-manager', [...]],  // Paramètres graph + métadonnées
    ['archi-graph/project-specs', []],        // Fiche technique du projet
];
```

### 3. Illustrations (archi_illustration)

**Avant :** 12+ blocs (images, colonnes, galeries, groupes, etc.)

**Après :** 2 blocs essentiels uniquement
```php
$post_type_object->template = [
    ['archi-graph/article-manager', [...]],  // Paramètres graph + métadonnées
    ['archi-graph/illustration-specs', []],   // Fiche technique de l'illustration
];
```

### 4. Restriction des Blocs Autorisés

La fonction `archi_allowed_block_types()` a été modifiée pour limiter drastiquement les blocs disponibles dans l'éditeur :

```php
// Blocs de base minimum
$minimal_blocks = [
    'core/paragraph',  // Pour ajouter du texte et des liens
];

// Blocs personnalisés essentiels
$essential_archi_blocks = [
    'archi-graph/article-manager',  // Paramètres graph et métadonnées
];
```

**Par type de contenu :**
- **Articles** : `core/paragraph`, `article-manager`, `article-specs`
- **Projets** : `core/paragraph`, `article-manager`, `project-specs`
- **Illustrations** : `core/paragraph`, `article-manager`, `illustration-specs`
- **Pages** : Tous les blocs restent disponibles (pas de restriction)

## 📦 Blocs Conservés

### Blocs WordPress Core
- ✅ `core/paragraph` - Pour le texte et les liens

### Blocs Personnalisés Archi-Graph

#### 1. **article-manager** (commun à tous)
- Gestion des paramètres du graph
- Métadonnées de l'article
- Relations entre contenus
- Visibilité dans le graph
- Couleur et taille du nœud

#### 2. **article-specs** (articles)
- Fiche identité de l'article
- Catégories et tags
- Date de publication
- Auteur

#### 3. **project-specs** (projets)
- Fiche technique du projet
- Surface
- Coût
- Client
- Localisation
- Type de projet
- Statut

#### 4. **illustration-specs** (illustrations)
- Fiche technique de l'illustration
- Technique utilisée
- Dimensions
- Logiciels
- Type d'illustration

## 🎨 Expérience Utilisateur

### Dans l'Éditeur Gutenberg

Lors de la création d'un nouvel article, projet ou illustration :

1. **L'éditeur s'ouvre avec 2 blocs pré-insérés** :
   - Le bloc de gestion (article-manager)
   - Le bloc de spécifications techniques

2. **L'utilisateur peut uniquement** :
   - Remplir les champs des blocs pré-insérés
   - Ajouter des paragraphes de texte (pour des liens ou notes)
   - Supprimer des blocs existants (si `template_lock = false`)

3. **L'utilisateur ne peut PAS** :
   - Ajouter des images
   - Créer des galeries
   - Insérer des colonnes
   - Utiliser des blocs de mise en page complexes

### Ajout de Blocs

En cliquant sur le bouton "+" dans l'éditeur, seuls 3 types de blocs apparaissent :
- Paragraphe
- Article Manager
- Specs (selon le type de contenu)

## 🔄 Compatibilité

### Contenus Existants
Les articles, projets et illustrations créés avant cette modification **conservent tous leurs blocs existants**. Seuls les nouveaux contenus seront affectés par ces templates simplifiés.

### Migration
Si vous souhaitez nettoyer les anciens contenus :
1. Éditer l'article/projet/illustration
2. Supprimer manuellement les blocs non essentiels
3. Conserver uniquement `article-manager` et le bloc specs

## ⚙️ Configuration Technique

### Fichiers Concernés
- `inc/block-templates.php` - Templates et restrictions de blocs
- `inc/gutenberg-blocks.php` - Enregistrement du bloc `article-manager`
- `inc/technical-specs-blocks.php` - Enregistrement des blocs specs

### Hooks WordPress Utilisés
- `init` - Enregistrement des templates de blocs
- `allowed_block_types_all` - Restriction des blocs disponibles

### Template Lock
```php
$post_type_object->template_lock = false;
```
Permet toujours à l'utilisateur d'ajouter/supprimer des blocs, mais limite les choix disponibles.

## 🚀 Avantages

1. **Interface simplifiée** - Moins de confusion pour les utilisateurs
2. **Cohérence** - Tous les contenus suivent la même structure
3. **Maintenance facilitée** - Moins de blocs à gérer et styliser
4. **Performance** - Moins de CSS/JS chargé pour des blocs inutilisés
5. **Données structurées** - Focus sur les métadonnées essentielles pour le graph

## 🔧 Désactivation

Pour revenir aux blocs complets, commenter ou retirer ces lignes dans `inc/block-templates.php` :

```php
// Commenter cette ligne pour désactiver les restrictions
// add_filter('allowed_block_types_all', 'archi_allowed_block_types', 10, 2);
```

Ou modifier la fonction pour retourner `true` :

```php
function archi_allowed_block_types($allowed_blocks, $context) {
    return true; // Autoriser tous les blocs
}
```

## 📝 Notes Importantes

- Les **pages WordPress** conservent l'accès à tous les blocs
- Le bloc `core/paragraph` est conservé pour permettre l'ajout de liens et notes
- Les blocs patterns (Hero, CTA, etc.) restent disponibles mais uniquement pour les pages
- Cette modification n'affecte que l'éditeur Gutenberg, pas le rendu front-end

## 🎯 Résultat Final

### Articles (post)
```
┌─────────────────────────────┐
│  Article Manager            │
│  - Paramètres Graph         │
│  - Métadonnées             │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Article Specs              │
│  - Fiche Identité          │
└─────────────────────────────┘
```

### Projets (archi_project)
```
┌─────────────────────────────┐
│  Article Manager            │
│  - Paramètres Graph         │
│  - Métadonnées             │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Project Specs              │
│  - Fiche Technique         │
└─────────────────────────────┘
```

### Illustrations (archi_illustration)
```
┌─────────────────────────────┐
│  Article Manager            │
│  - Paramètres Graph         │
│  - Métadonnées             │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Illustration Specs         │
│  - Fiche Technique         │
└─────────────────────────────┘
```

## 📅 Date de Modification
4 novembre 2025

## 👤 Auteur
Modification demandée pour simplifier l'éditeur et se concentrer sur les fonctionnalités essentielles du graph.
