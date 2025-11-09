# Générateur de Témoignages pour le Livre d'Or

## 🎯 Objectif

Ce générateur crée automatiquement des témoignages réalistes pour le livre d'or (guestbook) du thème Archi-Graph. Il génère des commentaires variés provenant de différents types d'auteurs avec leurs métadonnées complètes.

## 🚀 Utilisation

### Accès au générateur

1. **WordPress Admin** → **Archi-Graph** → **Générateur de Contenu**
2. Faire défiler jusqu'à la section **"Générer des témoignages du livre d'or"**
3. Choisir le nombre de témoignages à créer (1-50)
4. Cliquer sur **"💬 Générer les témoignages"**

### Ordre recommandé

⚠️ **Important** : Pour que les témoignages puissent être liés à vos contenus, générez d'abord :
1. ✅ Projets architecturaux
2. ✅ Illustrations
3. ✅ **Puis** les témoignages

## 📊 Types de témoignages générés

Le générateur crée trois types d'auteurs différents :

### 1. Clients (33%)
Témoignages de clients satisfaits ayant fait appel aux services.

**Exemples de noms** :
- Marie Dubois
- Jean Martin
- Sophie Lefebvre
- Pierre Durant

**Exemples d'entreprises** :
- Ville de Bordeaux
- Conseil Régional Île-de-France
- Groupe Immobilier Moderne
- Office HLM Métropole

**Style de commentaires** :
- "Nous sommes ravis du travail accompli..."
- "Un accompagnement professionnel tout au long du projet..."
- "Excellent travail sur notre projet..."

### 2. Professionnels (33%)
Commentaires de confrères et partenaires du secteur.

**Exemples de noms** :
- Architecte DPLG Anne Rousseau
- Ingénieur BET Michel Fournier
- Paysagiste DPLG Sarah Cohen
- Urbaniste David Mercier

**Exemples d'entreprises** :
- Atelier d'Architecture Contemporaine
- BET Structures Innovantes
- Cabinet d'Urbanisme et Paysage
- Bureau d'Études Environnement

**Style de commentaires** :
- "Belle collaboration sur ce projet..."
- "Un projet exemplaire en termes de développement durable..."
- "Travail de qualité qui montre une vraie maîtrise..."

### 3. Visiteurs (33%)
Retours de visiteurs du site et passionnés d'architecture.

**Exemples de noms** :
- Étudiant en Architecture
- Passionné d'Architecture
- Amateur de Design
- Curieux

**Pas d'entreprise** (la plupart du temps)

**Style de commentaires** :
- "Magnifique portfolio qui témoigne d'une grande diversité..."
- "Bravo pour la qualité des réalisations présentées..."
- "Des projets inspirants qui montrent une belle vision..."

## 🔧 Métadonnées générées

Pour chaque témoignage, le générateur crée :

```php
// Métadonnées auteur
_archi_guestbook_author_name      // string - Nom de l'auteur
_archi_guestbook_author_email     // string - Email de l'auteur
_archi_guestbook_author_company   // string - Entreprise (optionnel)

// Liens vers contenus (30% des témoignages)
_archi_linked_articles            // array - IDs des articles liés (1-3 articles)

// Métadonnées du graphique
_archi_show_in_graph             // '0' ou '1' (50% de chance)
_archi_node_color                // '#2ecc71' (vert pour guestbook)
_archi_node_size                 // 50
_archi_priority_level            // 'low', 'normal', 'high'
```

## 📅 Dates de publication

Les témoignages sont créés avec des dates aléatoires sur la dernière année, pour simuler un flux naturel de commentaires.

## 🎨 Intégration au graphique

