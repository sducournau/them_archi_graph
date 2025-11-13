/**
 * Link Animations for Graph
 * 
 * Handles animated effects for links between nodes
 * Based on archi_graph_link_animation Customizer setting
 * 
 * @package Archi_Graph
 */

import * as d3 from 'd3';

/**
 * Apply animation to links based on settings
 * 
 * @param {d3.Selection} linkElements - D3 selection of link elements
 * @param {Object} settings - Animation settings from Customizer
 */
export function applyLinkAnimation(linkElements, settings = {}) {
    const linkAnimation = settings.linkAnimation || 'none';
    
    // Remove any existing animations first
    removeLinkAnimation(linkElements);
    
    if (linkAnimation === 'none') {
        return;
    }
    
    switch (linkAnimation) {
        case 'pulse':
            applyPulseAnimation(linkElements);
            break;
            
        case 'flow':
            applyFlowAnimation(linkElements, settings);
            break;
            
        case 'glow':
            applyGlowAnimation(linkElements);
            break;
    }
}

/**
 * Remove all animations from links
 */
export function removeLinkAnimation(linkElements) {
    linkElements
        .interrupt()
        .style('stroke-opacity', null)
        .style('filter', null)
        .attr('stroke-dasharray', function() {
            // Preserve original dash array if it was set for style
            const linkType = d3.select(this).attr('data-link-type');
            const currentDash = d3.select(this).style('stroke-dasharray');
            return linkType === 'guestbook' || currentDash !== 'none' ? currentDash : null;
        });
}

/**
 * Pulse animation - links fade in and out
 */
function applyPulseAnimation(linkElements) {
    const pulse = function() {
        linkElements
            .transition()
            .duration(1500)
            .ease(d3.easeSinInOut)
            .style('stroke-opacity', function() {
                const baseOpacity = parseFloat(d3.select(this).style('stroke-opacity')) || 0.6;
                return baseOpacity * 0.3;
            })
            .transition()
            .duration(1500)
            .ease(d3.easeSinInOut)
            .style('stroke-opacity', function() {
                const baseOpacity = parseFloat(d3.select(this).attr('data-base-opacity')) || 0.6;
                return baseOpacity;
            })
            .on('end', pulse);
    };
    
    // Store base opacity for reference
    linkElements.each(function() {
        const link = d3.select(this);
        const baseOpacity = parseFloat(link.style('stroke-opacity')) || 0.6;
        link.attr('data-base-opacity', baseOpacity);
    });
    
    pulse();
}

/**
 * Flow animation - animated dashes flow along links
 */
function applyFlowAnimation(linkElements, settings = {}) {
    const linkWidth = settings.linkWidth || 1.5;
    const dashLength = linkWidth * 10;
    const gapLength = linkWidth * 5;
    
    linkElements
        .attr('stroke-dasharray', `${dashLength},${gapLength}`)
        .attr('stroke-dashoffset', 0);
    
    const animate = function() {
        linkElements
            .transition()
            .duration(2000)
            .ease(d3.easeLinear)
            .attr('stroke-dashoffset', -(dashLength + gapLength))
            .on('end', function() {
                d3.select(this).attr('stroke-dashoffset', 0);
                animate();
            });
    };
    
    animate();
}

/**
 * Glow animation - links have a pulsing glow effect
 */
function applyGlowAnimation(linkElements) {
    // Create or reuse glow filter
    const svg = d3.select('svg');
    let defs = svg.select('defs');
    
    if (defs.empty()) {
        defs = svg.insert('defs', ':first-child');
    }
    
    if (defs.select('#link-glow').empty()) {
        const glowFilter = defs.append('filter')
            .attr('id', 'link-glow')
            .attr('x', '-50%')
            .attr('y', '-50%')
            .attr('width', '200%')
            .attr('height', '200%');
        
        glowFilter.append('feGaussianBlur')
            .attr('in', 'SourceGraphic')
            .attr('stdDeviation', '2')
            .attr('result', 'blur');
        
        glowFilter.append('feFlood')
            .attr('flood-color', '#fff')
            .attr('flood-opacity', '0.6')
            .attr('result', 'flood');
        
        glowFilter.append('feComposite')
            .attr('in', 'flood')
            .attr('in2', 'blur')
            .attr('operator', 'in')
            .attr('result', 'colorBlur');
        
        const feMerge = glowFilter.append('feMerge');
        feMerge.append('feMergeNode').attr('in', 'colorBlur');
        feMerge.append('feMergeNode').attr('in', 'SourceGraphic');
    }
    
    linkElements.style('filter', 'url(#link-glow)');
    
    // Animate glow intensity
    const glow = function() {
        linkElements
            .transition()
            .duration(1000)
            .ease(d3.easeSinInOut)
            .style('stroke-opacity', function() {
                const baseOpacity = parseFloat(d3.select(this).attr('data-base-opacity')) || 0.6;
                return baseOpacity * 1.2;
            })
            .transition()
            .duration(1000)
            .ease(d3.easeSinInOut)
            .style('stroke-opacity', function() {
                const baseOpacity = parseFloat(d3.select(this).attr('data-base-opacity')) || 0.6;
                return baseOpacity;
            })
            .on('end', glow);
    };
    
    // Store base opacity
    linkElements.each(function() {
        const link = d3.select(this);
        const baseOpacity = parseFloat(link.style('stroke-opacity')) || 0.6;
        link.attr('data-base-opacity', baseOpacity);
    });
    
    glow();
}

/**
 * Update link animations when settings change
 */
export function updateLinkAnimations(linkElements, settings) {
    removeLinkAnimation(linkElements);
    applyLinkAnimation(linkElements, settings);
}

export default {
    applyLinkAnimation,
    removeLinkAnimation,
    updateLinkAnimations
};
