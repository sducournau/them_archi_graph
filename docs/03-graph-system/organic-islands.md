# Système d'Îles Architecturales Organiques

## Vue d'ensemble

Le système d'îles architecturales transforme la visualisation du graphe en regroupant les projets architecturaux (`archi_project`) connectés en "îles" visuelles organiques. Cette approche crée une dynamique plus naturelle et intuitive pour explorer les relations entre projets.

## Concepts clés

### 1. Îles architecturales

Les **îles** sont des groupes visuels de projets fortement liés entre eux :

- **Formation automatique** : Les îles se forment automatiquement en détectant les relations fortes entre projets
- **Critères de connexion** :
  - 2+ catégories partagées
  - 2+ tags partagés
  - Relations manuelles définies
- **Taille limitée** : Maximum 5 projets par île pour éviter les méga-îles

### 2. Forces physiques organiques

Le système utilise des forces D3.js améliorées pour un comportement plus naturel :

#### Forces de répulsion adaptatives
```javascript
// Répulsion réduite entre projets architecturaux
strength: (d) => d.post_type === 'archi_project' ? -200 : -300
```

#### Collision souple
```javascript
// Collision plus douce et organique
.forceCollide()
  .radius((d) => (d.node_size || 60) / 2 + 15)
  .strength(0.5)  // Plus souple que 0.7
  .iterations(2)  // Plus d'itérations pour mouvement fluide
```

#### Gravité douce
```javascript
// Force de gravité légère vers le centre vertical
.force("gravity", d3.forceY(height / 2).strength(0.01))
```

#### Friction augmentée
```javascript
// Plus de friction pour mouvement fluide
.velocityDecay(0.4)  // vs 0.3 standard
.alphaDecay(0.015)   // vs 0.02 standard
```

### 3. Visualisation des îles

Les îles sont rendues avec :

#### Enveloppe principale
- **Forme** : Enveloppe convexe autour des nœuds membres
- **Style** : Contour coloré semi-transparent avec effet de lueur
- **Animation** : Pulsation douce (8s)
- **Couleur** : Basée sur la catégorie dominante des membres

#### Texture interne
- **Forme** : Enveloppe réduite à l'intérieur
- **Style** : Lignes pointillées subtiles
- **Animation** : Ondulation (12s)

#### Coins arrondis
```javascript
smoothHull(hull, 0.3)  // Facteur d'arrondi 0-1
```

## Architecture technique

### Structure des fichiers

```
assets/
├── js/
│   ├── utils/
│   │   └── graphHelpers.js          # Forces et îles
│   └── components/
│       └── GraphContainer.jsx       # Rendu des îles
└── css/
    └── organic-islands.css          # Styles des îles
```

### Fonctions principales

#### `createArchitecturalIslands(nodes)`
Crée les données d'îles à partir des nœuds :
- Filtre les projets architecturaux
- Détecte les relations fortes
- Groupe en îles de 2-5 membres
- Retourne un tableau d'îles

#### `forceIslands()`
Force D3.js personnalisée pour les îles :
- Calcule les centres d'îles dynamiquement
- Applique attraction douce au sein des îles
- Applique répulsion entre îles
- Force adaptative avec alpha

#### `updateArchitecturalIslands(container, articlesData)`
Met à jour le rendu SVG des îles :
- Crée/supprime les éléments SVG
- Calcule les enveloppes convexes
- Applique les styles et animations
- S'exécute à chaque tick de simulation

#### `smoothHull(hull, smoothness)`
Arrondit les coins des enveloppes :
- Utilise des courbes de Bézier implicites
- Paramètre `smoothness` : 0 (aucun) à 1 (très arrondi)
- Recommandé : 0.3 pour effet naturel

## Configuration

### Activer/désactiver le mode organique

```javascript
const simulation = createForceSimulation(nodes, categories, {
  organicMode: true,  // Active le système d'îles
  width: 1200,
  height: 800
});
```

### Ajuster les paramètres

#### Force d'îles
```javascript
.force("islands", 
  forceIslands()
    .islands(islands)
    .strength(0.15)  // 0.1-0.3 recommandé
)
```

#### Taille max d'île
```javascript
// Dans findRelatedProjects()
const maxIslandSize = 5;  // 3-8 recommandé
```

#### Expansion d'enveloppe
```javascript
hull = expandHull(hull, 60);  // Pixels de padding
```

#### Arrondi des coins
```javascript
hull = smoothHull(hull, 0.3);  // 0.2-0.5 recommandé
```

## Styles CSS

### Classes principales

