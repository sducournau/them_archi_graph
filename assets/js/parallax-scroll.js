/**
 * Script Parallax Scroll
 * Gère l'effet de parallax scroll (transform) pour les images
 * Optimisé avec requestAnimationFrame et IntersectionObserver
 */

(function () {
  "use strict";

  // Configuration
  const PARALLAX_CONFIG = {
    throttleDelay: 10, // ms entre chaque update
    rootMargin: "200px", // Charger avant que l'élément soit visible
  };

  // État
  let ticking = false;
  let parallaxElements = [];

  /**
   * Initialisation au chargement de la page
   */
  function init() {
    // Trouver tous les éléments avec effet parallax scroll (nouveau et anciens blocs)
    const elements = document.querySelectorAll(
      '.archi-parallax-image[data-parallax-effect="scroll"], ' +
      '.archi-fullsize-parallax-image.parallax-scroll, ' +
      '.archi-fixed-background.has-parallax-effect, ' +
      '.wp-block-cover.has-parallax .wp-block-cover__image-background'
    );

    if (elements.length === 0) {
      return;
    }

    // Vérifier si le mouvement réduit est préféré
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      return;
    }

    // Initialiser chaque élément
    elements.forEach((element) => {
      initParallaxElement(element);
    });

    // Observer les éléments pour lazy loading
    if ("IntersectionObserver" in window) {
      observeParallaxElements();
    } else {
      // Fallback pour navigateurs anciens
      parallaxElements.forEach((item) => {
        item.isVisible = true;
      });
      window.addEventListener("scroll", onScroll, { passive: true });
    }
  }

  /**
   * Initialiser un élément parallax
   */
  function initParallaxElement(element) {
    // For cover blocks, the element itself is the image
    const image = element.querySelector(".parallax-image") || 
                  element.querySelector("img") ||
                  (element.tagName === 'IMG' ? element : null);
    if (!image) return;

    const speed = parseFloat(element.getAttribute("data-parallax-speed")) || 0.5;

    parallaxElements.push({
      element: element,
      image: image,
      speed: speed,
      isVisible: false,
      bounds: null,
    });
  }

  /**
   * Observer les éléments avec IntersectionObserver
   */
  function observeParallaxElements() {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const item = parallaxElements.find((item) => item.element === entry.target);
          if (item) {
            item.isVisible = entry.isIntersecting;

            if (entry.isIntersecting) {
              // Calculer les bounds quand l'élément devient visible
              item.bounds = entry.target.getBoundingClientRect();
            }
          }
        });

        // Activer/désactiver le scroll listener
        const hasVisibleElements = parallaxElements.some((item) => item.isVisible);
        if (hasVisibleElements) {
          window.addEventListener("scroll", onScroll, { passive: true });
        } else {
          window.removeEventListener("scroll", onScroll);
        }
      },
      {
        rootMargin: PARALLAX_CONFIG.rootMargin,
        threshold: [0, 0.1, 0.5, 1],
      }
    );

    parallaxElements.forEach((item) => {
      observer.observe(item.element);
    });
  }

  /**
   * Gestionnaire de scroll
   */
  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(updateParallax);
      ticking = true;
    }
  }

  /**
   * Mettre à jour les positions parallax
   */
  function updateParallax() {
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;

    parallaxElements.forEach((item) => {
      if (!item.isVisible) return;

      // Recalculer bounds pour plus de précision
      const bounds = item.element.getBoundingClientRect();
      const elementTop = bounds.top + scrollY;
      const elementHeight = bounds.height;

      // Position de l'élément dans le viewport
      const elementCenter = elementTop + elementHeight / 2;
      const viewportCenter = scrollY + windowHeight / 2;

      // Distance du centre du viewport
      const distance = elementCenter - viewportCenter;

      // Calculer le déplacement avec la vitesse
      // Speed: 0 = très lent, 1 = rapide
      const movement = distance * item.speed * -0.5;

      // Appliquer le transform
      // Scale de base à 1.2 pour éviter les bords blancs
      const scale = 1.2;
      item.image.style.transform = `translateY(${movement}px) scale(${scale})`;
    });

    ticking = false;
  }

  /**
   * Gérer le redimensionnement de la fenêtre
   */
  function onResize() {
    // Recalculer les bounds de tous les éléments
    parallaxElements.forEach((item) => {
      if (item.element) {
        item.bounds = item.element.getBoundingClientRect();
      }
    });

    // Mettre à jour immédiatement
    updateParallax();
  }

  // Debounce pour le resize
  let resizeTimeout;
  function debouncedResize() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(onResize, 200);
  }

  /**
   * Gérer l'effet fixed background
   */
  function initFixedBackground() {
    // Nouveau bloc et anciens blocs
    const fixedElements = document.querySelectorAll(
      '.archi-parallax-image[data-parallax-effect="fixed"], ' +
      '.archi-fixed-background.has-parallax-effect, ' +
      '.parallax-fixed'
    );

    fixedElements.forEach((element) => {
      const image = element.querySelector(".parallax-image, img");
      if (!image) return;

      const imageUrl = image.getAttribute("src");
      if (!imageUrl) return;

      // Appliquer le background-image au container ou à l'élément lui-même
      const container = element.querySelector(".fullsize-image-container") || element;
      if (container) {
        container.style.backgroundImage = `url(${imageUrl})`;
        container.style.backgroundSize = "cover";
        container.style.backgroundPosition = "center";
        container.style.backgroundAttachment = "fixed";

        // Cacher l'image wrapper si présent
        const wrapper = element.querySelector(".image-wrapper");
        if (wrapper) {
          wrapper.style.display = "none";
        }
      }
    });
  }

  /**
   * Nettoyage
   */
  function destroy() {
    window.removeEventListener("scroll", onScroll);
    window.removeEventListener("resize", debouncedResize);
    parallaxElements = [];
  }

  // Initialiser au chargement du DOM
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Initialiser les fixed backgrounds
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initFixedBackground);
  } else {
    initFixedBackground();
  }

  // Gérer le resize
  window.addEventListener("resize", debouncedResize, { passive: true });

  // Exposer pour debug
  window.archiParallax = {
    elements: parallaxElements,
    update: updateParallax,
    destroy: destroy,
  };
})();
