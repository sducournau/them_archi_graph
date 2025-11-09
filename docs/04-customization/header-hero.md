# Header & Hero Section - Documentation Complète

## Vue d'ensemble

Le thème Archi Graph propose deux sections visuelles principales sur la page d'accueil :
- **Le Header** : Navigation fixe intelligente avec comportement de scroll
- **La Section Hero** : Introduction visuelle plein écran avant le graphique

---

## 🎯 Section Hero

### Fonctionnalités

#### 1. Affichage Plein Écran
- La section hero occupe 100% de la hauteur de la fenêtre (viewport)
- Elle s'affiche avant le graphe interactif
- Design moderne avec dégradé de couleurs personnalisable

#### 2. Contenu Personnalisable
- **Titre** : Affiche le nom du site ou un titre personnalisé
- **Description** : Affiche le slogan du site ou une description personnalisée
- **Instructions** : Message d'invitation pour guider l'utilisateur
- **Statistiques** : Affiche le nombre de projets, illustrations et articles

#### 3. Actions Utilisateur
- **Bouton principal** : Scroll automatique vers le graphe
- **Bouton secondaire** : Lien vers l'archive des projets
- **Indicateur de scroll** : Flèche animée en bas de page

### Configuration

Accessible depuis **Apparence > Archi Graph Settings > Section Hero** :

#### Options Disponibles

| Option | Description | Par défaut |
|--------|-------------|------------|
| **Activer/Désactiver** | Active ou désactive complètement la section | Activé |
| **Titre Personnalisé** | Remplace le nom du site | Nom du site |
| **Description** | Remplace le slogan du site | Slogan du site |
| **Variante de Couleur** | 5 thèmes de couleurs | Violet |
| **Masquage Automatique** | Scroll automatique après délai | Désactivé |
| **Délai de Masquage** | De 1 à 30 secondes | 5 secondes |
| **Afficher Statistiques** | Compte projets/illustrations | Activé |

#### Variantes de Couleur

Choisissez parmi 5 thèmes de couleurs :

| Variante | Dégradé | Usage |
|----------|---------|-------|
| **Violet** (défaut) | #667eea → #764ba2 | Élégant, professionnel |
| **Bleu** | #4facfe → #00f2fe | Dynamique, moderne |
| **Orange** | #f093fb → #f5576c | Énergique, créatif |
| **Vert** | #4facfe → #43e97b | Frais, naturel |
| **Sombre** | #434343 → #000000 | Professionnel, sobre |

### Personnalisation CSS

**Fichier** : `assets/css/hero-section.css`

```css
/* Variables personnalisables */
:root {
    --hero-gradient-start: #667eea;
    --hero-gradient-end: #764ba2;
    --hero-text-color: #ffffff;
    --hero-overlay-opacity: 0.85;
}

/* Modifier le dégradé */
.hero-section.hero-variant-custom {
    background: linear-gradient(135deg, 
        var(--hero-gradient-start) 0%, 
        var(--hero-gradient-end) 100%);
}
```

### Structure des Fichiers

```
inc/
  └── hero-management.php       # Gestion options & helpers
assets/
  └── css/
      └── hero-section.css      # Tous les styles hero
template-parts/
  └── hero-section.php          # Template hero (si utilisé)
```

---

## 🎯 Header Navigation

### Comportement Intelligent

Le header utilise un comportement de disparition/réapparition basé sur :
- **Direction du scroll** : Disparaît au scroll vers le bas, réapparaît vers le haut
- **Position de la souris** : Réapparaît au survol de la zone supérieure
- **Position dans la page** : Toujours visible en haut de page

#### 1. Position Fixe
- Le header est en `position: fixed` pour un contrôle optimal
- Transition fluide : 0.3s avec easing `ease-in-out`
- `z-index` élevé pour rester au-dessus du contenu

#### 2. Disparition au Scroll Vers le Bas
- **Seuil de déclenchement** : 100px de scroll
- **Animation** : `transform: translateY(-100%)` + `opacity: 0`
- **Durée** : 0.3s
- **Classe ajoutée** : `.header-hidden`

#### 3. Réapparition au Scroll Vers le Haut
- Dès que l'utilisateur scrolle vers le haut, le header réapparaît
- Instantané et fluide
- Le header reste visible si position < 100px

#### 4. Réapparition au Survol
Deux méthodes pour faire réapparaître le header :
- **Survol zone supérieure** : Déplacer la souris dans les 50 premiers pixels
- **Survol direct** : Survoler le header lui-même
- **Classe ajoutée** : `.header-peek`

### Implémentation Technique

#### CSS (assets/css/header.css)

```css
.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
    transition: transform 0.3s ease-in-out, 
                opacity 0.3s ease-in-out;
    transform: translateY(0);
    opacity: 1;
}

.site-header.header-hidden {
    transform: translateY(-100%);
    opacity: 0;
    pointer-events: none;
}

.site-header.header-hidden:hover,
.site-header.header-peek {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}

/* Compensation pour header fixe */
body {
    padding-top: 80px;
}

@media (max-width: 768px) {
    body {
        padding-top: 70px;
    }
}
```

#### JavaScript (assets/js/navigation.js)

Le script gère :

