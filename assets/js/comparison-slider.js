/**
 * Slider de Comparaison d'Images
 * Script pour le mode comparaison avant/après du bloc Image Universel
 * 
 * @package Archi_Graph
 * @since 1.1.0
 */

(function () {
  "use strict";

  /**
   * Initialise un slider de comparaison
   * @param {string} blockId - ID du bloc container
   */
  window.archiInitComparisonSlider = function (blockId) {
    const container = document.getElementById(blockId);
    if (!container) return;

    const comparisonContainer = container.querySelector(".comparison-container");
    const beforeImage = container.querySelector(".before-image");
    const handle = container.querySelector(".comparison-slider-handle");

    if (!comparisonContainer || !beforeImage || !handle) return;

    const orientation = container.dataset.orientation || "vertical";
    const initialPosition =
      parseInt(container.dataset.initialPosition, 10) || 50;

    let isDragging = false;
    let currentPosition = initialPosition;

    /**
     * Met à jour la position du slider
     * @param {number} position - Position en pourcentage (0-100)
     */
    function updatePosition(position) {
      // Limiter entre 0 et 100
      position = Math.max(0, Math.min(100, position));
      currentPosition = position;

      if (orientation === "vertical") {
        // Gauche/Droite
        handle.style.left = position + "%";
        beforeImage.style.clipPath = `inset(0 ${100 - position}% 0 0)`;
      } else {
        // Haut/Bas
        handle.style.top = position + "%";
        beforeImage.style.clipPath = `inset(0 0 ${100 - position}% 0)`;
      }
    }

    /**
     * Obtient la position depuis un événement
     * @param {Event} e - Événement mouse ou touch
     * @returns {number} Position en pourcentage
     */
    function getPositionFromEvent(e) {
      const rect = comparisonContainer.getBoundingClientRect();
      let clientX, clientY;

      if (e.type.startsWith("touch")) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
      } else {
        clientX = e.clientX;
        clientY = e.clientY;
      }

      if (orientation === "vertical") {
        const x = clientX - rect.left;
        return (x / rect.width) * 100;
      } else {
        const y = clientY - rect.top;
        return (y / rect.height) * 100;
      }
    }

    /**
     * Démarre le glissement
     */
    function onStart(e) {
      isDragging = true;
      comparisonContainer.style.cursor =
        orientation === "vertical" ? "ew-resize" : "ns-resize";

      // Empêcher la sélection de texte
      e.preventDefault();

      // Position initiale
      const position = getPositionFromEvent(e);
      updatePosition(position);
    }

    /**
     * Glissement en cours
     */
    function onMove(e) {
      if (!isDragging) return;

      const position = getPositionFromEvent(e);
      updatePosition(position);
    }

    /**
     * Fin du glissement
     */
    function onEnd() {
      isDragging = false;
      comparisonContainer.style.cursor =
        orientation === "vertical" ? "ew-resize" : "ns-resize";
    }

    // Événements souris
    handle.addEventListener("mousedown", onStart);
    comparisonContainer.addEventListener("mousedown", onStart);
    document.addEventListener("mousemove", onMove);
    document.addEventListener("mouseup", onEnd);

    // Événements tactiles
    handle.addEventListener("touchstart", onStart, { passive: false });
    comparisonContainer.addEventListener("touchstart", onStart, {
      passive: false,
    });
    document.addEventListener("touchmove", onMove, { passive: false });
    document.addEventListener("touchend", onEnd);

    // Initialiser la position
    updatePosition(initialPosition);

    // Cleanup au unload (optionnel mais bonne pratique)
    window.addEventListener("beforeunload", () => {
      document.removeEventListener("mousemove", onMove);
      document.removeEventListener("mouseup", onEnd);
      document.removeEventListener("touchmove", onMove);
      document.removeEventListener("touchend", onEnd);
    });
  };

  /**
   * Auto-initialisation au chargement de la page
   */
  document.addEventListener("DOMContentLoaded", function () {
    // Legacy blocks
    const comparisonBlocks = document.querySelectorAll(
      '.archi-unified-image[data-mode="comparison"]'
    );

    comparisonBlocks.forEach((block) => {
      const blockId = block.getAttribute("id");
      if (blockId) {
        archiInitComparisonSlider(blockId);
      }
    });

    // New comparison slider blocks
    initNewComparisonSliders();
  });

  /**
   * Initialize new comparison slider blocks
   */
  function initNewComparisonSliders() {
    const sliders = document.querySelectorAll('.archi-image-comparison');
    
    sliders.forEach(slider => {
      const comparisonId = slider.getAttribute('data-comparison-id');
      const initialPosition = parseInt(slider.getAttribute('data-initial-position') || '50', 10);
      const orientation = slider.getAttribute('data-orientation') || 'vertical';
      const handleColor = slider.getAttribute('data-handle-color') || '#ffffff';

      initComparisonSlider(slider, {
        initialPosition,
        orientation,
        handleColor
      });
    });
  }

  /**
   * Initialize a comparison slider with modern approach
   */
  function initComparisonSlider(container, options) {
    const beforeImage = container.querySelector('.archi-comparison-before');
    const afterImage = container.querySelector('.archi-comparison-after');
    const handle = container.querySelector('.archi-comparison-handle');

    if (!beforeImage || !afterImage || !handle) return;

    const {
      initialPosition = 50,
      orientation = 'vertical',
      handleColor = '#ffffff'
    } = options;

    let isDragging = false;
    let currentPosition = initialPosition;

    /**
     * Update slider position
     */
    function updatePosition(position) {
      position = Math.max(0, Math.min(100, position));
      currentPosition = position;

      if (orientation === 'vertical') {
        handle.style.left = position + '%';
        afterImage.style.clipPath = `inset(0 0 0 ${position}%)`;
      } else {
        handle.style.top = position + '%';
        afterImage.style.clipPath = `inset(${position}% 0 0 0)`;
      }
    }

    /**
     * Get position from event
     */
    function getPositionFromEvent(e) {
      const rect = container.getBoundingClientRect();
      let clientX, clientY;

      if (e.type.startsWith('touch')) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
      } else {
        clientX = e.clientX;
        clientY = e.clientY;
      }

      if (orientation === 'vertical') {
        const x = clientX - rect.left;
        return (x / rect.width) * 100;
      } else {
        const y = clientY - rect.top;
        return (y / rect.height) * 100;
      }
    }

    /**
     * Start dragging
     */
    function onStart(e) {
      isDragging = true;
      container.classList.add('is-dragging');
      e.preventDefault();

      const position = getPositionFromEvent(e);
      updatePosition(position);
    }

    /**
     * During drag
     */
    function onMove(e) {
      if (!isDragging) return;
      const position = getPositionFromEvent(e);
      updatePosition(position);
    }

    /**
     * End dragging
     */
    function onEnd() {
      isDragging = false;
      container.classList.remove('is-dragging');
    }

    // Mouse events
    handle.addEventListener('mousedown', onStart);
    container.addEventListener('mousedown', onStart);
    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onEnd);

    // Touch events
    handle.addEventListener('touchstart', onStart, { passive: false });
    container.addEventListener('touchstart', onStart, { passive: false });
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);

    // Initialize position
    updatePosition(initialPosition);

    // Cleanup
    window.addEventListener('beforeunload', () => {
      document.removeEventListener('mousemove', onMove);
      document.removeEventListener('mouseup', onEnd);
      document.removeEventListener('touchmove', onMove);
      document.removeEventListener('touchend', onEnd);
    });
  }
})();
