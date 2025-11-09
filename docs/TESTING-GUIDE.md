# Guide de Test - WordPress Customizer

## 🧪 Tests à effectuer après installation

### Prérequis
- WordPress 6.0+
- PHP 7.4+
- Thème Archi-Graph activé

---

## 1. Test d'activation du Customizer

### Étapes :
1. Aller dans **WordPress Admin**
2. Naviguer vers **Apparence > Personnaliser**
3. Vérifier la présence des nouvelles sections

### Résultat attendu :
✅ 6 nouvelles sections visibles dans le panneau gauche :
- Options du Header
- Visualisation du graphique
- Typographie
- Couleurs
- Réseaux sociaux
- Pied de page

### En cas d'échec :
- Vérifier que `inc/customizer.php` est bien inclus dans `functions.php`
- Vérifier les logs d'erreur PHP
- Tester avec un autre thème pour écarter un conflit plugin

---

## 2. Test du Header - Délai de disparition

### Étapes :
1. Ouvrir **Apparence > Personnaliser > Options du Header**
2. Modifier "Temps avant disparition du header" (ex: 2000ms)
3. Observer l'aperçu en direct ⚡

### Résultat attendu :
✅ Dans l'aperçu, aller sur la page d'accueil et constater que le header disparaît après 2 secondes (au lieu de 0.5s par défaut)

### Tests complémentaires :
- **Valeur minimale** (0ms) : Le header disparaît immédiatement
- **Valeur maximale** (5000ms) : Le header reste visible 5 secondes

### En cas d'échec :
- Vérifier que `customizer-preview.js` est chargé (onglet Network du navigateur)
- Vérifier la console JavaScript pour erreurs
- Tester sur page d'accueil uniquement (front-page.php ou page-home.php)

---

## 3. Test du Header - Type d'animation

### Étapes :
1. Dans **Options du Header**, trouver "Type d'animation"
2. Sélectionner différentes options :
   - Linear
   - Ease
   - Ease-in
   - Ease-out
   - Ease-in-out (défaut)
   - Cubic-bezier

### Résultat attendu :
✅ L'animation de disparition/apparition du header change de style instantanément

**Différences visuelles :**
- **Linear** : Vitesse constante, mécanique
- **Ease** : Accélération puis décélération douce
- **Ease-in** : Démarrage lent, accélération progressive
- **Ease-out** : Démarrage rapide, décélération progressive
- **Ease-in-out** : Doux au début et à la fin
- **Cubic-bezier** : Courbe personnalisée

---

## 4. Test du Header - Durée d'animation

### Étapes :
1. Dans **Options du Header**, ajuster "Durée de l'animation"
2. Tester 0.1s (très rapide) et 2s (très lent)

### Résultat attendu :
✅ L'animation devient plus rapide ou plus lente selon la valeur

### Combinaisons intéressantes à tester :
- **Rapide + Linear** : Disparition instantanée et linéaire
- **Lent + Ease-in-out** : Disparition douce et élégante

---

## 5. Test du Header - Zone de déclenchement

