/**
 * Parallax Fixed Effect Handler
 * Gère l'effet parallax pour les images en mode parallax-fixed
 */

(function() {
    'use strict';
    
    function initParallaxFixed(blockId) {
        var container = document.getElementById(blockId);
        if (!container) return;
        
        var imageBlock = container.querySelector('.image-block.parallax-fixed');
        if (!imageBlock) return;
        
        // Force scroll mode
        imageBlock.style.backgroundAttachment = 'scroll';
        var ticking = false;
        
        function updateParallax() {
            var scrolled = window.pageYOffset || window.scrollY;
            var rect = container.getBoundingClientRect();
            var containerTop = rect.top + scrolled;
            var windowHeight = window.innerHeight;
            
            if (rect.top < windowHeight && rect.bottom > 0) {
                var speed = parseFloat(imageBlock.dataset.parallaxSpeed) || 0.5;
                var yPos = -(scrolled - containerTop) * speed;
                imageBlock.style.backgroundPosition = 'center calc(50% + ' + yPos + 'px)';
            }
            ticking = false;
        }
        
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }
        
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', updateParallax);
        
        // Initial update
        setTimeout(updateParallax, 100);
    }
    
    // Export to global scope
    window.archiInitParallaxFixed = initParallaxFixed;
    
    // Auto-init on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            var blocks = document.querySelectorAll('.archi-image-block.display-mode-parallax-fixed');
            blocks.forEach(function(block) {
                initParallaxFixed(block.id);
            });
        });
    } else {
        var blocks = document.querySelectorAll('.archi-image-block.display-mode-parallax-fixed');
        blocks.forEach(function(block) {
            initParallaxFixed(block.id);
        });
    }
})();
