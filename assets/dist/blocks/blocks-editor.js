/******/ (() => { // webpackBootstrap
/*!************************************!*\
  !*** ./assets/js/blocks-editor.js ***!
  \************************************/
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
/**
 * Scripts JavaScript pour les blocs Gutenberg personnalisés
 */

// Imports WordPress
var registerBlockType = wp.blocks.registerBlockType;
var _wp$blockEditor = wp.blockEditor,
  InspectorControls = _wp$blockEditor.InspectorControls,
  MediaUpload = _wp$blockEditor.MediaUpload,
  MediaUploadCheck = _wp$blockEditor.MediaUploadCheck;
var _wp$components = wp.components,
  PanelBody = _wp$components.PanelBody,
  TextControl = _wp$components.TextControl,
  SelectControl = _wp$components.SelectControl,
  RangeControl = _wp$components.RangeControl,
  ToggleControl = _wp$components.ToggleControl,
  Button = _wp$components.Button,
  ButtonGroup = _wp$components.ButtonGroup,
  CheckboxControl = _wp$components.CheckboxControl,
  TextareaControl = _wp$components.TextareaControl;
var _wp$element = wp.element,
  useState = _wp$element.useState,
  Fragment = _wp$element.Fragment;
var __ = wp.i18n.__;

/**
 * Bloc Graphique Interactif
 */
registerBlockType("archi-graph/interactive-graph", {
  title: __("Graphique Interactif", "archi-graph"),
  description: __("Affiche le graphique interactif avec les projets et illustrations", "archi-graph"),
  icon: "networking",
  category: "archi-graph",
  attributes: {
    width: {
      type: "number",
      "default": 1200
    },
    height: {
      type: "number",
      "default": 800
    },
    maxArticles: {
      type: "number",
      "default": 100
    },
    enableFilters: {
      type: "boolean",
      "default": true
    },
    enableSearch: {
      type: "boolean",
      "default": true
    },
    animationDuration: {
      type: "number",
      "default": 1000
    },
    nodeSpacing: {
      type: "number",
      "default": 100
    },
    clusterStrength: {
      type: "number",
      "default": 10
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var width = attributes.width,
      height = attributes.height,
      maxArticles = attributes.maxArticles,
      enableFilters = attributes.enableFilters,
      enableSearch = attributes.enableSearch,
      animationDuration = attributes.animationDuration,
      nodeSpacing = attributes.nodeSpacing,
      clusterStrength = attributes.clusterStrength;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Dimensions", "archi-graph")
    }, /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Largeur", "archi-graph"),
      value: width,
      onChange: function onChange(value) {
        return setAttributes({
          width: value
        });
      },
      min: 400,
      max: 1600
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Hauteur", "archi-graph"),
      value: height,
      onChange: function onChange(value) {
        return setAttributes({
          height: value
        });
      },
      min: 300,
      max: 1200
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Paramètres", "archi-graph")
    }, /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Nombre maximum d'articles", "archi-graph"),
      value: maxArticles,
      onChange: function onChange(value) {
        return setAttributes({
          maxArticles: value
        });
      },
      min: 10,
      max: 500
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Activer les filtres", "archi-graph"),
      checked: enableFilters,
      onChange: function onChange(value) {
        return setAttributes({
          enableFilters: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Activer la recherche", "archi-graph"),
      checked: enableSearch,
      onChange: function onChange(value) {
        return setAttributes({
          enableSearch: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Animation", "archi-graph"),
      initialOpen: false
    }, /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Durée d'animation (ms)", "archi-graph"),
      value: animationDuration,
      onChange: function onChange(value) {
        return setAttributes({
          animationDuration: value
        });
      },
      min: 100,
      max: 5000
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Espacement des nœuds", "archi-graph"),
      value: nodeSpacing,
      onChange: function onChange(value) {
        return setAttributes({
          nodeSpacing: value
        });
      },
      min: 50,
      max: 200
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Force de clustering", "archi-graph"),
      value: clusterStrength,
      onChange: function onChange(value) {
        return setAttributes({
          clusterStrength: value
        });
      },
      min: 0,
      max: 50
    }))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("div", {
      className: "archi-graph-preview",
      style: {
        width: "100%",
        height: Math.min(height, 400) + "px",
        background: "#f0f0f0",
        border: "2px dashed #ccc",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        borderRadius: "8px"
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        textAlign: "center",
        color: "#666"
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: "48px",
        marginBottom: "1rem"
      }
    }, "\uD83D\uDDC2\uFE0F"), /*#__PURE__*/React.createElement("h3", null, __("Graphique Interactif", "archi-graph")), /*#__PURE__*/React.createElement("p", null, maxArticles, " ", __("articles maximum", "archi-graph")), /*#__PURE__*/React.createElement("p", null, width, " \xD7 ", height, "px")))));
  },
  save: function save() {
    return null; // Rendu côté serveur
  }
});

