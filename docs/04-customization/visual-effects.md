# 🎨 Effets Visuels du Graphe

Guide complet de paramétrage des effets visuels du graphe depuis WordPress Customizer.

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Accès aux paramètres](#accès-aux-paramètres)
3. [Presets d'effets](#presets-deffets)
4. [Paramètres détaillés](#paramètres-détaillés)
5. [Exemples de code](#exemples-de-code)
6. [Résolution de problèmes](#résolution-de-problèmes)

---

## 🎯 Vue d'ensemble

Depuis la version 2.0.0, **tous les effets visuels du graphe sont paramétrables** via WordPress Customizer :
- ✅ Lueur des nœuds actifs (glow)
- ✅ Ombres portées (shadows)
- ✅ Pulsations (pulse)
- ✅ Particules flottantes (particles)
- ✅ Lueur ambiante (ambient glow)
- ✅ Effets au survol (hover)
- ✅ Animations des nœuds actifs

### Architecture technique

```
WordPress Customizer (PHP)
    ↓ wp_localize_script()
JavaScript (React refs)
    ↓ customizerSettingsRef.current
SVG Filters + D3.js
    ↓ document.documentElement.style.setProperty()
CSS Variables (--archi-*)
    ↓
Visual Rendering
```

---

## 🚀 Accès aux paramètres

### Dans WordPress Admin

1. **Aller à** : `Apparence` → `Personnaliser`
2. **Ouvrir la section** : `Graphe d'Articles` → `Effets Visuels`
3. **Preview en temps réel** : Les changements s'affichent instantanément

### Dans le code

```php
// Récupérer une valeur
$glow_intensity = get_theme_mod('archi_active_node_glow_intensity', 25);

// Modifier une valeur par programmation
set_theme_mod('archi_hover_scale', 1.3);

// Appliquer un preset
$preset_values = archi_get_effects_preset_values('intense');
foreach ($preset_values as $key => $value) {
    set_theme_mod($key, $value);
}
```

---

## 🎛️ Presets d'effets

### 1. None (Aucun effet)

**Usage** : Performance maximale, design minimaliste

```
✅ Activé  : Rien
❌ Désactivé : Glow, Shadows, Pulse, Particles, Ambient
```

**Valeurs** :
- Hover scale : 1.0 (pas de zoom)
- Hover brightness : 1.0 (pas de luminosité)
- Tout le reste désactivé

### 2. Subtle (Discret)

**Usage** : Effets très légers, élégant et professionnel

```
✅ Activé  : Glow (faible), Shadows (légères), Particles (peu), Ambient (doux)
❌ Désactivé : Pulse
```

**Valeurs clés** :
- Glow intensity : 15px (très léger halo)
- Glow opacity : 0.5 (semi-transparent)
- Particles count : 10 (peu de particules)
- Particles opacity : 0.08 (quasi invisibles)
- Hover scale : 1.1 (zoom très léger)

### 3. Normal (Recommandé) ⭐

**Usage** : Équilibre parfait entre esthétique et performance

```
✅ Activé  : Tous les effets avec des valeurs équilibrées
```

**Valeurs clés** :
- Glow intensity : 25px (halo visible)
- Glow opacity : 0.8 (bien visible)
- Pulse duration : 2500ms (rythme agréable)
- Particles count : 20 (nombre optimal)
- Particles opacity : 0.15 (visibles mais non invasives)
- Hover scale : 1.2 (zoom standard)

### 4. Intense (Spectaculaire)

**Usage** : Effets très marqués, expérience immersive

```
✅ Activé  : Tous les effets au maximum
```

**Valeurs clés** :
- Glow intensity : 40px (halo très large)
- Glow opacity : 1.0 (opacité maximale)
- Pulse duration : 1500ms (pulsation rapide)
- Pulse intensity : 0.7 (pulsation marquée)
- Particles count : 40 (nombreuses particules)
- Particles opacity : 0.25 (très visibles)
- Hover scale : 1.35 (zoom important)
- Active node scale : 1.8 (nœud actif très grand)

### 5. Custom (Personnalisé)

**Usage** : Ajustements fins selon vos besoins

Sélectionner "Custom" permet d'ajuster chaque paramètre individuellement sans être écrasé par un preset.

---

## ⚙️ Paramètres détaillés

### 🌟 Active Node Glow (Lueur du nœud actif)

#### `archi_active_node_glow_enabled`
- **Type** : Boolean (Toggle)
- **Défaut** : `true`
- **Description** : Active/désactive la lueur autour du nœud actif

#### `archi_active_node_glow_intensity`
- **Type** : Range (10-50)
- **Défaut** : `25`
- **Unité** : pixels
- **Description** : Rayon du halo lumineux
- **CSS Variable** : `--archi-active-glow-intensity`

#### `archi_active_node_glow_opacity`
- **Type** : Range (0.0-1.0, step 0.1)
- **Défaut** : `0.8`
- **Description** : Opacité de la lueur (0 = transparent, 1 = opaque)
- **CSS Variable** : `--archi-active-glow-opacity`

**Exemple d'utilisation** :
```css
/* CSS généré automatiquement */
.node-circle.active {
    filter: url(#glow); /* SVG filter with intensity/opacity */
}

@keyframes halo-expand {
    0%, 100% { opacity: var(--archi-active-glow-opacity, 0.8); }
    50% { opacity: calc(var(--archi-active-glow-opacity, 0.8) * 0.5); }
}
```

---

### 🔳 Node Shadows (Ombres portées)

#### `archi_node_shadow_enabled`
- **Type** : Boolean
- **Défaut** : `true`
- **Description** : Active/désactive les ombres sous les nœuds

#### `archi_node_shadow_blur`
- **Type** : Range (2-20)
- **Défaut** : `6`
- **Unité** : pixels
- **Description** : Rayon de flou de l'ombre
- **CSS Variable** : `--archi-shadow-blur`

#### `archi_node_shadow_opacity`
- **Type** : Range (0.0-1.0, step 0.1)
- **Défaut** : `0.3`
- **Description** : Opacité de l'ombre
- **CSS Variable** : `--archi-shadow-opacity`

**Exemple SVG Filter** :
```jsx
<filter id="drop-shadow">
  <feGaussianBlur in="SourceAlpha" stdDeviation={shadowBlur} />
  <feOffset dx="0" dy="2" />
  <feComponentTransfer>
    <feFuncA type="linear" slope={shadowOpacity} />
  </feComponentTransfer>
  <feMerge>
    <feMergeNode />
    <feMergeNode in="SourceGraphic" />
  </feMerge>
</filter>
```

---

### 💓 Node Pulse (Pulsation)

#### `archi_node_pulse_enabled`
- **Type** : Boolean
- **Défaut** : `true`
- **Description** : Active/désactive la pulsation des nœuds importants

#### `archi_node_pulse_duration`
- **Type** : Range (1000-5000)
- **Défaut** : `2500`
- **Unité** : millisecondes
- **Description** : Durée d'un cycle complet de pulsation
- **CSS Variable** : `--archi-pulse-duration`

#### `archi_node_pulse_intensity`
- **Type** : Range (0.5-1.0, step 0.05)
- **Défaut** : `0.85`
- **Description** : Intensité de la pulsation (plus bas = pulsation plus marquée)
- **CSS Variable** : `--archi-pulse-intensity`

**JavaScript** :
```javascript
function applyPulseEffect(imageElement, nodeData, settings) {
    const duration = settings.nodePulseDuration ?? 2500;
    const intensity = settings.nodePulseIntensity ?? 0.85;
    const pulseScale = 1 + (1 - intensity) * 0.5;
    
    d3.select(imageElement)
        .transition()
        .duration(duration / 2)
        .attr('opacity', intensity)
        .transition()
        .duration(duration / 2)
        .attr('opacity', 1)
        .on('end', function repeat() {
            // Loop
        });
}
```

---

### ✨ Particles (Particules flottantes)

#### `archi_particles_enabled`
- **Type** : Boolean
- **Défaut** : `true`
- **Description** : Active/désactive le système de particules

#### `archi_particles_count`
- **Type** : Range (10-50)
- **Défaut** : `20`
- **Description** : Nombre de particules affichées
- **⚠️ Note** : Nécessite un rechargement de la page en preview

#### `archi_particles_opacity`
- **Type** : Range (0.05-0.5, step 0.01)
- **Défaut** : `0.15`
- **Description** : Opacité des particules
- **CSS Variable** : `--archi-particles-opacity`

#### `archi_particles_speed`
- **Type** : Range (10-30)
- **Défaut** : `15`
- **Unité** : secondes
- **Description** : Vitesse de déplacement (plus haut = plus lent)
- **CSS Variable** : `--archi-particles-speed`

**CSS Animations** :
```css
.graph-particle {
    opacity: var(--archi-particles-opacity, 0.15);
    animation: particle-float var(--archi-particles-speed, 15s) linear infinite;
}

.graph-particle:nth-child(2n) {
    animation-duration: calc(var(--archi-particles-speed, 15s) * 1.33);
}

@keyframes particle-float {
    0% { 
        opacity: 0;
        transform: translateY(100vh) scale(0);
    }
    10% { 
        opacity: calc(var(--archi-particles-opacity, 0.15) * 0.6);
    }
    50% { 
        opacity: var(--archi-particles-opacity, 0.15);
        transform: translateY(50vh) scale(1);
    }
    90% { 
        opacity: calc(var(--archi-particles-opacity, 0.15) * 0.6);
    }
    100% { 
        opacity: 0;
        transform: translateY(0) scale(0);
    }
}
```

---

### 🌊 Ambient Glow (Lueur ambiante)

#### `archi_ambient_glow_enabled`
- **Type** : Boolean
- **Défaut** : `true`
- **Description** : Active/désactive la pulsation lumineuse de fond

#### `archi_ambient_glow_opacity`
- **Type** : Range (0.1-0.6, step 0.05)
- **Défaut** : `0.3`
- **Description** : Opacité de la lueur ambiante
- **CSS Variable** : `--archi-ambient-glow-opacity`

#### `archi_ambient_glow_duration`
- **Type** : Range (4-15)
- **Défaut** : `8`
- **Unité** : secondes
- **Description** : Durée d'un cycle complet
- **CSS Variable** : `--archi-ambient-glow-duration`

**CSS** :
```css
.graph-ambient-glow {
    background: radial-gradient(
        circle at center,
        rgba(52, 152, 219, var(--archi-ambient-glow-opacity, 0.3)),
        transparent 70%
    );
    animation: ambient-pulse var(--archi-ambient-glow-duration, 8s) ease-in-out infinite;
}

@keyframes ambient-pulse {
    0%, 100% { opacity: calc(var(--archi-ambient-glow-opacity, 0.3) * 0.5); }
    50% { opacity: var(--archi-ambient-glow-opacity, 0.3); }
}
```

---

### 👆 Hover Effects (Effets au survol)

#### `archi_hover_scale`
- **Type** : Range (1.0-1.5, step 0.05)
- **Défaut** : `1.2`
- **Description** : Zoom appliqué au survol (1.0 = pas de zoom)
- **CSS Variable** : `--archi-hover-scale`

#### `archi_hover_transition_duration`
- **Type** : Range (100-800)
- **Défaut** : `300`
- **Unité** : millisecondes
- **Description** : Durée de la transition hover
- **CSS Variable** : `--archi-hover-transition`

#### `archi_hover_brightness`
- **Type** : Range (1.0-1.5, step 0.05)
- **Défaut** : `1.15`
- **Description** : Augmentation de luminosité (1.0 = pas de changement)
- **CSS Variable** : `--archi-hover-brightness`

**JavaScript** :
```javascript
function applyHoverScale(imageElement, nodeData, isHovering, settings) {
    const scale = isHovering ? (settings.hoverScale ?? 1.2) : 1;
    const brightness = isHovering ? (settings.hoverBrightness ?? 1.15) : 1;
    
    d3.select(imageElement)
        .transition()
        .duration(settings.hoverTransitionDuration ?? 300)
        .attr('transform', `scale(${scale})`)
        .style('filter', `brightness(${brightness})`);
}
```

---

### 🎯 Active Node (Nœud actif)

#### `archi_active_node_scale`
- **Type** : Range (1.2-2.0, step 0.1)
- **Défaut** : `1.5`
- **Description** : Zoom du nœud actif
- **CSS Variable** : `--archi-active-scale`

#### `archi_active_node_glow_animation`
- **Type** : Select
- **Options** : `none`, `pulse`, `breathe`, `glow`
- **Défaut** : `pulse`
- **Description** : Type d'animation du halo
- **CSS Variable** : `--archi-active-animation`

**Animations disponibles** :
- **none** : Pas d'animation, halo statique
- **pulse** : Pulsation rapide et marquée
- **breathe** : Respiration lente et douce
- **glow** : Scintillement aléatoire

---

## 💻 Exemples de code

### Exemple 1 : Désactiver tous les effets par programmation

```php
function archi_disable_all_effects() {
    set_theme_mod('archi_active_node_glow_enabled', false);
    set_theme_mod('archi_node_shadow_enabled', false);
    set_theme_mod('archi_node_pulse_enabled', false);
    set_theme_mod('archi_particles_enabled', false);
    set_theme_mod('archi_ambient_glow_enabled', false);
}
add_action('after_setup_theme', 'archi_disable_all_effects');
```

### Exemple 2 : Créer un preset custom

```php
function archi_my_custom_preset() {
    return [
        'archi_active_node_glow_enabled' => true,
        'archi_active_node_glow_intensity' => 30,
        'archi_active_node_glow_opacity' => 0.9,
        'archi_node_shadow_enabled' => true,
        'archi_node_shadow_blur' => 8,
        'archi_hover_scale' => 1.25,
        'archi_particles_count' => 30,
        // ... autres valeurs
    ];
}

// Appliquer le preset
$preset_values = archi_my_custom_preset();
foreach ($preset_values as $key => $value) {
    set_theme_mod($key, $value);
}
```

### Exemple 3 : Modifier dynamiquement depuis JavaScript

```javascript
// Dans le Customizer preview
wp.customize('archi_hover_scale', function(value) {
    value.bind(function(newval) {
        document.documentElement.style.setProperty('--archi-hover-scale', newval);
    });
});

// Dans le front-end normal
if (typeof archiGraphSettings !== 'undefined') {
    const glowIntensity = archiGraphSettings.activeNodeGlowIntensity;
    console.log('Glow intensity:', glowIntensity);
}
```

### Exemple 4 : Hook pour modifier les valeurs avant injection

```php
add_filter('archi_visual_effects_css_vars', function($vars) {
    // Doubler l'intensité du glow en mode sombre
    if (get_theme_mod('archi_dark_mode_enabled', false)) {
        $vars['--archi-active-glow-intensity'] = 
            (intval($vars['--archi-active-glow-intensity']) * 2) . 'px';
    }
    
    return $vars;
}, 10, 1);
```

---

## 🐛 Résolution de problèmes

### Problème 1 : Les changements ne s'affichent pas

**Symptômes** : Modifications dans Customizer sans effet visuel

**Solutions** :
1. **Vider le cache** :
   ```php
   // Aller dans l'admin WP et exécuter :
   delete_transient('archi_graph_articles');
   ```

2. **Vérifier que les scripts sont chargés** :
   ```javascript
   // Dans la console navigateur :
   console.log(archiGraphSettings);
   // Doit afficher un objet avec tous les paramètres
   ```

3. **Vérifier les CSS variables** :
   ```javascript
   // Dans la console :
   getComputedStyle(document.documentElement)
       .getPropertyValue('--archi-hover-scale');
   // Doit retourner la valeur définie
   ```

### Problème 2 : Les particules ne s'affichent pas

**Symptômes** : Canvas vide, pas de particules flottantes

**Solutions** :
1. **Vérifier que particles est activé** :
   ```php
   $enabled = get_theme_mod('archi_particles_enabled', true);
   var_dump($enabled); // Doit être true
   ```

2. **Vérifier le canvas** :
   ```javascript
   // Console navigateur :
   document.querySelector('.graph-particles-canvas');
   // Doit retourner un élément <canvas>
   ```

3. **Augmenter l'opacité** :
   - Aller dans Customizer
   - Augmenter `archi_particles_opacity` à 0.3+
   - Si toujours invisible, vérifier z-index CSS

### Problème 3 : Pulsation trop rapide/lente

**Symptômes** : Animation pulse désagréable

**Solutions** :
1. **Ajuster la durée** :
   - Trop rapide : augmenter `archi_node_pulse_duration` (3000-4000ms)
   - Trop lente : diminuer à 1500-2000ms

2. **Ajuster l'intensité** :
   - Trop marquée : augmenter `archi_node_pulse_intensity` vers 0.9-0.95
   - Pas assez visible : diminuer vers 0.7-0.8

### Problème 4 : Performance dégradée

**Symptômes** : Graphe lag, animations saccadées

**Solutions** :
1. **Utiliser le preset "subtle"** :
   ```php
   set_theme_mod('archi_effects_preset', 'subtle');
   ```

2. **Désactiver les particules** :
   ```php
   set_theme_mod('archi_particles_enabled', false);
   ```

3. **Réduire le nombre de particules** :
   ```php
   set_theme_mod('archi_particles_count', 10);
   ```

4. **Désactiver le pulse** :
   ```php
   set_theme_mod('archi_node_pulse_enabled', false);
   ```

### Problème 5 : Preset ne s'applique pas

**Symptômes** : Sélection d'un preset sans changement

**Solutions** :
1. **Vérifier le mode Custom** :
   - Si "Custom" est sélectionné, les presets n'écrasent rien
   - Sélectionner un autre preset pour appliquer ses valeurs

2. **Rafraîchir la preview** :
   - Dans Customizer, cliquer sur l'icône refresh ⟳
   - Ou recharger la page manuellement

3. **Vérifier les hooks** :
   ```php
   // S'assurer qu'aucun filtre ne bloque les presets
   remove_all_filters('archi_effects_preset_values');
   ```

### Problème 6 : Variables CSS non reconnues

**Symptômes** : Effets ne fonctionnent pas, console affiche des erreurs CSS

**Solutions** :
1. **Vérifier l'injection dans <head>** :
   ```html
   <!-- Doit être présent dans le <head> : -->
   <style id="archi-visual-effects-vars">
   :root {
       --archi-active-glow-intensity: 25px;
       --archi-hover-scale: 1.2;
       /* ... */
   }
   </style>
   ```

2. **Forcer la régénération** :
   ```php
   delete_option('theme_mods_archi-graph-template');
   // Puis reconfigurer dans Customizer
   ```

---

## 📊 Recommandations de performance

### Sites à fort trafic
```php
// Preset recommandé : subtle
set_theme_mod('archi_effects_preset', 'subtle');
set_theme_mod('archi_particles_count', 10);
set_theme_mod('archi_node_pulse_enabled', false);
```

### Portfolios visuels
```php
// Preset recommandé : normal ou intense
set_theme_mod('archi_effects_preset', 'normal');
set_theme_mod('archi_active_node_glow_intensity', 30);
set_theme_mod('archi_particles_count', 25);
```

### Mode sombre
```php
// Augmenter les intensités pour contraste
add_filter('archi_visual_effects_css_vars', function($vars) {
    $vars['--archi-active-glow-opacity'] = '1.0';
    $vars['--archi-shadow-opacity'] = '0.5';
    return $vars;
});
```

---

## 🔗 Voir aussi

- [Graph System Documentation](../03-graph-system/README.md)
- [Customizer API](../05-development/customizer-api.md)
- [Performance Optimization](../05-development/performance.md)

---

**Dernière mise à jour** : Version 2.0.0 (Nov 2025)  
**Auteur** : Archi-Graph Theme
