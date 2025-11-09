/**
 * Featured Image Parallax Effects
 * Gère les effets parallax sur les images à la une
 * 
 * @package Archi_Graph
 * @since 1.1.0
 */

(function() {
    'use strict';

    // Configuration
    const PARALLAX_SPEED = 0.5; // Vitesse du parallax scroll
    const ZOOM_SPEED = 0.0002; // Vitesse du zoom progressif
    const MAX_ZOOM = 1.2; // Zoom maximum

    /**
     * Initialise le parallax scroll
     */
    function initParallaxScroll() {
        const parallaxElements = document.querySelectorAll('.archi-hero-fullscreen.parallax-scroll');
        
        if (parallaxElements.length === 0) return;
        
        // Vérifier la préférence de mouvement réduit
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) return;
        
        // Vérifier si mobile
        const isMobile = window.innerWidth <= 768;
        if (isMobile) return;
        
        let ticking = false;
        
        function updateParallax() {
            parallaxElements.forEach(container => {
                const image = container.querySelector('.hero-media');
                if (!image) return;
                
                const rect = container.getBoundingClientRect();
                const scrolled = window.pageYOffset || document.documentElement.scrollTop;
                
                // Calculer l'offset en fonction de la position dans le viewport
                const offset = scrolled * PARALLAX_SPEED;
                
                // Appliquer la transformation
                image.style.transform = `translateY(${offset}px)`;
            });
            
            ticking = false;
        }
        
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }
        
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Initialiser la position
        updateParallax();
    }

    /**
     * Initialise le parallax fixed avec background
     */
    function initParallaxFixed() {
        const parallaxElements = document.querySelectorAll('.archi-hero-fullscreen.parallax-fixed');
        
        parallaxElements.forEach(container => {
            const image = container.querySelector('.hero-media');
            if (!image) return;
            
            // Récupérer l'URL de l'image
            const imageUrl = image.src;
            
            // Appliquer en background
            container.style.backgroundImage = `url(${imageUrl})`;
            
            // Cacher l'image originale
            image.style.display = 'none';
        });
    }

    /**
     * Initialise le zoom progressif
     */
    function initParallaxZoom() {
        const zoomElements = document.querySelectorAll('.archi-hero-fullscreen.parallax-zoom');
        
        if (zoomElements.length === 0) return;
        
        // Vérifier la préférence de mouvement réduit
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) return;
        
        // Vérifier si mobile
        const isMobile = window.innerWidth <= 768;
        if (isMobile) return;
        
        let ticking = false;
        
        function updateZoom() {
            zoomElements.forEach(container => {
                const image = container.querySelector('.hero-media');
                if (!image) return;
                
                const rect = container.getBoundingClientRect();
                const scrolled = window.pageYOffset || document.documentElement.scrollTop;
                
                // Calculer le zoom en fonction du scroll
                // Plus on scrolle, plus on zoome
                let scale = 1 + (scrolled * ZOOM_SPEED);
                scale = Math.min(scale, MAX_ZOOM); // Limiter au zoom max
                
                // Appliquer la transformation
                image.style.transform = `scale(${scale})`;
            });
            
            ticking = false;
        }
        
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(updateZoom);
                ticking = true;
            }
        }
        
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Initialiser la position
        updateZoom();
    }

    /**
     * Initialisation au chargement de la page
     */
    function init() {
        // Attendre que les images soient chargées
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initAll, 100);
            });
        } else {
            setTimeout(initAll, 100);
        }
    }

    function initAll() {
        initParallaxScroll();
        initParallaxFixed();
        initParallaxZoom();
    }

    // Démarrer
    init();

    // Réinitialiser au redimensionnement de la fenêtre
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(initAll, 250);
    });

})();
