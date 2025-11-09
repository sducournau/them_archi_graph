/**
 * Node Interaction Utilities
 * 
 * Helper functions for node interactions in the graph:
 * - Tooltip display
 * - Side panel management
 * - Node selection
 * 
 * Extracted from GraphContainer.jsx for better code organization
 */

/**
 * Show tooltip near a graph node
 * 
 * @param {Object} node - Node data
 * @param {Event} event - Mouse event
 * @param {Object} svgRef - Reference to SVG element
 * @param {Object} transformRef - Reference to current zoom transform
 * @param {Object} options - Display options (colors, etc.)
 */
export const showNodeTooltip = (node, event, svgRef, transformRef, options = {}) => {
  // Find or create tooltip
  let tooltip = document.getElementById('graph-node-tooltip');
  if (!tooltip) {
    tooltip = document.createElement('div');
    tooltip.id = 'graph-node-tooltip';
    tooltip.className = 'graph-node-tooltip';
    document.body.appendChild(tooltip);
  }

  // Determine color based on content type
  const defaultProjectColor = options.islandColor || '#f39c12';
  const defaultIllustrationColor = options.illustrationIslandColor || '#3498db';
  
  let titleColor = '#3498db';
  if (node.post_type === 'archi_project') {
    titleColor = defaultProjectColor;
  } else if (node.post_type === 'archi_illustration') {
    titleColor = defaultIllustrationColor;
  } else if (node.post_type === 'post' && node.categories && node.categories.length > 0) {
    titleColor = node.categories[0].color || defaultProjectColor;
  }

  // Build tooltip content
  const title = (node.title || '').toUpperCase();
  
  // Get description (excerpt, content, or custom meta)
  let description = '';
  if (node.excerpt) {
    description = node.excerpt;
  } else if (node.content) {
    // Extract excerpt from content (without HTML)
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = node.content;
    const textContent = tempDiv.textContent || tempDiv.innerText || '';
    description = textContent.substring(0, 200).trim() + (textContent.length > 200 ? '...' : '');
  } else if (node.custom_meta?.description) {
    description = node.custom_meta.description;
  }
  
  // Build HTML
  let tooltipContent = `<div class="tooltip-title" style="color: ${titleColor};">${title}</div>`;
  if (description) {
    tooltipContent += `<div class="tooltip-description">${description}</div>`;
  }
  
  tooltip.innerHTML = tooltipContent;

  // Position tooltip to the right of the node
  if (!svgRef || !svgRef.current) return;
  
  const svgRect = svgRef.current.getBoundingClientRect();
  const transform = transformRef.current;
  
  // Calculate screen position of node
  const nodeScreenX = svgRect.left + (node.x * transform.k + transform.x);
  const nodeScreenY = svgRect.top + (node.y * transform.k + transform.y);
  const nodeSize = (node.node_size || 60) * transform.k;
  
  // Position to the right of node with offset
  const tooltipX = nodeScreenX + (nodeSize / 2) + 20;
  const tooltipY = nodeScreenY;
  
  tooltip.style.left = `${tooltipX}px`;
  tooltip.style.top = `${tooltipY}px`;
  tooltip.style.borderLeftColor = titleColor;
  
  // Show with animation
  setTimeout(() => {
    tooltip.classList.add('visible');
  }, 10);
};

/**
 * Hide node tooltip
 */
export const hideNodeTooltip = () => {
  const tooltip = document.getElementById('graph-node-tooltip');
  if (tooltip) {
    tooltip.classList.remove('visible');
  }
};

/**
 * Show side title panel with typewriter animation
 * 
 * @param {Object} node - Node data
 * @param {boolean} showLink - Whether to show consultation link
 * @param {Object} options - Display options
 */
export const showSideTitlePanel = (node, showLink = false, options = {}) => {
  // Find or create panel
  let panel = document.getElementById('graph-side-title-panel');
  if (!panel) {
    panel = document.createElement('div');
    panel.id = 'graph-side-title-panel';
    panel.className = 'graph-side-title-panel';
    document.body.appendChild(panel);
  }

  // Create title element if doesn't exist
  let titleElement = panel.querySelector('.side-title-text');
  if (!titleElement) {
    titleElement = document.createElement('div');
    titleElement.className = 'side-title-text';
    panel.appendChild(titleElement);
  }

  // Create link element if doesn't exist
  let linkElement = panel.querySelector('.side-title-link');
  if (!linkElement) {
    linkElement = document.createElement('a');
    linkElement.className = 'side-title-link';
    linkElement.textContent = 'Consulter';
    linkElement.target = '_self';
    panel.appendChild(linkElement);
  }

  // Reset content
  titleElement.textContent = '';
  
  // Determine title color based on node category
  const defaultProjectColor = options.islandColor || '#f39c12';
  const defaultIllustrationColor = options.illustrationIslandColor || '#3498db';
  
  let titleColor = '#3498db';
  if (node.post_type === 'archi_project') {
    titleColor = defaultProjectColor;
  } else if (node.post_type === 'archi_illustration') {
    titleColor = defaultIllustrationColor;
  } else if (node.post_type === 'post' && node.categories && node.categories.length > 0) {
    titleColor = node.categories[0].color || defaultProjectColor;
  }
  
  // Apply color
  titleElement.style.color = titleColor;
  panel.style.borderLeftColor = titleColor;
  
  // Configure link
  linkElement.classList.remove('visible');
  linkElement.style.display = 'block';
  
  if (showLink && node.permalink) {
    linkElement.href = node.permalink;
  } else {
    linkElement.href = '#';
  }
  
  // Show panel
  panel.classList.add('visible');

  // Typewriter animation
  const title = (node.title || '').toUpperCase();
  let currentIndex = 0;
  
  // Clear previous interval
  if (window.sideTitleInterval) {
    clearInterval(window.sideTitleInterval);
  }

  window.sideTitleInterval = setInterval(() => {
    if (currentIndex < title.length) {
      titleElement.textContent += title[currentIndex];
      currentIndex++;
    } else {
      clearInterval(window.sideTitleInterval);
      // Show link after animation
      setTimeout(() => {
        linkElement.classList.add('visible');
      }, 100);
    }
  }, 50); // 50ms between each letter
};

/**
 * Hide side title panel
 */
export const hideSideTitlePanel = () => {
  // Clear animation interval
  if (window.sideTitleInterval) {
    clearInterval(window.sideTitleInterval);
  }

  const panel = document.getElementById('graph-side-title-panel');
  if (panel) {
    panel.classList.remove('visible');
  }
};

/**
 * Get node color based on type and categories
 * 
 * @param {Object} node - Node data
 * @param {Object} options - Color options
 * @returns {string} Hex color
 */
export const getNodeColor = (node, options = {}) => {
  const defaultProjectColor = options.islandColor || '#f39c12';
  const defaultIllustrationColor = options.illustrationIslandColor || '#3498db';
  
  if (node.post_type === 'archi_project') {
    return defaultProjectColor;
  } else if (node.post_type === 'archi_illustration') {
    return defaultIllustrationColor;
  } else if (node.post_type === 'post' && node.categories?.[0]?.color) {
    return node.categories[0].color;
  }
  
  return '#3498db'; // Default fallback
};
