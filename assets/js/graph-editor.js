/**
 * Éditeur de graphique en direct pour administrateurs
 * Mode édition avec déplacement nodes, création liens, édition images
 */

class GraphEditor {
  constructor(graphInstance) {
    this.graph = graphInstance;
    this.enabled = false;
    this.linkCreationMode = false;
    this.selectedNode = null;
    this.pendingSaves = new Set();
    this.saveDebounce = null;
    this.originalDragHandlers = null;

    // Configuration
    this.config = window.archiGraphEditor || {};
    this.canEdit = this.config.canEdit || false;
    this.apiUrl = this.config.apiUrl || "/wp-json/archi/v1/graph-editor/";
    this.strings = this.config.strings || {};

    this.init();
  }

  /**
   * Initialiser l'éditeur
   */
  init() {
    if (!this.canEdit) {
      return;
    }

    this.createEditorPanel();
    this.setupKeyboardShortcuts();
  }

  /**
   * Créer le panneau d'édition
   */
  createEditorPanel() {
    const panel = document.createElement("div");
    panel.id = "archi-graph-editor-panel";
    panel.className = "archi-editor-panel";
    panel.innerHTML = `
            <div class="archi-editor-header">
                <h3>🎨 ${this.strings.editMode || "Mode Édition"}</h3>
                <button class="archi-editor-close" aria-label="Fermer">×</button>
            </div>
            
            <div class="archi-editor-body">
                <!-- Mode édition principal -->
                <div class="archi-editor-section">
                    <label class="archi-toggle-label">
                        <input type="checkbox" id="archi-edit-mode-toggle" />
                        <span class="archi-toggle-slider"></span>
                        <span class="archi-toggle-text">Activer l'édition</span>
                    </label>
                </div>
                
                <!-- Outils -->
                <div class="archi-editor-section archi-tools-section" style="display: none;">
                    <h4>Outils</h4>
                    
                    <button class="archi-editor-btn archi-btn-link-mode" data-mode="link">
                        🔗 ${this.strings.createLink || "Créer un lien"}
                    </button>
                    
                    <button class="archi-editor-btn archi-btn-save" data-action="save">
                        💾 ${this.strings.savePositions || "Sauvegarder"}
                    </button>
                </div>
                
                <!-- Nœud sélectionné -->
                <div class="archi-editor-section archi-node-section" style="display: none;">
                    <h4>Nœud sélectionné</h4>
                    <div class="archi-node-info">
                        <p class="archi-node-title">Aucun</p>
                        <p class="archi-node-id"></p>
                    </div>
                    
                    <button class="archi-editor-btn archi-btn-change-image" data-action="change-image">
                        🖼️ ${this.strings.changeImage || "Changer l'image"}
                    </button>
                    
                    <button class="archi-editor-btn archi-btn-toggle-visibility" data-action="toggle-visibility">
                        👁️ ${this.strings.toggleVisibility || "Visibilité"}
                    </button>
                    
                    <button class="archi-editor-btn archi-btn-edit-params" data-action="edit-params">
                        ⚙️ Paramètres avancés
                    </button>
                </div>
                
                <!-- Paramètres avancés -->
                <div class="archi-editor-section archi-params-section" style="display: none;">
                    <h4>Paramètres avancés</h4>
                    
                    <label>
                        Forme:
                        <select id="archi-param-shape">
                            <option value="circle">Cercle</option>
                            <option value="square">Carré</option>
                            <option value="diamond">Diamant</option>
                            <option value="triangle">Triangle</option>
                            <option value="star">Étoile</option>
                            <option value="hexagon">Hexagone</option>
                        </select>
                    </label>
                    
                    <label>
                        Couleur:
                        <input type="color" id="archi-param-color" />
                    </label>
                    
                    <label>
                        Taille:
                        <input type="range" id="archi-param-size" min="40" max="120" step="5" />
                        <span id="archi-param-size-value">60</span>px
                    </label>
                    
                    <label>
                        Icône:
                        <input type="text" id="archi-param-icon" placeholder="🏗️" maxlength="2" />
                    </label>
                    
                    <label>
                        Badge:
                        <select id="archi-param-badge">
                            <option value="">Aucun</option>
                            <option value="new">Nouveau</option>
                            <option value="featured">À la une</option>
                            <option value="hot">Populaire</option>
                            <option value="updated">Mis à jour</option>
                        </select>
                    </label>
                    
                    <button class="archi-editor-btn archi-btn-apply-params" data-action="apply-params">
                        ✅ Appliquer
                    </button>
                    
                    <button class="archi-editor-btn archi-btn-cancel-params" data-action="cancel-params">
                        ❌ Annuler
                    </button>
                </div>
                
                <!-- Statut -->
                <div class="archi-editor-status">
                    <span class="archi-status-text"></span>
                </div>
            </div>
        `;

    document.body.appendChild(panel);
    this.panel = panel;

    // Événements
    this.setupPanelEvents();
  }

