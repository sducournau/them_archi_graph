/**
 * Scripts pour l'interface d'administration du graphique
 */

(function ($) {
  "use strict";

  const ArchiGraphAdmin = {
    /**
     * Initialisation
     */
    init: function () {
      this.bindEvents();
      this.initColorPickers();
      this.loadStats();
    },

    /**
     * Liaison des événements
     */
    bindEvents: function () {
      // Réinitialiser les positions
      $("#archi-reset-positions").on("click", this.resetPositions.bind(this));

      // Exporter les données
      $("#archi-export-data").on("click", this.exportData.bind(this));

      // Ajouter une relation
      $("#archi-add-relation-form").on("submit", this.addRelation.bind(this));

      // Modifier/Supprimer une relation
      $(".edit-relation").on("click", this.editRelation.bind(this));
      $(".delete-relation").on("click", this.deleteRelation.bind(this));

      // Configurer une catégorie
      $(".edit-category").on("click", this.editCategory.bind(this));

      // Changement de couleur de catégorie
      $(".category-color").on("change", this.updateCategoryColor.bind(this));

      // Actions en masse
      $("#archi-bulk-form").on("submit", this.processBulkAction.bind(this));
    },

    /**
     * Initialiser les color pickers
     */
    initColorPickers: function () {
      if (typeof $.fn.wpColorPicker !== "undefined") {
        $(".archi-color-picker").wpColorPicker();
      }
    },

    /**
     * Charger les statistiques
     */
    loadStats: function () {
      if (!$(".archi-dashboard").length) return;

      $.ajax({
        url: archiGraphAdmin.apiUrl + "proximity-analysis",
        method: "GET",
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-WP-Nonce", archiGraphAdmin.restNonce);
        },
        success: function (response) {
        },
        error: function (error) {
          console.error("Erreur lors du chargement des statistiques:", error);
        },
      });
    },

    /**
     * Réinitialiser toutes les positions
     */
    resetPositions: function (e) {
      e.preventDefault();

      if (
        !confirm(
          "Êtes-vous sûr de vouloir réinitialiser toutes les positions du graphique ?"
        )
      ) {
        return;
      }

      const $btn = $(e.currentTarget);
      const originalText = $btn.text();

      $btn
        .prop("disabled", true)
        .html('<span class="archi-loading"></span> Réinitialisation...');

      $.ajax({
        url: archiGraphAdmin.ajaxUrl,
        method: "POST",
        data: {
          action: "archi_reset_all_positions",
          nonce: archiGraphAdmin.nonce,
        },
        success: function (response) {
          if (response.success) {
            ArchiGraphAdmin.showMessage(
              "success",
              "Les positions ont été réinitialisées avec succès !"
            );
          } else {
            ArchiGraphAdmin.showMessage(
              "error",
              response.data.message || "Une erreur est survenue."
            );
          }
        },
        error: function () {
          ArchiGraphAdmin.showMessage(
            "error",
            "Erreur de communication avec le serveur."
          );
        },
        complete: function () {
          $btn.prop("disabled", false).text(originalText);
        },
      });
    },

    /**
     * Exporter les données du graphique
     */
    exportData: function (e) {
      e.preventDefault();

      const $btn = $(e.currentTarget);
      const originalText = $btn.text();

      $btn
        .prop("disabled", true)
        .html('<span class="archi-loading"></span> Export en cours...');

      $.ajax({
        url: archiGraphAdmin.apiUrl + "articles",
        method: "GET",
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-WP-Nonce", archiGraphAdmin.restNonce);
        },
        success: function (response) {
          // Créer un blob JSON
          const dataStr = JSON.stringify(response, null, 2);
          const dataBlob = new Blob([dataStr], { type: "application/json" });

          // Créer un lien de téléchargement
          const url = URL.createObjectURL(dataBlob);
          const link = document.createElement("a");
          link.href = url;
          link.download = "archi-graph-data-" + new Date().getTime() + ".json";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          URL.revokeObjectURL(url);

          ArchiGraphAdmin.showMessage(
            "success",
            "Données exportées avec succès !"
          );
        },
        error: function () {
          ArchiGraphAdmin.showMessage(
            "error",
            "Erreur lors de l'export des données."
          );
        },
        complete: function () {
          $btn.prop("disabled", false).text(originalText);
        },
      });
    },

    /**
     * Ajouter une relation
     */
    addRelation: function (e) {
      e.preventDefault();

      const $form = $(e.currentTarget);
      const formData = {
        source_id: $form.find("#source_node").val(),
        target_id: $form.find("#target_node").val(),
        relation_type: $form.find("#relation_type").val(),
        strength: $form.find("#relation_strength").val(),
      };

      if (!formData.source_id || !formData.target_id) {
        ArchiGraphAdmin.showMessage(
          "error",
          "Veuillez sélectionner les deux nœuds."
        );
        return;
      }

      if (formData.source_id === formData.target_id) {
        ArchiGraphAdmin.showMessage(
          "error",
          "Un nœud ne peut pas être lié à lui-même."
        );
        return;
      }

      $.ajax({
        url: archiGraphAdmin.ajaxUrl,
        method: "POST",
        data: {
          action: "archi_add_relation",
          nonce: archiGraphAdmin.nonce,
          ...formData,
        },
        success: function (response) {
          if (response.success) {
            ArchiGraphAdmin.showMessage(
              "success",
              "Relation ajoutée avec succès !"
            );
            location.reload();
          } else {
            ArchiGraphAdmin.showMessage(
              "error",
              response.data.message || "Erreur lors de l'ajout."
            );
          }
        },
        error: function () {
          ArchiGraphAdmin.showMessage(
            "error",
            "Erreur de communication avec le serveur."
          );
        },
      });
    },

    /**
     * Modifier une relation
     */
    editRelation: function (e) {
      e.preventDefault();
      const relationId = $(e.currentTarget).data("id");
      alert("Fonctionnalité d'édition de relation à venir");
    },

    /**
     * Supprimer une relation
     */
    deleteRelation: function (e) {
      e.preventDefault();

      if (!confirm("Êtes-vous sûr de vouloir supprimer cette relation ?")) {
        return;
      }

      const relationId = $(e.currentTarget).data("id");

      $.ajax({
        url: archiGraphAdmin.ajaxUrl,
        method: "POST",
        data: {
          action: "archi_delete_relation",
          nonce: archiGraphAdmin.nonce,
          relation_id: relationId,
        },
        success: function (response) {
          if (response.success) {
            ArchiGraphAdmin.showMessage("success", "Relation supprimée !");
            location.reload();
          } else {
            ArchiGraphAdmin.showMessage(
              "error",
              response.data.message || "Erreur lors de la suppression."
            );
          }
        },
        error: function () {
          ArchiGraphAdmin.showMessage(
            "error",
            "Erreur de communication avec le serveur."
          );
        },
      });
    },

    /**
     * Éditer une catégorie
     */
    editCategory: function (e) {
      e.preventDefault();
      const categoryId = $(e.currentTarget).data("id");
      alert("Fonctionnalité de configuration de catégorie à venir");
    },

    /**
     * Mettre à jour la couleur d'une catégorie
     */
    updateCategoryColor: function (e) {
      const $input = $(e.currentTarget);
      const categoryId = $input.data("category");
      const color = $input.val();

      $.ajax({
        url: archiGraphAdmin.ajaxUrl,
        method: "POST",
        data: {
          action: "archi_update_category_color",
          nonce: archiGraphAdmin.nonce,
          category_id: categoryId,
          color: color,
        },
        success: function (response) {
          if (response.success) {
            ArchiGraphAdmin.showMessage(
              "success",
              "Couleur mise à jour !",
              2000
            );
          }
        },
      });
    },

    /**
     * Traiter les actions en masse
     */
    processBulkAction: function (e) {
      e.preventDefault();
      // La logique est gérée côté serveur via le formulaire POST
    },

    /**
     * Afficher un message
     */
    showMessage: function (type, message, duration = 5000) {
      const $message = $("<div>")
        .addClass("archi-message")
        .addClass(type)
        .html(message);

      $(".wrap").prepend($message);

      if (duration > 0) {
        setTimeout(function () {
          $message.fadeOut(function () {
            $(this).remove();
          });
        }, duration);
      }
    },
  };

  // Initialisation au chargement du DOM
  $(document).ready(function () {
    ArchiGraphAdmin.init();
  });

  // Exposer l'objet globalement si nécessaire
  window.ArchiGraphAdmin = ArchiGraphAdmin;
})(jQuery);