/**
 * Bloc Vitrine de Projets
 */
registerBlockType("archi-graph/project-showcase", {
  title: __("Vitrine de Projets", "archi-graph"),
  description: __("Affiche une sélection de projets en grille ou liste", "archi-graph"),
  icon: "portfolio",
  category: "archi-graph",
  attributes: {
    layout: {
      type: "string",
      "default": "grid"
    },
    columns: {
      type: "number",
      "default": 3
    },
    showDescription: {
      type: "boolean",
      "default": true
    },
    showMetadata: {
      type: "boolean",
      "default": true
    },
    imageSize: {
      type: "string",
      "default": "medium"
    },
    selectedProjects: {
      type: "array",
      "default": []
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var layout = attributes.layout,
      columns = attributes.columns,
      showDescription = attributes.showDescription,
      showMetadata = attributes.showMetadata,
      imageSize = attributes.imageSize,
      selectedProjects = attributes.selectedProjects;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Mise en page", "archi-graph")
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Layout", "archi-graph"),
      value: layout,
      options: [{
        label: __("Grille", "archi-graph"),
        value: "grid"
      }, {
        label: __("Liste", "archi-graph"),
        value: "list"
      }, {
        label: __("Carrousel", "archi-graph"),
        value: "carousel"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          layout: value
        });
      }
    }), layout === "grid" && /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Nombre de colonnes", "archi-graph"),
      value: columns,
      onChange: function onChange(value) {
        return setAttributes({
          columns: value
        });
      },
      min: 1,
      max: 4
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Taille d'image", "archi-graph"),
      value: imageSize,
      options: [{
        label: __("Miniature", "archi-graph"),
        value: "thumbnail"
      }, {
        label: __("Moyenne", "archi-graph"),
        value: "medium"
      }, {
        label: __("Grande", "archi-graph"),
        value: "large"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          imageSize: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Affichage", "archi-graph")
    }, /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher la description", "archi-graph"),
      checked: showDescription,
      onChange: function onChange(value) {
        return setAttributes({
          showDescription: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher les métadonnées", "archi-graph"),
      checked: showMetadata,
      onChange: function onChange(value) {
        return setAttributes({
          showMetadata: value
        });
      }
    }))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("h3", null, __("Vitrine de Projets", "archi-graph")), /*#__PURE__*/React.createElement("div", {
      style: {
        display: "grid",
        gridTemplateColumns: "repeat(".concat(columns, ", 1fr)"),
        gap: "1rem",
        margin: "1rem 0"
      }
    }, [1, 2, 3].map(function (i) {
      return /*#__PURE__*/React.createElement("div", {
        key: i,
        style: {
          border: "1px solid #ddd",
          borderRadius: "4px",
          padding: "1rem",
          background: "#fff"
        }
      }, /*#__PURE__*/React.createElement("div", {
        style: {
          height: "150px",
          background: "#f0f0f0",
          borderRadius: "4px",
          marginBottom: "0.5rem"
        }
      }), /*#__PURE__*/React.createElement("h4", null, "Projet ", i), showDescription && /*#__PURE__*/React.createElement("p", null, "Description du projet..."), showMetadata && /*#__PURE__*/React.createElement("div", {
        style: {
          fontSize: "0.85rem",
          color: "#666"
        }
      }, "\uD83D\uDCD0 150m\xB2 \u2022 \uD83D\uDCB0 250k\u20AC \u2022 \uD83D\uDCCD Lyon"));
    }))));
  },
  save: function save() {
    return null;
  }
});

/**
 * Bloc Grille d'Articles
 */
