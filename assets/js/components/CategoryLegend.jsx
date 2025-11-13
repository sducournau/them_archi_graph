/**
 * Category Legend Component
 * 
 * Displays a color-coded legend of categories shown in the graph.
 * Only appears when categoryColorsEnabled and showCategoryLegend are true.
 * 
 * @package Archi-Graph
 */

import React, { useState, useEffect } from 'react';
import { getUniqueCategoriesWithColors } from '../utils/categoryColors';

/**
 * CategoryLegend Component
 * 
 * @param {Object} props
 * @param {Array<Object>} props.articles - Array of article/node data
 * @param {Object} props.settings - Graph settings from window.archiGraphSettings
 * @param {Set} props.selectedCategories - Set of selected category IDs
 * @param {Function} props.onCategoryToggle - Callback to toggle category selection
 * @param {Function} props.onClearFilters - Callback to clear all filters
 */
const CategoryLegend = ({ articles, settings, selectedCategories, onCategoryToggle, onClearFilters }) => {
  const [categories, setCategories] = useState([]);
  const [isCollapsed, setIsCollapsed] = useState(false);

  useEffect(() => {
    if (settings.categoryColorsEnabled && settings.showCategoryLegend) {
      const uniqueCategories = getUniqueCategoriesWithColors(articles, settings);
      setCategories(uniqueCategories);
    } else {
      setCategories([]);
    }
  }, [articles, settings]);

  // Don't render if disabled or no categories
  if (!settings.categoryColorsEnabled || !settings.showCategoryLegend || categories.length === 0) {
    return null;
  }

  return (
    <div className={`category-legend ${isCollapsed ? 'collapsed' : ''}`}>
      <div className="legend-header" onClick={() => setIsCollapsed(!isCollapsed)}>
        <h3>
          <span className="legend-icon">🎨</span>
          Catégories
        </h3>
        <button 
          className="toggle-btn" 
          aria-label={isCollapsed ? 'Développer la légende' : 'Réduire la légende'}
        >
          {isCollapsed ? '▼' : '▲'}
        </button>
      </div>

      {!isCollapsed && (
        <div className="legend-content">
          {/* Bouton pour tout afficher */}
          {selectedCategories && selectedCategories.size > 0 && (
            <div className="legend-controls" style={{ marginBottom: '10px', borderBottom: '1px solid rgba(0,0,0,0.1)', paddingBottom: '10px' }}>
              <button
                className="clear-filters-btn"
                onClick={onClearFilters}
                style={{
                  width: '100%',
                  padding: '8px 12px',
                  background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                  color: 'white',
                  border: 'none',
                  borderRadius: '6px',
                  cursor: 'pointer',
                  fontWeight: '600',
                  fontSize: '13px',
                  transition: 'all 0.3s ease',
                  boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
                }}
                onMouseOver={(e) => {
                  e.target.style.transform = 'translateY(-1px)';
                  e.target.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
                }}
                onMouseOut={(e) => {
                  e.target.style.transform = 'translateY(0)';
                  e.target.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                }}
              >
                🔄 Afficher tout le graphique
              </button>
            </div>
          )}

          <ul className="legend-items">
            {categories.map(category => {
              const isSelected = selectedCategories && selectedCategories.has(category.id);
              
              return (
                <li 
                  key={category.id} 
                  className={`legend-item ${isSelected ? 'selected' : ''}`}
                  onClick={() => onCategoryToggle && onCategoryToggle(category.id)}
                  style={{ 
                    cursor: 'pointer',
                    padding: '8px 12px',
                    borderRadius: '6px',
                    marginBottom: '6px',
                    background: isSelected ? 'rgba(102, 126, 234, 0.1)' : 'transparent',
                    border: isSelected ? '2px solid #667eea' : '2px solid transparent',
                    transition: 'all 0.2s ease',
                    display: 'flex',
                    alignItems: 'center'
                  }}
                >
                  <span 
                    className="legend-color" 
                    style={{ 
                      backgroundColor: category.color,
                      width: '16px',
                      height: '16px',
                      display: 'inline-block',
                      borderRadius: '50%',
                      marginRight: '8px',
                      border: '2px solid rgba(255,255,255,0.3)',
                      boxShadow: '0 1px 3px rgba(0,0,0,0.2)',
                      opacity: isSelected ? 1 : 0.6
                    }}
                  />
                  <span className="legend-name" style={{ 
                    flex: 1,
                    fontWeight: isSelected ? '600' : '400',
                    color: isSelected ? '#667eea' : 'inherit'
                  }}>{category.name}</span>
                  <span className="legend-count" style={{ 
                    marginLeft: '8px',
                    opacity: 0.7,
                    fontSize: '12px'
                  }}>({category.count})</span>
                  {isSelected && (
                    <span style={{ 
                      marginLeft: '8px',
                      color: '#667eea',
                      fontWeight: 'bold'
                    }}>✓</span>
                  )}
                </li>
              );
            })}
          </ul>
        </div>
      )}
    </div>
  );
};

export default CategoryLegend;
