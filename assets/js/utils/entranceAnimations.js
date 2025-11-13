/**
 * Entrance Animations for Graph Nodes
 * 
 * Handles different entrance animation modes for nodes appearing on the graph
 * Based on archi_graph_animation_mode Customizer setting
 * 
 * @package Archi_Graph
 */

import * as d3 from 'd3';

/**
 * Apply entrance animation to nodes based on settings
 * 
 * @param {d3.Selection} nodeElements - D3 selection of node groups
 * @param {Object} settings - Animation settings from Customizer
 * @param {Object} centerPosition - {x, y} center of the canvas
 */
export function applyEntranceAnimation(nodeElements, settings = {}, centerPosition = {x: 0, y: 0}) {
    const animationMode = settings.animationMode || 'fade-in';
    const transitionSpeed = settings.transitionSpeed || 500;
    
    if (animationMode === 'none') {
        // No animation, just show nodes
        nodeElements.style('opacity', 1);
        return;
    }
    
    // Apply animation based on mode
    switch (animationMode) {
        case 'fade-in':
            applyFadeInAnimation(nodeElements, transitionSpeed);
            break;
            
        case 'scale-up':
            applyScaleUpAnimation(nodeElements, transitionSpeed);
            break;
            
        case 'slide-in':
            applySlideInAnimation(nodeElements, transitionSpeed, centerPosition);
            break;
            
        case 'bounce':
            applyBounceAnimation(nodeElements, transitionSpeed);
            break;
            
        default:
            applyFadeInAnimation(nodeElements, transitionSpeed);
    }
}

/**
 * Fade in animation - nodes gradually become visible
 * ⚠️ N'utilise QUE opacity - le transform est géré par updateNodePositions
 */
function applyFadeInAnimation(nodeElements, duration) {
    nodeElements
        .style('opacity', 0)
        .transition()
        .duration(duration)
        .delay((d, i) => i * 30) // Stagger animation
        .ease(d3.easeCubicOut)
        .style('opacity', 1);
}

/**
 * Scale up animation - nodes grow from center
 * ⚠️ Utilise transform sur l'IMAGE uniquement, pas sur le groupe
 */
function applyScaleUpAnimation(nodeElements, duration) {
    nodeElements.each(function(d, i) {
        const node = d3.select(this);
        const imageElement = node.select('.node-image');
        
        // Animer l'image de 0 à 100%
        node.style('opacity', 0);
        
        imageElement
            .attr('transform', 'scale(0.1)')
            .transition()
            .duration(duration)
            .delay(i * 30)
            .ease(d3.easeCubicOut)
            .attr('transform', 'scale(1)');
        
        node
            .transition()
            .duration(duration * 0.5)
            .delay(i * 30)
            .ease(d3.easeCubicOut)
            .style('opacity', 1);
    });
}

/**
 * Slide in animation - nodes slide from edges to position
 * ⚠️ Modifie les coordonnées D3 temporairement, puis laisse la simulation corriger
 */
function applySlideInAnimation(nodeElements, duration, centerPosition) {
    nodeElements.each(function(d, i) {
        const node = d3.select(this);
        const finalX = d.x || centerPosition.x;
        const finalY = d.y || centerPosition.y;
        
        // Sauvegarder les positions finales
        d._targetX = finalX;
        d._targetY = finalY;
        
        // Determine slide direction based on final position
        const fromCenterX = finalX - centerPosition.x;
        const fromCenterY = finalY - centerPosition.y;
        const angle = Math.atan2(fromCenterY, fromCenterX);
        
        // Définir position de départ temporaire
        d.x = finalX + Math.cos(angle) * 500;
        d.y = finalY + Math.sin(angle) * 500;
        
        // Animer vers la position finale
        node
            .style('opacity', 0)
            .transition()
            .duration(duration)
            .delay(i * 30)
            .ease(d3.easeCubicOut)
            .style('opacity', 1)
            .tween('position', function() {
                const startX = d.x;
                const startY = d.y;
                return function(t) {
                    d.x = startX + (d._targetX - startX) * t;
                    d.y = startY + (d._targetY - startY) * t;
                };
            });
    });
}

/**
 * Bounce animation - nodes bounce into place
 * ⚠️ Utilise transform sur l'IMAGE uniquement
 */
function applyBounceAnimation(nodeElements, duration) {
    nodeElements.each(function(d, i) {
        const node = d3.select(this);
        const imageElement = node.select('.node-image');
        
        node.style('opacity', 0);
        
        imageElement
            .attr('transform', 'translate(0, -50) scale(0.1)')
            .transition()
            .duration(duration)
            .delay(i * 30)
            .ease(d3.easeBounceOut)
            .attr('transform', 'translate(0, 0) scale(1)');
        
        node
            .transition()
            .duration(duration * 0.5)
            .delay(i * 30)
            .ease(d3.easeCubicOut)
            .style('opacity', 1);
    });
}

/**
 * Reset node to initial state before animation
 * Useful when re-animating nodes
 * ⚠️ Ne touche PAS au transform du groupe - seulement opacity
 */
export function resetNodeAnimation(nodeElement) {
    nodeElement
        .interrupt()
        .style('opacity', 0);
    
    // Reset image transform si présent
    const imageElement = nodeElement.select('.node-image');
    if (!imageElement.empty()) {
        imageElement
            .interrupt()
            .attr('transform', 'scale(1)');
    }
}

export default {
    applyEntranceAnimation,
    resetNodeAnimation
};
