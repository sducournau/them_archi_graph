/**
 * NOUVEAUX LISTENERS LIVE PREVIEW À AJOUTER
 * 
 * Ce fichier contient tous les listeners wp.customize() pour les 50+ nouveaux paramètres.
 * Ces listeners permettent la mise à jour en temps réel dans le Customizer.
 * 
 * À INTÉGRER DANS: assets/js/customizer-preview.js
 * 
 * Instructions:
 * 1. Ajouter ces listeners après les listeners existants
 * 2. Tous déclenchent une reconstruction complète du graphe via 'archi:refreshGraph'
 * 3. Certains pourraient être optimisés pour des mises à jour partielles
 */

(function($) {
    'use strict';

    // ========================================
    // PHYSIQUE DE LA SIMULATION
    // ========================================
    
    // Force de répulsion
    wp.customize('archi_charge_strength', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.chargeStrength = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Distance de répulsion
    wp.customize('archi_charge_distance', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.chargeDistance = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Padding de collision
    wp.customize('archi_collision_padding', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.collisionPadding = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Alpha initial
    wp.customize('archi_simulation_alpha', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.simulationAlpha = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Déclin d'alpha
    wp.customize('archi_simulation_alpha_decay', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.simulationAlphaDecay = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Déclin de vélocité
    wp.customize('archi_simulation_velocity_decay', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.simulationVelocityDecay = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Alpha au resize
    wp.customize('archi_resize_alpha', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.resizeAlpha = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // ========================================
    // LIENS AVANCÉS
    // ========================================
    
    // Distance de lien
    wp.customize('archi_link_distance', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.linkDistance = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Variation de distance
    wp.customize('archi_link_distance_variation', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.linkDistanceVariation = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Diviseur de force
    wp.customize('archi_link_strength_divisor', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.linkStrengthDivisor = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Motif pointillé
    wp.customize('archi_dashed_line_pattern', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.dashedLinePattern = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Motif points
    wp.customize('archi_dotted_line_pattern', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.dottedLinePattern = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // ========================================
    // LIENS LIVRE D'OR
    // ========================================
    
    // Couleur
    wp.customize('archi_guestbook_link_color', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.guestbookLinkColor = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Épaisseur
    wp.customize('archi_guestbook_link_width', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.guestbookLinkWidth = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité
    wp.customize('archi_guestbook_link_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.guestbookLinkOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Motif de tirets
    wp.customize('archi_guestbook_dash_pattern', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.guestbookDashPattern = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // ========================================
    // BADGES DE PRIORITÉ
    // ========================================
    
    // Décalage
    wp.customize('archi_priority_badge_offset', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.priorityBadgeOffset = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Couleur du contour
    wp.customize('archi_priority_badge_stroke_color', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.priorityBadgeStrokeColor = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Épaisseur du contour
    wp.customize('archi_priority_badge_stroke_width', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.priorityBadgeStrokeWidth = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // ========================================
    // CLUSTERS
    // ========================================
    
    // Opacité de remplissage
    wp.customize('archi_cluster_fill_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterFillOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Épaisseur du contour
    wp.customize('archi_cluster_stroke_width', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterStrokeWidth = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité du contour
    wp.customize('archi_cluster_stroke_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterStrokeOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Taille police label
    wp.customize('archi_cluster_label_font_size', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterLabelFontSize = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Poids police label
    wp.customize('archi_cluster_label_font_weight', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterLabelFontWeight = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Taille compteur
    wp.customize('archi_cluster_count_font_size', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterCountFontSize = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité compteur
    wp.customize('archi_cluster_count_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterCountOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Ombre du texte
    wp.customize('archi_cluster_text_shadow', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterTextShadow = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Padding de l'enveloppe
    wp.customize('archi_cluster_hull_padding', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterHullPadding = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Rayon du cercle
    wp.customize('archi_cluster_circle_radius', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterCircleRadius = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Points du cercle
    wp.customize('archi_cluster_circle_points', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.clusterCirclePoints = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // ========================================
    // ÎLES ARCHITECTURALES
    // ========================================
    
    // Padding de l'enveloppe
    wp.customize('archi_island_hull_padding', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandHullPadding = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Facteur de lissage
    wp.customize('archi_island_smooth_factor', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandSmoothFactor = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Rayon du cercle
    wp.customize('archi_island_circle_radius', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandCircleRadius = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Points du cercle
    wp.customize('archi_island_circle_points', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandCirclePoints = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Padding interne
    wp.customize('archi_island_inner_padding', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandInnerPadding = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Motif du contour
    wp.customize('archi_island_stroke_dash_array', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandStrokeDashArray = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Taille police label
    wp.customize('archi_island_label_font_size', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandLabelFontSize = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Poids police label
    wp.customize('archi_island_label_font_weight', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandLabelFontWeight = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité label
    wp.customize('archi_island_label_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandLabelOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Décalage Y
    wp.customize('archi_island_label_y_offset', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandLabelYOffset = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Ombre texte
    wp.customize('archi_island_text_shadow', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandTextShadow = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Taille compteur
    wp.customize('archi_island_count_font_size', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandCountFontSize = parseInt(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité compteur
    wp.customize('archi_island_count_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandCountOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Opacité texture
    wp.customize('archi_island_texture_opacity', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandTextureOpacity = parseFloat(newval);
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

    // Motif texture
    wp.customize('archi_island_texture_dash_array', function(value) {
        value.bind(function(newval) {
            if (window.archiGraphSettings) {
                window.archiGraphSettings.islandTextureDashArray = newval;
                $(document).trigger('archi:refreshGraph');
            }
        });
    });

})(jQuery);
