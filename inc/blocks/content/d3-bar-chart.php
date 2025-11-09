<?php
/**
 * Bloc Graphique en Barres D3.js
 * Diagramme en barres interactif avec D3.js
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Graphique en Barres D3.js
 */
function archi_register_d3_bar_chart_block() {
    register_block_type('archi-graph/d3-bar-chart', [
        'attributes' => [
            'title' => [
                'type' => 'string',
                'default' => 'Titre du graphique'
            ],
            'data' => [
                'type' => 'array',
                'default' => []
            ],
            'orientation' => [
                'type' => 'string',
                'default' => 'vertical'
            ],
            'colorScheme' => [
                'type' => 'string',
                'default' => 'blue'
            ],
            'customColor' => [
                'type' => 'string',
                'default' => '#3498db'
            ],
            'showValues' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showGrid' => [
                'type' => 'boolean',
                'default' => true
            ],
            'height' => [
                'type' => 'number',
                'default' => 400
            ],
            'animate' => [
                'type' => 'boolean',
                'default' => true
            ]
        ],
        'render_callback' => 'archi_render_d3_bar_chart_block',
        'editor_script' => 'archi-d3-bar-chart',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

/**
 * Rendu côté serveur du bloc
 */
function archi_render_d3_bar_chart_block($attributes) {
    $title = isset($attributes['title']) ? esc_html($attributes['title']) : __('Titre du graphique', 'archi-graph');
    $data = isset($attributes['data']) ? $attributes['data'] : [];
    $orientation = isset($attributes['orientation']) ? esc_attr($attributes['orientation']) : 'vertical';
    $color_scheme = isset($attributes['colorScheme']) ? esc_attr($attributes['colorScheme']) : 'blue';
    $custom_color = isset($attributes['customColor']) ? esc_attr($attributes['customColor']) : '#3498db';
    $show_values = isset($attributes['showValues']) ? (bool)$attributes['showValues'] : true;
    $show_grid = isset($attributes['showGrid']) ? (bool)$attributes['showGrid'] : true;
    $height = isset($attributes['height']) ? absint($attributes['height']) : 400;
    $animate = isset($attributes['animate']) ? (bool)$attributes['animate'] : true;
    
    if (empty($data)) {
        return '';
    }
    
    // ID unique pour ce graphique
    $chart_id = 'archi-d3-bar-' . uniqid();
    
    // Déterminer la couleur
    $color_map = [
        'blue' => '#3498db',
        'green' => '#2ecc71',
        'orange' => '#e67e22',
        'purple' => '#9b59b6',
        'custom' => $custom_color
    ];
    $bar_color = isset($color_map[$color_scheme]) ? $color_map[$color_scheme] : $color_map['blue'];
    
    ob_start();
    ?>
    <div class="archi-d3-bar-chart-wrapper">
        <h3 class="chart-title"><?php echo $title; ?></h3>
        <div id="<?php echo $chart_id; ?>" class="archi-d3-bar-chart"></div>
    </div>
    
    <script type="module">
    import * as d3 from 'https://cdn.jsdelivr.net/npm/d3@7/+esm';
    
    (function() {
        function initChart() {
            const container = document.getElementById('<?php echo $chart_id; ?>');
            if (!container || container.dataset.initialized === 'true') return;
            
            // Configuration
            const data = <?php echo json_encode($data); ?>;
            const orientation = '<?php echo $orientation; ?>';
            const showValues = <?php echo $show_values ? 'true' : 'false'; ?>;
            const showGrid = <?php echo $show_grid ? 'true' : 'false'; ?>;
            const animate = <?php echo $animate ? 'true' : 'false'; ?>;
            const color = '<?php echo $bar_color; ?>';
            
            const margin = { top: 20, right: 30, bottom: 60, left: 60 };
            const width = container.clientWidth || 800;
            const height = <?php echo $height; ?> - margin.top - margin.bottom;
            
            // Créer le SVG
            const svg = d3.select(container)
                .append('svg')
                .attr('width', width)
                .attr('height', <?php echo $height; ?>)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);
            
            if (orientation === 'vertical') {
                // Graphique vertical
                const x = d3.scaleBand()
                    .domain(data.map(d => d.label))
                    .range([0, width - margin.left - margin.right])
                    .padding(0.2);
                
                const y = d3.scaleLinear()
                    .domain([0, d3.max(data, d => d.value)])
                    .nice()
                    .range([height, 0]);
                
                // Grille
                if (showGrid) {
                    svg.append('g')
                        .attr('class', 'grid')
                        .attr('opacity', 0.1)
                        .call(d3.axisLeft(y).ticks(5).tickSize(-width + margin.left + margin.right).tickFormat(''));
                }
                
                // Axes
                svg.append('g')
                    .attr('transform', `translate(0,${height})`)
                    .call(d3.axisBottom(x))
                    .selectAll('text')
                    .attr('transform', 'rotate(-45)')
                    .style('text-anchor', 'end');
                
                svg.append('g')
                    .call(d3.axisLeft(y));
                
                // Barres
                const bars = svg.selectAll('.bar')
                    .data(data)
                    .enter()
                    .append('rect')
                    .attr('class', 'bar')
                    .attr('x', d => x(d.label))
                    .attr('width', x.bandwidth())
                    .attr('fill', color)
                    .attr('y', animate ? height : d => y(d.value))
                    .attr('height', animate ? 0 : d => height - y(d.value));
                
                if (animate) {
                    bars.transition()
                        .duration(800)
                        .attr('y', d => y(d.value))
                        .attr('height', d => height - y(d.value));
                }
                
                // Valeurs
                if (showValues) {
                    svg.selectAll('.value')
                        .data(data)
                        .enter()
                        .append('text')
                        .attr('class', 'value')
                        .attr('x', d => x(d.label) + x.bandwidth() / 2)
                        .attr('y', d => y(d.value) - 5)
                        .attr('text-anchor', 'middle')
                        .text(d => d.value)
                        .style('font-size', '12px')
                        .style('font-weight', '600');
                }
                
            } else {
                // Graphique horizontal
                const x = d3.scaleLinear()
                    .domain([0, d3.max(data, d => d.value)])
                    .nice()
                    .range([0, width - margin.left - margin.right]);
                
                const y = d3.scaleBand()
                    .domain(data.map(d => d.label))
                    .range([0, height])
                    .padding(0.2);
                
                // Grille
                if (showGrid) {
                    svg.append('g')
                        .attr('class', 'grid')
                        .attr('opacity', 0.1)
                        .call(d3.axisBottom(x).ticks(5).tickSize(height).tickFormat(''));
                }
                
                // Axes
                svg.append('g')
                    .attr('transform', `translate(0,${height})`)
                    .call(d3.axisBottom(x));
                
                svg.append('g')
                    .call(d3.axisLeft(y));
                
                // Barres
                const bars = svg.selectAll('.bar')
                    .data(data)
                    .enter()
                    .append('rect')
                    .attr('class', 'bar')
                    .attr('y', d => y(d.label))
                    .attr('height', y.bandwidth())
                    .attr('x', 0)
                    .attr('width', animate ? 0 : d => x(d.value))
                    .attr('fill', color);
                
                if (animate) {
                    bars.transition()
                        .duration(800)
                        .attr('width', d => x(d.value));
                }
                
                // Valeurs
                if (showValues) {
                    svg.selectAll('.value')
                        .data(data)
                        .enter()
                        .append('text')
                        .attr('class', 'value')
                        .attr('x', d => x(d.value) + 5)
                        .attr('y', d => y(d.label) + y.bandwidth() / 2)
                        .attr('dy', '0.35em')
                        .text(d => d.value)
                        .style('font-size', '12px')
                        .style('font-weight', '600');
                }
            }
            
            // Interactivité
            svg.selectAll('.bar')
                .on('mouseover', function() {
                    d3.select(this).attr('opacity', 0.7);
                })
                .on('mouseout', function() {
                    d3.select(this).attr('opacity', 1);
                });
            
            container.dataset.initialized = 'true';
        }
        
        // Initialiser au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