  /**
   * Configurer les événements du panneau
   */
  setupPanelEvents() {
    const toggle = this.panel.querySelector("#archi-edit-mode-toggle");
    const closeBtn = this.panel.querySelector(".archi-editor-close");
    const toolsSection = this.panel.querySelector(".archi-tools-section");
    const linkModeBtn = this.panel.querySelector(".archi-btn-link-mode");
    const saveBtn = this.panel.querySelector(".archi-btn-save");
    const changeImageBtn = this.panel.querySelector(".archi-btn-change-image");
    const toggleVisibilityBtn = this.panel.querySelector(
      ".archi-btn-toggle-visibility"
    );
    const editParamsBtn = this.panel.querySelector(".archi-btn-edit-params");
    const applyParamsBtn = this.panel.querySelector(".archi-btn-apply-params");
    const cancelParamsBtn = this.panel.querySelector(
      ".archi-btn-cancel-params"
    );

    // Toggle mode édition
    toggle.addEventListener("change", () => {
      this.setEditMode(toggle.checked);
      toolsSection.style.display = toggle.checked ? "block" : "none";
    });

    // Fermer panneau
    closeBtn.addEventListener("click", () => {
      this.panel.classList.remove("archi-panel-open");
    });

    // Mode création de lien
    linkModeBtn.addEventListener("click", () => {
      this.toggleLinkCreationMode();
    });

    // Sauvegarder positions
    saveBtn.addEventListener("click", () => {
      this.saveAllPositions();
    });

    // Changer image
    changeImageBtn.addEventListener("click", () => {
      this.openMediaLibrary();
    });

    // Toggle visibilité
    toggleVisibilityBtn.addEventListener("click", () => {
      this.toggleNodeVisibility();
    });

    // Éditer paramètres
    editParamsBtn.addEventListener("click", () => {
      this.openParamsEditor();
    });

    // Appliquer paramètres
    applyParamsBtn.addEventListener("click", () => {
      this.applyNodeParams();
    });

    // Annuler paramètres
    cancelParamsBtn.addEventListener("click", () => {
      this.closeParamsEditor();
    });

    // Slider taille
    const sizeSlider = this.panel.querySelector("#archi-param-size");
    const sizeValue = this.panel.querySelector("#archi-param-size-value");
    sizeSlider.addEventListener("input", (e) => {
      sizeValue.textContent = e.target.value;
    });
  }

  /**
   * Activer/désactiver le mode édition
   */
  setEditMode(enabled) {
    this.enabled = enabled;

    if (enabled) {
      this.enableDragAndSave();
      this.enableNodeSelection();
      this.showStatus("Mode édition activé - déplacez les nœuds", "success");
    } else {
      this.disableLinkCreationMode();
      this.showStatus("Mode édition désactivé", "info");
    }

    // Ajouter classe au body
    document.body.classList.toggle("archi-edit-mode-active", enabled);
  }

