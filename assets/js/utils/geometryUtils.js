/**
 * Geometry Utilities for Graph Visualization
 * 
 * Helper functions for geometric calculations used in graph rendering:
 * - Convex hull calculation
 * - Hull expansion/smoothing
 * - Polygon operations
 * 
 * Extracted from GraphContainer.jsx for better code organization
 */

/**
 * Calculate the convex hull of a set of points
 * Uses Graham scan algorithm
 * 
 * @param {Array} points - Array of {x, y} points
 * @returns {Array} Points forming the convex hull
 */
export const convexHull = (points) => {
  if (points.length < 3) return points;

  // Copy and sort points
  const sorted = [...points].sort((a, b) => {
    if (a.x === b.x) return a.y - b.y;
    return a.x - b.x;
  });

  // Build lower hull
  const lower = [];
  for (let i = 0; i < sorted.length; i++) {
    while (
      lower.length >= 2 &&
      cross(lower[lower.length - 2], lower[lower.length - 1], sorted[i]) <= 0
    ) {
      lower.pop();
    }
    lower.push(sorted[i]);
  }

  // Build upper hull
  const upper = [];
  for (let i = sorted.length - 1; i >= 0; i--) {
    while (
      upper.length >= 2 &&
      cross(upper[upper.length - 2], upper[upper.length - 1], sorted[i]) <= 0
    ) {
      upper.pop();
    }
    upper.push(sorted[i]);
  }

  // Remove last point from each half as it's duplicated
  lower.pop();
  upper.pop();

  return lower.concat(upper);
};

/**
 * Cross product for convex hull algorithm
 * 
 * @param {Object} o - Origin point {x, y}
 * @param {Object} a - Point A {x, y}
 * @param {Object} b - Point B {x, y}
 * @returns {number} Cross product
 */
export const cross = (o, a, b) => {
  return (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
};

/**
 * Expand a convex hull by adding padding
 * Pushes all points away from the centroid
 * 
 * @param {Array} hull - Array of {x, y} points forming the hull
 * @param {number} padding - Padding distance in pixels (default: 30)
 * @returns {Array} Expanded hull points
 */
export const expandHull = (hull, padding = 30) => {
  if (hull.length < 3) return hull;

  // Calculate centroid
  const centroid = {
    x: hull.reduce((sum, p) => sum + p.x, 0) / hull.length,
    y: hull.reduce((sum, p) => sum + p.y, 0) / hull.length,
  };

  // Expand each point from the centroid
  return hull.map((point) => {
    const dx = point.x - centroid.x;
    const dy = point.y - centroid.y;
    const dist = Math.sqrt(dx * dx + dy * dy);

    if (dist === 0) return point;

    const factor = (dist + padding) / dist;
    return {
      x: centroid.x + dx * factor,
      y: centroid.y + dy * factor,
    };
  });
};

/**
 * Smooth hull corners for more organic appearance
 * Uses Bezier curve control points
 * 
 * @param {Array} hull - Array of {x, y} points
 * @param {number} smoothness - Smoothing factor 0-1 (default: 0.3)
 * @returns {Array} Smoothed hull with interpolated points
 */
export const smoothHull = (hull, smoothness = 0.3) => {
  if (hull.length < 3) return hull;
  
  const smoothedHull = [];
  for (let i = 0; i < hull.length; i++) {
    const prev = hull[(i - 1 + hull.length) % hull.length];
    const curr = hull[i];
    const next = hull[(i + 1) % hull.length];
    
    // Control points for Bezier curve
    const cp1x = curr.x + (prev.x - curr.x) * smoothness;
    const cp1y = curr.y + (prev.y - curr.y) * smoothness;
    const cp2x = curr.x + (next.x - curr.x) * smoothness;
    const cp2y = curr.y + (next.y - curr.y) * smoothness;
    
    smoothedHull.push({ x: cp1x, y: cp1y });
    smoothedHull.push({ x: curr.x, y: curr.y });
    smoothedHull.push({ x: cp2x, y: cp2y });
  }
  
  return smoothedHull;
};

/**
 * Create circular hull around points
 * Used when there are too few points for a proper convex hull
 * 
 * @param {Array} points - Array of {x, y} points
 * @param {number} radius - Circle radius (default: 100)
 * @param {number} segments - Number of circle segments (default: 12)
 * @returns {Array} Circle points
 */
export const createCircularHull = (points, radius = 100, segments = 12) => {
  if (points.length === 0) return [];
  
  const avgX = points.reduce((sum, p) => sum + p.x, 0) / points.length;
  const avgY = points.reduce((sum, p) => sum + p.y, 0) / points.length;
  
  return Array.from({ length: segments }, (_, i) => {
    const angle = (i / segments) * Math.PI * 2;
    return {
      x: avgX + Math.cos(angle) * radius,
      y: avgY + Math.sin(angle) * radius,
    };
  });
};

/**
 * Convert hull points to SVG path string
 * 
 * @param {Array} hull - Array of {x, y} points
 * @returns {string} SVG path data
 */
export const hullToPath = (hull) => {
  if (!hull || hull.length === 0) return "";

  const pathData = hull
    .map((point, i) => {
      const command = i === 0 ? "M" : "L";
      return `${command}${point.x},${point.y}`;
    })
    .join(" ");

  return pathData + " Z"; // Close the path
};

/**
 * Calculate the centroid (center point) of a set of points
 * 
 * @param {Array} points - Array of {x, y} points
 * @returns {Object} Centroid point {x, y}
 */
export const calculateCentroid = (points) => {
  if (points.length === 0) return { x: 0, y: 0 };
  
  return {
    x: points.reduce((sum, p) => sum + p.x, 0) / points.length,
    y: points.reduce((sum, p) => sum + p.y, 0) / points.length,
  };
};