registerBlockType("archi-graph/illustration-grid", {
  title: __("Grille d'Illustrations", "archi-graph"),
  description: __("Affiche les illustrations et explorations graphiques en grille", "archi-graph"),
  icon: "grid-view",
  category: "archi-graph",
  attributes: {
    postsPerPage: {
      type: "number",
      "default": 6
    },
    columns: {
      type: "number",
      "default": 2
    },
    showExcerpt: {
      type: "boolean",
      "default": true
    },
    showDate: {
      type: "boolean",
      "default": true
    },
    showAuthor: {
      type: "boolean",
      "default": false
    },
    showCategories: {
      type: "boolean",
      "default": true
    },
    orderBy: {
      type: "string",
      "default": "date"
    },
    order: {
      type: "string",
      "default": "DESC"
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var postsPerPage = attributes.postsPerPage,
      columns = attributes.columns,
      showExcerpt = attributes.showExcerpt,
      showDate = attributes.showDate,
      showAuthor = attributes.showAuthor,
      showCategories = attributes.showCategories,
      orderBy = attributes.orderBy,
      order = attributes.order;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Paramètres de requête", "archi-graph")
    }, /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Nombre d'articles", "archi-graph"),
      value: postsPerPage,
      onChange: function onChange(value) {
        return setAttributes({
          postsPerPage: value
        });
      },
      min: 1,
      max: 12
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Trier par", "archi-graph"),
      value: orderBy,
      options: [{
        label: __("Date", "archi-graph"),
        value: "date"
      }, {
        label: __("Titre", "archi-graph"),
        value: "title"
      }, {
        label: __("Aléatoire", "archi-graph"),
        value: "rand"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          orderBy: value
        });
      }
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Ordre", "archi-graph"),
      value: order,
      options: [{
        label: __("Décroissant", "archi-graph"),
        value: "DESC"
      }, {
        label: __("Croissant", "archi-graph"),
        value: "ASC"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          order: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Mise en page", "archi-graph")
    }, /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Colonnes", "archi-graph"),
      value: columns,
      onChange: function onChange(value) {
        return setAttributes({
          columns: value
        });
      },
      min: 1,
      max: 4
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Affichage", "archi-graph")
    }, /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher l'extrait", "archi-graph"),
      checked: showExcerpt,
      onChange: function onChange(value) {
        return setAttributes({
          showExcerpt: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher la date", "archi-graph"),
      checked: showDate,
      onChange: function onChange(value) {
        return setAttributes({
          showDate: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher l'auteur", "archi-graph"),
      checked: showAuthor,
      onChange: function onChange(value) {
        return setAttributes({
          showAuthor: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher les catégories", "archi-graph"),
      checked: showCategories,
      onChange: function onChange(value) {
        return setAttributes({
          showCategories: value
        });
      }
    }))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("h3", null, __("Grille d'Articles", "archi-graph")), /*#__PURE__*/React.createElement("div", {
      style: {
        display: "grid",
        gridTemplateColumns: "repeat(".concat(columns, ", 1fr)"),
        gap: "1rem",
        margin: "1rem 0"
      }
    }, Array.from({
      length: Math.min(postsPerPage, 4)
    }).map(function (_, i) {
      return /*#__PURE__*/React.createElement("div", {
        key: i,
        style: {
          border: "1px solid #ddd",
          borderRadius: "4px",
          overflow: "hidden",
          background: "#fff"
        }
      }, /*#__PURE__*/React.createElement("div", {
        style: {
          height: "120px",
          background: "#f0f0f0"
        }
      }), /*#__PURE__*/React.createElement("div", {
        style: {
          padding: "1rem"
        }
      }, /*#__PURE__*/React.createElement("h4", {
        style: {
          margin: "0 0 0.5rem 0"
        }
      }, "Article ", i + 1), showDate && /*#__PURE__*/React.createElement("div", {
        style: {
          fontSize: "0.8rem",
          color: "#666",
          marginBottom: "0.5rem"
        }
      }, "\uD83D\uDCC5 ", new Date().toLocaleDateString()), showExcerpt && /*#__PURE__*/React.createElement("p", {
        style: {
          margin: "0 0 0.5rem 0",
          fontSize: "0.9rem"
        }
      }, "Extrait de l'article..."), showCategories && /*#__PURE__*/React.createElement("div", {
        style: {
          fontSize: "0.8rem"
        }
      }, /*#__PURE__*/React.createElement("span", {
        style: {
          background: "#e9ecef",
          padding: "0.2rem 0.5rem",
          borderRadius: "12px"
        }
      }, "Cat\xE9gorie"))));
    }))));
  },
  save: function save() {
    return null;
  }
});

/**
 * Bloc Timeline
 */
registerBlockType("archi-graph/timeline", {
  title: __("Timeline", "archi-graph"),
  description: __("Affiche une timeline d'événements ou de projets", "archi-graph"),
  icon: "clock",
  category: "archi-graph",
  attributes: {
    timelineItems: {
      type: "array",
      "default": []
    },
    orientation: {
      type: "string",
      "default": "vertical"
    },
    showDates: {
      type: "boolean",
      "default": true
    },
    alternating: {
      type: "boolean",
      "default": true
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var timelineItems = attributes.timelineItems,
      orientation = attributes.orientation,
      showDates = attributes.showDates,
      alternating = attributes.alternating;
    var addTimelineItem = function addTimelineItem() {
      var newItems = [].concat(_toConsumableArray(timelineItems), [{
        id: Date.now(),
        date: "",
        title: __("Nouveau élément", "archi-graph"),
        description: ""
      }]);
      setAttributes({
        timelineItems: newItems
      });
    };
    var updateTimelineItem = function updateTimelineItem(index, field, value) {
      var newItems = _toConsumableArray(timelineItems);
      newItems[index][field] = value;
      setAttributes({
        timelineItems: newItems
      });
    };
    var removeTimelineItem = function removeTimelineItem(index) {
      var newItems = timelineItems.filter(function (_, i) {
        return i !== index;
      });
      setAttributes({
        timelineItems: newItems
      });
    };
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Paramètres Timeline", "archi-graph")
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Orientation", "archi-graph"),
      value: orientation,
      options: [{
        label: __("Verticale", "archi-graph"),
        value: "vertical"
      }, {
        label: __("Horizontale", "archi-graph"),
        value: "horizontal"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          orientation: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher les dates", "archi-graph"),
      checked: showDates,
      onChange: function onChange(value) {
        return setAttributes({
          showDates: value
        });
      }
    }), orientation === "vertical" && /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Alternance gauche/droite", "archi-graph"),
      checked: alternating,
      onChange: function onChange(value) {
        return setAttributes({
          alternating: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Éléments de la Timeline", "archi-graph")
    }, /*#__PURE__*/React.createElement(Button, {
      isPrimary: true,
      onClick: addTimelineItem
    }, __("Ajouter un élément", "archi-graph")))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        display: "flex",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: "1rem"
      }
    }, /*#__PURE__*/React.createElement("h3", null, __("Timeline", "archi-graph")), /*#__PURE__*/React.createElement(Button, {
      isPrimary: true,
      onClick: addTimelineItem
    }, __("Ajouter", "archi-graph"))), timelineItems.length === 0 ? /*#__PURE__*/React.createElement("div", {
      style: {
        padding: "2rem",
        textAlign: "center",
        border: "2px dashed #ddd",
        borderRadius: "8px",
        color: "#666"
      }
    }, __('Aucun élément de timeline. Cliquez sur "Ajouter" pour commencer.', "archi-graph")) : /*#__PURE__*/React.createElement("div", {
      style: {
        position: "relative"
      }
    }, timelineItems.map(function (item, index) {
      return /*#__PURE__*/React.createElement("div", {
        key: item.id || index,
        style: {
          border: "1px solid #ddd",
          borderRadius: "4px",
          padding: "1rem",
          marginBottom: "1rem",
          background: "#fff"
        }
      }, /*#__PURE__*/React.createElement("div", {
        style: {
          display: "flex",
          justifyContent: "space-between",
          alignItems: "flex-start",
          marginBottom: "0.5rem"
        }
      }, /*#__PURE__*/React.createElement("strong", null, __("Élément", "archi-graph"), " ", index + 1), /*#__PURE__*/React.createElement(Button, {
        isDestructive: true,
        isSmall: true,
        onClick: function onClick() {
          return removeTimelineItem(index);
        }
      }, __("Supprimer", "archi-graph"))), /*#__PURE__*/React.createElement(TextControl, {
        label: __("Date", "archi-graph"),
        value: item.date || "",
        onChange: function onChange(value) {
          return updateTimelineItem(index, "date", value);
        },
        placeholder: "2024"
      }), /*#__PURE__*/React.createElement(TextControl, {
        label: __("Titre", "archi-graph"),
        value: item.title || "",
        onChange: function onChange(value) {
          return updateTimelineItem(index, "title", value);
        },
        placeholder: __("Titre de l'événement", "archi-graph")
      }), /*#__PURE__*/React.createElement(TextareaControl, {
        label: __("Description", "archi-graph"),
        value: item.description || "",
        onChange: function onChange(value) {
          return updateTimelineItem(index, "description", value);
        },
        placeholder: __("Description de l'événement", "archi-graph"),
        rows: 3
      }));
    }))));
  },
  save: function save() {
    return null;
  }
});

