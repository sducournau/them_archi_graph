import React from "react";

/**
 * Composant Tooltip qui s'affiche lors du survol d'un nœud
 */
const NodeTooltip = ({ node, position }) => {
  if (!node) return null;

  const tooltipStyle = {
    position: "fixed",
    left: position.x + 15,
    top: position.y - 10,
    transform: "translateY(-100%)",
    zIndex: 1000,
    pointerEvents: "none",
  };

  return (
    <div className="node-tooltip" style={tooltipStyle}>
      <div className="tooltip-content">
        <div className="tooltip-header">
          {node.thumbnail && (
            <img
              src={node.thumbnail}
              alt={node.title}
              className="tooltip-thumbnail"
            />
          )}
          <div className="tooltip-title-section">
            <h4 className="tooltip-title">{node.title}</h4>
            <time className="tooltip-date">
              {new Date(node.date).toLocaleDateString("fr-FR")}
            </time>
          </div>
        </div>

        {node.excerpt && <p className="tooltip-excerpt">{node.excerpt}</p>}

        {node.categories && node.categories.length > 0 && (
          <div className="tooltip-categories">
            {node.categories.map((category) => (
              <span
                key={category.id}
                className="tooltip-category-tag"
                style={{
                  backgroundColor: category.color,
                  color: getContrastColor(category.color),
                }}
              >
                {category.name}
              </span>
            ))}
          </div>
        )}

        {node.tags && node.tags.length > 0 && (
          <div className="tooltip-tags">
            {node.tags.slice(0, 3).map((tag) => (
              <span key={tag.id} className="tooltip-tag">
                #{tag.name}
              </span>
            ))}
            {node.tags.length > 3 && (
              <span className="tooltip-tag-more">+{node.tags.length - 3}</span>
            )}
          </div>
        )}

        <div className="tooltip-footer">
          <span className="tooltip-hint">🖱️ Cliquez pour lire l'article</span>
        </div>
      </div>

      {/* Flèche du tooltip */}
      <div className="tooltip-arrow"></div>
    </div>
  );
};

/**
 * Utilitaire pour calculer une couleur contrastante
 */
const getContrastColor = (hexColor) => {
  // Convertir hex en RGB
  const hex = hexColor.replace("#", "");
  const r = parseInt(hex.substr(0, 2), 16);
  const g = parseInt(hex.substr(2, 2), 16);
  const b = parseInt(hex.substr(4, 2), 16);

  // Calculer la luminosité
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

  return luminance > 0.5 ? "#000000" : "#ffffff";
};

export default NodeTooltip;