  /**
   * Activer le drag-and-drop avec sauvegarde
   */
  enableDragAndSave() {
    if (!this.graph.svg) return;

    const nodes = this.graph.svg.selectAll(".node");

    nodes.call(
      d3
        .drag()
        .on("start", (event, d) => this.dragStarted(event, d))
        .on("drag", (event, d) => this.dragged(event, d))
        .on("end", (event, d) => this.dragEnded(event, d))
    );
  }

  /**
   * Activer la sélection de nœuds
   */
  enableNodeSelection() {
    if (!this.graph.svg) return;

    const nodes = this.graph.svg.selectAll(".node");

    nodes.on("click", (event, d) => {
      if (this.linkCreationMode) {
        this.handleLinkCreation(d);
      } else {
        this.selectNode(d);
      }
      event.stopPropagation();
    });
  }

  /**
   * Gestionnaires de drag
   */
  dragStarted(event, d) {
    d3.select(event.sourceEvent.target.parentNode).raise();
    this.showStatus(`Déplacement de "${d.title}"...`, "info");
  }

  dragged(event, d) {
    d.x = event.x;
    d.y = event.y;
    d3.select(event.sourceEvent.target.parentNode).attr(
      "transform",
      `translate(${d.x},${d.y})`
    );

    // Mettre à jour les liens si présents
    if (this.graph.svg) {
      this.graph.svg
        .selectAll(".link")
        .filter((l) => l.source.id === d.id || l.target.id === d.id)
        .attr("d", (l) => {
          const sx = l.source.x || 0;
          const sy = l.source.y || 0;
          const tx = l.target.x || 0;
          const ty = l.target.y || 0;
          return `M${sx},${sy}L${tx},${ty}`;
        });
    }
  }

  dragEnded(event, d) {
    // Marquer pour sauvegarde
    this.pendingSaves.add(d.id);

    // Debounce save
    clearTimeout(this.saveDebounce);
    this.saveDebounce = setTimeout(() => {
      this.savePendingPositions();
    }, 1000);

    this.showStatus("Position modifiée - sauvegarde automatique...", "info");
  }

  /**
   * Sauvegarder les positions en attente
   */
  async savePendingPositions() {
    if (this.pendingSaves.size === 0) return;

    const positions = [];
    this.pendingSaves.forEach((nodeId) => {
      const node = this.graph.nodes?.find((n) => n.id === nodeId);
      if (node) {
        positions.push({
          id: nodeId,
          x: node.x,
          y: node.y,
        });
      }
    });

    try {
      const response = await this.apiRequest("save-positions", {
        method: "POST",
        body: JSON.stringify({ positions }),
      });

      if (response.success) {
        this.pendingSaves.clear();
        this.showStatus(
          `✅ ${response.saved} position(s) sauvegardée(s)`,
          "success"
        );
      }
    } catch (error) {
      console.error("Save positions error:", error);
      this.showStatus("❌ Erreur de sauvegarde", "error");
    }
  }

  /**
   * Sauvegarder toutes les positions
   */
  async saveAllPositions() {
    if (!this.graph.nodes) return;

    const positions = this.graph.nodes.map((node) => ({
      id: node.id,
      x: node.x,
      y: node.y,
    }));

    this.showStatus("Sauvegarde en cours...", "info");

    try {
      const response = await this.apiRequest("save-positions", {
        method: "POST",
        body: JSON.stringify({ positions }),
      });

      if (response.success) {
        this.showStatus(
          `✅ ${response.saved} positions sauvegardées`,
          "success"
        );
      }
    } catch (error) {
      console.error("Save all positions error:", error);
      this.showStatus("❌ Erreur de sauvegarde", "error");
    }
  }

  /**
   * Activer/désactiver le mode création de lien
   */
  toggleLinkCreationMode() {
    this.linkCreationMode = !this.linkCreationMode;
    const btn = this.panel.querySelector(".archi-btn-link-mode");

    if (this.linkCreationMode) {
      btn.classList.add("active");
      this.showStatus(
        this.strings.selectSource || "Cliquez sur le nœud source",
        "info"
      );
      this.selectedNode = null;
    } else {
      btn.classList.remove("active");
      this.selectedNode = null;
      this.showStatus("Mode création de lien désactivé", "info");
    }

    document.body.classList.toggle(
      "archi-link-creation-mode",
      this.linkCreationMode
    );
  }