/**
 * Bloc Avant/Après
 */
registerBlockType("archi-graph/before-after", {
  title: __("Avant/Après", "archi-graph"),
  description: __("Comparaison d'images avant/après avec curseur interactif", "archi-graph"),
  icon: "image-flip-horizontal",
  category: "archi-graph",
  attributes: {
    beforeImage: {
      type: "object",
      "default": null
    },
    afterImage: {
      type: "object",
      "default": null
    },
    beforeLabel: {
      type: "string",
      "default": "Avant"
    },
    afterLabel: {
      type: "string",
      "default": "Après"
    },
    sliderPosition: {
      type: "number",
      "default": 50
    },
    orientation: {
      type: "string",
      "default": "vertical"
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var beforeImage = attributes.beforeImage,
      afterImage = attributes.afterImage,
      beforeLabel = attributes.beforeLabel,
      afterLabel = attributes.afterLabel,
      sliderPosition = attributes.sliderPosition,
      orientation = attributes.orientation;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Configuration", "archi-graph")
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Orientation du curseur", "archi-graph"),
      value: orientation,
      options: [{
        label: __("Vertical (gauche/droite)", "archi-graph"),
        value: "vertical"
      }, {
        label: __("Horizontal (haut/bas)", "archi-graph"),
        value: "horizontal"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          orientation: value
        });
      }
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: __("Position initiale du curseur (%)", "archi-graph"),
      value: sliderPosition,
      onChange: function onChange(value) {
        return setAttributes({
          sliderPosition: value
        });
      },
      min: 0,
      max: 100
    }), /*#__PURE__*/React.createElement(TextControl, {
      label: __('Label "Avant"', "archi-graph"),
      value: beforeLabel,
      onChange: function onChange(value) {
        return setAttributes({
          beforeLabel: value
        });
      }
    }), /*#__PURE__*/React.createElement(TextControl, {
      label: __('Label "Après"', "archi-graph"),
      value: afterLabel,
      onChange: function onChange(value) {
        return setAttributes({
          afterLabel: value
        });
      }
    }))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("h3", null, __("Comparaison Avant/Après", "archi-graph")), /*#__PURE__*/React.createElement("div", {
      style: {
        display: "grid",
        gridTemplateColumns: "1fr 1fr",
        gap: "1rem",
        marginBottom: "1rem"
      }
    }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("h4", null, beforeLabel), /*#__PURE__*/React.createElement(MediaUploadCheck, null, /*#__PURE__*/React.createElement(MediaUpload, {
      onSelect: function onSelect(media) {
        return setAttributes({
          beforeImage: media
        });
      },
      allowedTypes: ["image"],
      value: beforeImage === null || beforeImage === void 0 ? void 0 : beforeImage.id,
      render: function render(_ref) {
        var open = _ref.open;
        return /*#__PURE__*/React.createElement("div", {
          onClick: open,
          style: {
            height: "200px",
            border: "2px dashed #ddd",
            borderRadius: "4px",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            cursor: "pointer",
            background: beforeImage ? "url(".concat(beforeImage.url, ") center/cover") : "#f9f9f9"
          }
        }, !beforeImage && /*#__PURE__*/React.createElement("div", {
          style: {
            textAlign: "center",
            color: "#666"
          }
        }, /*#__PURE__*/React.createElement("div", {
          style: {
            fontSize: "24px",
            marginBottom: "0.5rem"
          }
        }, "\uD83D\uDCF7"), __('Sélectionner l\'image "avant"', "archi-graph")));
      }
    }))), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("h4", null, afterLabel), /*#__PURE__*/React.createElement(MediaUploadCheck, null, /*#__PURE__*/React.createElement(MediaUpload, {
      onSelect: function onSelect(media) {
        return setAttributes({
          afterImage: media
        });
      },
      allowedTypes: ["image"],
      value: afterImage === null || afterImage === void 0 ? void 0 : afterImage.id,
      render: function render(_ref2) {
        var open = _ref2.open;
        return /*#__PURE__*/React.createElement("div", {
          onClick: open,
          style: {
            height: "200px",
            border: "2px dashed #ddd",
            borderRadius: "4px",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            cursor: "pointer",
            background: afterImage ? "url(".concat(afterImage.url, ") center/cover") : "#f9f9f9"
          }
        }, !afterImage && /*#__PURE__*/React.createElement("div", {
          style: {
            textAlign: "center",
            color: "#666"
          }
        }, /*#__PURE__*/React.createElement("div", {
          style: {
            fontSize: "24px",
            marginBottom: "0.5rem"
          }
        }, "\uD83D\uDCF7"), __('Sélectionner l\'image "après"', "archi-graph")));
      }
    })))), beforeImage && afterImage && /*#__PURE__*/React.createElement("div", {
      style: {
        padding: "1rem",
        background: "#f0f8ff",
        borderRadius: "4px",
        textAlign: "center"
      }
    }, "\u2705", " ", __("Comparaison configurée ! Le curseur interactif sera affiché sur le front-end.", "archi-graph"))));
  },
  save: function save() {
    return null;
  }
});

