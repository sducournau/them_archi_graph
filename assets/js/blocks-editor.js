/**
 * Scripts JavaScript pour les blocs Gutenberg personnalisés
 */

// Imports WordPress
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const {
  PanelBody,
  TextControl,
  SelectControl,
  RangeControl,
  ToggleControl,
  Button,
  ButtonGroup,
  CheckboxControl,
  TextareaControl,
} = wp.components;
const { useState, Fragment } = wp.element;
const { __ } = wp.i18n;

/**
 * Bloc Graphique Interactif
 * NOTE: This block is now registered in inc/blocks/graph/interactive-graph.php
 * Keeping this commented to avoid duplicate registration
 */
/*
registerBlockType("archi-graph/interactive-graph", {
  title: __("Graphique Interactif", "archi-graph"),
  description: __(
    "Affiche le graphique interactif avec les projets et illustrations",
    "archi-graph"
  ),
  icon: "networking",
  category: "archi-graph",
  attributes: {
    width: { type: "number", default: 16000 }, // 🔥 Doublé pour viewBox étendu
    height: { type: "number", default: 11200 }, // 🔥 Doublé pour viewBox étendu
    maxArticles: { type: "number", default: 100 },
    enableFilters: { type: "boolean", default: true },
    enableSearch: { type: "boolean", default: true },
    animationDuration: { type: "number", default: 1000 },
    nodeSpacing: { type: "number", default: 200 }, // 🔥 Doublé pour meilleur espacement
    clusterStrength: { type: "number", default: 10 },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const {
      width,
      height,
      maxArticles,
      enableFilters,
      enableSearch,
      animationDuration,
      nodeSpacing,
      clusterStrength,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Dimensions", "archi-graph")}>
            <RangeControl
              label={__("Largeur", "archi-graph")}
              value={width}
              onChange={(value) => setAttributes({ width: value })}
              min={4000} // 🔥 Doublé
              max={20000} // 🔥 Doublé
            />
            <RangeControl
              label={__("Hauteur", "archi-graph")}
              value={height}
              onChange={(value) => setAttributes({ height: value })}
              min={2800} // 🔥 Doublé
              max={16000} // 🔥 Doublé
            />
          </PanelBody>

          <PanelBody title={__("Paramètres", "archi-graph")}>
            <RangeControl
              label={__("Nombre maximum d'articles", "archi-graph")}
              value={maxArticles}
              onChange={(value) => setAttributes({ maxArticles: value })}
              min={10}
              max={500}
            />

            <ToggleControl
              label={__("Activer les filtres", "archi-graph")}
              checked={enableFilters}
              onChange={(value) => setAttributes({ enableFilters: value })}
            />

            <ToggleControl
              label={__("Activer la recherche", "archi-graph")}
              checked={enableSearch}
              onChange={(value) => setAttributes({ enableSearch: value })}
            />
          </PanelBody>

          <PanelBody title={__("Animation", "archi-graph")} initialOpen={false}>
            <RangeControl
              label={__("Durée d'animation (ms)", "archi-graph")}
              value={animationDuration}
              onChange={(value) => setAttributes({ animationDuration: value })}
              min={100}
              max={5000}
            />

            <RangeControl
              label={__("Espacement des nœuds", "archi-graph")}
              value={nodeSpacing}
              onChange={(value) => setAttributes({ nodeSpacing: value })}
              min={50}
              max={200}
            />

            <RangeControl
              label={__("Force de clustering", "archi-graph")}
              value={clusterStrength}
              onChange={(value) => setAttributes({ clusterStrength: value })}
              min={0}
              max={50}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <div
            className="archi-graph-preview"
            style={{
              width: "100%",
              height: Math.min(height, 400) + "px",
              background: "#f0f0f0",
              border: "2px dashed #ccc",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              borderRadius: "8px",
            }}
          >
            <div style={{ textAlign: "center", color: "#666" }}>
              <div style={{ fontSize: "48px", marginBottom: "1rem" }}>🗂️</div>
              <h3>{__("Graphique Interactif", "archi-graph")}</h3>
              <p>
                {maxArticles} {__("articles maximum", "archi-graph")}
              </p>
              <p>
                {width} × {height}px
              </p>
            </div>
          </div>
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null; // Rendu côté serveur
  },
});
*/

/**
 * Bloc Vitrine de Projets
 * NOTE: This block is now registered in inc/blocks/projects/project-showcase.php
 * Keeping this commented to avoid duplicate registration
 */
