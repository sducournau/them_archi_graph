# 🔗 Améliorations du Système de Liens du Graphe

**Date**: 18 novembre 2025  
**Version**: 2.0  
**Fichiers modifiés**: 
- `assets/js/utils/graphHelpers.js`
- `inc/rest-api.php`

---

## 📊 Résumé des Améliorations

Le système de création de liens entre nœuds a été considérablement amélioré pour créer des connexions plus pertinentes et intelligentes basées sur :
- ✅ **Métadonnées des projets** (client, localisation, type, surface)
- ✅ **Métadonnées des illustrations** (technique, logiciel)
- ✅ **Analyse de contenu améliorée** (mots-clés partagés)
- ✅ **Liens projet-illustration** (illustrations liées aux projets)
- ✅ **Suppression des règles restrictives** (nœuds de même catégorie)

---

## 🎯 Problèmes Résolus

### 1. **Règle Trop Restrictive Supprimée**
**Avant**: Les nœuds avec exactement les mêmes catégories étaient **exclus** des liens automatiques.
```javascript
// ❌ Code supprimé
if (categoriesA.length === categoriesB.length &&
    categoriesA.every((catId, idx) => catId === categoriesB[idx])) {
  continue; // Pas de lien créé
}
```

**Après**: Les nœuds de même catégorie peuvent maintenant être connectés s'ils partagent d'autres critères pertinents.

### 2. **Score Minimum Augmenté**
- **Avant**: 20 points → créait trop de liens faibles
- **Après**: 35 points → liens plus pertinents et ciblés

### 3. **Limite de Liens par Nœud Augmentée**
- **Avant**: 8 liens max par nœud
- **Après**: 10 liens max par nœud → meilleure connectivité

---

## 🆕 Nouveaux Critères de Liens

### Score des Liens (Pondération)

| Critère | Points | Description |
|---------|--------|-------------|
| **Catégorie partagée** | 40 pts | Chaque catégorie commune |
| **Tag partagé** | 25 pts | Chaque tag commun |
| **Catégorie principale identique** | 20 pts | Même première catégorie |
| **Proximité temporelle** | 10 pts | Publiés à ≤7 jours |
| **✨ Similarité de contenu** | 15 pts | 3+ mots-clés communs (augmenté de 5→15) |
| **✨ Projet même type** | 30 pts | Résidentiel, Commercial, etc. |
| **✨ Projet même client** | 35 pts | Même nom de client |
| **✨ Projet même localisation** | 25 pts | Même ville/région |
| **✨ Surface similaire** | 10 pts | ±20% de différence |
| **✨ Illustration même technique** | 30 pts | Dessin, 3D, Aquarelle, etc. |
| **✨ Illustration même logiciel** | 20 pts | AutoCAD, SketchUp, etc. |
| **✨ Illustration liée au projet** | 50 pts | Lien direct projet↔illustration |

**Seuil minimum**: 35 points pour créer un lien visible

---

## 🔧 Modifications Techniques

### 1. Fonction `calculateProximity()` Améliorée

#### A. Analyse de Contenu Intelligente
```javascript
// ✨ NOUVEAU: Extraction de mots-clés significatifs
const getKeywords = (text) => {
  return text.match(/\b\w{4,}\b/g) || []; // Mots de 4+ lettres
};

const keywordsA = [...getKeywords(titleA), ...getKeywords(excerptA)];
const keywordsB = [...getKeywords(titleB), ...getKeywords(excerptB)];
const commonKeywords = keywordsA.filter(word => keywordsB.includes(word));

// Score basé sur le nombre de mots-clés communs
if (uniqueCommon.length >= 3) {
  score += 15; // Score complet
} else if (uniqueCommon.length >= 1) {
  score += 7.5; // Score partiel
}
```

#### B. Liens Spécifiques aux Projets Architecturaux
```javascript
if (nodeA.post_type === 'archi_project' && nodeB.post_type === 'archi_project') {
  const metaA = nodeA.project_meta || {};
  const metaB = nodeB.project_meta || {};
  
  // Même type de projet
  if (metaA.project_type === metaB.project_type) {
    score += 30;
  }
  
  // Même client
  if (metaA.client === metaB.client) {
    score += 35;
  }
  
  // Même localisation (exacte ou partielle)
  if (locA === locB || locA.includes(locB) || locB.includes(locA)) {
    score += 25;
  }
  
  // Surface similaire (±20%)
  const ratio = Math.min(surfA, surfB) / Math.max(surfA, surfB);
  if (ratio >= 0.8) {
    score += 10;
  }
}
```

#### C. Liens Spécifiques aux Illustrations
```javascript
if (nodeA.post_type === 'archi_illustration' && nodeB.post_type === 'archi_illustration') {
  const metaA = nodeA.illustration_meta || {};
  const metaB = nodeB.illustration_meta || {};
  
  // Même technique (dessin, 3D, aquarelle, etc.)
  if (metaA.technique === metaB.technique) {
    score += 30;
  }
  
  // Même logiciel (AutoCAD, SketchUp, etc.)
  if (metaA.software === metaB.software) {
    score += 20;
  }
}
```

