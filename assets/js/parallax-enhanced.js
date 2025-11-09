/**
 * Enhanced Parallax Scroll with GPU Acceleration
 * 
 * Features:
 * - GPU acceleration with transform3d
 * - IntersectionObserver for performance
 * - Mobile detection and optimization
 * - Smooth scroll handling
 * - Reduced motion support
 * - Debounced resize handler
 * 
 * @package ArchiGraph
 * @since 1.0.0
 */

(function () {
  "use strict";

  // ============================================================================
  // Configuration
  // ============================================================================

  const CONFIG = {
    // Performance
    throttleDelay: 16, // ~60fps
    rootMargin: "100px 0px", // Load slightly before visible
    
    // Mobile detection
    mobileBreakpoint: 768,
    disableOnMobile: true,
    
    // GPU acceleration
    useGPU: true,
    willChange: true,
    
    // Smooth scroll
    smoothness: 0.1, // Lower = smoother (0.05-0.2)
  };

  // ============================================================================
  // State Management
  // ============================================================================

  let state = {
    parallaxElements: [],
    rafId: null,
    scrollY: window.pageYOffset,
    targetScrollY: window.pageYOffset,
    isMobile: window.innerWidth <= CONFIG.mobileBreakpoint,
    isReducedMotion: false,
    isInitialized: false,
  };

  // ============================================================================
  // Utility Functions
  // ============================================================================

  /**
   * Check if device prefers reduced motion
   */
  function checkReducedMotion() {
    const mediaQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
    state.isReducedMotion = mediaQuery.matches;
    
    // Listen for changes
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener("change", (e) => {
        state.isReducedMotion = e.matches;
        if (e.matches) {
          disableParallax();
        } else {
          enableParallax();
        }
      });
    }
  }

  /**
   * Check if device is mobile
   */
  function checkMobile() {
    state.isMobile = window.innerWidth <= CONFIG.mobileBreakpoint;
    return state.isMobile;
  }

  /**
   * Debounce function for resize handler
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  /**
   * Linear interpolation for smooth scrolling
   */
  function lerp(start, end, factor) {
    return start + (end - start) * factor;
  }

  // ============================================================================
  // Parallax Element Management
  // ============================================================================

  /**
   * Initialize a parallax element
   */
  function initParallaxElement(element) {
    // Find the image element
    const image = element.querySelector(".parallax-image") || 
                  element.querySelector("img") ||
                  element;

    // Get parallax settings
    const speed = parseFloat(element.dataset.parallaxSpeed || "0.5");
    const direction = element.dataset.parallaxDirection || "vertical";
    const disableOnMobile = element.dataset.parallaxDisableMobile !== "false";

    // Calculate initial bounds
    const bounds = element.getBoundingClientRect();

    // Create parallax item
    const item = {
      element: element,
      image: image,
      speed: speed,
      direction: direction,
      disableOnMobile: disableOnMobile,
      isVisible: false,
      bounds: {
        top: bounds.top + window.pageYOffset,
        bottom: bounds.bottom + window.pageYOffset,
        height: bounds.height,
      },
      currentOffset: 0,
      targetOffset: 0,
    };

    // Apply GPU optimization
    if (CONFIG.useGPU) {
      image.style.transform = "translate3d(0, 0, 0)";
      image.style.backfaceVisibility = "hidden";
      image.style.perspective = "1000px";
      
      if (CONFIG.willChange) {
        image.style.willChange = "transform";
      }
    }

    state.parallaxElements.push(item);
    return item;
  }

  /**
   * Setup IntersectionObserver for lazy parallax
   */
  function setupIntersectionObserver() {
    if (!("IntersectionObserver" in window)) {
      // Fallback: all elements always visible
      state.parallaxElements.forEach((item) => {
        item.isVisible = true;
      });
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const item = state.parallaxElements.find(
            (item) => item.element === entry.target
          );

          if (item) {
            item.isVisible = entry.isIntersecting;

            if (entry.isIntersecting) {
              // Update bounds when element becomes visible
              const bounds = entry.target.getBoundingClientRect();
              item.bounds = {
                top: bounds.top + window.pageYOffset,
                bottom: bounds.bottom + window.pageYOffset,
                height: bounds.height,
              };
            }
          }
        });

        // Start/stop animation loop based on visibility
        updateAnimationLoop();
      },
      {
        root: null,
        rootMargin: CONFIG.rootMargin,
        threshold: 0,
      }
    );

    // Observe all elements
    state.parallaxElements.forEach((item) => {
      observer.observe(item.element);
    });
  }

  // ============================================================================
  // Parallax Calculation & Animation
  // ============================================================================

  /**
   * Calculate parallax offset for an element
   */
  function calculateParallaxOffset(item) {
    const viewportHeight = window.innerHeight;
    const scrollY = state.scrollY;

    // Element position relative to viewport
    const elementTop = item.bounds.top - scrollY;
    const elementBottom = elementTop + item.bounds.height;

    // Skip if element is completely outside viewport
    if (elementBottom < 0 || elementTop > viewportHeight) {
      return item.currentOffset;
    }

    // Calculate progress through viewport (0 = top, 1 = bottom)
    const progress = (viewportHeight - elementTop) / (viewportHeight + item.bounds.height);

    // Calculate offset based on speed and progress
    const maxOffset = item.bounds.height * item.speed;
    const offset = (progress - 0.5) * maxOffset * 2;

    return offset;
  }

  /**
   * Apply transform to parallax image with GPU acceleration
   */
  function applyParallaxTransform(item, offset) {
    if (!item.image) return;

    const transform =
      item.direction === "horizontal"
        ? `translate3d(${offset}px, 0, 0)`
        : `translate3d(0, ${offset}px, 0)`;

    item.image.style.transform = transform;
  }

  /**
   * Update all visible parallax elements
   */
  function updateParallax() {
    // Smooth scroll interpolation
    state.scrollY = lerp(state.scrollY, state.targetScrollY, CONFIG.smoothness);

    // Update each visible element
    state.parallaxElements.forEach((item) => {
      if (!item.isVisible) return;

      // Skip on mobile if disabled
      if (state.isMobile && item.disableOnMobile && CONFIG.disableOnMobile) {
        return;
      }

      // Calculate target offset
      item.targetOffset = calculateParallaxOffset(item);

      // Smooth interpolation
      item.currentOffset = lerp(
        item.currentOffset,
        item.targetOffset,
        CONFIG.smoothness
      );

      // Apply transform
      applyParallaxTransform(item, item.currentOffset);
    });
  }

  /**
   * Animation loop using requestAnimationFrame
   */
  function animationLoop() {
    updateParallax();

    // Continue loop if elements are visible
    const hasVisibleElements = state.parallaxElements.some(
      (item) => item.isVisible && (!state.isMobile || !item.disableOnMobile)
    );

    if (hasVisibleElements && !state.isReducedMotion) {
      state.rafId = requestAnimationFrame(animationLoop);
    } else {
      state.rafId = null;
    }
  }

  /**
   * Start or stop animation loop based on visibility
   */
  function updateAnimationLoop() {
    const hasVisibleElements = state.parallaxElements.some(
      (item) => item.isVisible && (!state.isMobile || !item.disableOnMobile)
    );

    if (hasVisibleElements && !state.isReducedMotion && !state.rafId) {
      state.rafId = requestAnimationFrame(animationLoop);
    } else if (!hasVisibleElements && state.rafId) {
      cancelAnimationFrame(state.rafId);
      state.rafId = null;
    }
  }

  // ============================================================================
  // Event Handlers
  // ============================================================================

  /**
   * Handle scroll events
   */
  function handleScroll() {
    state.targetScrollY = window.pageYOffset;
    updateAnimationLoop();
  }

  /**
   * Handle resize events
   */
  const handleResize = debounce(() => {
    const wasMobile = state.isMobile;
    checkMobile();

    // Recalculate bounds for all elements
    state.parallaxElements.forEach((item) => {
      const bounds = item.element.getBoundingClientRect();
      item.bounds = {
        top: bounds.top + window.pageYOffset,
        bottom: bounds.bottom + window.pageYOffset,
        height: bounds.height,
      };
    });

    // Restart animation if mobile state changed
    if (wasMobile !== state.isMobile) {
      updateAnimationLoop();
    }
  }, 250);

  // ============================================================================
  // Enable/Disable Parallax
  // ============================================================================

  /**
   * Disable all parallax effects
   */
  function disableParallax() {
    if (state.rafId) {
      cancelAnimationFrame(state.rafId);
      state.rafId = null;
    }

    // Reset transforms
    state.parallaxElements.forEach((item) => {
      if (item.image) {
        item.image.style.transform = "translate3d(0, 0, 0)";
      }
    });
  }

  /**
   * Enable parallax effects
   */
  function enableParallax() {
    if (!state.isReducedMotion) {
      updateAnimationLoop();
    }
  }

  // ============================================================================
  // Initialization
  // ============================================================================

  /**
   * Initialize enhanced parallax system
   */
  function init() {
    // Check preferences
    checkReducedMotion();
    checkMobile();

    // Skip if reduced motion preferred
    if (state.isReducedMotion) {
      return;
    }

    // Find all parallax elements
    const selectors = [
      '.archi-parallax-image[data-parallax-effect="scroll"]',
      ".archi-fullsize-parallax-image.parallax-scroll",
      ".parallax-scroll",
      "[data-parallax]",
      ".wp-block-cover.has-parallax .wp-block-cover__image-background", // WordPress cover blocks
    ];

    const elements = document.querySelectorAll(selectors.join(", "));

    if (elements.length === 0) {
      return;
    }

    // Initialize each element
    elements.forEach((element) => {
      initParallaxElement(element);
    });

    // Setup intersection observer
    setupIntersectionObserver();

    // Add event listeners
    window.addEventListener("scroll", handleScroll, { passive: true });
    window.addEventListener("resize", handleResize);

    // Initial update
    state.targetScrollY = window.pageYOffset;
    state.scrollY = window.pageYOffset;
    updateParallax();

    state.isInitialized = true;
  }

  /**
   * Cleanup on page unload
   */
  function cleanup() {
    if (state.rafId) {
      cancelAnimationFrame(state.rafId);
    }
    window.removeEventListener("scroll", handleScroll);
    window.removeEventListener("resize", handleResize);
  }

  // ============================================================================
  // Export & Auto-Initialize
  // ============================================================================

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Cleanup on unload
  window.addEventListener("beforeunload", cleanup);

  // Export for external access
  window.ArchiEnhancedParallax = {
    init,
    disable: disableParallax,
    enable: enableParallax,
    state,
    CONFIG,
  };
})();