/*
registerBlockType("archi-graph/project-showcase", {
  title: __("Vitrine de Projets", "archi-graph"),
  description: __(
    "Affiche une sélection de projets en grille ou liste",
    "archi-graph"
  ),
  icon: "portfolio",
  category: "archi-graph",
  attributes: {
    layout: { type: "string", default: "grid" },
    columns: { type: "number", default: 3 },
    showDescription: { type: "boolean", default: true },
    showMetadata: { type: "boolean", default: true },
    imageSize: { type: "string", default: "medium" },
    selectedProjects: { type: "array", default: [] },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const {
      layout,
      columns,
      showDescription,
      showMetadata,
      imageSize,
      selectedProjects,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Mise en page", "archi-graph")}>
            <SelectControl
              label={__("Layout", "archi-graph")}
              value={layout}
              options={[
                { label: __("Grille", "archi-graph"), value: "grid" },
                { label: __("Liste", "archi-graph"), value: "list" },
                { label: __("Carrousel", "archi-graph"), value: "carousel" },
              ]}
              onChange={(value) => setAttributes({ layout: value })}
            />

            {layout === "grid" && (
              <RangeControl
                label={__("Nombre de colonnes", "archi-graph")}
                value={columns}
                onChange={(value) => setAttributes({ columns: value })}
                min={1}
                max={4}
              />
            )}

            <SelectControl
              label={__("Taille d'image", "archi-graph")}
              value={imageSize}
              options={[
                { label: __("Miniature", "archi-graph"), value: "thumbnail" },
                { label: __("Moyenne", "archi-graph"), value: "medium" },
                { label: __("Grande", "archi-graph"), value: "large" },
              ]}
              onChange={(value) => setAttributes({ imageSize: value })}
            />
          </PanelBody>

          <PanelBody title={__("Affichage", "archi-graph")}>
            <ToggleControl
              label={__("Afficher la description", "archi-graph")}
              checked={showDescription}
              onChange={(value) => setAttributes({ showDescription: value })}
            />

            <ToggleControl
              label={__("Afficher les métadonnées", "archi-graph")}
              checked={showMetadata}
              onChange={(value) => setAttributes({ showMetadata: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <h3>{__("Vitrine de Projets", "archi-graph")}</h3>
          <div
            style={{
              display: "grid",
              gridTemplateColumns: `repeat(${columns}, 1fr)`,
              gap: "1rem",
              margin: "1rem 0",
            }}
          >
            {[1, 2, 3].map((i) => (
              <div
                key={i}
                style={{
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                  padding: "1rem",
                  background: "#fff",
                }}
              >
                <div
                  style={{
                    height: "150px",
                    background: "#f0f0f0",
                    borderRadius: "4px",
                    marginBottom: "0.5rem",
                  }}
                ></div>
                <h4>Projet {i}</h4>
                {showDescription && <p>Description du projet...</p>}
                {showMetadata && (
                  <div style={{ fontSize: "0.85rem", color: "#666" }}>
                    📐 150m² • 💰 250k€ • 📍 Lyon
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null;
  },
});
*/

/**
 * Bloc Grille d'Articles
 */
