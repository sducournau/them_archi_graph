import React from "react";

/**
 * Composant pour un nœud individuel du graphique
 * Note: Ce composant est principalement géré par D3.js dans GraphContainer
 * Il sert de référence pour la structure des données
 */
const Node = ({ data, position, isSelected, onHover, onClick }) => {
  // Récupérer les settings depuis window.archiGraphSettings
  const settings = window.archiGraphSettings || {};
  const priorityFeaturedColor = settings.priorityFeaturedColor || '#e74c3c';
  const priorityHighColor = settings.priorityHighColor || '#f39c12';
  const priorityBadgeSize = settings.priorityBadgeSize || 8;
  const priorityBadgeOffset = settings.priorityBadgeOffset || 5;
  const priorityBadgeStrokeColor = settings.priorityBadgeStrokeColor || '#ffffff';
  const priorityBadgeStrokeWidth = settings.priorityBadgeStrokeWidth || 2;
  
  return (
    <g
      className="graph-node"
      transform={`translate(${position.x}, ${position.y})`}
      style={{ cursor: "pointer" }}
      onMouseEnter={() => onHover && onHover(data, true)}
      onMouseLeave={() => onHover && onHover(data, false)}
      onClick={() => onClick && onClick(data)}
    >
      {/* Image PNG avec fond transparent - apparaît en entier */}
      <image
        className="node-image"
        href={data.thumbnail}
        width={data.node_size || 80}
        height={data.node_size || 80}
        x={-(data.node_size || 80) / 2}
        y={-(data.node_size || 80) / 2}
        preserveAspectRatio="xMidYMid meet"
        style={{
          filter: "drop-shadow(2px 2px 4px rgba(0,0,0,0.3))",
          overflow: "visible",
        }}
      />

      {/* Badge de priorité */}
      {(data.priority_level === "featured" ||
        data.priority_level === "high") && (
        <circle
          className="priority-badge"
          r={priorityBadgeSize}
          cx={(data.node_size || 80) / 2 - priorityBadgeOffset}
          cy={-(data.node_size || 80) / 2 + priorityBadgeOffset}
          fill={data.priority_level === "featured" ? priorityFeaturedColor : priorityHighColor}
          stroke={priorityBadgeStrokeColor}
          strokeWidth={priorityBadgeStrokeWidth}
        />
      )}

      {/* Label optionnel */}
      {data.showLabel && (
        <text
          className="node-label"
          textAnchor="middle"
          y={(data.node_size || 80) / 2 + 20}
          fontSize="12px"
          fill="#333"
          fontWeight="500"
        >
          {data.title.length > 15
            ? data.title.substring(0, 15) + "..."
            : data.title}
        </text>
      )}
    </g>
  );
};

export default Node;
