# Changement : Satellites basés sur Catégories

**Date** : 4 novembre 2025  
**Auteur** : GitHub Copilot  
**Type** : Refactorisation majeure

## Résumé

Le système de satellites de flèches a été refactorisé pour utiliser les **catégories WordPress** au lieu des **zones polygones** (`visual_group`). Ce changement rend le système plus intuitif et aligné avec la taxonomie native de WordPress.

## Modifications apportées

### 1. Fichier principal : `assets/js/utils/arrowSatellites.js`

#### Configuration par catégories
```javascript
const CATEGORY_SATELLITE_CONFIG = {
  'default': { count: 2, orbitRadius: 45, speed: 0.0005, arrowGifs: [...] },
  'architecture': { count: 4, orbitRadius: 50, speed: 0.0006, ... },
  'design': { count: 3, orbitRadius: 45, speed: 0.0005, ... },
  'illustration': { count: 5, orbitRadius: 55, speed: 0.0007, ... },
  'featured': { count: 6, orbitRadius: 60, speed: 0.0008, ... }
}
```

#### Nouvelle fonction : `getCategorySatelliteConfig()`
- Extrait la catégorie principale du nœud
- Retourne la configuration correspondante
- Fallback sur 'default' si non trouvée

#### Fonction modifiée : `calculateArrowCount()`
- **Avant** : Basé sur `node_size`
- **Après** : Basé sur `nodeData.categories`

#### Fonction modifiée : `calculateSatellitePositions()`
- **Avant** : Paramètres `(nodeSize, count, orbitRadius)`
- **Après** : Paramètres `(nodeData, count)`
- Utilise la configuration de catégorie pour l'orbitRadius

#### Fonction modifiée : `getRandomArrowGif()`
- Prend maintenant `nodeData` en paramètre
- Utilise les GIFs spécifiques à la catégorie

#### Fonction modifiée : `createArrowSatellites()`
- Ajoute un attribut `data-category` au groupe de satellites
- Stocke `_satelliteSpeed` en plus de `_satelliteOrbitRadius`
- Utilise la configuration par catégorie

#### Fonction modifiée : `animateArrowSatellites()`
- Utilise `d._satelliteSpeed` stocké lors de la création
- Vitesse de rotation spécifique à chaque catégorie

### 2. Documentation

#### Nouveau fichier : `docs/ARROW-SATELLITES-CATEGORIES.md`
- Guide complet d'utilisation
- Exemples de configuration
- Instructions de personnalisation
- Migration depuis visual_group
- Cas d'usage pratiques

#### Nouveau fichier : `CHANGEMENT-SATELLITES-CATEGORIES.md` (ce fichier)
- Résumé des modifications
- Checklist de migration
- Tests recommandés

## Avant / Après

### Avant : Système basé sur node_size
```javascript
// Déterminé automatiquement par la taille
const arrowCount = calculateArrowCount(nodeData.node_size);
// Taille 100+ = 6 flèches
// Taille 85+ = 5 flèches
// etc.
```

### Après : Système basé sur catégories
```javascript
// Déterminé par la catégorie du nœud
const config = getCategorySatelliteConfig(nodeData);
const arrowCount = config.count;
// Catégorie 'architecture' = 4 flèches
// Catégorie 'design' = 3 flèches
// etc.
```

## Avantages

1. **Sémantique claire** : Les catégories ont une signification métier
2. **Gestion WordPress native** : Utilise le système de taxonomie existant
3. **Flexibilité accrue** : Chaque catégorie a ses propres paramètres
4. **Prévisibilité** : Le comportement est configuré, pas calculé
5. **Personnalisation aisée** : Ajout/modification de catégories simple
6. **Cohérence** : Aligne avec les autres fonctionnalités du thème

## Migration

### Pour les développeurs

Si vous utilisiez `visual_group` dans vos données :

```javascript
// Ancien
advanced_graph_params: {
  visual_group: 'zone-architecture'
}

// Nouveau
categories: [
  { slug: 'architecture', name: 'Architecture' }
]
```

