# 🚀 Guide Rapide de Test des Améliorations du Graphe

## ⚡ Test Rapide (5 minutes)

### 1. Compiler les Modifications
```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
npm run build
```

### 2. Vider le Cache du Graphe
```bash
# Option 1: Via navigateur
http://votre-site.local/wp-content/themes/archi-graph-template/clear-graph-cache.php

# Option 2: Via PHP CLI
php clear-graph-cache.php
```

### 3. Exécuter le Script de Test
```bash
php test-graph-links.php
```

Ce script affichera:
- ✅ Nombre de nœuds dans le graphe
- ✅ Liens potentiels par même client
- ✅ Liens potentiels par même localisation
- ✅ Liens potentiels par même technique/logiciel
- ✅ Liens projet↔illustration

### 4. Visualiser dans le Navigateur
1. Ouvrez votre site WordPress
2. Allez sur la page d'accueil (avec le graphe)
3. Observez les nouvelles connexions

---

## 🧪 Tests Détaillés

### Test 1: Liens par Métadonnées Projet

**Créer deux projets avec même client:**

1. Projet A:
   - Titre: "Villa Moderne"
   - Client: "Jean Dupont"
   - Localisation: "Paris"
   - Cocher "Afficher dans le graphe"

2. Projet B:
   - Titre: "Appartement Contemporain"
   - Client: "Jean Dupont"
   - Localisation: "Lyon"
   - Cocher "Afficher dans le graphe"

**Résultat attendu**: Lien créé avec 35 pts (même client)

### Test 2: Liens par Même Localisation

**Créer deux projets dans la même ville:**

1. Projet A:
   - Titre: "Maison Écologique"
   - Localisation: "Marseille"
   - Cocher "Afficher dans le graphe"

2. Projet B:
   - Titre: "Bureau Moderne"
   - Localisation: "Marseille"
   - Cocher "Afficher dans le graphe"

**Résultat attendu**: Lien créé avec 25 pts (même localisation)

### Test 3: Liens par Technique d'Illustration

**Créer deux illustrations avec même technique:**

1. Illustration A:
   - Titre: "Perspective 3D Villa"
   - Technique: "Rendu 3D"
   - Logiciel: "SketchUp"
   - Cocher "Afficher dans le graphe"

2. Illustration B:
   - Titre: "Vue 3D Appartement"
   - Technique: "Rendu 3D"
   - Logiciel: "SketchUp"
   - Cocher "Afficher dans le graphe"

**Résultat attendu**: Lien créé avec 50 pts (30 technique + 20 logiciel)

### Test 4: Liens Projet ↔ Illustration

**Créer un projet et une illustration liée:**

1. Projet:
   - Titre: "Rénovation Bureau"
   - ID: Notez l'ID après création

2. Illustration:
   - Titre: "Plan 3D Bureau"
   - Lien vers le projet: Sélectionner le projet créé
   - Cocher "Afficher dans le graphe"

**Résultat attendu**: Lien fort créé avec 50 pts

### Test 5: Analyse de Contenu

**Créer deux articles avec mots-clés similaires:**

1. Article A:
   - Titre: "Architecture durable et écologique"
   - Contenu: "Les bâtiments écologiques modernes utilisent..."

2. Article B:
   - Titre: "Constructions écologiques durables"
   - Contenu: "L'architecture moderne durable privilégie..."

**Résultat attendu**: Lien créé avec 15 pts (mots-clés: architecture, durable, écologique, moderne)

---

## 📊 Vérification Visuelle

### Dans le Graphe:

1. **Liens plus épais** = score de proximité plus élevé
2. **Plus de connexions** entre nœuds similaires
3. **Clusters mieux définis** par type de projet/technique
4. **Nœuds moins isolés** grâce aux nouveaux critères

### Console Développeur (F12):

```javascript
// Voir les détails des liens
console.log(links);

// Chaque lien contient maintenant:
// - strength: Force du lien (basé sur le score)
// - proximity: Détails du score de proximité
// - weight: Poids total
```

---

## 🔍 Débogage

### Problème: Pas de nouveaux liens

**Vérifications:**

1. Cache vidé ?
   ```bash
   php clear-graph-cache.php
   ```

2. Compilation effectuée ?
   ```bash
   npm run build
   ```

3. Métadonnées remplies ?
   - Projets: client, localisation, type
   - Illustrations: technique, logiciel

4. Nœuds visibles dans le graphe ?
   - Meta `_archi_show_in_graph` = '1'

### Problème: Trop de liens

**Ajuster le seuil minimum:**

Dans `graphHelpers.js`, ligne ~498:
```javascript
const {
  minProximityScore = 35, // Augmenter pour moins de liens
  maxLinksPerNode = 10,   // Réduire pour limiter
  useProximityScore = true,
} = options;
```

Puis recompiler:
```bash
npm run build
```

---

## 📈 Métriques de Succès

✅ **Bonne amélioration si:**
- Nœuds précédemment isolés maintenant connectés
- Clusters plus cohérents (projets similaires groupés)
- Liens plus pertinents (pas de spam de connexions)
- Illustrations liées à leurs projets
- Projets d'un même client connectés

❌ **Problème si:**
- Trop de liens (graphe illisible)
- Liens non pertinents
- Nœuds toujours isolés malgré critères matchant

---

## 🆘 Support

**Fichiers à consulter:**
- Documentation complète: `docs/GRAPH-LINKS-IMPROVEMENTS.md`
- Code principal: `assets/js/utils/graphHelpers.js`
- API REST: `inc/rest-api.php`
- Instructions Copilot: `.github/copilot-instructions.md`

**Commandes utiles:**
```bash
# Voir les erreurs JS
npm run build

# Tester l'API REST
curl http://votre-site.local/wp-json/archi/v1/articles

# Vider tous les caches
php clear-all-caches.php
```

---

**✨ Le système est maintenant plus intelligent. Profitez des nouvelles connexions !**
