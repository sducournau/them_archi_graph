<?php
/**
 * Bloc Carte Interactive (Leaflet.js)
 * Affiche une carte interactive avec marqueurs personnalisables
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Carte Interactive
 */
function archi_register_interactive_map_block() {
    register_block_type('archi-graph/interactive-map', [
        'attributes' => [
            'latitude' => [
                'type' => 'number',
                'default' => 48.8566
            ],
            'longitude' => [
                'type' => 'number',
                'default' => 2.3522
            ],
            'zoom' => [
                'type' => 'number',
                'default' => 13
            ],
            'height' => [
                'type' => 'number',
                'default' => 400
            ],
            'mapStyle' => [
                'type' => 'string',
                'default' => 'osm'
            ],
            'markers' => [
                'type' => 'array',
                'default' => []
            ],
            'showControls' => [
                'type' => 'boolean',
                'default' => true
            ],
            'enableScroll' => [
                'type' => 'boolean',
                'default' => true
            ]
        ],
        'render_callback' => 'archi_render_interactive_map_block',
        'editor_script' => 'archi-interactive-map',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

/**
 * Rendu côté serveur du bloc
 */
function archi_render_interactive_map_block($attributes) {
    $latitude = isset($attributes['latitude']) ? floatval($attributes['latitude']) : 48.8566;
    $longitude = isset($attributes['longitude']) ? floatval($attributes['longitude']) : 2.3522;
    $zoom = isset($attributes['zoom']) ? absint($attributes['zoom']) : 13;
    $height = isset($attributes['height']) ? absint($attributes['height']) : 400;
    $map_style = isset($attributes['mapStyle']) ? esc_attr($attributes['mapStyle']) : 'osm';
    $markers = isset($attributes['markers']) ? $attributes['markers'] : [];
    $show_controls = isset($attributes['showControls']) ? (bool)$attributes['showControls'] : true;
    $enable_scroll = isset($attributes['enableScroll']) ? (bool)$attributes['enableScroll'] : true;
    
    // ID unique pour cette carte
    $map_id = 'archi-map-' . uniqid();
    
    // Enqueue Leaflet CSS et JS
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);
    
    // Déterminer le tile layer selon le style
    $tile_layers = [
        'osm' => [
            'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        ],
        'osm-fr' => [
            'url' => 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
            'attribution' => '&copy; OpenStreetMap France'
        ],
        'terrain' => [
            'url' => 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg',
            'attribution' => 'Map tiles by Stamen Design'
        ],
        'satellite' => [
            'url' => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            'attribution' => 'Tiles &copy; Esri'
        ]
    ];
    
    $tile_config = isset($tile_layers[$map_style]) ? $tile_layers[$map_style] : $tile_layers['osm'];
    
    ob_start();
    ?>
    <div class="archi-interactive-map-wrapper">
        <div id="<?php echo $map_id; ?>" 
             class="archi-interactive-map" 
             style="width: 100%; height: <?php echo $height; ?>px;">
        </div>
    </div>
    
    <script>
    (function() {
        function initMap() {
            // Vérifier que Leaflet est chargé
            if (typeof L === 'undefined') {
                setTimeout(initMap, 100);
                return;
            }
            
            const mapElement = document.getElementById('<?php echo $map_id; ?>');
            if (!mapElement || mapElement.dataset.initialized === 'true') return;
            
            // Créer la carte
            const map = L.map('<?php echo $map_id; ?>', {
                center: [<?php echo $latitude; ?>, <?php echo $longitude; ?>],
                zoom: <?php echo $zoom; ?>,
                zoomControl: <?php echo $show_controls ? 'true' : 'false'; ?>,
                scrollWheelZoom: <?php echo $enable_scroll ? 'true' : 'false'; ?>
            });
            
            // Ajouter le tile layer
            L.tileLayer('<?php echo $tile_config['url']; ?>', {
                attribution: '<?php echo $tile_config['attribution']; ?>',
                maxZoom: 19
            }).addTo(map);
            
            // Ajouter les marqueurs
            <?php if (!empty($markers)) : ?>
            const markers = <?php echo json_encode($markers); ?>;
            markers.forEach(function(markerData) {
                // Créer une icône personnalisée avec la couleur
                const iconHtml = '<div style="background-color: ' + markerData.color + '; width: 25px; height: 41px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); position: relative; left: -12.5px; top: -41px;"><div style="width: 10px; height: 10px; background: white; border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg);"></div></div>';
                
                const customIcon = L.divIcon({
                    html: iconHtml,
                    className: 'custom-marker',
                    iconSize: [25, 41],
                    iconAnchor: [12.5, 41],
                    popupAnchor: [0, -41]
                });
                
                const marker = L.marker([markerData.lat, markerData.lng], { icon: customIcon }).addTo(map);
                
                // Ajouter le popup si description existe
                if (markerData.title || markerData.description) {
                    let popupContent = '';
                    if (markerData.title) {
                        popupContent += '<strong>' + markerData.title + '</strong>';
                    }
                    if (markerData.description) {
                        popupContent += (markerData.title ? '<br>' : '') + markerData.description;
                    }
                    marker.bindPopup(popupContent);
                }
            });
            <?php endif; ?>
            
            // Marquer comme initialisé
            mapElement.dataset.initialized = 'true';
            
            // Recalculer la taille après un court délai (pour éviter les problèmes de rendu)
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        }
        
        // Initialiser au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            initMap();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
