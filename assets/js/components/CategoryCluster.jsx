import React from "react";

/**
 * Composant pour représenter un cluster de catégorie
 */
const CategoryCluster = ({
  category,
  position,
  articles,
  isVisible = true,
}) => {
  if (!isVisible || !category) return null;

  const radius = Math.max(40, Math.min(100, articles.length * 20));
  const opacity = 0.1;

  return (
    <g
      className="category-cluster"
      transform={`translate(${position.x}, ${position.y})`}
    >
      {/* Cercle de fond du cluster */}
      <circle
        className="cluster-background"
        r={radius}
        fill={category.color}
        fillOpacity={opacity}
        stroke={category.color}
        strokeWidth={2}
        strokeOpacity={0.3}
        style={{
          transition: "all 0.5s ease",
        }}
      />

      {/* Label de la catégorie */}
      <text
        className="cluster-label"
        textAnchor="middle"
        dy="0.35em"
        fontSize="14px"
        fontWeight="bold"
        fill={category.color}
        style={{
          textShadow: "1px 1px 2px rgba(0,0,0,0.3)",
          pointerEvents: "none",
        }}
      >
        {category.name}
      </text>

      {/* Nombre d'articles dans le cluster */}
      <text
        className="cluster-count"
        textAnchor="middle"
        dy="20"
        fontSize="12px"
        fill={category.color}
        opacity={0.7}
        style={{
          pointerEvents: "none",
        }}
      >
        {articles.length} article{articles.length > 1 ? "s" : ""}
      </text>
    </g>
  );
};

export default CategoryCluster;
