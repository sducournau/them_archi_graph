/**
 * Blocs Gutenberg pour les spécifications techniques
 * Éditeur pour LazyBlocks/Gutenberg natif
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const {
  PanelBody,
  TextControl,
  SelectControl,
  TextareaControl,
  Button,
} = wp.components;
const { Fragment } = wp.element;
const { __ } = wp.i18n;

/**
 * Bloc de spécifications de projet architectural
 */
registerBlockType("archi-graph/project-specs", {
  title: __("Spécifications Projet", "archi-graph"),
  icon: "building",
  category: "archi-graph",
  keywords: [
    __("projet", "archi-graph"),
    __("architecture", "archi-graph"),
    __("spécifications", "archi-graph"),
  ],

  attributes: {
    surface: { type: "string", default: "" },
    cost: { type: "string", default: "" },
    client: { type: "string", default: "" },
    location: { type: "string", default: "" },
    startDate: { type: "string", default: "" },
    endDate: { type: "string", default: "" },
    bet: { type: "string", default: "" },
    certifications: { type: "string", default: "" },
    displayStyle: { type: "string", default: "card" },
  },

  edit: ({ attributes, setAttributes }) => {
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody
            title={__("Informations du Projet", "archi-graph")}
            initialOpen={true}
          >
            <TextControl
              label={__("Surface (m²)", "archi-graph")}
              value={attributes.surface}
              onChange={(value) => setAttributes({ surface: value })}
              type="number"
              help={__("Surface du projet en mètres carrés", "archi-graph")}
            />

            <TextControl
              label={__("Budget (€)", "archi-graph")}
              value={attributes.cost}
              onChange={(value) => setAttributes({ cost: value })}
              type="number"
              help={__("Budget total du projet", "archi-graph")}
            />

            <TextControl
              label={__("Maîtrise d'ouvrage", "archi-graph")}
              value={attributes.client}
              onChange={(value) => setAttributes({ client: value })}
              help={__("Nom du client ou maître d'ouvrage", "archi-graph")}
            />

            <TextControl
              label={__("Localisation", "archi-graph")}
              value={attributes.location}
              onChange={(value) => setAttributes({ location: value })}
              help={__("Ville, région ou adresse", "archi-graph")}
            />

            <TextControl
              label={__("Date de début", "archi-graph")}
              value={attributes.startDate}
              onChange={(value) => setAttributes({ startDate: value })}
              type="date"
            />

            <TextControl
              label={__("Date de fin", "archi-graph")}
              value={attributes.endDate}
              onChange={(value) => setAttributes({ endDate: value })}
              type="date"
            />

            <TextControl
              label={__("BET", "archi-graph")}
              value={attributes.bet}
              onChange={(value) => setAttributes({ bet: value })}
              help={__("Bureau d'études techniques", "archi-graph")}
            />

            <TextareaControl
              label={__("Certifications", "archi-graph")}
              value={attributes.certifications}
              onChange={(value) => setAttributes({ certifications: value })}
              help={__(
                "Labels et certifications (HQE, BBC, etc.)",
                "archi-graph"
              )}
            />
          </PanelBody>

          <PanelBody
            title={__("Style d'affichage", "archi-graph")}
            initialOpen={false}
          >
            <SelectControl
              label={__("Type d'affichage", "archi-graph")}
              value={attributes.displayStyle}
              options={[
                { label: __("Carte", "archi-graph"), value: "card" },
                { label: __("Liste", "archi-graph"), value: "list" },
                { label: __("Compact", "archi-graph"), value: "inline" },
              ]}
              onChange={(value) => setAttributes({ displayStyle: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-specs-block-editor archi-specs-project">
          <div className="archi-specs-preview">
            <div className="archi-specs-header">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="currentColor"
              >
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
              </svg>
              <h3>{__("Informations du Projet", "archi-graph")}</h3>
            </div>

            <div className="archi-specs-content">
              {attributes.surface && (
                <div className="spec-item">
                  <strong>{__("Surface:", "archi-graph")}</strong>{" "}
                  {attributes.surface} m²
                </div>
              )}
              {attributes.cost && (
                <div className="spec-item">
                  <strong>{__("Budget:", "archi-graph")}</strong>{" "}
                  {Number(attributes.cost).toLocaleString("fr-FR")} €
                </div>
              )}
              {attributes.client && (
                <div className="spec-item">
                  <strong>{__("Client:", "archi-graph")}</strong>{" "}
                  {attributes.client}
                </div>
              )}
              {attributes.location && (
                <div className="spec-item">
                  <strong>{__("Localisation:", "archi-graph")}</strong>{" "}
                  {attributes.location}
                </div>
              )}
              {(attributes.startDate || attributes.endDate) && (
                <div className="spec-item">
                  <strong>{__("Période:", "archi-graph")}</strong>{" "}
                  {attributes.startDate} - {attributes.endDate}
                </div>
              )}
              {attributes.bet && (
                <div className="spec-item">
                  <strong>{__("BET:", "archi-graph")}</strong> {attributes.bet}
                </div>
              )}

              {!attributes.surface &&
                !attributes.cost &&
                !attributes.client &&
                !attributes.location && (
                  <p className="archi-specs-empty">
                    {__(
                      "Ajoutez les informations du projet dans le panneau de droite →",
                      "archi-graph"
                    )}
                  </p>
                )}
            </div>

            {attributes.certifications && (
              <div className="archi-specs-certifications">
                <h4>{__("Certifications", "archi-graph")}</h4>
                <p>{attributes.certifications}</p>
              </div>
            )}
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});

/**
 * Bloc de spécifications d'illustration
 */
registerBlockType("archi-graph/illustration-specs", {
  title: __("Spécifications Illustration", "archi-graph"),
  icon: "art",
  category: "archi-graph",
  keywords: [
    __("illustration", "archi-graph"),
    __("graphique", "archi-graph"),
    __("spécifications", "archi-graph"),
  ],

  attributes: {
    technique: { type: "string", default: "" },
    dimensions: { type: "string", default: "" },
    software: { type: "string", default: "" },
    projectLink: { type: "string", default: "" },
    duration: { type: "string", default: "" },
    displayStyle: { type: "string", default: "card" },
  },

  edit: ({ attributes, setAttributes }) => {
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody
            title={__("Détails Techniques", "archi-graph")}
            initialOpen={true}
          >
            <TextControl
              label={__("Technique", "archi-graph")}
              value={attributes.technique}
              onChange={(value) => setAttributes({ technique: value })}
              help={__(
                "Ex: Aquarelle, Digital, 3D, Collage...",
                "archi-graph"
              )}
            />

            <TextControl
              label={__("Dimensions", "archi-graph")}
              value={attributes.dimensions}
              onChange={(value) => setAttributes({ dimensions: value })}
              help={__("Ex: 1920x1080px, A4, 50x70cm", "archi-graph")}
            />

            <TextControl
              label={__("Logiciels", "archi-graph")}
              value={attributes.software}
              onChange={(value) => setAttributes({ software: value })}
              help={__(
                "Ex: Photoshop, Illustrator, Blender...",
                "archi-graph"
              )}
            />

            <TextControl
              label={__("Durée de réalisation", "archi-graph")}
              value={attributes.duration}
              onChange={(value) => setAttributes({ duration: value })}
              help={__("Ex: 2 jours, 5 heures...", "archi-graph")}
            />

            <TextControl
              label={__("Lien vers projet associé", "archi-graph")}
              value={attributes.projectLink}
              onChange={(value) => setAttributes({ projectLink: value })}
              type="url"
              help={__("URL vers le projet lié", "archi-graph")}
            />
          </PanelBody>

          <PanelBody
            title={__("Style d'affichage", "archi-graph")}
            initialOpen={false}
          >
            <SelectControl
              label={__("Type d'affichage", "archi-graph")}
              value={attributes.displayStyle}
              options={[
                { label: __("Carte", "archi-graph"), value: "card" },
                { label: __("Liste", "archi-graph"), value: "list" },
                { label: __("Compact", "archi-graph"), value: "inline" },
              ]}
              onChange={(value) => setAttributes({ displayStyle: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div className="archi-specs-block-editor archi-specs-illustration">
          <div className="archi-specs-preview">
            <div className="archi-specs-header">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="currentColor"
              >
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7H7v-2h7v2z" />
              </svg>
              <h3>{__("Détails Techniques", "archi-graph")}</h3>
            </div>

            <div className="archi-specs-content">
              {attributes.technique && (
                <div className="spec-item">
                  <strong>{__("Technique:", "archi-graph")}</strong>{" "}
                  {attributes.technique}
                </div>
              )}
              {attributes.dimensions && (
                <div className="spec-item">
                  <strong>{__("Dimensions:", "archi-graph")}</strong>{" "}
                  {attributes.dimensions}
                </div>
              )}
              {attributes.software && (
                <div className="spec-item">
                  <strong>{__("Logiciels:", "archi-graph")}</strong>{" "}
                  {attributes.software}
                </div>
              )}
              {attributes.duration && (
                <div className="spec-item">
                  <strong>{__("Durée:", "archi-graph")}</strong>{" "}
                  {attributes.duration}
                </div>
              )}

              {!attributes.technique &&
                !attributes.dimensions &&
                !attributes.software && (
                  <p className="archi-specs-empty">
                    {__(
                      "Ajoutez les détails techniques dans le panneau de droite →",
                      "archi-graph"
                    )}
                  </p>
                )}
            </div>

            {attributes.projectLink && (
              <div className="archi-specs-link">
                <a
                  href={attributes.projectLink}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {__("Voir le projet associé", "archi-graph")}
                </a>
              </div>
            )}
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null,
});

/**
 * Bloc de spécifications génériques pour articles
 */
registerBlockType("archi-graph/article-specs", {
  title: __("Spécifications Article", "archi-graph"),
  icon: "admin-settings",
  category: "archi-graph",
  keywords: [
    __("article", "archi-graph"),
    __("spécifications", "archi-graph"),
    __("info", "archi-graph"),
  ],

  attributes: {
    title: {
      type: "string",
      default: __("Spécifications Techniques", "archi-graph"),
    },
    specs: {
      type: "array",
      default: [],
    },
    displayStyle: { type: "string", default: "card" },
  },

  edit: ({ attributes, setAttributes }) => {
    const addSpec = () => {
      const newSpecs = [
        ...attributes.specs,
        { label: "", value: "", icon: "" },
      ];
      setAttributes({ specs: newSpecs });
    };

    const updateSpec = (index, field, value) => {
      const newSpecs = [...attributes.specs];
      newSpecs[index][field] = value;
      setAttributes({ specs: newSpecs });
    };

    const removeSpec = (index) => {
      const newSpecs = attributes.specs.filter((_, i) => i !== index);
      setAttributes({ specs: newSpecs });
    };

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody
            title={__("Configuration", "archi-graph")}
            initialOpen={true}
          >
            <TextControl
              label={__("Titre du bloc", "archi-graph")}
              value={attributes.title}
              onChange={(value) => setAttributes({ title: value })}
            />

            <SelectControl
              label={__("Type d'affichage", "archi-graph")}
              value={attributes.displayStyle}
              options={[
                { label: __("Carte", "archi-graph"), value: "card" },
                { label: __("Liste", "archi-graph"), value: "list" },
                { label: __("Compact", "archi-graph"), value: "inline" },
              ]}
              onChange={(value) => setAttributes({ displayStyle: value })}
            />
          </PanelBody>

          <PanelBody
            title={__("Spécifications", "archi-graph")}
            initialOpen={true}
          >
            {attributes.specs.map((spec, index) => (
              <div
                key={index}
                style={{
                  border: "1px solid #ddd",
                  padding: "10px",
                  marginBottom: "10px",
                  borderRadius: "4px",
                }}
              >
                <TextControl
                  label={__("Label", "archi-graph")}
                  value={spec.label}
                  onChange={(value) => updateSpec(index, "label", value)}
                  placeholder={__("Ex: Technique", "archi-graph")}
                />
                <TextControl
                  label={__("Valeur", "archi-graph")}
                  value={spec.value}
                  onChange={(value) => updateSpec(index, "value", value)}
                  placeholder={__("Ex: Aquarelle sur papier", "archi-graph")}
                />
                <Button
                  isDestructive
                  isSmall
                  onClick={() => removeSpec(index)}
                >
                  {__("Supprimer", "archi-graph")}
                </Button>
              </div>
            ))}

            <Button isPrimary onClick={addSpec}>
              {__("+ Ajouter une spécification", "archi-graph")}
            </Button>
          </PanelBody>
        </InspectorControls>

        <div className="archi-specs-block-editor archi-specs-article">
          <div className="archi-specs-preview">
            <div className="archi-specs-header">
              <h3>{attributes.title}</h3>
            </div>

            <div className="archi-specs-content">
              {attributes.specs.length > 0 ? (
                attributes.specs.map((spec, index) => (
                  <div key={index} className="spec-item">
                    <strong>{spec.label}:</strong> {spec.value}
                  </div>
                ))
              ) : (
                <p className="archi-specs-empty">
                  {__(
                    "Ajoutez des spécifications dans le panneau de droite →",
                    "archi-graph"
                  )}
                </p>
              )}
            </div>
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null,
});