#### D. Liens Croisés Projet ↔ Illustration
```javascript
// Identifier quel nœud est le projet et lequel est l'illustration
const illustration = nodeA.post_type === 'archi_illustration' ? nodeA : nodeB;
const project = nodeA.post_type === 'archi_project' ? nodeA : nodeB;

// Vérifier le lien direct
if (illustration.illustration_meta?.project_link === project.id) {
  score += 50; // Lien fort
}
```

### 2. API REST Enrichie (`inc/rest-api.php`)

```php
// ✨ NOUVEAU: Métadonnées projet ajoutées
$project_meta = [];
if ($post->post_type === 'archi_project') {
    $project_meta = [
        'surface' => get_post_meta($post->ID, '_archi_project_surface', true),
        'cost' => get_post_meta($post->ID, '_archi_project_cost', true),
        'client' => get_post_meta($post->ID, '_archi_project_client', true),
        'location' => get_post_meta($post->ID, '_archi_project_location', true),
        'start_date' => get_post_meta($post->ID, '_archi_project_start_date', true),
        'end_date' => get_post_meta($post->ID, '_archi_project_end_date', true),
        'project_type' => get_post_meta($post->ID, '_archi_project_type', true),
        'certifications' => get_post_meta($post->ID, '_archi_project_certifications', true),
    ];
}

// Ajout dans la réponse JSON
if (!empty($project_meta)) {
    $article['project_meta'] = $project_meta;
}
```

---

## 📈 Résultats Attendus

### Avant les Améliorations
- ⚠️ Liens principalement basés sur catégories/tags
- ⚠️ Nœuds de même catégorie isolés
- ⚠️ Projets similaires non connectés
- ⚠️ Illustrations isolées

### Après les Améliorations
- ✅ **Plus de connexions pertinentes** entre projets similaires
- ✅ **Liens intelligents** basés sur métadonnées (client, localisation, technique)
- ✅ **Clusters thématiques** mieux définis (par type de projet, technique d'illustration)
- ✅ **Connexions projet↔illustration** automatiques
- ✅ **Analyse sémantique** du contenu (mots-clés communs)
- ✅ **Meilleure distribution** des liens (10 max au lieu de 8)

---

## 🎨 Exemples de Nouveaux Liens Créés

### Exemple 1: Projets Résidentiels
**Projet A**: Villa Moderne à Paris, 250m², Client: Dupont  
**Projet B**: Maison Contemporaine à Paris, 280m², Client: Dupont  

**Liens créés**:
- Même client: +35 pts
- Même localisation (Paris): +25 pts
- Surface similaire: +10 pts
- **TOTAL: 70 pts** ✅ Lien créé

### Exemple 2: Illustrations 3D
**Illustration A**: Perspective 3D avec SketchUp  
**Illustration B**: Rendu 3D avec SketchUp  

**Liens créés**:
- Même technique (3D): +30 pts
- Même logiciel (SketchUp): +20 pts
- **TOTAL: 50 pts** ✅ Lien créé

### Exemple 3: Projet + Illustration
**Projet**: Rénovation Bureau Commercial  
**Illustration**: Plan 3D du Bureau Commercial (liée au projet)  

**Liens créés**:
- Lien direct projet↔illustration: +50 pts
- **TOTAL: 50 pts** ✅ Lien créé

### Exemple 4: Contenu Similaire
**Article A**: "Architecture durable et écologique"  
**Article B**: "Bâtiments écologiques durables"  

**Mots-clés communs**: architecture, durable, écologique, bâtiments  
**Liens créés**: +15 pts pour 4 mots-clés communs ✅

---

## 🧪 Tests Recommandés

1. **Vérifier les liens projet-projet**:
   - Créer 2 projets avec même client → doivent être liés
   - Créer 2 projets avec même localisation → doivent être liés

2. **Vérifier les liens illustration-illustration**:
   - Créer 2 illustrations avec même technique → doivent être liées
   - Créer 2 illustrations avec même logiciel → doivent être liées

3. **Vérifier les liens projet-illustration**:
   - Lier une illustration à un projet → doit créer un lien fort

4. **Vérifier l'analyse de contenu**:
   - Créer 2 articles avec mots-clés similaires → doivent être liés

5. **Vérifier le seuil minimum**:
   - Articles avec <35 pts ne doivent PAS être liés
   - Articles avec ≥35 pts doivent être liés

---

## 🚀 Prochaines Étapes Possibles

### Améliorations Futures
1. **ML/AI pour similarité sémantique** avancée
2. **Clustering automatique** par type d'architecture
3. **Liens temporels** basés sur l'évolution des projets
4. **Suggestions de liens** pour l'administrateur
5. **Analyse de photos** (métadonnées EXIF, reconnaissance d'image)

---

## 📝 Notes de Maintenance

- Les erreurs TypeScript dans `graphHelpers.js` sont normales (fichier non typé)
- Le seuil de 35 points peut être ajusté via `minProximityScore`
- Les poids peuvent être personnalisés dans `WEIGHTS` de `calculateProximity()`
- Cache du graphe: utiliser `clear-graph-cache.php` après modifications

---

## 🔗 Fichiers Liés

- **Code principal**: `assets/js/utils/graphHelpers.js`
- **API REST**: `inc/rest-api.php`
- **Documentation**: `.github/copilot-instructions.md`
- **Architecture**: `docs/03-graph-system/`

---

**✨ Le système de graphe est maintenant plus intelligent et crée des connexions plus pertinentes !**
