# Paramètres du Graphe D3.js - Documentation Complète

## 📋 Vue d'ensemble

Ce document liste **tous les paramètres configurables** du graphe D3.js dans le thème Archi-Graph. Tous ces paramètres sont transmis via `window.archiGraphSettings` et peuvent être modifiés depuis le Customizer WordPress.

## ✅ Statut : Zéro Valeur Hardcodée

**Toutes les valeurs précédemment hardcodées ont été remplacées par des paramètres configurables.** Le graphe est maintenant entièrement personnalisable sans modifier le code.

---

## 🎨 Paramètres des Nœuds

### Apparence de base
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Couleur par défaut | `archi_default_node_color` | `#3498db` | Couleur des nœuds sans couleur spécifique |
| Taille par défaut | `archi_default_node_size` | `60` | Taille en pixels des nœuds |
| Type de symbole | `archi_node_symbol_type` | `none` | Forme de fond : `none`, `circle`, `square`, `diamond`, `triangle` |
| Force de regroupement | `archi_cluster_strength` | `0.1` | Force de collision entre nœuds (0-1) |

### Badges de priorité
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Couleur "Featured" | `archi_priority_featured_color` | `#e74c3c` | Couleur du badge priorité maximale |
| Couleur "High" | `archi_priority_high_color` | `#f39c12` | Couleur du badge haute priorité |
| Taille du badge | `archi_priority_badge_size` | `8` | Rayon en pixels du badge |
| Décalage du badge | `archi_priority_badge_offset` | `5` | Distance depuis le bord du nœud |
| Couleur du contour | `archi_priority_badge_stroke_color` | `#ffffff` | Couleur du contour du badge |
| Épaisseur du contour | `archi_priority_badge_stroke_width` | `2` | Épaisseur en pixels |

### Échelle et interaction
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Échelle nœud actif | `archi_active_node_scale` | `1.5` | Facteur d'agrandissement au clic |

---

## 🔗 Paramètres des Liens

### Apparence de base
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Couleur | `archi_graph_link_color` | `#999999` | Couleur des liens standards |
| Épaisseur | `archi_graph_link_width` | `1.5` | Épaisseur en pixels |
| Opacité | `archi_graph_link_opacity` | `0.6` | Transparence (0-1) |
| Style | `archi_graph_link_style` | `solid` | `solid`, `dashed`, `dotted` |
| Afficher flèches | `archi_graph_show_arrows` | `false` | Flèches directionnelles |
| Animation | `archi_graph_link_animation` | `none` | Type d'animation des liens |

### Physique des liens
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Distance de base | `archi_link_distance` | `150` | Distance entre nœuds liés |
| Variation de distance | `archi_link_distance_variation` | `50` | Ajustement selon la proximité |
| Diviseur de force | `archi_link_strength_divisor` | `200` | Contrôle la force d'attraction |

### Styles de lignes
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Motif pointillé | `archi_dashed_line_pattern` | `5,5` | Pattern SVG pour `dashed` |
| Motif points | `archi_dotted_line_pattern` | `2,2` | Pattern SVG pour `dotted` |

### Liens spéciaux (Livre d'or)
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Couleur | `archi_guestbook_link_color` | `#2ecc71` | Couleur distinctive |
| Épaisseur | `archi_guestbook_link_width` | `3` | Plus épais que standard |
| Opacité | `archi_guestbook_link_opacity` | `0.8` | Plus visible |
| Motif de tirets | `archi_guestbook_dash_pattern` | `10,5` | Pattern SVG unique |

---

## ⚙️ Physique de la Simulation D3.js

### Forces de base
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Force de répulsion | `archi_charge_strength` | `-300` | Force entre nœuds (négatif = répulsion) |
| Distance de répulsion | `archi_charge_distance` | `200` | Distance max d'effet |
| Padding de collision | `archi_collision_padding` | `10` | Espace entre nœuds |

### Paramètres de simulation
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Alpha initial | `archi_simulation_alpha` | `1` | Énergie de départ |
| Déclin d'alpha | `archi_simulation_alpha_decay` | `0.02` | Vitesse de stabilisation |
| Déclin de vélocité | `archi_simulation_velocity_decay` | `0.3` | Amortissement du mouvement |
| Alpha au resize | `archi_resize_alpha` | `0.3` | Énergie après redimensionnement |

---

## 🏝️ Clusters et Îles Architecturales

### Apparence des clusters
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Opacité de remplissage | `archi_cluster_fill_opacity` | `0.12` | Transparence du fond |
| Épaisseur du contour | `archi_cluster_stroke_width` | `3` | Pixels du contour |
| Opacité du contour | `archi_cluster_stroke_opacity` | `0.35` | Transparence du contour |