/**
 * Bloc Spécifications Techniques
 */
registerBlockType("archi-graph/technical-specs", {
  title: __("Spécifications Techniques", "archi-graph"),
  description: __("Affiche les spécifications techniques d'un projet", "archi-graph"),
  icon: "editor-table",
  category: "archi-graph",
  attributes: {
    specifications: {
      type: "array",
      "default": []
    },
    layout: {
      type: "string",
      "default": "table"
    },
    showIcons: {
      type: "boolean",
      "default": true
    },
    groupByCategory: {
      type: "boolean",
      "default": false
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes,
      setAttributes = props.setAttributes;
    var specifications = attributes.specifications,
      layout = attributes.layout,
      showIcons = attributes.showIcons,
      groupByCategory = attributes.groupByCategory;
    var addSpecification = function addSpecification() {
      var newSpecs = [].concat(_toConsumableArray(specifications), [{
        id: Date.now(),
        label: "",
        value: "",
        unit: "",
        category: "",
        icon: ""
      }]);
      setAttributes({
        specifications: newSpecs
      });
    };
    var updateSpecification = function updateSpecification(index, field, value) {
      var newSpecs = _toConsumableArray(specifications);
      newSpecs[index][field] = value;
      setAttributes({
        specifications: newSpecs
      });
    };
    var removeSpecification = function removeSpecification(index) {
      var newSpecs = specifications.filter(function (_, i) {
        return i !== index;
      });
      setAttributes({
        specifications: newSpecs
      });
    };
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Mise en page", "archi-graph")
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: __("Layout", "archi-graph"),
      value: layout,
      options: [{
        label: __("Tableau", "archi-graph"),
        value: "table"
      }, {
        label: __("Cartes", "archi-graph"),
        value: "cards"
      }],
      onChange: function onChange(value) {
        return setAttributes({
          layout: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Afficher les icônes", "archi-graph"),
      checked: showIcons,
      onChange: function onChange(value) {
        return setAttributes({
          showIcons: value
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: __("Grouper par catégorie", "archi-graph"),
      checked: groupByCategory,
      onChange: function onChange(value) {
        return setAttributes({
          groupByCategory: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: __("Spécifications", "archi-graph")
    }, /*#__PURE__*/React.createElement(Button, {
      isPrimary: true,
      onClick: addSpecification
    }, __("Ajouter une spécification", "archi-graph")))), /*#__PURE__*/React.createElement("div", {
      className: "archi-block-preview"
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        display: "flex",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: "1rem"
      }
    }, /*#__PURE__*/React.createElement("h3", null, __("Spécifications Techniques", "archi-graph")), /*#__PURE__*/React.createElement(Button, {
      isPrimary: true,
      onClick: addSpecification
    }, __("Ajouter", "archi-graph"))), specifications.length === 0 ? /*#__PURE__*/React.createElement("div", {
      style: {
        padding: "2rem",
        textAlign: "center",
        border: "2px dashed #ddd",
        borderRadius: "8px",
        color: "#666"
      }
    }, __('Aucune spécification. Cliquez sur "Ajouter" pour commencer.', "archi-graph")) : /*#__PURE__*/React.createElement("div", null, specifications.map(function (spec, index) {
      return /*#__PURE__*/React.createElement("div", {
        key: spec.id || index,
        style: {
          border: "1px solid #ddd",
          borderRadius: "4px",
          padding: "1rem",
          marginBottom: "1rem",
          background: "#fff"
        }
      }, /*#__PURE__*/React.createElement("div", {
        style: {
          display: "flex",
          justifyContent: "space-between",
          alignItems: "flex-start",
          marginBottom: "0.5rem"
        }
      }, /*#__PURE__*/React.createElement("strong", null, __("Spécification", "archi-graph"), " ", index + 1), /*#__PURE__*/React.createElement(Button, {
        isDestructive: true,
        isSmall: true,
        onClick: function onClick() {
          return removeSpecification(index);
        }
      }, __("Supprimer", "archi-graph"))), /*#__PURE__*/React.createElement("div", {
        style: {
          display: "grid",
          gridTemplateColumns: "1fr 1fr",
          gap: "1rem",
          marginBottom: "1rem"
        }
      }, /*#__PURE__*/React.createElement(TextControl, {
        label: __("Label", "archi-graph"),
        value: spec.label || "",
        onChange: function onChange(value) {
          return updateSpecification(index, "label", value);
        },
        placeholder: __("Ex: Surface habitable", "archi-graph")
      }), /*#__PURE__*/React.createElement(TextControl, {
        label: __("Valeur", "archi-graph"),
        value: spec.value || "",
        onChange: function onChange(value) {
          return updateSpecification(index, "value", value);
        },
        placeholder: __("Ex: 150", "archi-graph")
      })), /*#__PURE__*/React.createElement("div", {
        style: {
          display: "grid",
          gridTemplateColumns: "1fr 1fr 1fr",
          gap: "1rem"
        }
      }, /*#__PURE__*/React.createElement(TextControl, {
        label: __("Unité", "archi-graph"),
        value: spec.unit || "",
        onChange: function onChange(value) {
          return updateSpecification(index, "unit", value);
        },
        placeholder: __("Ex: m²", "archi-graph")
      }), /*#__PURE__*/React.createElement(TextControl, {
        label: __("Catégorie", "archi-graph"),
        value: spec.category || "",
        onChange: function onChange(value) {
          return updateSpecification(index, "category", value);
        },
        placeholder: __("Ex: Dimensions", "archi-graph")
      }), /*#__PURE__*/React.createElement(TextControl, {
        label: __("Icône (classe CSS)", "archi-graph"),
        value: spec.icon || "",
        onChange: function onChange(value) {
          return updateSpecification(index, "icon", value);
        },
        placeholder: __("Ex: fas fa-home", "archi-graph")
      })));
    }))));
  },
  save: function save() {
    return null;
  }
});

// Styles pour l'éditeur
wp.domReady(function () {
  // Ajouter des styles personnalisés à l'éditeur
  var editorStyle = document.createElement("style");
  editorStyle.textContent = "\n        .archi-block-preview {\n            padding: 1rem;\n            border: 1px solid #e2e4e7;\n            border-radius: 4px;\n            background: #fff;\n            margin: 1rem 0;\n        }\n        \n        .archi-block-preview h3 {\n            margin-top: 0;\n            color: #1e1e1e;\n            border-bottom: 2px solid #0073aa;\n            padding-bottom: 0.5rem;\n        }\n        \n        .archi-graph-preview {\n            position: relative;\n            overflow: hidden;\n        }\n        \n        .archi-graph-preview::after {\n            content: '';\n            position: absolute;\n            top: 0;\n            left: 0;\n            right: 0;\n            bottom: 0;\n            background: radial-gradient(circle at 30% 40%, rgba(0, 115, 170, 0.1) 0%, transparent 50%),\n                        radial-gradient(circle at 70% 60%, rgba(231, 76, 60, 0.1) 0%, transparent 50%),\n                        radial-gradient(circle at 50% 80%, rgba(52, 152, 219, 0.1) 0%, transparent 50%);\n            pointer-events: none;\n        }\n    ";
  document.head.appendChild(editorStyle);
});
/******/ })()
;
//# sourceMappingURL=blocks-editor.js.map