/**
 * Utility functions for sidebar interactions
 */

/**
 * Show the info panel with node data
 * @param {Object} nodeData - Node data object
 */
export function showInfoPanel(nodeData) {
  console.log("showInfoPanel called with:", nodeData);
  
  const panel = document.getElementById("graph-info-panel");
  if (!panel) {
    console.warn("Info panel not found");
    return;
  }

  console.log("Panel found, updating content...");

  // Fill panel data
  const thumbnail = document.getElementById("panel-thumbnail");
  const title = document.getElementById("panel-title");
  const excerpt = document.getElementById("panel-excerpt");
  const link = document.getElementById("panel-link");
  const panelActions = document.querySelector(".panel-actions");
  const panelMeta = document.querySelector(".panel-meta");

  if (thumbnail) {
    thumbnail.src = nodeData.thumbnail_large || nodeData.thumbnail || "";
    thumbnail.alt = nodeData.title || "";
    thumbnail.style.display = nodeData.thumbnail || nodeData.thumbnail_large ? "block" : "none";
    console.log("Thumbnail updated:", thumbnail.src);
  }

  if (title) {
    title.textContent = nodeData.title || "";
    console.log("Title updated:", title.textContent);
  }

  if (excerpt) {
    excerpt.textContent = nodeData.excerpt || "";
    console.log("Excerpt updated:", excerpt.textContent);
  }

  if (link) {
    link.href = nodeData.permalink || "#";
    console.log("Link updated:", link.href);
  }

  // Show the link button when we have actual content
  if (panelActions) {
    panelActions.style.display = nodeData.permalink ? "block" : "none";
  }

  // Show meta info if available
  if (panelMeta) {
    panelMeta.style.display = "flex";
  }

  // Categories
  const categoriesContainer = document.getElementById("panel-categories");
  if (categoriesContainer && nodeData.categories) {
    categoriesContainer.innerHTML = "";
    nodeData.categories.forEach((cat) => {
      const span = document.createElement("span");
      span.className = "category-tag";
      span.style.backgroundColor = cat.color || "#3498db";
      span.textContent = cat.name || "";
      categoriesContainer.appendChild(span);
    });
    console.log("Categories updated:", nodeData.categories.length);
  }

  // Tags
  const tagsContainer = document.getElementById("panel-tags");
  if (tagsContainer) {
    tagsContainer.innerHTML = "";
    if (nodeData.tags && nodeData.tags.length > 0) {
      nodeData.tags.forEach((tag) => {
        const span = document.createElement("span");
        span.className = "tag-item";
        span.textContent = tag.name || "";
        tagsContainer.appendChild(span);
      });
      console.log("Tags updated:", nodeData.tags.length);
    }
  }

  // Comments section (if enabled in Customizer and comments exist)
  const commentsContainer = document.getElementById("panel-comments");
  if (commentsContainer && window.archiGraphConfig?.showComments && nodeData.comments) {
    commentsContainer.innerHTML = "";
    
    if (nodeData.comments.count > 0) {
      const commentsSection = document.createElement("div");
      commentsSection.className = "comments-section";
      
      const commentsTitle = document.createElement("h4");
      commentsTitle.textContent = `Commentaires (${nodeData.comments.count})`;
      commentsSection.appendChild(commentsTitle);
      
      if (nodeData.comments.recent && nodeData.comments.recent.length > 0) {
        const commentsList = document.createElement("ul");
        commentsList.className = "comments-list";
        
        nodeData.comments.recent.forEach((comment) => {
          const li = document.createElement("li");
          li.className = "comment-item";
          
          const author = document.createElement("strong");
          author.textContent = comment.author;
          
          const date = document.createElement("time");
          date.className = "comment-date";
          date.textContent = new Date(comment.date).toLocaleDateString();
          
          const content = document.createElement("p");
          content.className = "comment-content";
          content.textContent = comment.content;
          
          li.appendChild(author);
          li.appendChild(document.createTextNode(" - "));
          li.appendChild(date);
          li.appendChild(content);
          commentsList.appendChild(li);
        });
        
        commentsSection.appendChild(commentsList);
      }
      
      commentsContainer.appendChild(commentsSection);
      commentsContainer.style.display = "block";
      console.log("Comments updated:", nodeData.comments.count);
    } else {
      commentsContainer.style.display = "none";
    }
  } else if (commentsContainer) {
    commentsContainer.style.display = "none";
  }

  // Show the panel
  panel.classList.remove("hidden");
  console.log("Panel displayed");
}

/**
 * Hide the info panel
 */
export function hideInfoPanel() {
  const panel = document.getElementById("graph-info-panel");
  if (panel) {
    panel.classList.add("hidden");
  }
}

/**
 * Toggle info panel visibility
 */
export function toggleInfoPanel() {
  const panel = document.getElementById("graph-info-panel");
  if (panel) {
    panel.classList.toggle("hidden");
  }
}
