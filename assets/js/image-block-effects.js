/**
 * Image Block Effects
 * JavaScript pour gérer les interactions du bloc image universel
 * 
 * Fonctionnalités:
 * - Lightbox (modal plein écran)
 * - Scroll animations (Intersection Observer)
 * - Effet 3D Tilt au survol
 * 
 * @package Archi_Graph
 * @since 1.2.0
 */

(function() {
    'use strict';

    // ======================================================================
    // Lightbox Modal
    // ======================================================================
    
    function initLightbox() {
        const lightboxBlocks = document.querySelectorAll('.archi-image-block.lightbox-enabled');
        
        if (lightboxBlocks.length === 0) return;
        
        // Créer la modal si elle n'existe pas
        let modal = document.getElementById('archi-lightbox-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'archi-lightbox-modal';
            modal.className = 'archi-lightbox-modal';
            modal.innerHTML = `
                <div class="archi-lightbox-content">
                    <button class="archi-lightbox-close" aria-label="Fermer">&times;</button>
                    <img src="" alt="">
                    <div class="archi-lightbox-caption"></div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Fermer au clic sur le fond noir
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeLightbox();
                }
            });
            
            // Fermer avec le bouton X
            const closeBtn = modal.querySelector('.archi-lightbox-close');
            closeBtn.addEventListener('click', closeLightbox);
            
            // Fermer avec la touche Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeLightbox();
                }
            });
        }
        
        // Ajouter les listeners sur chaque bloc lightbox
        lightboxBlocks.forEach(block => {
            block.style.cursor = 'pointer';
            
            block.addEventListener('click', function(e) {
                // Ne pas ouvrir si on clique sur un lien ou un bouton
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                const img = block.querySelector('.image-block');
                const caption = block.getAttribute('data-lightbox-caption') || '';
                
                if (img) {
                    const imgSrc = img.src || img.style.backgroundImage.replace(/url\(['"]?([^'"]*)['"]?\)/, '$1');
                    openLightbox(imgSrc, caption);
                }
            });
        });
    }
    
    function openLightbox(imageSrc, caption) {
        const modal = document.getElementById('archi-lightbox-modal');
        const modalImg = modal.querySelector('img');
        const modalCaption = modal.querySelector('.archi-lightbox-caption');
        
        modalImg.src = imageSrc;
        modalCaption.textContent = caption;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        const modal = document.getElementById('archi-lightbox-modal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // ======================================================================
    // Scroll Animations (Intersection Observer)
    // ======================================================================
    
    function initScrollAnimations() {
        const animatedBlocks = document.querySelectorAll('.archi-image-block.scroll-animation-enabled');
        
        if (animatedBlocks.length === 0) return;
        
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Ajouter la classe animated quand l'élément entre dans le viewport
                    setTimeout(() => {
                        entry.target.classList.add('animated');
                    }, 50); // Petit délai pour garantir que le CSS est appliqué
                    
                    // Optionnel: ne plus observer après l'animation (one-time)
                    // observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        animatedBlocks.forEach(block => {
            observer.observe(block);
        });
    }
    
    // ======================================================================
    // Effet 3D Tilt au survol
    // ======================================================================
    
    function initTiltEffect() {
        const tiltBlocks = document.querySelectorAll('.archi-image-block.tilt-enabled');
        
        if (tiltBlocks.length === 0) return;
        
        tiltBlocks.forEach(block => {
            const container = block.querySelector('.image-block-container');
            if (!container) return;
            
            const intensity = parseInt(block.style.getPropertyValue('--tilt-intensity')) || 10;
            
            block.addEventListener('mousemove', function(e) {
                const rect = block.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * intensity;
                const rotateY = ((centerX - x) / centerX) * intensity;
                
                container.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });
            
            block.addEventListener('mouseleave', function() {
                container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            });
        });
    }
    
    // ======================================================================
    // Initialisation
    // ======================================================================
    
    function init() {
        initLightbox();
        initScrollAnimations();
        initTiltEffect();
    }
    
    // Lancer au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Re-initialiser après un chargement AJAX (pour les thèmes avec infinite scroll, etc.)
    document.addEventListener('archi-image-block-loaded', init);
    
})();
