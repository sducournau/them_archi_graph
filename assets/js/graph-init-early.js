/**
 * Initialization code that MUST run before the main bundle
 * This file is loaded directly as a separate script tag BEFORE app.bundle.js
 */

(function() {
  'use strict';
  
  console.log('🎨 [GRAPH-INIT-EARLY] Executing - Initializing window.updateGraphSettings');
  
  // Define window.updateGraphSettings function for Customizer live preview
  window.updateGraphSettings = function(newSettings) {
    console.log('🎨 [GRAPH-INIT-EARLY] Graph settings update requested:', newSettings);
    
    // Update global settings object
    if (typeof window.archiGraphSettings === 'object') {
      Object.assign(window.archiGraphSettings, newSettings);
    } else {
      window.archiGraphSettings = newSettings;
    }
    
    // Dispatch custom event for React components to listen
    const event = new CustomEvent('graphSettingsUpdated', {
      detail: newSettings
    });
    window.dispatchEvent(event);
  };
  
  console.log('🎨 [GRAPH-INIT-EARLY] window.updateGraphSettings is now:', typeof window.updateGraphSettings);
  
})();
