/**
 * Graph Settings Integration Helper
 * 
 * Utilitaires pour intégrer les paramètres du Customizer dans le composant React GraphContainer
 * 
 * @package Archi_Graph
 * @since 1.2.0
 */

/**
 * Récupère tous les paramètres du graph depuis le Customizer
 */
export function getGraphSettings() {
    return window.archiGraphSettings || {
        // Valeurs par défaut au cas où
        defaultNodeColor: '#3498db',
        defaultNodeSize: 60,
        clusterStrength: 0.1,
        popupTitleOnly: false,
        showComments: true,
        animationMode: 'fade-in',
        transitionSpeed: 500,
        hoverEffect: 'highlight',
        linkColor: '#999999',
        linkWidth: 1.5,
        linkOpacity: 0.6,
        linkStyle: 'solid',
        showArrows: false,
        linkAnimation: 'none',
        categoryColorsEnabled: false,
        categoryPalette: 'default',
        showCategoryLegend: true,
        categoryColors: []
    };
}

/**
 * Applique l'animation d'entrée sur les nœuds
 * 
 * @param {d3.Selection} nodeSelection - Sélection D3 des nœuds
 * @param {Object} settings - Paramètres du graph
 */
export function applyNodeEntryAnimation(nodeSelection, settings) {
    const { animationMode, transitionSpeed } = settings;
    
    switch (animationMode) {
        case 'fade-in':
            nodeSelection
                .style('opacity', 0)
                .transition()
                .duration(transitionSpeed)
                .style('opacity', 1);
            break;
            
        case 'scale-up':
            nodeSelection
                .attr('transform', 'scale(0)')
                .transition()
                .duration(transitionSpeed)
                .attr('transform', 'scale(1)');
            break;
            
        case 'slide-in':
            nodeSelection
                .attr('transform', d => `translate(${d.x - 200}, ${d.y})`)
                .transition()
                .duration(transitionSpeed)
                .attr('transform', d => `translate(${d.x}, ${d.y})`);
            break;
            
        case 'bounce':
            nodeSelection
                .style('opacity', 0)
                .transition()
                .duration(transitionSpeed)
                .ease(d3.easeBounceOut)
                .style('opacity', 1);
            break;
            
        case 'none':
        default:
            // Pas d'animation
            break;
    }
}

/**
 * Applique l'effet de survol sur les nœuds
 * 
 * @param {d3.Selection} nodeSelection - Sélection D3 des nœuds
 * @param {Object} settings - Paramètres du graph
 */
export function applyHoverEffect(nodeSelection, settings) {
    const { hoverEffect, transitionSpeed } = settings;
    
    nodeSelection
        .on('mouseenter', function(event, d) {
            const node = d3.select(this);
            
            switch (hoverEffect) {
                case 'highlight':
                    node.transition()
                        .duration(transitionSpeed / 2)
                        .style('filter', 'brightness(1.3)');
                    break;
                    
                case 'scale':
                    node.transition()
                        .duration(transitionSpeed / 2)
                        .attr('transform', 'scale(1.2)');
                    break;
                    
                case 'glow':
                    node.transition()
                        .duration(transitionSpeed / 2)
                        .style('filter', 'drop-shadow(0 0 10px currentColor)');
                    break;
                    
                case 'pulse':
                    node.transition()
                        .duration(transitionSpeed / 2)
                        .style('opacity', 0.7)
                        .transition()
                        .duration(transitionSpeed / 2)
                        .style('opacity', 1);
                    break;
                    
                case 'none':
                default:
                    // Pas d'effet
                    break;
            }
        })
        .on('mouseleave', function(event, d) {
            const node = d3.select(this);
            
            // Réinitialisation
            node.transition()
                .duration(transitionSpeed / 2)
                .attr('transform', 'scale(1)')
                .style('filter', 'none')
                .style('opacity', 1);
        });
}

/**
 * Configure le style des liens
 * 
 * @param {d3.Selection} linkSelection - Sélection D3 des liens
 * @param {Object} settings - Paramètres du graph
 */
export function configureLinkStyle(linkSelection, settings) {
    const { linkColor, linkWidth, linkOpacity, linkStyle } = settings;
    
    linkSelection
        .style('stroke', linkColor)
        .style('stroke-width', linkWidth)
        .style('opacity', linkOpacity);
    
    // Style de ligne
    if (linkStyle === 'dashed') {
        linkSelection.style('stroke-dasharray', '5,5');
    } else {
        linkSelection.style('stroke-dasharray', 'none');
    }
}

/**
 * Applique l'animation sur les liens
 * 
 * @param {d3.Selection} linkSelection - Sélection D3 des liens
 * @param {Object} settings - Paramètres du graph
 */
