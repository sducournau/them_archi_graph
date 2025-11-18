🔧 Instructions pour déboguer les valeurs du graphe

## Étape 1 : Afficher window.archiGraphSettings

Ouvrez la console du navigateur et tapez :

```javascript
console.log('Current settings:', window.archiGraphSettings);
```

Vous devriez voir les valeurs actuellement chargées. Comparez avec les valeurs attendues :

```javascript
{
  chargeStrength: -80,      // PAS -200 ou autre
  chargeDistance: 300,
  collisionPadding: 15,
  centerStrength: 0.08,
  clusterStrength: 0.15,    // PAS 0.5
  defaultNodeSize: 80,      // PAS 90
  simulationAlpha: 0.3,
  simulationAlphaDecay: 0.02,
  simulationVelocityDecay: 0.4
}
```

## Étape 2 : Si les valeurs sont incorrectes

1. **Retourner sur le script clear-graph-cache.php**
   http://localhost/wordpress/wp-content/themes/archi-graph-template/clear-graph-cache.php

2. **Vérifier la section "Configuration actuelle du graphe"**
   - Les valeurs affichées sont celles qui DEVRAIENT être chargées

3. **Si les valeurs dans le script sont correctes mais pas dans la console**
   - Le problème vient du Customizer WordPress qui override les valeurs
   - Aller dans **Apparence → Personnaliser**
   - Chercher les réglages du graphe
   - Cliquer sur "Réinitialiser" ou ajuster manuellement

## Étape 3 : Forcer les valeurs par défaut

Si rien ne fonctionne, tapez dans la console du navigateur :

```javascript
window.archiGraphSettings = {
  chargeStrength: -80,
  chargeDistance: 300,
  collisionPadding: 15,
  centerStrength: 0.08,
  clusterStrength: 0.15,
  defaultNodeSize: 80,
  simulationAlpha: 0.3,
  simulationAlphaDecay: 0.02,
  simulationVelocityDecay: 0.4
};

// Puis recharger le graphe
if (window.updateGraphSettings) {
  window.updateGraphSettings(window.archiGraphSettings);
}
```

Ensuite rafraîchir la page avec Ctrl+F5.

## Erreurs SVG (NaN dans les coordonnées)

Les erreurs `MNaN,NaN` viennent de nodes sans coordonnées valides.
La conversion forcée en nombres (parseInt/parseFloat) devrait régler ça.

Après avoir fait npm run build, rafraîchir avec Ctrl+F5.