- **50% des témoignages** sont visibles dans le graphique D3.js
- **Couleur distinctive** : Vert (#2ecc71)
- **Taille des nœuds** : 50px
- **Liens automatiques** : 30% des témoignages sont liés à 1-3 articles/projets/illustrations existants

## 💻 Code technique

### Fonction principale

```php
archi_generate_sample_guestbook($count = 10)
```

**Paramètres** :
- `$count` (int) : Nombre de témoignages à créer (1-50)

**Retour** :
```php
[
    'guestbook' => 10,  // Nombre de témoignages créés
    'errors' => []      // Tableau d'erreurs éventuelles
]
```

### Fonction utilitaire

```php
archi_sample_generate_guestbook_comment($author_type)
```

**Paramètres** :
- `$author_type` (string) : 'client', 'professional', ou 'visitor'

**Retour** :
- String - Un commentaire aléatoire correspondant au type d'auteur

## 🔍 Exemple de témoignage généré

```
Titre : "Témoignage de Architecte DPLG Anne Rousseau"

Contenu : "Belle collaboration sur ce projet. L'approche architecturale 
est pertinente et bien pensée."

Métadonnées :
- Nom : Architecte DPLG Anne Rousseau
- Email : contact@atelier.fr
- Entreprise : Atelier d'Architecture Contemporaine
- Articles liés : [12, 45] (IDs de projets/illustrations)
- Visible dans le graphe : Oui
- Couleur : #2ecc71 (vert)
- Date : 2024-03-15 (aléatoire dans l'année passée)
```

## 🎭 Personnalisation

### Ajouter vos propres commentaires

Éditez `inc/sample-data-generator.php`, fonction `archi_sample_generate_guestbook_comment()` :

```php
$comments = [
    'client' => [
        "Votre nouveau commentaire client...",
        // Ajouter d'autres commentaires
    ],
    'professional' => [
        "Votre nouveau commentaire professionnel...",
    ],
    'visitor' => [
        "Votre nouveau commentaire visiteur...",
    ],
];
```

### Modifier les noms et entreprises

Dans la fonction `archi_generate_sample_guestbook()` :

```php
$author_types = [
    'client' => [
        'names' => [
            'Votre Nom 1',
            'Votre Nom 2',
            // ...
        ],
        'companies' => [
            'Votre Entreprise 1',
            'Votre Entreprise 2',
            // ...
        ]
    ],
    // ...
];
```

### Modifier les proportions

**Changer la probabilité d'apparition dans le graphe** (actuellement 50%) :

```php
// Dans archi_generate_sample_guestbook()
$show_in_graph = rand(0, 1) ? '1' : '0';  // 50%

// Pour 70% :
$show_in_graph = rand(1, 10) <= 7 ? '1' : '0';
```

**Changer la probabilité de liens** (actuellement 30%) :

```php
// Dans archi_generate_sample_guestbook()
if (!empty($all_posts) && rand(1, 10) <= 3) {  // 30%

// Pour 50% :
if (!empty($all_posts) && rand(1, 10) <= 5) {
```

## 🛠️ Fichiers modifiés

- ✅ `inc/sample-data-generator.php` - Ajout des fonctions de génération
  - `archi_sample_generate_guestbook_comment()` - Génère les commentaires
  - `archi_generate_sample_guestbook()` - Génère les entrées complètes
  - `archi_sample_data_page()` - Interface admin mise à jour

## 📱 Utilisation CLI (optionnel)

Vous pouvez aussi générer des témoignages via PHP :

```php
// Dans functions.php ou un script custom
if (function_exists('archi_generate_sample_guestbook')) {
    $stats = archi_generate_sample_guestbook(20);
    echo "Créé : " . $stats['guestbook'] . " témoignages";
}
```

## ⚡ Performance

- ✅ Génération rapide (< 1 seconde pour 10 témoignages)
- ✅ Pas de duplication de données
- ✅ Métadonnées indexées automatiquement
- ✅ Compatible avec cache WordPress

## 🐛 Dépannage

### Les témoignages n'ont pas de liens
→ Générez d'abord des projets/illustrations
→ Vérifiez qu'ils sont publiés (status = 'publish')

### Les témoignages n'apparaissent pas dans le graphe
→ Seulement 50% sont visibles par défaut
→ Vérifier `_archi_show_in_graph = '1'` dans les métadonnées
→ Vider le cache : `delete_transient('archi_graph_articles')`

### Erreurs lors de la génération
→ Vérifier que WPForms est activé
→ Vérifier les permissions d'écriture
→ Consulter les erreurs dans `$stats['errors']`

## 🎉 Résultat

Après génération, vous obtenez :

- ✅ Témoignages réalistes et variés
- ✅ Auteurs diversifiés (clients, pros, visiteurs)
- ✅ Métadonnées complètes
- ✅ Liens intelligents vers vos contenus
- ✅ Intégration au graphique D3.js
- ✅ Contenu prêt pour modération/publication

---

**Fichier** : `inc/sample-data-generator.php`  
**Version** : 1.0  
**Date** : Novembre 2025
