/**
 * Slider de Comparaison d'Images
 */
(function () {
  "use strict";

  function initComparisonSlider(container) {
    if (!container || container.dataset.initialized === 'true') return;
    container.dataset.initialized = 'true';

    const beforeImage = container.querySelector(".before-image");
    const handle = container.querySelector(".comparison-slider-handle");

    if (!beforeImage || !handle) return;

    const orientation = container.dataset.orientation || "vertical";
    const initialPosition = parseInt(container.dataset.initialPosition, 10) || 50;
    let isDragging = false;

    function updatePosition(position) {
      position = Math.max(0, Math.min(100, position));
      if (orientation === "vertical") {
        handle.style.left = position + "%";
        beforeImage.style.clipPath = "inset(0 " + (100 - position) + "% 0 0)";
      } else {
        handle.style.top = position + "%";
        beforeImage.style.clipPath = "inset(0 0 " + (100 - position) + "% 0)";
      }
    }

    function getPositionFromEvent(e) {
      const rect = container.getBoundingClientRect();
      let clientX, clientY;
      if (e.type.startsWith("touch")) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
      } else {
        clientX = e.clientX;
        clientY = e.clientY;
      }
      if (orientation === "vertical") {
        return ((clientX - rect.left) / rect.width) * 100;
      } else {
        return ((clientY - rect.top) / rect.height) * 100;
      }
    }

    function onStart(e) {
      isDragging = true;
      e.preventDefault();
      updatePosition(getPositionFromEvent(e));
    }

    function onMove(e) {
      if (!isDragging) return;
      e.preventDefault();
      updatePosition(getPositionFromEvent(e));
    }

    function onEnd() {
      isDragging = false;
    }

    handle.addEventListener("mousedown", onStart);
    container.addEventListener("mousedown", onStart);
    document.addEventListener("mousemove", onMove);
    document.addEventListener("mouseup", onEnd);
    handle.addEventListener("touchstart", onStart, { passive: false });
    container.addEventListener("touchstart", onStart, { passive: false });
    document.addEventListener("touchmove", onMove, { passive: false });
    document.addEventListener("touchend", onEnd);

    updatePosition(initialPosition);
  }

  function initAll() {
    const containers = document.querySelectorAll('.comparison-container');
    containers.forEach(initComparisonSlider);
  }

  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  window.archiInitComparisonSlider = initComparisonSlider;
})();