  disableLinkCreationMode() {
    this.linkCreationMode = false;
    const btn = this.panel.querySelector(".archi-btn-link-mode");
    if (btn) btn.classList.remove("active");
    document.body.classList.remove("archi-link-creation-mode");
  }

  /**
   * Gérer la création de lien
   */
  async handleLinkCreation(targetNode) {
    if (!this.selectedNode) {
      // Premier clic : sélectionner source
      this.selectedNode = targetNode;
      this.highlightNode(targetNode.id, true);
      this.showStatus(
        this.strings.selectTarget || "Cliquez sur le nœud cible",
        "info"
      );
    } else {
      // Deuxième clic : créer le lien
      if (this.selectedNode.id === targetNode.id) {
        this.showStatus("❌ Impossible de lier un nœud à lui-même", "error");
        return;
      }

      try {
        const response = await this.apiRequest("create-link", {
          method: "POST",
          body: JSON.stringify({
            source_id: this.selectedNode.id,
            target_id: targetNode.id,
          }),
        });

        if (response.success) {
          this.showStatus(
            this.strings.linkCreated || "Lien créé !",
            "success"
          );

          // Ajouter le lien visuellement
          this.addLinkVisually(this.selectedNode, targetNode);

          // Réinitialiser
          this.highlightNode(this.selectedNode.id, false);
          this.selectedNode = null;

          // Rester en mode création
          this.showStatus(
            this.strings.selectSource || "Cliquez sur le nœud source",
            "info"
          );
        }
      } catch (error) {
        console.error("Create link error:", error);
        this.showStatus("❌ Erreur création lien", "error");
        this.highlightNode(this.selectedNode.id, false);
        this.selectedNode = null;
      }
    }
  }

  /**
   * Ajouter un lien visuellement
   */
  addLinkVisually(sourceNode, targetNode) {
    if (!this.graph.svg) return;

    const linkData = {
      source: sourceNode,
      target: targetNode,
    };

    this.graph.svg
      .select(".links")
      .append("path")
      .datum(linkData)
      .attr("class", "link link-manual")
      .attr("stroke", "#999")
      .attr("stroke-width", 2)
      .attr("fill", "none")
      .attr(
        "d",
        `M${sourceNode.x},${sourceNode.y}L${targetNode.x},${targetNode.y}`
      );
  }

  /**
   * Sélectionner un nœud
   */
  selectNode(nodeData) {
    this.selectedNode = nodeData;

    const nodeSection = this.panel.querySelector(".archi-node-section");
    const nodeTitle = this.panel.querySelector(".archi-node-title");
    const nodeId = this.panel.querySelector(".archi-node-id");

    nodeSection.style.display = "block";
    nodeTitle.textContent = nodeData.title;
    nodeId.textContent = `ID: ${nodeData.id}`;

    // Highlight visuel
    this.highlightNode(nodeData.id, true);

    this.showStatus(`Nœud sélectionné: ${nodeData.title}`, "info");
  }

  /**
   * Highlight un nœud
   */
  highlightNode(nodeId, highlight) {
    if (!this.graph.svg) return;

    this.graph.svg.selectAll(".node").classed("node-selected", false);

    if (highlight) {
      this.graph.svg
        .selectAll(".node")
        .filter((d) => d.id === nodeId)
        .classed("node-selected", true);
    }
  }

