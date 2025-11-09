# Guide Rapide : Animations & Polygones

## 🎬 Configurer les Animations du Graphique

### Accès
**Tableau de bord WordPress → Archi Graph → Onglet "Graphique"**

### Paramètres disponibles

#### Section "Animations & Interactions"

1. **Type d'animation**
   - Choisissez parmi 10 animations différentes
   - Recommandé : "Bounce" ou "Elastic" pour un effet dynamique
   - "Fade In" pour un rendu sobre et professionnel

2. **Durée d'animation** (200-2000ms)
   - Courte (400ms) : rapide et dynamique
   - Moyenne (800ms) : équilibrée ✅ 
   - Longue (1500ms) : effet dramatique

3. **Effet de survol**
   - Active le zoom au survol des nœuds
   - Recommandé : ✅ Activé

4. **Intensité du zoom** (1.0-1.5x)
   - 1.15x : subtil ✅
   - 1.3x : visible
   - 1.5x : marqué

5. **Animation des liens**
   - Effet de tracé progressif des connexions
   - Recommandé : ✅ Activé

#### Section "Mode Organique"

6. **Mode organique**
   - Crée des regroupements naturels (îles architecturales)
   - Recommandé : ✅ Activé pour projets liés

7. **Force de clustering** (0-1)
   - 0.1 : regroupement léger ✅
   - 0.3 : regroupement moyen
   - 0.5+ : regroupement fort

---

## 🎨 Configurer les Polygones de Catégories

### Accès
**Articles → Catégories → Modifier une catégorie**

### Paramètres par catégorie

#### 1. Polygone dans le graphique
- ☑️ Cochez pour afficher un polygone autour des articles de cette catégorie
- Le polygone englobe visuellement tous les articles partageant cette catégorie

#### 2. Couleur du polygone
- Cliquez sur le sélecteur de couleur
- Choisissez une couleur distinctive pour cette catégorie
- **Conseil** : Utilisez des couleurs contrastées entre catégories

#### 3. Opacité du polygone (0-1)
- 0 = Transparent
- 0.2 = Léger ✅ (recommandé)
- 0.5 = Moyen
- 1.0 = Opaque

### Aperçu en temps réel
L'interface affiche un aperçu visuel du polygone pendant l'édition.

---

## 💡 Exemples de Configuration

### Configuration 1 : Moderne et Dynamique
```
Animation : Bounce
Durée : 800ms
Effet survol : ✅ Activé (1.15x)
Animation liens : ✅ Activé
Mode organique : ✅ Activé
Clustering : 0.1

Polygones :
- Architecture : #e74c3c (rouge) - Opacité 0.25
- Urbanisme : #3498db (bleu) - Opacité 0.2
- Design : #2ecc71 (vert) - Opacité 0.2
```

### Configuration 2 : Sobre et Professionnel
```
Animation : Fade In
Durée : 600ms
Effet survol : ✅ Activé (1.1x)
Animation liens : ❌ Désactivé
Mode organique : ✅ Activé
Clustering : 0.15

Polygones :
- Architecture : #34495e (gris foncé) - Opacité 0.15
- Urbanisme : #7f8c8d (gris) - Opacité 0.15
- Design : #95a5a6 (gris clair) - Opacité 0.15
```

### Configuration 3 : Coloré et Énergique
```
Animation : Explode
Durée : 1000ms
Effet survol : ✅ Activé (1.3x)
Animation liens : ✅ Activé
Mode organique : ✅ Activé
Clustering : 0.2

Polygones :
- Architecture : #f39c12 (orange) - Opacité 0.3
- Urbanisme : #9b59b6 (violet) - Opacité 0.3
- Design : #1abc9c (turquoise) - Opacité 0.3
```

---

## ⚡ Astuces de Performance

### Pour les petits graphiques (<50 nœuds)
- ✅ Toutes les animations disponibles
- ✅ Tous les effets activés
- ✅ Durées longues possibles

### Pour les graphiques moyens (50-100 nœuds)
- ✅ Animations : Fade In, Scale Up, Bounce
- ✅ Durée : 600-800ms
- ⚠️ Limiter les polygones à 5-8 catégories

### Pour les grands graphiques (>100 nœuds)
- ✅ Animations : Fade In uniquement
- ✅ Durée : 400-600ms
- ❌ Désactiver animation des liens
- ⚠️ Limiter les polygones à 3-5 catégories

---

## 🎯 Workflow Recommandé

### Étape 1 : Configurer les catégories
1. Allez dans **Articles → Catégories**
2. Pour chaque catégorie importante :
   - ✅ Activez le polygone
   - 🎨 Choisissez une couleur distinctive
   - 📊 Réglez l'opacité à 0.2

### Étape 2 : Tester le graphique
1. Visualisez le graphique sur le site
2. Vérifiez que les polygones sont visibles
3. Ajustez les couleurs si besoin

### Étape 3 : Configurer les animations
1. Allez dans **Archi Graph → Graphique**
2. Testez différentes animations
3. Choisissez celle qui correspond à votre style
4. Ajustez la durée et les effets

### Étape 4 : Optimiser
1. Surveillez la performance
2. Réduisez les effets si le graphique est lent
3. Ajustez le clustering selon les besoins

---

## 🔍 Vérification

### Les polygones fonctionnent si :
- ✅ Au moins 3 articles par catégorie
- ✅ "Polygone dans le graphique" activé
- ✅ Couleur définie
- ✅ Articles visibles dans le graphique

### Les animations fonctionnent si :
- ✅ "Activer les animations" coché
- ✅ Type d'animation sélectionné
- ✅ Durée entre 200-2000ms
- ✅ JavaScript activé dans le navigateur

---

## 📞 Support

### Problèmes courants

**Les polygones ne s'affichent pas**
→ Vérifiez qu'il y a au moins 3 articles par catégorie avec "Afficher dans le graphique" activé

**Les animations sont saccadées**
→ Réduisez la durée d'animation et choisissez "Fade In"

**Les couleurs se mélangent**
→ Réduisez l'opacité des polygones à 0.15-0.2

**Le graphique est lent**
→ Désactivez les animations des liens et réduisez le nombre de polygones

---

## 📚 Plus d'informations

Consultez `ANIMATIONS-POLYGONS-DOCUMENTATION.md` pour :
- Documentation technique détaillée
- API JavaScript complète
- Exemples de code
- Guide de développement
