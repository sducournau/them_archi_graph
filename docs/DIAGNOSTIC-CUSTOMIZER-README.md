# 🔍 Résumé du Diagnostic Customizer

## Outils de Diagnostic Créés

J'ai créé 3 outils de diagnostic pour vous aider à identifier et résoudre les problèmes de persistance des paramètres du Customizer :

### 1. **test-customizer-debug.php** ⭐ PRINCIPAL
**Ce qu'il fait :**
- Teste TOUS les paramètres du Customizer définis dans `inc/customizer.php`
- Vérifie les types de données (bool, int, float, string, color)
- Compare les valeurs en base de données vs valeurs récupérées
- Vérifie que tous les hooks WordPress sont correctement enregistrés
- Affiche le CSS généré par le Customizer
- **Met en évidence automatiquement les problèmes détectés**

**Comment l'utiliser :**
```
http://votre-site.local/wp-content/themes/archi-graph-template/test-customizer-debug.php
```

### 2. **test-header-params.php** 🎯 HEADER SPÉCIFIQUE
**Ce qu'il fait :**
- Teste uniquement les 9 paramètres du header
- Montre comment le HTML du header est généré
- Affiche le CSS qui devrait être appliqué
- Utilise JavaScript pour vérifier les styles réellement calculés par le navigateur

**Comment l'utiliser :**
```
http://votre-site.local/wp-content/themes/archi-graph-template/test-header-params.php
```
Puis ouvrez la console du navigateur (F12) pour voir les styles calculés.

### 3. **CUSTOMIZER-DIAGNOSTIC-GUIDE.md** 📖 GUIDE COMPLET
**Ce qu'il contient :**
- Procédure complète de diagnostic étape par étape
- Explication de chaque type de problème possible
- Solutions détaillées pour chaque problème
- Checklist de vérification

**Où le trouver :**
```
docs/CUSTOMIZER-DIAGNOSTIC-GUIDE.md
```

## 🚀 Procédure Recommandée

### Étape 1 : Tester Maintenant
1. Modifiez quelques paramètres du header dans le Customizer :
   - Changez la hauteur du header
   - Modifiez la couleur de fond
   - Changez la position du logo

2. Cliquez sur **"Publier"**

3. Ouvrez dans votre navigateur :
   ```
   test-customizer-debug.php
   ```

4. Regardez la section **"⚠️ Problèmes Détectés"** en haut - elle vous dira EXACTEMENT quels paramètres ont un problème

### Étape 2 : Identifier le Type de Problème

Le script va automatiquement détecter :

#### Type 1 : Problème de Type de Données
```
✗ Type incorrect: attendu bool, obtenu string
```
**Cause :** La fonction `sanitize_callback` retourne le mauvais type.

#### Type 2 : Valeur Non Sauvegardée
```
⚠️ Valeur non trouvée en base de données mais différente du défaut
```
**Cause :** La valeur n'est pas enregistrée correctement en BD.

#### Type 3 : Hook Manquant
```
✗ wp_head → archi_customizer_css NON ENREGISTRÉ
```
**Cause :** Le CSS n'est pas injecté dans le `<head>`.

### Étape 3 : Vérifier Visuellement

1. Ouvrez `test-header-params.php`
2. Comparez les 3 sections :
   - **Paramètres récupérés** : Ce que WordPress lit
   - **CSS généré** : Ce qui devrait être appliqué
   - **Vérification JavaScript** : Ce que le navigateur calcule réellement

Si les 3 correspondent mais que le header ne change pas visuellement, c'est probablement un conflit CSS.

## 🔧 Solutions Rapides aux Problèmes Courants

### Problème : Les booléens ne fonctionnent pas

**Vérifiez dans inc/customizer.php :**
```php
function archi_sanitize_checkbox($value) {
    return (bool) $value; // ← Doit convertir en vrai booléen, pas en string
}
```

### Problème : Les valeurs ne sont pas sauvegardées

**Vérifiez que le `sanitize_callback` existe :**
```php
$wp_customize->add_setting('archi_header_transparent', [
    'default' => false,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_checkbox' // ← Cette fonction doit exister
]);
```

### Problème : Le CSS ne s'applique pas

**Vérifiez la priorité du hook dans inc/customizer.php (ligne ~1034) :**
```php
add_action('wp_head', 'archi_customizer_css', 999); // ← Priorité élevée
```

## 📊 Ce Que Vous Devez Voir

### Dans test-customizer-debug.php

**Section "Problèmes Détectés" :**
- ✅ **Vide ou message de succès** = Tout va bien
- ⚠️ **Alertes orange/rouge** = Problèmes à corriger

**Tableau par catégorie :**
- Colonne **"En BD"** : Doit afficher la valeur, pas `null`
- Colonne **"Status"** : Doit afficher ✓ Personnalisé (pas "Défaut")

**Section "Hooks WordPress" :**
- Toutes les lignes doivent avoir ✓ en vert

### Dans test-header-params.php

**Paramètres récupérés :**
- Doivent correspondre à ce que vous avez configuré dans le Customizer

**Console du navigateur (F12) :**
- "Hauteur calculée" doit correspondre à votre choix
- "Background calculé" doit correspondre à la couleur choisie

## ⚠️ Ce Qu'il Faut Vérifier Spécifiquement pour le Header

D'après votre question, certains paramètres du header ne fonctionnent pas. Voici les plus susceptibles d'avoir un problème :

1. **archi_header_transparent** (type: bool)
2. **archi_header_height** (type: string, valeurs: compact|normal|large|extra-large)
3. **archi_header_shadow** (type: string, valeurs: none|light|medium|strong)
4. **archi_header_logo_position** (type: string, valeurs: left|center|right)
5. **archi_header_sticky_behavior** (type: string, valeurs: always|hide-on-scroll-down|show-on-scroll-up)

## 🎯 Action Immédiate

**Exécutez ceci MAINTENANT :**

1. Ouvrez votre navigateur
2. Allez sur : `test-customizer-debug.php`
3. Faites défiler jusqu'à "⚠️ Problèmes Détectés"
4. **Copiez-collez** tout le contenu de cette section dans votre réponse

Cela me permettra de voir exactement quels paramètres posent problème et pourquoi.

---

**Note importante :** Ces scripts sont des outils de DIAGNOSTIC uniquement. Ils ne modifient rien, ils ne font que lire et afficher les informations pour identifier les problèmes.
