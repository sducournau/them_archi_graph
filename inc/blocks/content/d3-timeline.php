<?php
/**
 * Bloc Timeline D3.js  
 * Timeline horizontale interactive
 */

if (!defined('ABSPATH')) {
    exit;
}

function archi_register_d3_timeline_block() {
    register_block_type('archi-graph/d3-timeline', [
        'attributes' => [
            'title' => ['type' => 'string', 'default' => 'Chronologie du projet'],
            'events' => ['type' => 'array', 'default' => []],
            'height' => ['type' => 'number', 'default' => 300],
        ],
        'render_callback' => 'archi_render_d3_timeline_block',
        'editor_script' => 'archi-d3-timeline',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

function archi_render_d3_timeline_block($attributes) {
    $title = isset($attributes['title']) ? esc_html($attributes['title']) : 'Chronologie';
    $events = isset($attributes['events']) ? $attributes['events'] : [];
    $height = isset($attributes['height']) ? absint($attributes['height']) : 300;
    
    if (empty($events)) return '';
    
    $timeline_id = 'archi-d3-timeline-' . uniqid();
    
    usort($events, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
    
    ob_start();
    ?>
    <div class="archi-d3-timeline-wrapper">
        <h3 class="timeline-title"><?php echo $title; ?></h3>
        <div id="<?php echo $timeline_id; ?>" class="archi-d3-timeline"></div>
    </div>
    
    <script type="module">
    import * as d3 from 'https://cdn.jsdelivr.net/npm/d3@7/+esm';
    (function() {
        function initTimeline() {
            const container = document.getElementById('<?php echo $timeline_id; ?>');
            if (!container || container.dataset.initialized === 'true') return;
            
            const events = <?php echo json_encode($events); ?>;
            const margin = { top: 40, right: 20, bottom: 60, left: 20 };
            const width = container.clientWidth || 800;
            const height = <?php echo $height; ?> - margin.top - margin.bottom;
            
            const svg = d3.select(container).append('svg')
                .attr('width', width).attr('height', <?php echo $height; ?>)
                .append('g').attr('transform', `translate(${margin.left},${margin.top})`);
            
            const parseDate = d3.timeParse("%Y-%m-%d");
            events.forEach(d => d.parsedDate = parseDate(d.date));
            
            const x = d3.scaleTime()
                .domain(d3.extent(events, d => d.parsedDate))
                .range([0, width - margin.left - margin.right]);
            
            svg.append('line').attr('x1', 0).attr('x2', width - margin.left - margin.right)
                .attr('y1', height / 2).attr('y2', height / 2)
                .attr('stroke', '#ddd').attr('stroke-width', 2);
            
            const eventGroups = svg.selectAll('.event').data(events).enter()
                .append('g').attr('class', 'event')
                .attr('transform', d => `translate(${x(d.parsedDate)},${height / 2})`);
            
            eventGroups.append('circle').attr('r', 6)
                .attr('fill', d => d.color).attr('stroke', '#fff').attr('stroke-width', 2);
            
            eventGroups.append('text').attr('y', -15).attr('text-anchor', 'middle')
                .style('font-size', '12px').style('font-weight', '600').text(d => d.title);
            
            eventGroups.append('text').attr('y', 25).attr('text-anchor', 'middle')
                .style('font-size', '10px').style('fill', '#666')
                .text(d => new Date(d.date).toLocaleDateString());
            
            if (events.some(e => e.description)) {
                eventGroups.append('title').text(d => d.description);
            }
            
            container.dataset.initialized = 'true';
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTimeline);
        } else {
            initTimeline();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