registerBlockType("archi-graph/illustration-grid", {
  title: __("Grille d'Illustrations", "archi-graph"),
  description: __(
    "Affiche les illustrations et explorations graphiques en grille",
    "archi-graph"
  ),
  icon: "grid-view",
  category: "archi-graph",
  attributes: {
    postsPerPage: { type: "number", default: 6 },
    columns: { type: "number", default: 2 },
    showExcerpt: { type: "boolean", default: true },
    showDate: { type: "boolean", default: true },
    showAuthor: { type: "boolean", default: false },
    showCategories: { type: "boolean", default: true },
    orderBy: { type: "string", default: "date" },
    order: { type: "string", default: "DESC" },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const {
      postsPerPage,
      columns,
      showExcerpt,
      showDate,
      showAuthor,
      showCategories,
      orderBy,
      order,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Paramètres de requête", "archi-graph")}>
            <RangeControl
              label={__("Nombre d'articles", "archi-graph")}
              value={postsPerPage}
              onChange={(value) => setAttributes({ postsPerPage: value })}
              min={1}
              max={12}
            />

            <SelectControl
              label={__("Trier par", "archi-graph")}
              value={orderBy}
              options={[
                { label: __("Date", "archi-graph"), value: "date" },
                { label: __("Titre", "archi-graph"), value: "title" },
                { label: __("Aléatoire", "archi-graph"), value: "rand" },
              ]}
              onChange={(value) => setAttributes({ orderBy: value })}
            />

            <SelectControl
              label={__("Ordre", "archi-graph")}
              value={order}
              options={[
                { label: __("Décroissant", "archi-graph"), value: "DESC" },
                { label: __("Croissant", "archi-graph"), value: "ASC" },
              ]}
              onChange={(value) => setAttributes({ order: value })}
            />
          </PanelBody>

          <PanelBody title={__("Mise en page", "archi-graph")}>
            <RangeControl
              label={__("Colonnes", "archi-graph")}
              value={columns}
              onChange={(value) => setAttributes({ columns: value })}
              min={1}
              max={4}
            />
          </PanelBody>

          <PanelBody title={__("Affichage", "archi-graph")}>
            <ToggleControl
              label={__("Afficher l'extrait", "archi-graph")}
              checked={showExcerpt}
              onChange={(value) => setAttributes({ showExcerpt: value })}
            />

            <ToggleControl
              label={__("Afficher la date", "archi-graph")}
              checked={showDate}
              onChange={(value) => setAttributes({ showDate: value })}
            />

            <ToggleControl
              label={__("Afficher l'auteur", "archi-graph")}
              checked={showAuthor}
              onChange={(value) => setAttributes({ showAuthor: value })}
            />

            <ToggleControl
              label={__("Afficher les catégories", "archi-graph")}
              checked={showCategories}
              onChange={(value) => setAttributes({ showCategories: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <h3>{__("Grille d'Articles", "archi-graph")}</h3>
          <div
            style={{
              display: "grid",
              gridTemplateColumns: `repeat(${columns}, 1fr)`,
              gap: "1rem",
              margin: "1rem 0",
            }}
          >
            {Array.from({ length: Math.min(postsPerPage, 4) }).map((_, i) => (
              <div
                key={i}
                style={{
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                  overflow: "hidden",
                  background: "#fff",
                }}
              >
                <div
                  style={{
                    height: "120px",
                    background: "#f0f0f0",
                  }}
                ></div>
                <div style={{ padding: "1rem" }}>
                  <h4 style={{ margin: "0 0 0.5rem 0" }}>Article {i + 1}</h4>
                  {showDate && (
                    <div
                      style={{
                        fontSize: "0.8rem",
                        color: "#666",
                        marginBottom: "0.5rem",
                      }}
                    >
                      📅 {new Date().toLocaleDateString()}
                    </div>
                  )}
                  {showExcerpt && (
                    <p style={{ margin: "0 0 0.5rem 0", fontSize: "0.9rem" }}>
                      Extrait de l'article...
                    </p>
                  )}
                  {showCategories && (
                    <div style={{ fontSize: "0.8rem" }}>
                      <span
                        style={{
                          background: "#e9ecef",
                          padding: "0.2rem 0.5rem",
                          borderRadius: "12px",
                        }}
                      >
                        Catégorie
                      </span>
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null;
  },
});

/**
 * Bloc Timeline
 */
registerBlockType("archi-graph/timeline", {
  title: __("Timeline", "archi-graph"),
  description: __(
    "Affiche une timeline d'événements ou de projets",
    "archi-graph"
  ),
  icon: "clock",
  category: "archi-graph",
  attributes: {
    timelineItems: { type: "array", default: [] },
    orientation: { type: "string", default: "vertical" },
    showDates: { type: "boolean", default: true },
    alternating: { type: "boolean", default: true },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const { timelineItems, orientation, showDates, alternating } = attributes;

    const addTimelineItem = () => {
      const newItems = [
        ...timelineItems,
        {
          id: Date.now(),
          date: "",
          title: __("Nouveau élément", "archi-graph"),
          description: "",
        },
      ];
      setAttributes({ timelineItems: newItems });
    };

    const updateTimelineItem = (index, field, value) => {
      const newItems = [...timelineItems];
      newItems[index][field] = value;
      setAttributes({ timelineItems: newItems });
    };

    const removeTimelineItem = (index) => {
      const newItems = timelineItems.filter((_, i) => i !== index);
      setAttributes({ timelineItems: newItems });
    };

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Paramètres Timeline", "archi-graph")}>
            <SelectControl
              label={__("Orientation", "archi-graph")}
              value={orientation}
              options={[
                { label: __("Verticale", "archi-graph"), value: "vertical" },
                {
                  label: __("Horizontale", "archi-graph"),
                  value: "horizontal",
                },
              ]}
              onChange={(value) => setAttributes({ orientation: value })}
            />

            <ToggleControl
              label={__("Afficher les dates", "archi-graph")}
              checked={showDates}
              onChange={(value) => setAttributes({ showDates: value })}
            />

            {orientation === "vertical" && (
              <ToggleControl
                label={__("Alternance gauche/droite", "archi-graph")}
                checked={alternating}
                onChange={(value) => setAttributes({ alternating: value })}
              />
            )}
          </PanelBody>

          <PanelBody title={__("Éléments de la Timeline", "archi-graph")}>
            <Button isPrimary onClick={addTimelineItem}>
              {__("Ajouter un élément", "archi-graph")}
            </Button>
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              marginBottom: "1rem",
            }}
          >
            <h3>{__("Timeline", "archi-graph")}</h3>
            <Button isPrimary onClick={addTimelineItem}>
              {__("Ajouter", "archi-graph")}
            </Button>
          </div>

          {timelineItems.length === 0 ? (
            <div
              style={{
                padding: "2rem",
                textAlign: "center",
                border: "2px dashed #ddd",
                borderRadius: "8px",
                color: "#666",
              }}
            >
              {__(
                'Aucun élément de timeline. Cliquez sur "Ajouter" pour commencer.',
                "archi-graph"
              )}
            </div>
          ) : (
            <div style={{ position: "relative" }}>
              {timelineItems.map((item, index) => (
                <div
                  key={item.id || index}
                  style={{
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                    padding: "1rem",
                    marginBottom: "1rem",
                    background: "#fff",
                  }}
                >
                  <div
                    style={{
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "flex-start",
                      marginBottom: "0.5rem",
                    }}
                  >
                    <strong>
                      {__("Élément", "archi-graph")} {index + 1}
                    </strong>
                    <Button
                      isDestructive
                      isSmall
                      onClick={() => removeTimelineItem(index)}
                    >
                      {__("Supprimer", "archi-graph")}
                    </Button>
                  </div>

                  <TextControl
                    label={__("Date", "archi-graph")}
                    value={item.date || ""}
                    onChange={(value) =>
                      updateTimelineItem(index, "date", value)
                    }
                    placeholder="2024"
                  />

                  <TextControl
                    label={__("Titre", "archi-graph")}
                    value={item.title || ""}
                    onChange={(value) =>
                      updateTimelineItem(index, "title", value)
                    }
                    placeholder={__("Titre de l'événement", "archi-graph")}
                  />

                  <TextareaControl
                    label={__("Description", "archi-graph")}
                    value={item.description || ""}
                    onChange={(value) =>
                      updateTimelineItem(index, "description", value)
                    }
                    placeholder={__(
                      "Description de l'événement",
                      "archi-graph"
                    )}
                    rows={3}
                  />
                </div>
              ))}
            </div>
          )}
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null;
  },
});

/**
 * Bloc Avant/Après
 */
registerBlockType("archi-graph/before-after", {
  title: __("Avant/Après", "archi-graph"),
  description: __(
    "Comparaison d'images avant/après avec curseur interactif",
    "archi-graph"
  ),
  icon: "image-flip-horizontal",
  category: "archi-graph",
  attributes: {
    beforeImage: { type: "object", default: null },
    afterImage: { type: "object", default: null },
    beforeLabel: { type: "string", default: "Avant" },
    afterLabel: { type: "string", default: "Après" },
    sliderPosition: { type: "number", default: 50 },
    orientation: { type: "string", default: "vertical" },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const {
      beforeImage,
      afterImage,
      beforeLabel,
      afterLabel,
      sliderPosition,
      orientation,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Configuration", "archi-graph")}>
            <SelectControl
              label={__("Orientation du curseur", "archi-graph")}
              value={orientation}
              options={[
                {
                  label: __("Vertical (gauche/droite)", "archi-graph"),
                  value: "vertical",
                },
                {
                  label: __("Horizontal (haut/bas)", "archi-graph"),
                  value: "horizontal",
                },
              ]}
              onChange={(value) => setAttributes({ orientation: value })}
            />

            <RangeControl
              label={__("Position initiale du curseur (%)", "archi-graph")}
              value={sliderPosition}
              onChange={(value) => setAttributes({ sliderPosition: value })}
              min={0}
              max={100}
            />

            <TextControl
              label={__('Label "Avant"', "archi-graph")}
              value={beforeLabel}
              onChange={(value) => setAttributes({ beforeLabel: value })}
            />

            <TextControl
              label={__('Label "Après"', "archi-graph")}
              value={afterLabel}
              onChange={(value) => setAttributes({ afterLabel: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <h3>{__("Comparaison Avant/Après", "archi-graph")}</h3>

          <div
            style={{
              display: "grid",
              gridTemplateColumns: "1fr 1fr",
              gap: "1rem",
              marginBottom: "1rem",
            }}
          >
            <div>
              <h4>{beforeLabel}</h4>
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) => setAttributes({ beforeImage: media })}
                  allowedTypes={["image"]}
                  value={beforeImage?.id}
                  render={({ open }) => (
                    <div
                      onClick={open}
                      style={{
                        height: "200px",
                        border: "2px dashed #ddd",
                        borderRadius: "4px",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        cursor: "pointer",
                        background: beforeImage
                          ? `url(${beforeImage.url}) center/cover`
                          : "#f9f9f9",
                      }}
                    >
                      {!beforeImage && (
                        <div style={{ textAlign: "center", color: "#666" }}>
                          <div
                            style={{ fontSize: "24px", marginBottom: "0.5rem" }}
                          >
                            📷
                          </div>
                          {__('Sélectionner l\'image "avant"', "archi-graph")}
                        </div>
                      )}
                    </div>
                  )}
                />
              </MediaUploadCheck>
            </div>

            <div>
              <h4>{afterLabel}</h4>
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) => setAttributes({ afterImage: media })}
                  allowedTypes={["image"]}
                  value={afterImage?.id}
                  render={({ open }) => (
                    <div
                      onClick={open}
                      style={{
                        height: "200px",
                        border: "2px dashed #ddd",
                        borderRadius: "4px",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        cursor: "pointer",
                        background: afterImage
                          ? `url(${afterImage.url}) center/cover`
                          : "#f9f9f9",
                      }}
                    >
                      {!afterImage && (
                        <div style={{ textAlign: "center", color: "#666" }}>
                          <div
                            style={{ fontSize: "24px", marginBottom: "0.5rem" }}
                          >
                            📷
                          </div>
                          {__('Sélectionner l\'image "après"', "archi-graph")}
                        </div>
                      )}
                    </div>
                  )}
                />
              </MediaUploadCheck>
            </div>
          </div>

          {beforeImage && afterImage && (
            <div
              style={{
                padding: "1rem",
                background: "#f0f8ff",
                borderRadius: "4px",
                textAlign: "center",
              }}
            >
              ✅{" "}
              {__(
                "Comparaison configurée ! Le curseur interactif sera affiché sur le front-end.",
                "archi-graph"
              )}
            </div>
          )}
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null;
  },
});

/**
 * Bloc Spécifications Techniques
 */
registerBlockType("archi-graph/technical-specs", {
  title: __("Spécifications Techniques", "archi-graph"),
  description: __(
    "Affiche les spécifications techniques d'un projet",
    "archi-graph"
  ),
  icon: "editor-table",
  category: "archi-graph",
  attributes: {
    specifications: { type: "array", default: [] },
    layout: { type: "string", default: "table" },
    showIcons: { type: "boolean", default: true },
    groupByCategory: { type: "boolean", default: false },
  },
  edit: function (props) {
    const { attributes, setAttributes } = props;
    const { specifications, layout, showIcons, groupByCategory } = attributes;

    const addSpecification = () => {
      const newSpecs = [
        ...specifications,
        {
          id: Date.now(),
          label: "",
          value: "",
          unit: "",
          category: "",
          icon: "",
        },
      ];
      setAttributes({ specifications: newSpecs });
    };

    const updateSpecification = (index, field, value) => {
      const newSpecs = [...specifications];
      newSpecs[index][field] = value;
      setAttributes({ specifications: newSpecs });
    };

    const removeSpecification = (index) => {
      const newSpecs = specifications.filter((_, i) => i !== index);
      setAttributes({ specifications: newSpecs });
    };

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Mise en page", "archi-graph")}>
            <SelectControl
              label={__("Layout", "archi-graph")}
              value={layout}
              options={[
                { label: __("Tableau", "archi-graph"), value: "table" },
                { label: __("Cartes", "archi-graph"), value: "cards" },
              ]}
              onChange={(value) => setAttributes({ layout: value })}
            />

            <ToggleControl
              label={__("Afficher les icônes", "archi-graph")}
              checked={showIcons}
              onChange={(value) => setAttributes({ showIcons: value })}
            />

            <ToggleControl
              label={__("Grouper par catégorie", "archi-graph")}
              checked={groupByCategory}
              onChange={(value) => setAttributes({ groupByCategory: value })}
            />
          </PanelBody>

          <PanelBody title={__("Spécifications", "archi-graph")}>
            <Button isPrimary onClick={addSpecification}>
              {__("Ajouter une spécification", "archi-graph")}
            </Button>
          </PanelBody>
        </InspectorControls>

        <div className="archi-block-preview">
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              marginBottom: "1rem",
            }}
          >
            <h3>{__("Spécifications Techniques", "archi-graph")}</h3>
            <Button isPrimary onClick={addSpecification}>
              {__("Ajouter", "archi-graph")}
            </Button>
          </div>

          {specifications.length === 0 ? (
            <div
              style={{
                padding: "2rem",
                textAlign: "center",
                border: "2px dashed #ddd",
                borderRadius: "8px",
                color: "#666",
              }}
            >
              {__(
                'Aucune spécification. Cliquez sur "Ajouter" pour commencer.',
                "archi-graph"
              )}
            </div>
          ) : (
            <div>
              {specifications.map((spec, index) => (
                <div
                  key={spec.id || index}
                  style={{
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                    padding: "1rem",
                    marginBottom: "1rem",
                    background: "#fff",
                  }}
                >
                  <div
                    style={{
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "flex-start",
                      marginBottom: "0.5rem",
                    }}
                  >
                    <strong>
                      {__("Spécification", "archi-graph")} {index + 1}
                    </strong>
                    <Button
                      isDestructive
                      isSmall
                      onClick={() => removeSpecification(index)}
                    >
                      {__("Supprimer", "archi-graph")}
                    </Button>
                  </div>

                  <div
                    style={{
                      display: "grid",
                      gridTemplateColumns: "1fr 1fr",
                      gap: "1rem",
                      marginBottom: "1rem",
                    }}
                  >
                    <TextControl
                      label={__("Label", "archi-graph")}
                      value={spec.label || ""}
                      onChange={(value) =>
                        updateSpecification(index, "label", value)
                      }
                      placeholder={__("Ex: Surface habitable", "archi-graph")}
                    />

                    <TextControl
                      label={__("Valeur", "archi-graph")}
                      value={spec.value || ""}
                      onChange={(value) =>
                        updateSpecification(index, "value", value)
                      }
                      placeholder={__("Ex: 150", "archi-graph")}
                    />
                  </div>

                  <div
                    style={{
                      display: "grid",
                      gridTemplateColumns: "1fr 1fr 1fr",
                      gap: "1rem",
                    }}
                  >
                    <TextControl
                      label={__("Unité", "archi-graph")}
                      value={spec.unit || ""}
                      onChange={(value) =>
                        updateSpecification(index, "unit", value)
                      }
                      placeholder={__("Ex: m²", "archi-graph")}
                    />

                    <TextControl
                      label={__("Catégorie", "archi-graph")}
                      value={spec.category || ""}
                      onChange={(value) =>
                        updateSpecification(index, "category", value)
                      }
                      placeholder={__("Ex: Dimensions", "archi-graph")}
                    />

                    <TextControl
                      label={__("Icône (classe CSS)", "archi-graph")}
                      value={spec.icon || ""}
                      onChange={(value) =>
                        updateSpecification(index, "icon", value)
                      }
                      placeholder={__("Ex: fas fa-home", "archi-graph")}
                    />
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </Fragment>
    );
  },
  save: function () {
    return null;
  },
});

// Styles pour l'éditeur
wp.domReady(() => {
  // Ajouter des styles personnalisés à l'éditeur
  const editorStyle = document.createElement("style");
  editorStyle.textContent = `
        .archi-block-preview {
            padding: 1rem;
            border: 1px solid #e2e4e7;
            border-radius: 4px;
            background: #fff;
            margin: 1rem 0;
        }
        
        .archi-block-preview h3 {
            margin-top: 0;
            color: #1e1e1e;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 0.5rem;
        }
        
        .archi-graph-preview {
            position: relative;
            overflow: hidden;
        }
        
        .archi-graph-preview::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 40%, rgba(0, 115, 170, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(231, 76, 60, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 50% 80%, rgba(52, 152, 219, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
    `;
  document.head.appendChild(editorStyle);
});