  /**
   * Ouvrir l'éditeur de paramètres
   */
  openParamsEditor() {
    if (!this.selectedNode) return;

    const paramsSection = this.panel.querySelector(".archi-params-section");
    paramsSection.style.display = "block";

    // Pré-remplir les valeurs
    const node = this.selectedNode;
    this.panel.querySelector("#archi-param-shape").value =
      node.advanced_graph_params?.node_shape || "circle";
    this.panel.querySelector("#archi-param-color").value =
      node.color || "#3498db";
    this.panel.querySelector("#archi-param-size").value = node.size || 60;
    this.panel.querySelector("#archi-param-size-value").textContent =
      node.size || 60;
    this.panel.querySelector("#archi-param-icon").value =
      node.advanced_graph_params?.node_icon || "";
    this.panel.querySelector("#archi-param-badge").value =
      node.advanced_graph_params?.node_badge || "";
  }

  /**
   * Fermer l'éditeur de paramètres
   */
  closeParamsEditor() {
    const paramsSection = this.panel.querySelector(".archi-params-section");
    paramsSection.style.display = "none";
  }

  /**
   * Appliquer les paramètres au nœud
   */
  async applyNodeParams() {
    if (!this.selectedNode) return;

    const params = {
      node_shape: this.panel.querySelector("#archi-param-shape").value,
      node_color: this.panel.querySelector("#archi-param-color").value,
      node_size: parseInt(this.panel.querySelector("#archi-param-size").value),
      node_icon: this.panel.querySelector("#archi-param-icon").value,
      node_badge: this.panel.querySelector("#archi-param-badge").value,
    };

    this.showStatus("Mise à jour des paramètres...", "info");

    try {
      const response = await this.apiRequest("update-params", {
        method: "POST",
        body: JSON.stringify({
          post_id: this.selectedNode.id,
          params,
        }),
      });

      if (response.success) {
        // Mettre à jour les données locales
        Object.assign(this.selectedNode.advanced_graph_params || {}, params);
        this.selectedNode.color = params.node_color;
        this.selectedNode.size = params.node_size;

        // Ré-afficher le nœud
        this.rerenderNode(this.selectedNode.id);

        this.showStatus("✅ Paramètres mis à jour", "success");
        this.closeParamsEditor();
      }
    } catch (error) {
      console.error("Update params error:", error);
      this.showStatus("❌ Erreur mise à jour", "error");
    }
  }

  /**
   * Ré-afficher un nœud
   */
  rerenderNode(nodeId) {
    if (!this.graph.svg) return;

    // Re-render node with updated shape
    if (this.selectedNode) {
      this.updateNodeVisual(this.selectedNode);
    }
  }

  /**
   * Toggle visibilité du nœud
   */
  async toggleNodeVisibility() {
    if (!this.selectedNode) return;

    const currentVisibility = this.selectedNode.show_in_graph !== "0";
    const newVisibility = !currentVisibility;

    try {
      const response = await this.apiRequest("toggle-visibility", {
        method: "POST",
        body: JSON.stringify({
          post_id: this.selectedNode.id,
          visible: newVisibility,
        }),
      });

      if (response.success) {
        this.selectedNode.show_in_graph = newVisibility ? "1" : "0";
        this.showStatus(
          newVisibility ? "✅ Nœud activé" : "⚪ Nœud désactivé",
          "success"
        );

        // Masquer/afficher le nœud
        if (!newVisibility) {
          this.graph.svg
            .selectAll(".node")
            .filter((d) => d.id === this.selectedNode.id)
            .style("opacity", 0.3);
        } else {
          this.graph.svg
            .selectAll(".node")
            .filter((d) => d.id === this.selectedNode.id)
            .style("opacity", 1);
        }
      }
    } catch (error) {
      console.error("Toggle visibility error:", error);
      this.showStatus("❌ Erreur visibilité", "error");
    }
  }

  /**
   * Ouvrir la bibliothèque média WordPress
   */
  openMediaLibrary() {
    if (!this.selectedNode || typeof wp === "undefined" || !wp.media) {
      console.error("WordPress media library not available");
      return;
    }

    const mediaFrame = wp.media({
      title: "Choisir une image pour le nœud",
      button: {
        text: "Utiliser cette image",
      },
      multiple: false,
    });

    mediaFrame.on("select", () => {
      const attachment = mediaFrame.state().get("selection").first().toJSON();
      this.updateNodeImage(attachment.id);
    });

    mediaFrame.open();
  }

