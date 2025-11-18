/**
 * Graph Ambient Particles Generator
 * Creates subtle animated particles in the graph background
 * Respects accessibility preferences
 */

(function() {
  'use strict';
  
  // Check if user prefers reduced motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  
  if (prefersReducedMotion) {
    console.log('⚡ Ambient particles disabled due to reduced motion preference');
    return;
  }
  
  // Configuration
  const config = {
    particleCount: window.innerWidth < 768 ? 15 : 30, // Fewer on mobile
    glowCount: 2,
    updateInterval: 100 // Update positions every 100ms for performance
  };
  
  /**
   * Create ambient particles in the graph container
   */
  function createAmbientParticles() {
    const graphContainer = document.querySelector('#graph-container, .graph-container');
    
    if (!graphContainer) {
      console.warn('⚠️ Graph container not found, particles not created');
      return;
    }
    
    // Check if particles already exist
    if (graphContainer.querySelector('.graph-ambient-particles')) {
      return;
    }
    
    // Create particles container
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'graph-ambient-particles';
    particlesContainer.setAttribute('aria-hidden', 'true');
    
    // Generate particles with random positions and delays
    for (let i = 0; i < config.particleCount; i++) {
      const particle = document.createElement('div');
      particle.className = 'ambient-particle';
      
      // Random horizontal position
      particle.style.left = `${Math.random() * 100}%`;
      
      // Random animation delay for staggered effect
      particle.style.animationDelay = `${Math.random() * 15}s`;
      
      // Random animation duration variation
      const baseDuration = 15 + Math.random() * 10;
      particle.style.animationDuration = `${baseDuration}s`;
      
      particlesContainer.appendChild(particle);
    }
    
    // Create ambient glow effects
    for (let i = 0; i < config.glowCount; i++) {
      const glow = document.createElement('div');
      glow.className = 'graph-ambient-glow';
      
      // Random initial position
      glow.style.left = `${Math.random() * 80}%`;
      glow.style.top = `${Math.random() * 80}%`;
      
      // Random animation delay
      glow.style.animationDelay = `${i * 4}s`;
      
      particlesContainer.appendChild(glow);
    }
    
    // Insert at the beginning of the container (behind the graph)
    graphContainer.insertBefore(particlesContainer, graphContainer.firstChild);
    
    console.log('✨ Ambient particles created:', config.particleCount, 'particles +', config.glowCount, 'glows');
  }
  
  /**
   * Update particle positions dynamically (optional enhancement)
   */
  function updateParticlePositions() {
    const glows = document.querySelectorAll('.graph-ambient-glow');
    
    glows.forEach((glow, index) => {
      // Slowly move glows to create dynamic ambiance
      const time = Date.now() / 1000;
      const x = 50 + Math.sin(time * 0.1 + index) * 30;
      const y = 50 + Math.cos(time * 0.15 + index) * 30;
      
      glow.style.left = `${x}%`;
      glow.style.top = `${y}%`;
    });
  }
  
  /**
   * Initialize particles when graph is ready
   */
  function init() {
    // Wait for graph container to be available
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        setTimeout(createAmbientParticles, 500);
      });
    } else {
      setTimeout(createAmbientParticles, 500);
    }
    
    // Optional: Update glow positions periodically for dynamic effect
    // Disabled by default for performance, enable if desired
    // setInterval(updateParticlePositions, config.updateInterval);
  }
  
  // Initialize
  init();
  
  // Re-create particles on window resize (debounced)
  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      const container = document.querySelector('.graph-ambient-particles');
      if (container) {
        container.remove();
        createAmbientParticles();
      }
    }, 500);
  });
  
  // Expose cleanup function
  window.archiGraphParticles = {
    destroy: function() {
      const container = document.querySelector('.graph-ambient-particles');
      if (container) {
        container.remove();
      }
    },
    recreate: function() {
      this.destroy();
      createAmbientParticles();
    }
  };
  
})();
