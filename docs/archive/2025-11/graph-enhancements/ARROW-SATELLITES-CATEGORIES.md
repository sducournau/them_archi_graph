# Arrow Satellites - Configuration par Catégories

## Vue d'ensemble

Le système de satellites de flèches a été modifié pour utiliser les **catégories** au lieu des **zones polygones** (`visual_group`). Chaque catégorie peut maintenant avoir sa propre configuration de satellites.

## Changements principaux

### Avant (basé sur node_size)
```javascript
// Le nombre de flèches était déterminé par la taille du nœud
if (size >= 100) return 6;
if (size >= 85) return 5;
// etc.
```

### Après (basé sur catégories)
```javascript
// Le nombre de flèches est déterminé par la catégorie du nœud
const config = getCategorySatelliteConfig(nodeData);
return config.count;
```

## Configuration des catégories

### Structure de configuration

Chaque catégorie peut définir :
- **count** : Nombre de flèches satellites (0-6)
- **orbitRadius** : Rayon de l'orbite en pixels
- **speed** : Vitesse de rotation (radians par milliseconde)
- **arrowGifs** : Tableau des GIFs à utiliser pour cette catégorie

### Exemple de configuration

```javascript
const CATEGORY_SATELLITE_CONFIG = {
  'default': {
    count: 2,
    orbitRadius: 45,
    speed: 0.0005,
    arrowGifs: ARROW_GIFS // Tous les GIFs disponibles
  },
  
  'architecture': {
    count: 4,
    orbitRadius: 50,
    speed: 0.0006,
    arrowGifs: ['white-arrow-pointing-right-transparent-background-usagif.gif']
  },
  
  'design': {
    count: 3,
    orbitRadius: 45,
    speed: 0.0005,
    arrowGifs: ['dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif']
  },
  
  'illustration': {
    count: 5,
    orbitRadius: 55,
    speed: 0.0007,
    arrowGifs: ['red-bouncing-arrow-pointer-transparent-background-usagif.gif']
  },
  
  'featured': {
    count: 6,
    orbitRadius: 60,
    speed: 0.0008,
    arrowGifs: ARROW_GIFS // Utilise tous les GIFs
  }
};
```

## Personnalisation

### Ajouter une nouvelle catégorie

1. Ouvrez `/assets/js/utils/arrowSatellites.js`
2. Ajoutez votre configuration dans `CATEGORY_SATELLITE_CONFIG`

```javascript
'ma-categorie': {
  count: 3,           // Nombre de flèches
  orbitRadius: 50,    // Distance du centre (px)
  speed: 0.0005,      // Vitesse de rotation
  arrowGifs: [        // GIFs à utiliser
    'dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif'
  ]
}
```

### Modifier une catégorie existante

Modifiez les valeurs dans la configuration :

```javascript
'architecture': {
  count: 6,           // Augmente le nombre de flèches
  orbitRadius: 70,    // Augmente la distance
  speed: 0.001,       // Accélère la rotation
  arrowGifs: ARROW_GIFS // Utilise tous les GIFs
}
```

## Fonctionnement technique

### Détermination de la catégorie

La fonction `getCategorySatelliteConfig()` utilise la **première catégorie** du nœud (catégorie principale) :

```javascript
export const getCategorySatelliteConfig = (nodeData) => {
  let primaryCategory = 'default';
  
  if (nodeData.categories && nodeData.categories.length > 0) {
    primaryCategory = nodeData.categories[0].slug || 
                      nodeData.categories[0].name?.toLowerCase() || 
                      'default';
  }
  
  return CATEGORY_SATELLITE_CONFIG[primaryCategory] || 
         CATEGORY_SATELLITE_CONFIG['default'];
};
```

### Attribution des GIFs

Chaque catégorie peut avoir ses propres GIFs. Si non spécifié, tous les GIFs disponibles sont utilisés :

```javascript
const getRandomArrowGif = (nodeData) => {
  const config = getCategorySatelliteConfig(nodeData);
  const availableGifs = config.arrowGifs || ARROW_GIFS;
  const randomIndex = Math.floor(Math.random() * availableGifs.length);
  const gifName = availableGifs[randomIndex];
  return `${getThemeUrl()}/gif/${gifName}`;
};
```

### Vitesse de rotation

La vitesse de rotation est maintenant spécifique à chaque catégorie et stockée dans les données du nœud :

```javascript
nodeData._satelliteSpeed = config.speed || 0.0005;
```

## Styling CSS

Le groupe de satellites reçoit maintenant un attribut `data-category` pour permettre un styling spécifique :

```css
/* Style pour une catégorie spécifique */
.satellites-group[data-category="architecture"] .arrow-gif {
  opacity: 1;
  filter: hue-rotate(45deg);
}

.satellites-group[data-category="design"] .arrow-gif {
  animation: custom-pulse 1.5s ease-in-out infinite;
}
```

## Migration depuis visual_group

Si vous utilisiez auparavant `visual_group`, voici comment migrer :

### Ancien système (visual_group)
```javascript
advanced_graph_params: {
  visual_group: 'zone-architecture'
}
```

### Nouveau système (categories)
```javascript
categories: [
  { 
    slug: 'architecture',
    name: 'Architecture'
  }
]
```

## Cas d'usage

### Exemple 1 : Projets importants
Configurez la catégorie "featured" avec 6 flèches et rotation rapide :

```javascript
'featured': {
  count: 6,
  orbitRadius: 60,
  speed: 0.0008,
  arrowGifs: ARROW_GIFS
}
```

### Exemple 2 : Catégorie discrète
Configurez une catégorie avec peu de flèches et rotation lente :

```javascript
'draft': {
  count: 1,
  orbitRadius: 40,
  speed: 0.0003,
  arrowGifs: ['white-arrow-pointing-right-transparent-background-usagif.gif']
}
```

### Exemple 3 : Désactiver les satellites
Configurez le count à 0 :

```javascript
'no-satellites': {
  count: 0,
  orbitRadius: 0,
  speed: 0,
  arrowGifs: []
}
```

## Avantages du nouveau système

1. **Sémantique claire** : Les catégories sont plus significatives que les zones polygones
2. **Facilité de gestion** : Les catégories WordPress sont déjà utilisées pour organiser le contenu
3. **Flexibilité** : Chaque catégorie peut avoir une configuration unique
4. **Cohérence** : Utilise le système de taxonomie standard de WordPress
5. **Performance** : Moins de calculs, configuration directe par catégorie

## Notes importantes

- La catégorie principale (première dans le tableau) est utilisée pour la configuration
- Si aucune configuration n'existe pour une catégorie, la configuration 'default' est utilisée
- Les slugs de catégories sont utilisés (pas les noms affichés)
- La configuration est définie côté JavaScript dans `arrowSatellites.js`

## Fichiers modifiés

- `/assets/js/utils/arrowSatellites.js` - Logique principale
- Cette documentation

## Tests recommandés

1. Vérifier que chaque catégorie affiche le bon nombre de flèches
2. Tester la vitesse de rotation pour chaque catégorie
3. Vérifier que les GIFs appropriés sont utilisés
4. Tester avec des nœuds sans catégorie (utilise 'default')
5. Vérifier les performances avec de nombreux satellites