```javascript
let lastScrollTop = 0;
const scrollThreshold = 100;
const header = document.querySelector('.site-header');

window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > scrollThreshold) {
        if (scrollTop > lastScrollTop) {
            // Scroll vers le bas
            header.classList.add('header-hidden');
        } else {
            // Scroll vers le haut
            header.classList.remove('header-hidden');
        }
    } else {
        // En haut de page
        header.classList.remove('header-hidden');
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
});

// Détection du survol
document.addEventListener('mousemove', function(e) {
    if (e.clientY <= 50) {
        header.classList.add('header-peek');
    } else if (e.clientY > 150) {
        header.classList.remove('header-peek');
    }
});
```

### Variables Clés

| Variable | Valeur | Description |
|----------|--------|-------------|
| `scrollThreshold` | 100px | Distance minimum avant disparition |
| `clientY trigger` | 50px | Zone de détection du survol en haut |
| `clientY release` | 150px | Distance de relâchement du survol |
| `transition duration` | 0.3s | Durée de l'animation |

### Responsive

```css
/* Desktop (> 768px) */
body {
    padding-top: 85px;
}

.site-header {
    height: 80px;
}

/* Mobile (≤ 768px) */
@media (max-width: 768px) {
    body {
        padding-top: 70px;
    }
    
    .site-header {
        height: 65px;
    }
}
```

---

## 🎨 Personnalisation Avancée

### Modifier la Hauteur du Header

```css
:root {
    --header-height: 80px;
}

.site-header {
    height: var(--header-height);
}

body {
    padding-top: var(--header-height);
}
```

### Changer le Comportement de Scroll

```javascript
// Désactiver le comportement auto-hide
const DISABLE_AUTO_HIDE = true;

if (DISABLE_AUTO_HIDE) {
    // Header toujours visible
    header.classList.remove('header-hidden');
}

// Modifier le seuil
const CUSTOM_THRESHOLD = 200; // Au lieu de 100px
```

### Ajouter des Effets au Header

```css
/* Ombre au scroll */
.site-header.scrolled {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Changement de fond au scroll */
.site-header.scrolled {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
```

---

## 🐛 Dépannage

### Le Header ne Disparaît Pas

**Problème** : Le header reste toujours visible même au scroll

**Solutions** :
1. Vérifier que `navigation.js` est chargé
2. Vérifier la console pour erreurs JavaScript
3. Vérifier que la classe `.site-header` est présente
4. Vérifier que `scrollThreshold` n'est pas trop élevé

```bash
# Vérifier chargement JS
wp eval "echo wp_script_is('archi-navigation', 'enqueued') ? 'OK' : 'NOK';"
```

### Le Header Ne Réapparaît Pas au Survol

**Problème** : Le survol ne fonctionne pas

**Solutions** :
1. Vérifier que `.header-hidden` a `pointer-events: none`
2. Vérifier la zone de détection (50px en haut)
3. Tester avec `e.clientY` dans la console

```javascript
// Debug dans console navigateur
document.addEventListener('mousemove', (e) => {
    console.log('Mouse Y:', e.clientY);
});
```

### La Section Hero ne S'Affiche Pas

**Problème** : Pas de section hero visible

**Solutions** :
1. Vérifier dans **Apparence > Archi Graph Settings** que c'est activé
2. Vérifier que `hero-section.css` est chargé
3. Vérifier la console pour erreurs

```bash
# Vérifier option
wp option get archi_hero_enabled
```

### Le Scroll Automatique ne Fonctionne Pas

**Problème** : Le bouton hero ne scroll pas vers le graphique

**Solutions** :
1. Vérifier que l'ID du graphique est correct (`#graph-container`)
2. Vérifier que le JavaScript est chargé
3. Tester manuellement :

```javascript
document.querySelector('#scroll-to-graph').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('#graph-container').scrollIntoView({ 
        behavior: 'smooth' 
    });
});
```

---

## 📋 Checklist d'Intégration

### Pour le Header
- [ ] CSS `header.css` chargé
- [ ] JavaScript `navigation.js` chargé
- [ ] Classe `.site-header` présente dans `header.php`
- [ ] `body padding-top` ajusté à la hauteur du header
- [ ] Test scroll vers le bas/haut
- [ ] Test survol zone supérieure
- [ ] Test responsive mobile

### Pour le Hero
- [ ] CSS `hero-section.css` chargé
- [ ] Options activées dans admin
- [ ] Contenu personnalisé configuré
- [ ] Variante de couleur choisie
- [ ] Test bouton scroll vers graphique
- [ ] Test statistiques affichées
- [ ] Test masquage automatique (si activé)
- [ ] Test responsive mobile

---

## 📚 Fichiers Concernés

### Header
```
inc/
  └── (pas de fichier PHP dédié - intégré dans theme)
assets/
  ├── css/
  │   └── header.css
  └── js/
      └── navigation.js
header.php
```

### Hero
```
inc/
  └── hero-management.php
assets/
  └── css/
      └── hero-section.css
front-page.php (intégration)
```

---

## 🔄 Mises à Jour Récentes

### Version 1.1.0
- ✅ Header intelligent avec auto-hide
- ✅ Réapparition au survol
- ✅ Hero avec variantes de couleurs
- ✅ Masquage automatique hero
- ✅ Statistiques temps réel

### Version 1.0.0
- Header sticky de base
- Hero section statique

---

**Dernière mise à jour** : 4 novembre 2025  
**Version du thème** : 1.1.0
