import { createRoot } from "react-dom/client";
import React from "react";
import GraphContainer from "@components/GraphContainer";
import "../css/main.scss";

/**
 * Point d'entrée principal de l'application React
 */
class ArchiGraphApp {
  constructor() {
    this.init();
  }

  init() {
    // Attendre que le DOM soit chargé
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () =>
        this.initializeGraph()
      );
    } else {
      this.initializeGraph();
    }
  }

  initializeGraph() {
    const container = document.getElementById("graph-container");

    if (!container) {
      console.warn("Conteneur du graphique non trouvé");
      return;
    }

    // Vérifications multiples pour détecter la page d'accueil
    const isHomePage =
      document.body.classList.contains("graph-homepage") ||
      document.body.classList.contains("home") ||
      document.body.classList.contains("front-page") ||
      container.closest(".graph-homepage-container") !== null ||
      window.location.pathname === "/" ||
      window.location.pathname === "/wordpress/";

    if (!isHomePage) {
      return;
    }

    // Configuration depuis WordPress
    const config = window.graphConfig || {};

    try {
      // Créer et monter le composant React (il gère son propre loading)
      const root = createRoot(container);
      root.render(
        React.createElement(GraphContainer, {
          config: config,
          onGraphReady: () => this.onGraphReady(),
          onError: (error) => this.onGraphError(error),
        })
      );

      // Stocker l'instance pour un accès global
      window.archiGraphApp = this;
    } catch (error) {
      console.error("Erreur lors de l'initialisation du graphique:", error);
      this.onGraphError(error);
    }
  }

  onGraphReady() {
    this.showLoading(false);
    this.setupGlobalControls();
  }

  onGraphError(error) {
    this.showLoading(false);
    this.showErrorMessage(
      error.message || "Erreur lors du chargement du graphique"
    );
    console.error("Erreur du graphique:", error);
  }

  showLoading(show) {
    const loader = document.getElementById("graph-loading");
    if (loader) {
      if (show) {
        loader.classList.remove("hidden");
      } else {
        loader.classList.add("hidden");
      }
    }
  }

  showErrorMessage(message) {
    const container = document.getElementById("graph-container");
    if (container) {
      container.innerHTML = `
                <div class="graph-error">
                    <h3>Erreur de chargement</h3>
                    <p>${message}</p>
                    <button onclick="location.reload()" class="btn btn-primary">
                        Réessayer
                    </button>
                </div>
            `;
    }
  }

  setupGlobalControls() {
    // Contrôle plein écran
    const fullscreenBtn = document.getElementById("graph-fullscreen");
    if (fullscreenBtn) {
      fullscreenBtn.addEventListener("click", () => this.toggleFullscreen());
    }

    // Contrôle reset zoom
    const resetBtn = document.getElementById("graph-reset-zoom");
    if (resetBtn) {
      resetBtn.addEventListener("click", () => this.resetZoom());
    }

    // Filtre par catégorie
    const categoryFilter = document.getElementById("category-filter");
    if (categoryFilter) {
      categoryFilter.addEventListener("change", (e) =>
        this.filterByCategory(e.target.value)
      );
    }

    // Raccourcis clavier
    document.addEventListener("keydown", (e) =>
      this.handleKeyboardShortcuts(e)
    );
  }

  toggleFullscreen() {
    const container = document.getElementById("graph-container");
    if (!container) return;

    if (!document.fullscreenElement) {
      container.requestFullscreen().catch((err) => {
        console.error("Erreur fullscreen:", err);
      });
    } else {
      document.exitFullscreen();
    }
  }

  resetZoom() {
    if (window.graphInstance && window.graphInstance.resetZoom) {
      window.graphInstance.resetZoom();
    }
  }

  filterByCategory(categoryId) {
    if (window.graphInstance && window.graphInstance.filterByCategory) {
      window.graphInstance.filterByCategory(categoryId);
    }
  }

  handleKeyboardShortcuts(event) {
    // F - Fullscreen
    if (event.key === "f" || event.key === "F") {
      event.preventDefault();
      this.toggleFullscreen();
    }

    // R - Reset zoom
    if (event.key === "r" || event.key === "R") {
      event.preventDefault();
      this.resetZoom();
    }

    // Échap - Fermer les panneaux
    if (event.key === "Escape") {
      const infoPanel = document.getElementById("graph-info-panel");
      if (infoPanel && !infoPanel.classList.contains("hidden")) {
        infoPanel.classList.add("hidden");
      }
    }
  }

  // Méthodes publiques pour l'interaction externe
  showNodeInfo(nodeData) {
    const panel = document.getElementById("graph-info-panel");
    if (!panel) return;

    // Remplir les données du panneau
    document.getElementById("panel-thumbnail").src =
      nodeData.thumbnail_large || nodeData.thumbnail;
    document.getElementById("panel-title").textContent = nodeData.title;
    document.getElementById("panel-excerpt").textContent = nodeData.excerpt;
    document.getElementById("panel-link").href = nodeData.permalink;

    // Catégories
    const categoriesContainer = document.getElementById("panel-categories");
    categoriesContainer.innerHTML = "";
    nodeData.categories.forEach((cat) => {
      const span = document.createElement("span");
      span.className = "category-tag";
      span.style.backgroundColor = cat.color;
      span.textContent = cat.name;
      categoriesContainer.appendChild(span);
    });

    // Tags
    const tagsContainer = document.getElementById("panel-tags");
    tagsContainer.innerHTML = "";
    if (nodeData.tags && nodeData.tags.length > 0) {
      nodeData.tags.forEach((tag) => {
        const span = document.createElement("span");
        span.className = "tag-item";
        span.textContent = tag.name;
        tagsContainer.appendChild(span);
      });
    }

    // Afficher le panneau
    panel.classList.remove("hidden");
  }

  hideNodeInfo() {
    const panel = document.getElementById("graph-info-panel");
    if (panel) {
      panel.classList.add("hidden");
    }
  }
}

// Initialiser l'application
new ArchiGraphApp();

// Exporter pour un accès global si nécessaire
window.ArchiGraphApp = ArchiGraphApp;