### Labels des clusters
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Taille police label | `archi_cluster_label_font_size` | `14` | Taille en pixels |
| Poids police label | `archi_cluster_label_font_weight` | `bold` | `normal`, `bold`, `600`, etc. |
| Taille compteur | `archi_cluster_count_font_size` | `11` | Taille en pixels |
| Opacité compteur | `archi_cluster_count_opacity` | `0.7` | Transparence (0-1) |
| Ombre du texte | `archi_cluster_text_shadow` | `2px 2px 4px rgba(255,255,255,0.8)` | CSS text-shadow |

### Géométrie des clusters
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Padding de l'enveloppe | `archi_cluster_hull_padding` | `40` | Espace autour des nœuds |
| Rayon du cercle | `archi_cluster_circle_radius` | `80` | Si moins de 3 nœuds |
| Points du cercle | `archi_cluster_circle_points` | `12` | Lissage du cercle |

### Îles architecturales
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Padding de l'enveloppe | `archi_island_hull_padding` | `60` | Espace généreux |
| Facteur de lissage | `archi_island_smooth_factor` | `0.3` | Arrondi des coins (0-1) |
| Rayon du cercle | `archi_island_circle_radius` | `80` | Si moins de 3 nœuds |
| Points du cercle | `archi_island_circle_points` | `12` | Lissage |
| Padding interne | `archi_island_inner_padding` | `-20` | Pour la texture |
| Motif du contour | `archi_island_stroke_dash_array` | `8,4` | Pattern SVG |

### Labels des îles
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Taille police | `archi_island_label_font_size` | `14` | Pixels |
| Poids police | `archi_island_label_font_weight` | `600` | Semi-bold |
| Opacité label | `archi_island_label_opacity` | `0.7` | Transparence |
| Décalage Y | `archi_island_label_y_offset` | `-10` | Position verticale |
| Ombre texte | `archi_island_text_shadow` | `2px 2px 6px rgba(255,255,255,0.9)` | CSS |
| Taille compteur | `archi_island_count_font_size` | `11` | Pixels |
| Opacité compteur | `archi_island_count_opacity` | `0.6` | Transparence |

### Texture des îles
| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Opacité texture | `archi_island_texture_opacity` | `0.15` | Lignes internes |
| Motif texture | `archi_island_texture_dash_array` | `3,3` | Pattern SVG |

---

## 🎨 Couleurs des Types de Contenu

| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Projets | `archi_project_color` | `#f39c12` | Zone des projets |
| Illustrations | `archi_illustration_color` | `#3498db` | Zone des illustrations |
| Pages | `archi_pages_zone_color` | `#9b59b6` | Zone des pages |

---

## 🎭 Animations et Effets

| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Mode d'animation | `archi_graph_animation_mode` | `fade-in` | Type d'entrée des nœuds |
| Vitesse de transition | `archi_graph_transition_speed` | `500` | Millisecondes |
| Effet au survol | `archi_graph_hover_effect` | `highlight` | Type d'effet hover |

---

## 🌈 Couleurs des Catégories

| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Couleurs activées | `archi_graph_category_colors_enabled` | `false` | Activer système de couleurs |
| Palette | `archi_graph_category_palette` | `default` | `default`, `warm`, `cool`, etc. |
| Afficher légende | `archi_graph_show_category_legend` | `true` | Légende des catégories |

---

## 📊 Options d'Affichage

| Paramètre | Clé PHP | Défaut | Description |
|-----------|---------|--------|-------------|
| Titre seul popup | `archi_graph_popup_title_only` | `false` | Popup minimaliste |
| Afficher commentaires | `archi_graph_show_comments` | `true` | Nombre de commentaires |

---

## 🔧 Utilisation dans le Code

Tous ces paramètres sont accessibles via `customizerSettingsRef.current` dans GraphContainer.jsx :

```javascript
const settings = customizerSettingsRef.current;
const linkColor = settings.linkColor || '#999999';
const nodeSize = settings.defaultNodeSize || 60;
```

## 📝 Ajout de Nouveaux Paramètres

Pour ajouter un nouveau paramètre configurable :

1. **functions.php** : Ajouter dans `wp_localize_script('archi-app', 'archiGraphSettings', [...])`
2. **customizer.php** : Ajouter le contrôle dans le Customizer (optionnel)
3. **customizer-preview.js** : Ajouter le listener pour live preview (optionnel)
4. **GraphContainer.jsx** : Utiliser `settings.nouveauParametre || valeurParDefaut`

## 🎯 Valeurs par Défaut

Toutes les valeurs par défaut sont définies dans `functions.php`. Elles sont utilisées si :
- Le paramètre n'est pas défini dans le Customizer
- L'utilisateur n'a pas encore personnalisé le thème
- Le paramètre est réinitialisé

---

**Date de dernière mise à jour** : 13 novembre 2025  
**Version** : 2.0 - Toutes valeurs hardcodées éliminées  
**Auteur** : Nettoyage complet des valeurs hardcodées
