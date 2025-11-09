/**
 * Hero Fullscreen avec Scroll Indicator
 * Gestion du smooth scroll et de l'animation de l'indicateur
 */

(function() {
    'use strict';

    // Attendre le chargement du DOM
    document.addEventListener('DOMContentLoaded', function() {
        initHeroScroll();
    });

    /**
     * Initialise le comportement de scroll du hero
     */
    function initHeroScroll() {
        const scrollIndicator = document.querySelector('.archi-scroll-indicator');
        const contentSection = document.querySelector('.archi-content-section');
        
        if (!scrollIndicator || !contentSection) {
            return;
        }

        // Click sur l'indicateur pour scroller vers le contenu
        scrollIndicator.addEventListener('click', function(e) {
            e.preventDefault();
            smoothScrollTo(contentSection);
        });

        // Masquer l'indicateur après le scroll
        window.addEventListener('scroll', function() {
            handleScrollIndicatorVisibility(scrollIndicator);
        }, { passive: true });

        // Initialiser la visibilité
        handleScrollIndicatorVisibility(scrollIndicator);
        
        // Ajouter l'effet magnétique
        initMagneticEffect(scrollIndicator);
    }

    /**
     * Scroll smooth vers un élément
     * @param {HTMLElement} element - L'élément cible
     */
    function smoothScrollTo(element) {
        if (!element) return;

        const targetPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        const duration = 1000; // 1 seconde
        let start = null;

        // Animation avec easing
        function animation(currentTime) {
            if (start === null) start = currentTime;
            const timeElapsed = currentTime - start;
            const progress = Math.min(timeElapsed / duration, 1);
            
            // Easing function (easeInOutCubic)
            const ease = progress < 0.5 
                ? 4 * progress * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;
            
            window.scrollTo(0, startPosition + distance * ease);
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        requestAnimationFrame(animation);
    }

    /**
     * Gère la visibilité de l'indicateur de scroll
     * @param {HTMLElement} indicator - L'élément indicateur
     */
    function handleScrollIndicatorVisibility(indicator) {
        const scrollThreshold = window.innerHeight * 0.2; // 20% de la hauteur de l'écran
        
        if (window.pageYOffset > scrollThreshold) {
            indicator.classList.add('hidden');
        } else {
            indicator.classList.remove('hidden');
        }
    }

    /**
     * Support pour le scroll avec la molette
     * Détecte un scroll down initial pour déclencher l'animation
     */
    let isFirstScroll = true;
    let scrollTimeout;

    window.addEventListener('wheel', function(e) {
        if (!isFirstScroll) return;
        
        // Si on scroll vers le bas
        if (e.deltaY > 0) {
            const contentSection = document.querySelector('.archi-content-section');
            if (contentSection && window.pageYOffset < 100) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    smoothScrollTo(contentSection);
                    isFirstScroll = false;
                }, 100);
            }
        }
    }, { passive: true });

    /**
     * Support pour le scroll tactile (mobile)
     */
    let touchStartY = 0;
    let touchEndY = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    document.addEventListener('touchmove', function(e) {
        touchEndY = e.touches[0].clientY;
    }, { passive: true });

    document.addEventListener('touchend', function() {
        if (!isFirstScroll) return;
        
        // Si on swipe vers le haut (scroll down)
        const swipeDistance = touchStartY - touchEndY;
        if (swipeDistance > 50 && window.pageYOffset < 100) {
            const contentSection = document.querySelector('.archi-content-section');
            if (contentSection) {
                smoothScrollTo(contentSection);
                isFirstScroll = false;
            }
        }
    }, { passive: true });

    /**
     * Ajuster la hauteur du hero sur les mobiles pour éviter les problèmes avec la barre d'adresse
     */
    function adjustHeroHeight() {
        const hero = document.querySelector('.archi-hero-fullscreen');
        if (!hero) return;
        
        // Utiliser la vraie hauteur du viewport sur mobile
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    // Ajuster au chargement et au redimensionnement
    adjustHeroHeight();
    window.addEventListener('resize', adjustHeroHeight);
    window.addEventListener('orientationchange', adjustHeroHeight);

    /**
     * Lazy loading pour les vidéos de fond
     */
    function initVideoLazyLoad() {
        const videos = document.querySelectorAll('.archi-hero-fullscreen video[data-src]');
        
        videos.forEach(function(video) {
            if ('IntersectionObserver' in window) {
                const videoObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const videoElement = entry.target;
                            videoElement.src = videoElement.dataset.src;
                            videoElement.load();
                            videoObserver.unobserve(videoElement);
                        }
                    });
                });
                
                videoObserver.observe(video);
            } else {
                // Fallback pour les navigateurs sans IntersectionObserver
                video.src = video.dataset.src;
                video.load();
            }
        });
    }

    initVideoLazyLoad();

    /**
     * Parallax subtle sur le hero (optionnel)
     */
    function initParallax() {
        const hero = document.querySelector('.archi-hero-fullscreen');
        const heroMedia = hero ? hero.querySelector('.hero-media') : null;
        
        if (!heroMedia) return;

        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroHeight = hero.offsetHeight;
            
            // Appliquer le parallax seulement quand le hero est visible
            if (scrolled < heroHeight) {
                const parallaxSpeed = 0.5;
                const yPos = scrolled * parallaxSpeed;
                heroMedia.style.transform = `translate3d(0, ${yPos}px, 0)`;
            }
        }, { passive: true });
    }

    // Activer le parallax (commenté par défaut, décommenter pour activer)
    // initParallax();

    /**
     * Effet magnétique sur le scroll indicator
     * L'indicateur suit subtilement le curseur quand il est à proximité
     */
    function initMagneticEffect(element) {
        if (!element) return;
        
        const strength = 0.3; // Force de l'attraction (0-1)
        const distance = 100; // Distance d'activation en pixels
        
        let animationFrame;
        let currentX = 0;
        let currentY = 0;
        let targetX = 0;
        let targetY = 0;
        
        // Fonction d'animation smooth
        function animate() {
            // Interpolation smooth (lerp)
            currentX += (targetX - currentX) * 0.15;
            currentY += (targetY - currentY) * 0.15;
            
            // Appliquer la transformation
            element.style.transform = `translate(calc(-50% + ${currentX}px), ${currentY}px)`;
            
            // Continuer l'animation
            animationFrame = requestAnimationFrame(animate);
        }
        
        // Démarrer l'animation
        animate();
        
        // Gérer le mouvement de la souris
        document.addEventListener('mousemove', function(e) {
            // Obtenir la position de l'élément
            const rect = element.getBoundingClientRect();
            const elementCenterX = rect.left + rect.width / 2;
            const elementCenterY = rect.top + rect.height / 2;
            
            // Calculer la distance entre la souris et l'élément
            const deltaX = e.clientX - elementCenterX;
            const deltaY = e.clientY - elementCenterY;
            const dist = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
            
            // Si la souris est à proximité
            if (dist < distance) {
                // Calculer le facteur d'attraction (plus proche = plus fort)
                const factor = (1 - dist / distance) * strength;
                
                // Définir la position cible
                targetX = deltaX * factor;
                targetY = deltaY * factor;
            } else {
                // Revenir à la position normale
                targetX = 0;
                targetY = 0;
            }
        });
        
        // Réinitialiser quand la souris quitte la fenêtre
        document.addEventListener('mouseleave', function() {
            targetX = 0;
            targetY = 0;
        });
        
        // Ajouter une classe pour identifier l'effet actif
        element.classList.add('magnetic-active');
    }

})();
