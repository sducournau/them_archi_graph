# Personnalisation des Polices

## 📝 Description

Le thème Archi-Graph permet maintenant de personnaliser la police principale utilisée sur tout le site via le Customizer WordPress.

## 🎨 Accès à l'option

1. Connectez-vous à l'administration WordPress
2. Allez dans **Apparence → Personnaliser**
3. Ouvrez la section **📝 Typographie**
4. Sélectionnez votre police dans le menu déroulant **"Police principale"**

## ✨ Polices disponibles

### Polices système (pas de chargement externe)
- **Système par défaut** - Police native de l'appareil de l'utilisateur
- **Arial** - Police classique, sans-serif
- **Helvetica** - Police élégante, sans-serif
- **Georgia** - Police serif traditionnelle
- **Times New Roman** - Police serif classique
- **Courier New** - Police monospace
- **Verdana** - Police sans-serif optimisée pour écran
- **Trebuchet MS** - Police sans-serif moderne

### Google Fonts (chargées automatiquement)
- **Roboto** - Police moderne et polyvalente
- **Open Sans** - Police très lisible, idéale pour le web
- **Lato** - Police humaniste élégante
- **Montserrat** - Police géométrique moderne
- **Poppins** - Police géométrique arrondie
- **Inter** - Police optimisée pour les interfaces
- **Playfair Display** - Police serif élégante (titres)
- **Merriweather** - Police serif optimisée pour la lecture

## 🔧 Fonctionnement technique

### Application de la police
La police sélectionnée est appliquée via CSS inline dans `<head>` :

```css
body {
    font-family: [police sélectionnée];
}
```

### Chargement des Google Fonts
Les Google Fonts sont chargées automatiquement via la fonction `archi_enqueue_google_fonts()` :
- Chargement uniquement si une Google Font est sélectionnée
- Utilise `display=swap` pour éviter le blocage du rendu
- Inclut les variations de graisse : 300, 400, 500, 600, 700

### Fallbacks
Chaque police inclut des polices de secours :
- Les Google Fonts incluent les polices système comme fallback
- Les polices système incluent des alternatives similaires

## 💡 Conseils d'utilisation

### Pour un site professionnel
- **Inter** ou **Roboto** - Modernes et professionnelles
- **Open Sans** - Très lisible, standard web

### Pour un site architectural/créatif
- **Montserrat** - Géométrique et moderne
- **Poppins** - Arrondie et accueillante
- **Playfair Display** - Élégante pour les titres

### Pour la performance
- **Système par défaut** - Aucun chargement externe, performance maximale
- **Arial** ou **Helvetica** - Polices système, chargement instantané

## 🚀 Personnalisation avancée

### Ajouter une nouvelle police

1. **Ajouter la police dans les choix** (`inc/customizer.php`, ligne ~316) :
```php
'choices' => [
    // ... polices existantes
    'ma-police' => 'Ma Police Personnalisée',
]
```

2. **Ajouter le CSS stack** (fonction `archi_get_font_family_css()`, ligne ~439) :
```php
$font_stacks = [
    // ... stacks existants
    'ma-police' => '"Ma Police", Arial, sans-serif',
];
```

3. **Si c'est une Google Font**, ajouter dans `archi_enqueue_google_fonts()` (ligne ~465) :
```php
$google_fonts = [
    // ... fonts existantes
    'ma-police' => 'Ma+Police:300,400,700',
];
```

## 📊 Impact sur les performances

### Polices système
- ✅ **Aucun impact** - Déjà présentes sur l'appareil
- ✅ **Rendu instantané**
- ✅ **Pas de requête réseau**

### Google Fonts
- ⚠️ **~10-15 KB** par police chargée
- ⚠️ **1 requête HTTP** vers Google Fonts
- ✅ **Mise en cache automatique**
- ✅ **CDN de Google** pour un chargement rapide

## 🔍 Débogage

### La police ne s'affiche pas
1. Vérifiez que vous avez sauvegardé dans le Customizer
2. Videz le cache du navigateur (Ctrl+F5)
3. Vérifiez dans l'inspecteur que le CSS est appliqué
4. Pour les Google Fonts, vérifiez la console pour les erreurs de chargement

### Vérifier la police appliquée
Ouvrez la console du navigateur et tapez :
```javascript
getComputedStyle(document.body).fontFamily
```

## 📝 Notes de développement

- **Version ajoutée** : Novembre 2025
- **Fichier principal** : `inc/customizer.php`
- **Transport** : `refresh` (rechargement de page nécessaire)
- **Sanitization** : `sanitize_text_field`
- **Compatibilité** : WordPress 5.0+

## 🔄 Compatibilité avec les autres options

La sélection de police fonctionne avec :
- ✅ Taille du texte (Customizer → Typographie)
- ✅ Toutes les couleurs du thème
- ✅ Mode responsive
- ✅ Tous les navigateurs modernes
