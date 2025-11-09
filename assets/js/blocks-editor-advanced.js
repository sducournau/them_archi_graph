/**
 * Bloc Gutenberg amélioré pour le graphique interactif
 * Éditeur avec contrôles avancés et aperçu en temps réel
 */

(function () {
  const { registerBlockType } = wp.blocks;
  const { InspectorControls } = wp.blockEditor || wp.editor;
  const {
    PanelBody,
    PanelRow,
    RangeControl,
    ToggleControl,
    SelectControl,
    CheckboxControl,
    Button,
  } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { withSelect } = wp.data;

  /**
   * Bloc Graphique Interactif Amélioré
   */
  registerBlockType("archi-graph/interactive-graph-advanced", {
    title: "Graphique Interactif Avancé",
    icon: "networking",
    category: "widgets",
    attributes: {
      width: {
        type: "number",
        default: 1200,
      },
      height: {
        type: "number",
        default: 800,
      },
      postTypes: {
        type: "array",
        default: ["post", "archi_article", "archi_illustration"],
      },
      selectedCategories: {
        type: "array",
        default: [],
      },
      maxNodes: {
        type: "number",
        default: 100,
      },
      enableFilters: {
        type: "boolean",
        default: true,
      },
      enableSearch: {
        type: "boolean",
        default: true,
      },
      enableZoom: {
        type: "boolean",
        default: true,
      },
      enableDrag: {
        type: "boolean",
        default: true,
      },
      animationDuration: {
        type: "number",
        default: 1000,
      },
      nodeSpacing: {
        type: "number",
        default: 100,
      },
      clusterStrength: {
        type: "number",
        default: 10,
      },
      showLabels: {
        type: "boolean",
        default: true,
      },
      showConnections: {
        type: "boolean",
        default: true,
      },
      colorScheme: {
        type: "string",
        default: "default",
      },
      layout: {
        type: "string",
        default: "force",
      },
    },

    edit: withSelect((select) => {
      return {
        categories: select("core").getEntityRecords("taxonomy", "category", {
          per_page: -1,
        }),
      };
    })(function (props) {
      const { attributes, setAttributes, categories } = props;

      return el(
        Fragment,
        {},
        el(
          InspectorControls,
          {},
          // Panneau Dimensions
          el(
            PanelBody,
            { title: "Dimensions", initialOpen: true },
            el(RangeControl, {
              label: "Largeur (px)",
              value: attributes.width,
              onChange: (value) => setAttributes({ width: value }),
              min: 600,
              max: 2000,
              step: 50,
            }),
            el(RangeControl, {
              label: "Hauteur (px)",
              value: attributes.height,
              onChange: (value) => setAttributes({ height: value }),
              min: 400,
              max: 1200,
              step: 50,
            })
          ),

          // Panneau Contenu
          el(
            PanelBody,
            { title: "Types de contenu", initialOpen: true },
            el(CheckboxControl, {
              label: "Articles",
              checked: attributes.postTypes.includes("post"),
              onChange: (checked) => {
                const types = checked
                  ? [...attributes.postTypes, "post"]
                  : attributes.postTypes.filter((t) => t !== "post");
                setAttributes({ postTypes: types });
              },
            }),
            el(CheckboxControl, {
              label: "Articles Archi",
              checked: attributes.postTypes.includes("archi_article"),
              onChange: (checked) => {
                const types = checked
                  ? [...attributes.postTypes, "archi_article"]
                  : attributes.postTypes.filter((t) => t !== "archi_article");
                setAttributes({ postTypes: types });
              },
            }),
            el(CheckboxControl, {
              label: "Illustrations",
              checked: attributes.postTypes.includes("archi_illustration"),
              onChange: (checked) => {
                const types = checked
                  ? [...attributes.postTypes, "archi_illustration"]
                  : attributes.postTypes.filter(
                      (t) => t !== "archi_illustration"
                    );
                setAttributes({ postTypes: types });
              },
            }),
            el(RangeControl, {
              label: "Nombre maximum de nœuds",
              value: attributes.maxNodes,
              onChange: (value) => setAttributes({ maxNodes: value }),
              min: 10,
              max: 500,
              step: 10,
            })
          ),

          // Panneau Catégories
          categories &&
            el(
              PanelBody,
              { title: "Filtrer par catégories", initialOpen: false },
              categories.map((category) =>
                el(CheckboxControl, {
                  key: category.id,
                  label: category.name,
                  checked: attributes.selectedCategories.includes(category.id),
                  onChange: (checked) => {
                    const cats = checked
                      ? [...attributes.selectedCategories, category.id]
                      : attributes.selectedCategories.filter(
                          (c) => c !== category.id
                        );
                    setAttributes({ selectedCategories: cats });
                  },
                })
              )
            ),

          // Panneau Interactivité
          el(
            PanelBody,
            { title: "Interactivité", initialOpen: false },
            el(ToggleControl, {
              label: "Activer les filtres",
              checked: attributes.enableFilters,
              onChange: (value) => setAttributes({ enableFilters: value }),
            }),
            el(ToggleControl, {
              label: "Activer la recherche",
              checked: attributes.enableSearch,
              onChange: (value) => setAttributes({ enableSearch: value }),
            }),
            el(ToggleControl, {
              label: "Activer le zoom",
              checked: attributes.enableZoom,
              onChange: (value) => setAttributes({ enableZoom: value }),
            }),
            el(ToggleControl, {
              label: "Activer le déplacement",
              checked: attributes.enableDrag,
              onChange: (value) => setAttributes({ enableDrag: value }),
            })
          ),

          // Panneau Apparence
          el(
            PanelBody,
            { title: "Apparence", initialOpen: false },
            el(SelectControl, {
              label: "Disposition",
              value: attributes.layout,
              options: [
                { label: "Force dirigée", value: "force" },
                { label: "Circulaire", value: "circular" },
                { label: "Hiérarchique", value: "hierarchical" },
                { label: "Grille", value: "grid" },
              ],
              onChange: (value) => setAttributes({ layout: value }),
            }),
            el(SelectControl, {
              label: "Palette de couleurs",
              value: attributes.colorScheme,
              options: [
                { label: "Par défaut", value: "default" },
                { label: "Pastel", value: "pastel" },
                { label: "Vibrant", value: "vibrant" },
                { label: "Monochrome", value: "monochrome" },
              ],
              onChange: (value) => setAttributes({ colorScheme: value }),
            }),
            el(ToggleControl, {
              label: "Afficher les labels",
              checked: attributes.showLabels,
              onChange: (value) => setAttributes({ showLabels: value }),
            }),
            el(ToggleControl, {
              label: "Afficher les connexions",
              checked: attributes.showConnections,
              onChange: (value) => setAttributes({ showConnections: value }),
            })
          ),

          // Panneau Animation
          el(
            PanelBody,
            { title: "Animation", initialOpen: false },
            el(RangeControl, {
              label: "Durée d'animation (ms)",
              value: attributes.animationDuration,
              onChange: (value) => setAttributes({ animationDuration: value }),
              min: 0,
              max: 3000,
              step: 100,
            }),
            el(RangeControl, {
              label: "Espacement des nœuds",
              value: attributes.nodeSpacing,
              onChange: (value) => setAttributes({ nodeSpacing: value }),
              min: 50,
              max: 300,
              step: 10,
            }),
            el(RangeControl, {
              label: "Force de clustering",
              value: attributes.clusterStrength,
              onChange: (value) => setAttributes({ clusterStrength: value }),
              min: 0,
              max: 50,
              step: 1,
            })
          )
        ),

        // Aperçu du bloc dans l'éditeur
        el(
          "div",
          {
            className: "archi-graph-block-preview",
            style: {
              width: "100%",
              height: Math.min(attributes.height, 400) + "px",
              background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
              borderRadius: "8px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              flexDirection: "column",
              color: "white",
              padding: "20px",
            },
          },
          el(
            "div",
            { style: { fontSize: "48px", marginBottom: "20px" } },
            "🕸️"
          ),
          el(
            "h3",
            { style: { margin: "0 0 10px 0" } },
            "Graphique Interactif Avancé"
          ),
          el(
            "p",
            { style: { margin: "0 0 20px 0", opacity: 0.9 } },
            `${attributes.width}×${attributes.height}px • ${attributes.postTypes.length} types de contenu`
          ),
          el(
            "div",
            {
              style: {
                display: "flex",
                gap: "10px",
                flexWrap: "wrap",
                justifyContent: "center",
              },
            },
            attributes.enableFilters &&
              el(
                "span",
                {
                  style: {
                    background: "rgba(255,255,255,0.2)",
                    padding: "5px 10px",
                    borderRadius: "15px",
                    fontSize: "12px",
                  },
                },
                "🔍 Filtres"
              ),
            attributes.enableSearch &&
              el(
                "span",
                {
                  style: {
                    background: "rgba(255,255,255,0.2)",
                    padding: "5px 10px",
                    borderRadius: "15px",
                    fontSize: "12px",
                  },
                },
                "🔎 Recherche"
              ),
            attributes.enableZoom &&
              el(
                "span",
                {
                  style: {
                    background: "rgba(255,255,255,0.2)",
                    padding: "5px 10px",
                    borderRadius: "15px",
                    fontSize: "12px",
                  },
                },
                "🔍 Zoom"
              ),
            attributes.showConnections &&
              el(
                "span",
                {
                  style: {
                    background: "rgba(255,255,255,0.2)",
                    padding: "5px 10px",
                    borderRadius: "15px",
                    fontSize: "12px",
                  },
                },
                "🔗 Connexions"
              )
          )
        )
      );
    }),

    save: function (props) {
      // Le rendu sera fait par PHP côté serveur
      return null;
    },
  });

  /**
   * Bloc Sélecteur de Nœuds
   */
  registerBlockType("archi-graph/node-selector", {
    title: "Sélecteur de Nœuds",
    icon: "grid-view",
    category: "widgets",
    attributes: {
      selectedNodes: {
        type: "array",
        default: [],
      },
      displayMode: {
        type: "string",
        default: "grid",
      },
      columns: {
        type: "number",
        default: 3,
      },
    },

    edit: function (props) {
      const { attributes, setAttributes } = props;

      return el(
        Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: "Configuration" },
            el(SelectControl, {
              label: "Mode d'affichage",
              value: attributes.displayMode,
              options: [
                { label: "Grille", value: "grid" },
                { label: "Liste", value: "list" },
                { label: "Carrousel", value: "carousel" },
              ],
              onChange: (value) => setAttributes({ displayMode: value }),
            }),
            attributes.displayMode === "grid" &&
              el(RangeControl, {
                label: "Colonnes",
                value: attributes.columns,
                onChange: (value) => setAttributes({ columns: value }),
                min: 1,
                max: 6,
                step: 1,
              })
          )
        ),
        el(
          "div",
          { className: "archi-node-selector-preview" },
          el("h3", {}, "Sélecteur de Nœuds"),
          el("p", {}, `${attributes.selectedNodes.length} nœuds sélectionnés`),
          el(
            Button,
            {
              isPrimary: true,
              onClick: () => {
                // Ouvrir un modal pour sélectionner les nœuds
                alert("Fonctionnalité de sélection en développement");
              },
            },
            "Sélectionner des nœuds"
          )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})();