### Étapes :
1. Dans **Options du Header**, modifier "Hauteur de la zone de déclenchement"
2. Tester 20px (petit) et 150px (grand)
3. **Publier les changements**
4. Aller sur le site public (pas l'aperçu)
5. Laisser le header disparaître
6. Déplacer la souris vers le haut de l'écran

### Résultat attendu :
✅ Avec 20px : Il faut être très près du bord supérieur pour déclencher le header  
✅ Avec 150px : Le header réapparaît même en étant assez loin du bord

**Note :** Ce paramètre nécessite un rechargement de page pour être visible (pas de live preview complet).

---

## 6. Test des couleurs - Live preview

### Étapes :
1. Aller dans **Apparence > Personnaliser > Couleurs**
2. Modifier la **Couleur primaire** (ex: #e74c3c rouge)
3. Observer l'aperçu en direct ⚡

### Résultat attendu :
✅ Les éléments utilisant la couleur primaire changent instantanément :
- Liens
- Boutons
- Bordures

### Test de la couleur secondaire :
1. Modifier la **Couleur secondaire** (ex: #f39c12 orange)
2. Vérifier que les éléments d'accentuation changent

---

## 7. Test de la typographie

### Étapes :
1. Aller dans **Apparence > Personnaliser > Typographie**
2. Changer la **Famille de police** (ex: Georgia, serif)
3. Observer l'aperçu ⚡

### Résultat attendu :
✅ Tout le texte du site change de police instantanément

### Test de la taille de police :
1. Modifier **Taille de base** à 18px
2. Vérifier que le texte devient plus grand
3. Tester 14px pour voir le texte plus petit

---

## 8. Test du graphique

### Étapes :
1. Aller dans **Visualisation du graphique**
2. Modifier les valeurs par défaut
3. **Publier**
4. Créer un nouvel article ou projet
5. Vérifier que les valeurs par défaut sont appliquées

### Paramètres à tester :
- **Couleur par défaut des nœuds** : Change la couleur des nouveaux nœuds
- **Taille par défaut** : Change la taille des nouveaux nœuds
- **Force de clustering** : Affecte l'espacement des nœuds
- **Durée des animations** : Vitesse des transitions du graphique

**Note :** Ces paramètres affectent les nouveaux contenus ou le rechargement du graphique.

---

## 9. Test des réseaux sociaux

### Étapes :
1. Aller dans **Réseaux sociaux**
2. Ajouter des URLs pour différents réseaux :
   ```
   Facebook: https://facebook.com/votrepage
   Twitter: https://twitter.com/votrecompte
   Instagram: https://instagram.com/votrecompte
   LinkedIn: https://linkedin.com/in/votrepage
   ```
3. **Publier**
4. Vérifier le footer du site

### Résultat attendu :
✅ Les icônes de réseaux sociaux apparaissent dans le footer avec les liens corrects

---

## 10. Test du pied de page

### Étapes :
1. Aller dans **Pied de page**
2. Modifier le **Texte de copyright** :
   ```
   © 2025 Mon Site Architecture. Tous droits réservés.
   ```
3. Observer l'aperçu ⚡

### Résultat attendu :
✅ Le texte du footer change instantanément

### Test du toggle social :
1. Décocher "Afficher les liens sociaux"
2. Les icônes disparaissent instantanément ⚡
3. Recocher pour les faire réapparaître

---

## 11. Test de persistance

### Étapes :
1. Modifier plusieurs paramètres dans le Customizer
2. Cliquer sur **Publier**
3. Fermer le Customizer
4. Rouvrir **Apparence > Personnaliser**

### Résultat attendu :
✅ Tous les paramètres modifiés sont conservés  
✅ Les valeurs affichent les dernières modifications

---

## 12. Test sur site public

### Étapes :
1. Après avoir publié des modifications dans le Customizer
2. Se déconnecter de WordPress
3. Visiter le site en tant que visiteur non connecté

### Résultat attendu :
✅ Tous les changements sont visibles sur le site public  
✅ Le comportement du header correspond aux paramètres  
✅ Les couleurs et la typographie sont appliquées

---

## 13. Test de réinitialisation

### Étapes :
1. Dans le Customizer, modifier plusieurs paramètres
2. **Ne pas publier**
3. Cliquer sur le bouton ❌ pour fermer sans sauvegarder

### Résultat attendu :
✅ Les modifications non publiées sont annulées  
✅ Le site affiche toujours les anciennes valeurs

---

## 14. Test multi-navigateurs

### Navigateurs à tester :
- ✅ Chrome/Chromium
- ✅ Firefox
- ✅ Safari (macOS/iOS)
- ✅ Edge

### Fonctionnalités à vérifier :
- Customizer s'ouvre correctement
- Live preview fonctionne
- Animations du header sont fluides
- Color picker fonctionne

---

## 15. Test responsive

### Étapes :
1. Dans l'aperçu du Customizer, cliquer sur l'icône 📱 en bas
2. Tester en mode :
   - Desktop
   - Tablet
   - Mobile

### Résultat attendu :
✅ Le Customizer fonctionne sur tous les formats  
✅ Les paramètres s'appliquent correctement  
✅ L'aperçu mobile est fonctionnel

**Note :** La zone de déclenchement du header est ajustée automatiquement pour la barre d'admin WordPress.

---

## 🐛 Debugging

### Console JavaScript
Ouvrir les DevTools (F12) et vérifier :
- Aucune erreur dans l'onglet Console
- Les fichiers `customizer-preview.js` et `customizer-controls.js` sont chargés (onglet Network)

### Logs PHP
Vérifier `/wp-content/debug.log` (si `WP_DEBUG` activé) pour :
- Erreurs de syntaxe PHP
- Hooks manquants
- Fonctions non définies

### Vérification manuelle
```php
// Ajouter temporairement dans functions.php pour debug
add_action('wp_footer', function() {
    echo '<!-- Header delay: ' . get_theme_mod('archi_header_hide_delay', 500) . ' -->';
});
```

---

## ✅ Checklist complète

- [ ] Customizer s'ouvre sans erreur
- [ ] 6 sections visibles
- [ ] Header : Délai fonctionne (live preview ⚡)
- [ ] Header : Animation type fonctionne (live preview ⚡)
- [ ] Header : Durée fonctionne (live preview ⚡)
- [ ] Header : Zone trigger fonctionne (après publication)
- [ ] Couleurs : Primaire fonctionne (live preview ⚡)
- [ ] Couleurs : Secondaire fonctionne (live preview ⚡)
- [ ] Typographie : Police fonctionne (live preview ⚡)
- [ ] Typographie : Taille fonctionne (live preview ⚡)
- [ ] Graphique : Paramètres par défaut appliqués
- [ ] Réseaux sociaux : URLs sauvegardées
- [ ] Footer : Copyright fonctionne (live preview ⚡)
- [ ] Footer : Toggle social fonctionne (live preview ⚡)
- [ ] Modifications persistent après publication
- [ ] Modifications visibles sur site public
- [ ] Fonctionne sur Chrome
- [ ] Fonctionne sur Firefox
- [ ] Fonctionne sur Safari
- [ ] Fonctionne sur mobile

---

## 📊 Résultat attendu global

**Tous les tests passent :** 🎉 Le Customizer est parfaitement fonctionnel !  
**Certains tests échouent :** 🔧 Voir section Debugging ci-dessus  
**Tous les tests échouent :** ⚠️ Vérifier que `inc/customizer.php` est bien inclus dans `functions.php`

---

## 📞 Support

En cas de problème :
1. Vérifier les prérequis (WordPress 6.0+, PHP 7.4+)
2. Consulter `docs/CUSTOMIZER-INTEGRATION.md` pour la documentation complète
3. Vérifier les logs d'erreur PHP et JavaScript
4. Tester avec les plugins désactivés pour écarter les conflits

**Bonne chance ! 🚀**