- `.islands` - Groupe SVG contenant toutes les îles
- `.architectural-island` - Groupe d'une île individuelle
- `.island-background` - Enveloppe principale de l'île
- `.island-texture` - Texture interne de l'île

### Personnalisation

```css
/* Modifier l'opacité des îles */
.island-background {
  fill-opacity: 0.12;        /* 0.08-0.20 */
  stroke-opacity: 0.3;       /* 0.2-0.5 */
}

/* Modifier l'animation de pulsation */
@keyframes island-pulse {
  0%, 100% { stroke-opacity: 0.3; }
  50% { stroke-opacity: 0.4; }
}

/* Désactiver les animations */
@media (prefers-reduced-motion: reduce) {
  .island-background,
  .island-texture {
    animation: none !important;
  }
}
```

### Effets hover

```css
.architectural-island:hover .island-background {
  stroke-opacity: 0.5 !important;
  fill-opacity: 0.18 !important;
  stroke-width: 4;
}
```

## Performances

### Optimisations implémentées

1. **Will-change CSS** : Prépare les transformations GPU
2. **Itérations limitées** : Collision en 2 itérations max
3. **Texture conditionnelle** : Masquée sur mobile
4. **Updates conditionnels** : Îles mises à jour uniquement au tick

### Métriques attendues

- **FPS** : 50-60 sur desktop, 30-50 sur mobile
- **Temps de calcul** : <10ms par frame pour 50 nœuds
- **Mémoire** : +5-10MB vs mode standard

## Accessibilité

### Respect des préférences utilisateur

```css
@media (prefers-reduced-motion: reduce) {
  /* Désactive toutes les animations */
}
```

### Contraste

Les labels utilisent des ombres multiples pour garantir la lisibilité :

```css
text-shadow: 
  2px 2px 4px rgba(255, 255, 255, 0.9),
  -1px -1px 3px rgba(255, 255, 255, 0.8);
```

## Responsive Design

### Mobile (< 768px)

- Texture d'île masquée pour performance
- Trait d'îles réduit à 2px
- Hover réduit à 3px

### Desktop

- Tous les effets activés
- Animations fluides
- Interactions riches

## Mode sombre

Support automatique via `prefers-color-scheme` :

```css
@media (prefers-color-scheme: dark) {
  .organic-mode-active .graph-container {
    background: radial-gradient(
      circle at 50% 50%,
      rgba(44, 62, 80, 0.3) 0%,
      rgba(52, 73, 94, 0.1) 100%
    );
  }
}
```

## Débogage

### Console logs

Activez les logs pour voir la formation des îles :

```javascript
console.log('Islands created:', islands.length);
islands.forEach(island => {
  console.log(`Island ${island.id}:`, island.members.length, 'members');
});
```

### Inspection visuelle

```javascript
// Afficher les centres d'îles
svg.selectAll('.island-center')
  .data(islands)
  .enter()
  .append('circle')
  .attr('cx', d => d.center.x)
  .attr('cy', d => d.center.y)
  .attr('r', 5)
  .attr('fill', 'red');
```

## Exemples d'utilisation

### Cas 1 : Portfolio architectural

Des projets d'un même architecte ou d'un même type (résidentiel, commercial) forment naturellement des îles, facilitant la navigation thématique.

### Cas 2 : Chronologie de projets

Des projets d'années similaires avec des styles apparentés se regroupent, créant une visualisation temporelle intuitive.

### Cas 3 : Géographie

Des projets de même région ou ville se connectent, formant des îles géographiques naturelles.

## Comparaison avec l'ancien système

| Aspect | Ancien système | Système d'îles |
|--------|---------------|----------------|
| Organisation | Par catégories strictes | Groupes fluides par relations |
| Mouvement | Rigide, mécanique | Fluide, organique |
| Relations | Implicites | Visuellement explicites |
| Exploration | Par filtres | Par îles visuelles |
| Performances | Standard | Optimisées |

## Améliorations futures

### Phase 2
- [ ] Étiquettes d'îles avec noms automatiques
- [ ] Zoom sur île avec transition douce
- [ ] Filtrage par île
- [ ] Connexions inter-îles plus visibles

### Phase 3
- [ ] Îles 3D avec perspective
- [ ] Animation d'entrée par île
- [ ] Couleurs d'îles personnalisables
- [ ] Export de vue d'île

## Support et ressources

- **Documentation D3.js** : https://d3js.org/
- **Forces D3** : https://d3js.org/d3-force
- **Enveloppe convexe** : https://d3js.org/d3-polygon/convex-hull
- **Animations CSS** : https://developer.mozilla.org/fr/docs/Web/CSS/CSS_Animations

## Licence

Ce système fait partie du thème Archi Graph, sous licence GPL v3.
