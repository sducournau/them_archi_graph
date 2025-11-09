/**
 * GIF Animation Controller for Graph Nodes
 * Controls GIF playback - pauses by default, plays on hover/active state
 */

/**
 * Cache for static GIF frames (first frame)
 */
const staticFrameCache = new Map();

/**
 * Extract the first frame of a GIF as a static image
 * @param {string} gifUrl - URL of the GIF image
 * @returns {Promise<string>} - Data URL of the first frame
 */
export const extractFirstFrame = (gifUrl) => {
  // Check cache first
  if (staticFrameCache.has(gifUrl)) {
    return Promise.resolve(staticFrameCache.get(gifUrl));
  }

  return new Promise((resolve, reject) => {
    const img = new Image();
    img.crossOrigin = "anonymous";

    img.onload = () => {
      try {
        // Create canvas with image dimensions
        const canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);

        // Convert to data URL (static image)
        const staticUrl = canvas.toDataURL("image/png");

        // Cache the result
        staticFrameCache.set(gifUrl, staticUrl);

        resolve(staticUrl);
      } catch (error) {
        console.warn("Failed to extract GIF frame:", error);
        // Fallback to original GIF
        resolve(gifUrl);
      }
    };

    img.onerror = () => {
      console.warn("Failed to load GIF:", gifUrl);
      resolve(gifUrl);
    };

    // Add cache buster to ensure we get a fresh image
    img.src = gifUrl + (gifUrl.includes("?") ? "&" : "?") + "_=" + Date.now();
  });
};

/**
 * Check if a URL is a GIF
 * @param {string} url - Image URL
 * @returns {boolean}
 */
export const isGif = (url) => {
  if (!url) return false;
  return url.toLowerCase().endsWith(".gif") || url.toLowerCase().includes(".gif?");
};

/**
 * Process thumbnail URL - convert GIF to static frame if needed
 * @param {string} url - Original image URL
 * @returns {Promise<object>} - Object with staticUrl and animatedUrl
 */
export const processNodeImage = async (url) => {
  if (!isGif(url)) {
    // Not a GIF, return as-is
    return {
      staticUrl: url,
      animatedUrl: url,
      isGif: false,
    };
  }

  try {
    const staticUrl = await extractFirstFrame(url);
    return {
      staticUrl,
      animatedUrl: url,
      isGif: true,
    };
  } catch (error) {
    console.warn("Failed to process GIF:", error);
    return {
      staticUrl: url,
      animatedUrl: url,
      isGif: false,
    };
  }
};

/**
 * Activate GIF animation for a node
 * @param {d3.Selection} nodeElement - D3 selection of the node
 * @param {object} nodeData - Node data with image URLs
 */
export const activateNodeGif = (nodeElement, nodeData) => {
  if (!nodeData._imageUrls || !nodeData._imageUrls.isGif) return;

  nodeElement
    .select(".node-image")
    .attr("href", nodeData._imageUrls.animatedUrl)
    .classed("gif-playing", true);
};

/**
 * Deactivate GIF animation for a node
 * @param {d3.Selection} nodeElement - D3 selection of the node
 * @param {object} nodeData - Node data with image URLs
 */
export const deactivateNodeGif = (nodeElement, nodeData) => {
  if (!nodeData._imageUrls || !nodeData._imageUrls.isGif) return;

  nodeElement
    .select(".node-image")
    .attr("href", nodeData._imageUrls.staticUrl)
    .classed("gif-playing", false);
};

/**
 * Preprocess all articles' images
 * @param {Array} articles - Array of article data
 * @returns {Promise<Array>} - Articles with processed image URLs
 */
export const preprocessArticleImages = async (articles) => {
  const promises = articles.map(async (article) => {
    const imageUrls = await processNodeImage(article.thumbnail);
    return {
      ...article,
      _imageUrls: imageUrls,
      // Use static frame as default thumbnail
      thumbnail: imageUrls.staticUrl,
    };
  });

  return Promise.all(promises);
};

/**
 * Clear the static frame cache
 */
export const clearCache = () => {
  staticFrameCache.clear();
};
