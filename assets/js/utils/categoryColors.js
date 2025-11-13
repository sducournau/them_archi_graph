/**
 * Category Colors Utility
 * 
 * Applies category-based colors to nodes when categoryColorsEnabled is true.
 * Uses the selected palette from WordPress Customizer settings.
 * 
 * @package Archi-Graph
 */

import * as d3 from 'd3';

/**
 * Apply category colors to nodes
 * 
 * When categoryColorsEnabled is true, this function assigns colors to nodes
 * based on their primary category using the selected color palette.
 * 
 * @param {d3.Selection} nodeSelection - D3 selection of node elements
 * @param {Object} settings - Settings object from window.archiGraphSettings
 * @param {boolean} settings.categoryColorsEnabled - Whether to use category colors
 * @param {Array<string>} settings.categoryColors - Array of hex color codes from palette
 */
export function applyCategoryColors(nodeSelection, settings) {
  if (!settings.categoryColorsEnabled || !settings.categoryColors) {
    return;
  }

  const palette = settings.categoryColors;

  nodeSelection.each(function(d) {
    const nodeElement = d3.select(this);
    
    // Get primary category (first category in the list)
    const primaryCategory = d.categories && d.categories.length > 0 
      ? d.categories[0] 
      : null;

    if (primaryCategory) {
      // Calculate color index based on category ID
      // This ensures consistent colors for the same category
      const colorIndex = primaryCategory.id % palette.length;
      const categoryColor = palette[colorIndex];

      // Apply color to node border/outline
      // Use a subtle colored ring around the node image
      const nodeSize = d.node_size || settings.defaultNodeSize || 80;
      const ringRadius = (nodeSize / 2) + 3;

      // Check if ring already exists
      let ring = nodeElement.select('.category-color-ring');
      
      if (ring.empty()) {
        // Create new ring behind the image
        ring = nodeElement.insert('circle', ':first-child')
          .attr('class', 'category-color-ring')
          .attr('r', ringRadius)
          .attr('cx', 0)
          .attr('cy', 0)
          .style('fill', 'none')
          .style('stroke-width', 4)
          .style('opacity', 0.8)
          .style('pointer-events', 'none');
      }

      // Update ring color and size
      ring
        .attr('r', ringRadius)
        .style('stroke', categoryColor)
        .transition()
        .duration(300)
        .style('opacity', 0.8);

      // Store category color on node data for use in other effects
      d.categoryColor = categoryColor;

    } else {
      // No category - remove ring if it exists
      nodeElement.select('.category-color-ring').remove();
      d.categoryColor = null;
    }
  });
}

/**
 * Update category colors when settings change
 * 
 * This function can be called when the user changes category color settings
 * in the Customizer to update all nodes dynamically.
 * 
 * @param {d3.Selection} container - The main SVG container
 * @param {Object} settings - Updated settings object
 */
export function updateCategoryColors(container, settings) {
  const nodes = container.selectAll('.graph-node');
  
  if (settings.categoryColorsEnabled) {
    applyCategoryColors(nodes, settings);
  } else {
    // Remove all category color rings when disabled
    nodes.selectAll('.category-color-ring').remove();
  }
}

/**
 * Get unique categories from articles data
 * 
 * Extracts and deduplicates all categories present in the graph data.
 * Returns categories with their IDs, names, and assigned colors.
 * 
 * @param {Array<Object>} articlesData - Array of article/node objects
 * @param {Object} settings - Settings object with categoryColors palette
 * @returns {Array<Object>} Array of unique category objects with colors
 */
export function getUniqueCategoriesWithColors(articlesData, settings) {
  if (!settings.categoryColorsEnabled || !settings.categoryColors) {
    return [];
  }

  const palette = settings.categoryColors;
  const categoriesMap = new Map();

  // Collect all unique categories
  articlesData.forEach(article => {
    if (article.categories && Array.isArray(article.categories)) {
      article.categories.forEach(cat => {
        if (!categoriesMap.has(cat.id)) {
          const colorIndex = cat.id % palette.length;
          categoriesMap.set(cat.id, {
            id: cat.id,
            name: cat.name,
            slug: cat.slug,
            color: palette[colorIndex],
            count: 0
          });
        }
        // Increment count
        const categoryInfo = categoriesMap.get(cat.id);
        categoryInfo.count++;
      });
    }
  });

  // Convert map to array and sort by count (most used first)
  return Array.from(categoriesMap.values())
    .sort((a, b) => b.count - a.count);
}
