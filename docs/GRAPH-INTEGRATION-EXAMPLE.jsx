/**
 * Exemple d'intégration des paramètres Customizer dans GraphContainer.jsx
 * 
 * Ce fichier montre comment utiliser les nouveaux paramètres du Customizer
 * dans le composant React GraphContainer
 */

import React, { useEffect, useState } from 'react';
import * as d3 from 'd3';
import {
    getGraphSettings,
    applyNodeEntryAnimation,
    applyHoverEffect,
    configureLinkStyle,
    applyLinkAnimation,
    getNodeColor,
    createCategoryLegend
} from '../utils/graph-settings-helper';

const GraphContainer = () => {
    const [graphSettings, setGraphSettings] = useState(getGraphSettings());
    const [categories, setCategories] = useState([]);
    
    // Écouter les changements de paramètres depuis le Customizer
    useEffect(() => {
        const handleSettingsUpdate = (event) => {
            setGraphSettings(event.detail);
            // Re-render le graph avec les nouveaux paramètres
            updateGraph(event.detail);
        };
        
        window.addEventListener('graphSettingsUpdated', handleSettingsUpdate);
        return () => window.removeEventListener('graphSettingsUpdated', handleSettingsUpdate);
    }, []);
    
    // Initialiser le graph
    useEffect(() => {
        initializeGraph();
    }, []);
    
    const initializeGraph = () => {
        const settings = graphSettings;
        
        // Configuration D3
        const width = window.innerWidth;
        const height = window.innerHeight;
        
        const svg = d3.select('#graph-container')
            .append('svg')
            .attr('width', width)
            .attr('height', height);
        
        // Charger les données
        fetch('/wp-json/archi/v1/articles')
            .then(response => response.json())
            .then(data => {
                renderGraph(svg, data, settings);
            });
    };
    
    const renderGraph = (svg, data, settings) => {
        // Préparer les données
        const nodes = data.map(article => ({
            id: article.id,
            title: article.title,
            categories: article.categories,
            color: article.custom_meta?.node_color,
            size: article.custom_meta?.node_size || settings.defaultNodeSize,
            x: Math.random() * svg.attr('width'),
            y: Math.random() * svg.attr('height')
        }));
        
        const links = [];
        data.forEach(article => {
            if (article.relationships?.related_articles) {
                article.relationships.related_articles.forEach(relatedId => {
                    links.push({
                        source: article.id,
                        target: relatedId
                    });
                });
            }
        });
        
        // Extraire les catégories uniques
        const uniqueCategories = [];
        data.forEach(article => {
            article.categories?.forEach(cat => {
                if (!uniqueCategories.find(c => c.id === cat.id)) {
                    uniqueCategories.push(cat);
                }
            });
        });
        setCategories(uniqueCategories);
        
        // Créer la simulation de force
        const simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links)
                .id(d => d.id)
                .distance(100))
            .force('charge', d3.forceManyBody()
                .strength(-300))
            .force('center', d3.forceCenter(
                svg.attr('width') / 2,
                svg.attr('height') / 2
            ))
            .force('collision', d3.forceCollide()
                .radius(d => d.size + 10));
        
        // Ajouter clustering si activé
        if (settings.clusterStrength > 0 && settings.categoryColorsEnabled) {
            simulation.force('cluster', forceCluster(settings.clusterStrength));
        }
        
        // Créer les liens
        const link = svg.append('g')
            .attr('class', 'links')
            .selectAll('line')
            .data(links)
            .enter()
            .append('line');
        
        // Appliquer le style des liens
        configureLinkStyle(link, settings);
        
        // Appliquer l'animation des liens si activée
        if (settings.linkAnimation !== 'none') {
            applyLinkAnimation(link, settings);
        }
        
        // Ajouter les flèches si activées
        if (settings.showArrows) {
            svg.append('defs').selectAll('marker')
                .data(['arrow'])
                .enter().append('marker')
                .attr('id', 'arrow')
                .attr('viewBox', '0 -5 10 10')
                .attr('refX', 15)
                .attr('refY', 0)
                .attr('markerWidth', 6)
                .attr('markerHeight', 6)
                .attr('orient', 'auto')
                .append('path')
                .attr('d', 'M0,-5L10,0L0,5')
                .style('fill', settings.linkColor);
            
            link.attr('marker-end', 'url(#arrow)');
        }
        
        // Créer les nœuds
        const node = svg.append('g')
            .attr('class', 'nodes')
            .selectAll('g')
            .data(nodes)
            .enter()
            .append('g');
        
        // Ajouter les cercles des nœuds
        node.append('circle')
            .attr('r', d => d.size)
            .style('fill', d => getNodeColor(d, settings))
            .style('stroke', '#fff')
            .style('stroke-width', 2);
        
        // Ajouter les labels si nécessaire
        if (!settings.popupTitleOnly) {
            node.append('text')
                .text(d => d.title)
                .attr('text-anchor', 'middle')
                .attr('dy', d => d.size + 15)
                .style('font-size', '12px')
                .style('pointer-events', 'none');
        }
        
        // Appliquer l'animation d'entrée
        if (settings.animationMode !== 'none') {
            applyNodeEntryAnimation(node, settings);
        }
        
        // Appliquer l'effet de survol
        if (settings.hoverEffect !== 'none') {
            applyHoverEffect(node, settings);
        }
        
        // Ajouter la popup au survol
        node.on('click', (event, d) => {
            showNodeInfo(d, settings);
        });
        
        // Activer le drag
        node.call(d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended));
        
        // Mettre à jour les positions à chaque tick
        simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);
            
            node.attr('transform', d => `translate(${d.x},${d.y})`);
        });
        
        // Fonctions de drag
        function dragstarted(event, d) {
            if (!event.active) simulation.alphaTarget(0.3).restart();
            d.fx = d.x;
            d.fy = d.y;
        }
        
        function dragged(event, d) {
            d.fx = event.x;
            d.fy = event.y;
        }
        
        function dragended(event, d) {
            if (!event.active) simulation.alphaTarget(0);
            d.fx = null;
            d.fy = null;
        }
    };
    
    const showNodeInfo = (node, settings) => {
        // Afficher le panneau latéral avec les infos du nœud
        const panel = document.querySelector('.side-panel');
        if (panel) {
            panel.innerHTML = `
                <h2>${node.title}</h2>
                ${settings.showComments ? '<div class="node-comments"><!-- Commentaires --></div>' : ''}
            `;
            panel.classList.add('active');
        }
    };
    
    const updateGraph = (newSettings) => {
        // Mettre à jour le graph avec les nouveaux paramètres
        const svg = d3.select('#graph-container svg');
        const links = svg.selectAll('.links line');
        const nodes = svg.selectAll('.nodes g');
        
        // Mettre à jour les liens
        configureLinkStyle(links, newSettings);
        
        // Mettre à jour les couleurs des nœuds si nécessaire
        if (newSettings.categoryColorsEnabled) {
            nodes.select('circle')
                .transition()
                .duration(newSettings.transitionSpeed)
                .style('fill', d => getNodeColor(d, newSettings));
        }
        
        // Mettre à jour la légende
        updateLegend(newSettings);
    };
    
    const updateLegend = (settings) => {
        const existingLegend = document.querySelector('.graph-legend');
        if (existingLegend) {
            existingLegend.remove();
        }
        
        if (settings.showCategoryLegend && settings.categoryColorsEnabled && categories.length > 0) {
            const legend = createCategoryLegend(categories, settings);
            if (legend) {
                document.querySelector('#graph-container').appendChild(legend);
            }
        }
    };
    
    // Force de clustering pour regrouper par catégorie
    const forceCluster = (strength) => {
        return (alpha) => {
            nodes.forEach(node => {
                if (node.categories && node.categories.length > 0) {
                    const categoryId = node.categories[0].id;
                    const clusterX = (categoryId % 3) * 300 + 200;
                    const clusterY = Math.floor(categoryId / 3) * 300 + 200;
                    
                    node.vx -= (node.x - clusterX) * strength * alpha;
                    node.vy -= (node.y - clusterY) * strength * alpha;
                }
            });
        };
    };
    
    return (
        <div id="graph-container" className="graph-visualization">
            {/* Le SVG sera créé par D3 */}
        </div>
    );
};

export default GraphContainer;
