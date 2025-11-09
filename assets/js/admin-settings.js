/**
 * JavaScript pour l'interface d'administration des paramètres
 * Archi Graph Theme
 */

(function($) {
    'use strict';
    
    const ArchiAdmin = {
        
        /**
         * Initialisation
         */
        init: function() {
            this.bindEvents();
            this.initRangeSliders();
            this.initTooltips();
        },
        
        /**
         * Bind des événements
         */
        bindEvents: function() {
            // Clear cache
            $('#archi-clear-cache').on('click', this.clearCache.bind(this));
            
            // Recalculate relations
            $('#archi-recalculate-relations').on('click', this.recalculateRelations.bind(this));
            
            // Form submit avec AJAX
            $('.archi-settings-form').on('submit', this.handleFormSubmit.bind(this));
        },
        
        /**
         * Initialiser les range sliders avec preview
         */
        initRangeSliders: function() {
            $('.archi-range-slider').each(function() {
                const $slider = $(this);
                const $valueDisplay = $slider.next('.range-value');
                
                // Afficher la valeur initiale
                $valueDisplay.text($slider.val());
                
                // Mettre à jour lors du changement
                $slider.on('input change', function() {
                    $valueDisplay.text($(this).val());
                });
            });
        },
        
        /**
         * Initialiser les tooltips (si nécessaire)
         */
        initTooltips: function() {
            // Placeholder pour tooltips futurs
        },
        
        /**
         * Vider le cache
         */
        clearCache: function(e) {
            e.preventDefault();
            
            if (!confirm(archiAdmin.strings.confirm)) {
                return;
            }
            
            const $button = $(e.currentTarget);
            const originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="archi-loading"></span> ' + 'En cours...');
            
            $.ajax({
                url: archiAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'archi_clear_cache',
                    nonce: archiAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        ArchiAdmin.showNotification('success', response.data.message || archiAdmin.strings.saved);
                    } else {
                        ArchiAdmin.showNotification('error', response.data.message || archiAdmin.strings.error);
                    }
                },
                error: function() {
                    ArchiAdmin.showNotification('error', archiAdmin.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },
        
        /**
         * Recalculer les relations
         */
        recalculateRelations: function(e) {
            e.preventDefault();
            
            if (!confirm(archiAdmin.strings.confirm)) {
                return;
            }
            
            const $button = $(e.currentTarget);
            const originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="archi-loading"></span> ' + 'Calcul en cours...');
            
            $.ajax({
                url: archiAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'archi_recalculate_relations',
                    nonce: archiAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        ArchiAdmin.showNotification('success', response.data.message || 'Relations recalculées');
                    } else {
                        ArchiAdmin.showNotification('error', response.data.message || archiAdmin.strings.error);
                    }
                },
                error: function() {
                    ArchiAdmin.showNotification('error', archiAdmin.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },
        
        /**
         * Gérer la soumission du formulaire
         */
        handleFormSubmit: function(e) {
            // Laisser WordPress gérer la soumission standard
            // Mais on peut ajouter de la validation ici si besoin
            
            const $form = $(e.currentTarget);
            const isValid = this.validateForm($form);
            
            if (!isValid) {
                e.preventDefault();
                this.showNotification('error', 'Veuillez corriger les erreurs dans le formulaire');
                return false;
            }
            
            // Afficher un loader
            this.showLoadingOverlay();
        },
        
        /**
         * Valider le formulaire
         */
        validateForm: function($form) {
            let isValid = true;
            
            // Valider les champs requis
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            // Valider les couleurs HEX
            $form.find('input[type="color"]').each(function() {
                const $field = $(this);
                const value = $field.val();
                const hexPattern = /^#[0-9A-F]{6}$/i;
                
                if (!hexPattern.test(value)) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            // Valider les ranges
            $form.find('input[type="range"], input[type="number"]').each(function() {
                const $field = $(this);
                const value = parseInt($field.val(), 10);
                const min = parseInt($field.attr('min'), 10);
                const max = parseInt($field.attr('max'), 10);
                
                if (value < min || value > max) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            return isValid;
        },
        
        /**
         * Afficher une notification
         */
        showNotification: function(type, message) {
            // Supprimer les notifications existantes
            $('.archi-notification').remove();
            
            const $notification = $('<div>')
                .addClass('archi-notification')
                .addClass(type)
                .html('<p>' + message + '</p>');
            
            $('.archi-admin-content').prepend($notification);
            
            // Auto-dismiss après 5 secondes
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        /**
         * Afficher un overlay de chargement
         */
        showLoadingOverlay: function() {
            if ($('.archi-loading-overlay').length === 0) {
                const $overlay = $('<div>')
                    .addClass('archi-loading-overlay')
                    .html('<div class="archi-loading"></div>');
                
                $('body').append($overlay);
            }
        },
        
        /**
         * Masquer l'overlay de chargement
         */
        hideLoadingOverlay: function() {
            $('.archi-loading-overlay').fadeOut(function() {
                $(this).remove();
            });
        },
        
        /**
         * Sauvegarder les paramètres via AJAX
         */
        saveSettings: function(settings) {
            return $.ajax({
                url: archiAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'archi_save_settings',
                    nonce: archiAdmin.nonce,
                    settings: settings
                }
            });
        },
        
        /**
         * Charger des données via REST API
         */
        loadData: function(endpoint) {
            return $.ajax({
                url: archiAdmin.restUrl + endpoint,
                type: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', archiAdmin.restNonce);
                }
            });
        }
    };
    
    /**
     * Initialisation au chargement du DOM
     */
    $(document).ready(function() {
        ArchiAdmin.init();
    });
    
    // Exposer l'objet globalement pour l'accès externe
    window.ArchiAdmin = ArchiAdmin;
    
})(jQuery);