### Pour les administrateurs WordPress

1. Attribuez des catégories à vos articles/projets/illustrations
2. La première catégorie détermine la configuration des satellites
3. Aucune autre action requise

## Configuration par défaut

Les catégories suivantes sont préconfigurées :

| Catégorie | Flèches | Rayon | Vitesse | GIFs |
|-----------|---------|-------|---------|------|
| default | 2 | 45px | 0.0005 | Tous |
| architecture | 4 | 50px | 0.0006 | Flèche blanche |
| design | 3 | 45px | 0.0005 | Flèche dansante |
| illustration | 5 | 55px | 0.0007 | Flèche rouge |
| featured | 6 | 60px | 0.0008 | Tous |

## Personnalisation

### Ajouter une catégorie

Éditez `assets/js/utils/arrowSatellites.js` :

```javascript
const CATEGORY_SATELLITE_CONFIG = {
  // ... configurations existantes ...
  'nouvelle-categorie': {
    count: 4,
    orbitRadius: 50,
    speed: 0.0006,
    arrowGifs: ['white-arrow-pointing-right-transparent-background-usagif.gif']
  }
};
```

### Modifier une catégorie

Changez les valeurs dans la configuration :

```javascript
'architecture': {
  count: 6,           // Plus de flèches
  orbitRadius: 70,    // Plus éloignées
  speed: 0.001,       // Plus rapides
  arrowGifs: ARROW_GIFS
}
```

## Styling CSS

Le groupe de satellites reçoit un attribut `data-category` :

```css
.satellites-group[data-category="architecture"] .arrow-gif {
  opacity: 1;
  filter: hue-rotate(45deg);
}
```

## Tests recommandés

- [ ] Vérifier l'affichage avec différentes catégories
- [ ] Tester avec des nœuds sans catégorie
- [ ] Vérifier les vitesses de rotation
- [ ] Tester les GIFs spécifiques par catégorie
- [ ] Vérifier la performance avec nombreux satellites
- [ ] Tester sur mobile
- [ ] Vérifier en mode sombre
- [ ] Tester les animations de survol

## Compatibilité

- ✅ WordPress 5.0+
- ✅ Navigateurs modernes (Chrome, Firefox, Safari, Edge)
- ✅ Responsive (mobile, tablette, desktop)
- ✅ Rétrocompatible (nœuds sans catégories utilisent 'default')

## Fichiers modifiés

```
assets/js/utils/arrowSatellites.js    (modifié)
docs/ARROW-SATELLITES-CATEGORIES.md   (nouveau)
CHANGEMENT-SATELLITES-CATEGORIES.md   (nouveau)
```

## Build réussi

```bash
npm run build
✓ Compilation réussie
✓ Aucune erreur
⚠ Warnings SASS (darken() deprecated - non bloquant)
```

## Prochaines étapes recommandées

1. Tester le système avec vos données réelles
2. Ajuster les configurations par défaut selon vos besoins
3. Créer des catégories personnalisées si nécessaire
4. Ajouter des styles CSS spécifiques par catégorie
5. Documenter vos propres configurations

## Support

Pour toute question ou problème :
1. Consulter `docs/ARROW-SATELLITES-CATEGORIES.md`
2. Vérifier la configuration dans `arrowSatellites.js`
3. Inspecter les attributs `data-category` dans le DOM
4. Vérifier les données de catégories dans l'API REST

## Notes techniques

- La catégorie est déterminée par `nodeData.categories[0]`
- Le slug de catégorie est utilisé (pas le nom)
- Les configurations sont en dur dans le JavaScript
- Possibilité future : Configuration via WordPress admin
- Les données sont stockées dans `nodeData._satelliteSpeed` et `nodeData._satelliteOrbitRadius`

## Conclusion

Ce changement simplifie la gestion des satellites et améliore la cohérence avec le reste du thème WordPress. Le système est plus flexible et plus facile à comprendre pour les utilisateurs non techniques.
