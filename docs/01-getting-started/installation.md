# Guide de Configuration - Thème Archi Graph

## Démarrage Rapide

### Étape 1 : Activer le Thème

**Administration WordPress → Apparence → Thèmes → Activer "Archi Graph Theme"**

### Étape 2 : Lancer le Diagnostic

**Administration WordPress → Apparence → 🔍 Diagnostic**

Le système vérifie automatiquement :

- ✅ Articles configurés
- ✅ Catégories disponibles
- ✅ Templates présents
- ✅ API fonctionnelle

### Étape 3 : Créer du Contenu

#### Option A : Articles de Test Automatiques

**Dans la Page de Diagnostic → Cliquer sur "Créer des articles de test"**

Crée automatiquement :

- 4 articles de démonstration
- 4 catégories colorées
- Configuration complète

#### Option B : Configuration Manuelle

Pour chaque article :

1. **Modifier l'article**
2. **Cocher "Afficher dans le graphique"** (méta-box à droite)
3. **Assigner des catégories**
4. **Sauvegarder**

### Étape 4 : Voir les Résultats

**Visiter : http://votresite.com/\*\*

Le graphique interactif s'affiche automatiquement ! 🎉

## Interactions Disponibles

- 🖱️ **Glisser** : Déplacer les nœuds
- 🔍 **Molette de la Souris** : Zoom avant/arrière
- 👆 **Survol** : Voir les détails
- 🖱️ **Clic** : Ouvrir l'article

## Relations Automatiques

Les articles sont connectés en fonction de :

- Catégories partagées (40 pts/cat)
- Tags communs (25 pts/tag)
- Catégorie principale identique (20 pts)
- Proximité temporelle (10 pts max)

**Score plus élevé = connexion plus forte !**

## Dépannage

### Problème : Graphique vide

**Solution 1 :** Vérifier les articles configurés

- Admin → Apparence → Diagnostic
- Vérifier : "X article(s) configuré(s)"

**Solution 2 :** Créer des articles de test

- Dans Diagnostic → "Créer des articles de test"

### Problème : Page blanche

**Solution :** Vérifier JavaScript

- Ouvrir la console du navigateur (F12)
- Chercher les erreurs en rouge
- Vider le cache (Ctrl+Shift+R)

### Problème : Erreur API 404

**Solution :** Réenregistrer les permaliens

- Admin → Réglages → Permaliens
- Sélectionner "Nom de l'article"
- Sauvegarder

## Détails de Configuration

### Configuration des Articles

Chaque article peut être configuré avec :

- **Visibilité** : Afficher dans le graphique (oui/non)
- **Couleur du nœud** : Couleur personnalisée pour le nœud
- **Taille du nœud** : Taille en pixels (20-100)
- **Niveau de priorité** : faible, normal, élevé

### Configuration des Catégories

Chaque catégorie peut avoir :

- **Couleur personnalisée** : Pour l'organisation visuelle
- **Description** : Informations supplémentaires
- **Icône** : Icône personnalisée (optionnel)

### Paramètres du Graphique

Le graphique peut être configuré via l'administration WordPress :

- **Admin → Apparence → Paramètres du Graphique**
- Dimensions du canevas
- Vitesse d'animation
- Espacement des nœuds
- Force de regroupement
- Seuil de visibilité des liens

## Prochaines Étapes

- [Documentation des Fonctionnalités](features.md)
- [Guide des Blocs Gutenberg](blocks.md)
- [Référence API](api.md)
- [Journal des Modifications](changelog.md)
