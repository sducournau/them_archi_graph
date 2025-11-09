# Mise à jour du Graphique - Images PNG Transparentes

**Date :** 3 novembre 2025  
**Version :** 2.0 - Graphique PNG Transparent

## 🎨 Changements Majeurs

### ✅ Suppression des bulles circulaires

Le graphique n'utilise plus de cercles colorés en arrière-plan. Les images PNG avec fond transparent sont maintenant affichées en entier.

### 🖼️ Images complètes PNG

- Les images ne sont plus découpées en forme de cercle
- Le `clip-path: circle(50%)` a été supprimé
- Les images PNG avec fond transparent s'affichent entièrement
- Meilleure utilisation de l'espace visuel

### 📏 Tailles agrandies pour les projets architecturaux

Les projets architecturaux (`archi_project`) ont maintenant accès à des **tailles beaucoup plus grandes** :

| Type de post | Taille min | Taille max | Pas |
|--------------|-----------|-----------|-----|
| Articles normaux | 40px | 120px | 10px |
| Projets architecturaux | **60px** | **200px** | **20px** |

### ⚙️ Paramétrage depuis l'éditeur

Chaque projet architectural peut maintenant avoir une taille différente, configurable directement depuis la meta box "Paramètres du graphique" dans l'éditeur.

## 📁 Fichiers Modifiés

### JavaScript
- `assets/js/components/GraphContainer.jsx` - Suppression du cercle de fond, ajustement du rendu des images
- `assets/js/components/Node.jsx` - Suppression de l'élément `<circle>` de fond

### CSS
- `assets/css/main.scss` - Masquage de `.node-background`, suppression du border-radius
- `assets/css/graph-white.css` - Suppression du `clip-path: circle(50%)`
- `assets/css/graph-force-visible.css` - Forcer le masquage des cercles de fond

### PHP
- `inc/meta-boxes.php` - Plages de taille différentes selon le type de post

### Documentation
- `docs/graph-png-transparent-images.md` - Documentation complète du nouveau système

## 🚀 Migration

### Pour les contenus existants

1. Les images existantes continuent de fonctionner
2. Recommandé : remplacer par des PNG avec fond transparent
3. Ajuster les tailles dans l'éditeur selon les besoins

### Pour les nouveaux contenus

1. Utiliser des images PNG avec canal alpha (transparence)
2. Dimensions recommandées :
   - Articles : 100-150px
   - Projets architecturaux : 150-250px
3. Optimiser pour le web (< 50 Ko)

## 🎯 Avantages

✅ **Meilleure flexibilité visuelle** - Les images peuvent avoir des formes variées  
✅ **Plus d'espace pour le contenu** - Pas de perte due au découpage circulaire  
✅ **Tailles différenciées** - Les projets importants peuvent être plus grands  
✅ **Design plus moderne** - Utilisation optimale des PNG transparents  
✅ **Configuration individuelle** - Chaque projet a sa propre taille  

## 🔧 Tests Recommandés

1. ✅ Vérifier l'affichage sur la page d'accueil
2. ✅ Tester le hover et les animations
3. ✅ Vérifier les différentes tailles de nœuds
4. ✅ Tester sur différents navigateurs
5. ✅ Vider le cache navigateur
6. ✅ Compiler les assets avec webpack

## 🏗️ Compilation

Pour compiler les changements JavaScript et CSS :

```bash
npm run build
# ou
npm run watch
```

## 📸 Création d'Images PNG Transparentes

### Outils recommandés
- **Photoshop** : Sauvegarder pour le web (PNG-24 avec transparence)
- **GIMP** : Exporter en PNG avec canal alpha
- **Figma** : Exporter en PNG avec transparence
- **Canva** : Télécharger avec fond transparent (Pro)

### Template Illustrator/Photoshop
1. Créer un document carré (ex: 200x200px)
2. Fond transparent
3. Centrer l'élément principal
4. Laisser de l'espace sur les bords (10-20px)
5. Exporter en PNG avec transparence

## 🐛 Débogage

### L'image apparaît carrée avec fond blanc
➡️ Vérifier que l'image est bien en PNG avec canal alpha  
➡️ Vérifier dans Photoshop/GIMP que la transparence est activée

### La taille ne change pas
➡️ Vider le cache du navigateur  
➡️ Vérifier que `_archi_node_size` est bien enregistré dans la base de données  
➡️ Recompiler les assets JavaScript

### Les cercles sont toujours visibles
➡️ Vérifier que le CSS est bien compilé  
➡️ Vider le cache  
➡️ Forcer le rechargement avec Ctrl+Shift+R

## 📞 Support

En cas de problème :
1. Vérifier la console JavaScript (F12)
2. Vérifier les erreurs PHP (logs WordPress)
3. S'assurer que tous les fichiers ont été sauvegardés
4. Recompiler les assets avec `npm run build`

## 🔄 Retour en arrière (si nécessaire)

Si vous souhaitez revenir aux cercles :

1. Utiliser `git revert` sur les commits concernés
2. Ou restaurer les fichiers depuis la sauvegarde
3. Recompiler les assets

## 📚 Documentation

Voir la documentation complète dans :
- `docs/graph-png-transparent-images.md` - Guide utilisateur complet
- `.github/copilot-instructions.md` - Instructions de développement