export function applyLinkAnimation(linkSelection, settings) {
    const { linkAnimation } = settings;
    
    switch (linkAnimation) {
        case 'pulse':
            linkSelection
                .transition()
                .duration(1000)
                .style('opacity', 0.3)
                .transition()
                .duration(1000)
                .style('opacity', settings.linkOpacity)
                .on('end', function repeat() {
                    d3.select(this)
                        .transition()
                        .duration(1000)
                        .style('opacity', 0.3)
                        .transition()
                        .duration(1000)
                        .style('opacity', settings.linkOpacity)
                        .on('end', repeat);
                });
            break;
            
        case 'flow':
            // Animation de flux - nécessite un gradient SVG
            linkSelection
                .style('stroke-dasharray', '10,10')
                .style('stroke-dashoffset', 0)
                .transition()
                .duration(2000)
                .ease(d3.easeLinear)
                .style('stroke-dashoffset', -20)
                .on('end', function repeat() {
                    d3.select(this)
                        .transition()
                        .duration(2000)
                        .ease(d3.easeLinear)
                        .style('stroke-dashoffset', -20)
                        .on('end', repeat);
                });
            break;
            
        case 'glow':
            linkSelection
                .style('filter', 'drop-shadow(0 0 2px currentColor)');
            break;
            
        case 'none':
        default:
            // Pas d'animation
            break;
    }
}

/**
 * Obtient la couleur d'un nœud selon sa catégorie
 * 
 * @param {Object} node - Données du nœud
 * @param {Object} settings - Paramètres du graph
 * @returns {string} Couleur hex
 */
export function getNodeColor(node, settings) {
    const { categoryColorsEnabled, categoryColors, defaultNodeColor } = settings;
    
    // Utiliser la couleur personnalisée du nœud si définie
    if (node.color) {
        return node.color;
    }
    
    // Utiliser la couleur de catégorie si activée
    if (categoryColorsEnabled && node.categories && node.categories.length > 0) {
        const categoryId = node.categories[0].id;
        const colorIndex = categoryId % categoryColors.length;
        return categoryColors[colorIndex];
    }
    
    // Couleur par défaut
    return defaultNodeColor;
}

/**
 * Crée la légende des catégories
 * 
 * @param {Array} categories - Liste des catégories
 * @param {Object} settings - Paramètres du graph
 * @returns {HTMLElement} Élément DOM de la légende
 */
export function createCategoryLegend(categories, settings) {
    const { categoryColors, showCategoryLegend } = settings;
    
    if (!showCategoryLegend || !categories || categories.length === 0) {
        return null;
    }
    
    const legend = document.createElement('div');
    legend.className = 'graph-legend';
    legend.style.cssText = `
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-size: 14px;
        z-index: 10;
    `;
    
    const title = document.createElement('div');
    title.textContent = 'Catégories';
    title.style.cssText = 'font-weight: bold; margin-bottom: 10px;';
    legend.appendChild(title);
    
    categories.forEach((category, index) => {
        const item = document.createElement('div');
        item.style.cssText = 'display: flex; align-items: center; margin: 5px 0;';
        
        const colorBox = document.createElement('span');
        colorBox.style.cssText = `
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: ${categoryColors[index % categoryColors.length]};
            border-radius: 3px;
            margin-right: 8px;
        `;
        
        const label = document.createElement('span');
        label.textContent = category.name;
        
        item.appendChild(colorBox);
        item.appendChild(label);
        legend.appendChild(item);
    });
    
    return legend;
}

/**
 * Fonction globale pour mettre à jour les paramètres du graph
 * À appeler depuis le Customizer preview
 */
window.updateGraphSettings = function(newSettings) {
    // Fusionner les nouveaux paramètres
    Object.assign(window.archiGraphSettings, newSettings);
    
    // Déclencher un événement personnalisé pour notifier le composant React
    const event = new CustomEvent('graphSettingsUpdated', {
        detail: window.archiGraphSettings
    });
    window.dispatchEvent(event);
};

/**
 * Hook pour écouter les changements de paramètres (à utiliser dans React)
 * 
 * @example
 * useEffect(() => {
 *   const handleSettingsUpdate = (event) => {
 *     const newSettings = event.detail;
 *     // Mettre à jour le graph
 *   };
 *   
 *   window.addEventListener('graphSettingsUpdated', handleSettingsUpdate);
 *   return () => window.removeEventListener('graphSettingsUpdated', handleSettingsUpdate);
 * }, []);
 */
export function useGraphSettings(callback) {
    if (typeof window !== 'undefined') {
        window.addEventListener('graphSettingsUpdated', callback);
        return () => window.removeEventListener('graphSettingsUpdated', callback);
    }
}