  /**
   * Mettre à jour l'image d'un nœud
   */
  async updateNodeImage(imageId) {
    if (!this.selectedNode) return;

    this.showStatus("Mise à jour de l'image...", "info");

    try {
      const response = await this.apiRequest("update-image", {
        method: "POST",
        body: JSON.stringify({
          post_id: this.selectedNode.id,
          image_id: imageId,
        }),
      });

      if (response.success) {
        // Mettre à jour l'image dans les données
        this.selectedNode.image = response.image_url;

        // Ré-afficher le nœud
        this.rerenderNode(this.selectedNode.id);

        this.showStatus("✅ Image mise à jour", "success");
      }
    } catch (error) {
      console.error("Update image error:", error);
      this.showStatus("❌ Erreur mise à jour image", "error");
    }
  }

  /**
   * Raccourcis clavier
   */
  setupKeyboardShortcuts() {
    document.addEventListener("keydown", (e) => {
      if (!this.enabled) return;

      // Echap : désactiver mode création lien
      if (e.key === "Escape" && this.linkCreationMode) {
        this.toggleLinkCreationMode();
      }

      // Ctrl+S : sauvegarder
      if (e.ctrlKey && e.key === "s") {
        e.preventDefault();
        this.saveAllPositions();
      }

      // Ctrl+E : toggle mode édition
      if (e.ctrlKey && e.key === "e") {
        e.preventDefault();
        const toggle = this.panel.querySelector("#archi-edit-mode-toggle");
        toggle.checked = !toggle.checked;
        this.setEditMode(toggle.checked);
      }
    });
  }

  /**
   * Afficher un message de statut
   */
  showStatus(message, type = "info") {
    const statusEl = this.panel.querySelector(".archi-status-text");
    if (!statusEl) return;

    statusEl.textContent = message;
    statusEl.className = `archi-status-text archi-status-${type}`;

    // Auto-clear après 5s
    setTimeout(() => {
      if (statusEl.textContent === message) {
        statusEl.textContent = "";
      }
    }, 5000);
  }

  /**
   * Requête API
   */
  async apiRequest(endpoint, options = {}) {
    const url = this.apiUrl + endpoint;
    const response = await fetch(url, {
      ...options,
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": this.config.nonce || "",
        ...options.headers,
      },
    });

    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }

    return response.json();
  }

  /**
   * Afficher/masquer le panneau
   */
  toggle() {
    this.panel.classList.toggle("archi-panel-open");
  }

  /**
   * Détruire l'éditeur
   */
  destroy() {
    if (this.panel) {
      this.panel.remove();
    }
    this.enabled = false;
    document.body.classList.remove("archi-edit-mode-active");
    document.body.classList.remove("archi-link-creation-mode");
  }
}

// Export pour utilisation globale
window.GraphEditor = GraphEditor;

// Auto-initialisation si graphique présent
document.addEventListener("DOMContentLoaded", () => {
  // Attendre que le graphique soit initialisé
  const checkGraph = setInterval(() => {
    if (window.archiGraphInstance) {
      clearInterval(checkGraph);

      // Créer l'éditeur
      window.graphEditor = new GraphEditor(window.archiGraphInstance);

      // Bouton d'ouverture du panneau
      if (window.archiGraphEditor?.canEdit) {
        const openBtn = document.createElement("button");
        openBtn.id = "archi-open-editor";
        openBtn.className = "archi-open-editor-btn";
        openBtn.innerHTML = "🎨 Éditer";
        openBtn.setAttribute("aria-label", "Ouvrir l'éditeur de graphique");
        openBtn.addEventListener("click", () => {
          window.graphEditor.toggle();
        });
        document.body.appendChild(openBtn);
      }
    }
  }, 500);

  // Timeout après 10s
  setTimeout(() => clearInterval(checkGraph), 10000);
});
