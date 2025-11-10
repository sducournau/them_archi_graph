import { registerBlockType } from "@wordpress/blocks";
import { 
  InspectorControls, 
  MediaUpload, 
  MediaPlaceholder, 
  RichText,
  useBlockProps 
} from "@wordpress/block-editor";
import { 
  PanelBody, 
  Button, 
  RangeControl, 
  SelectControl, 
  ToggleControl,
  ButtonGroup
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * Bloc Image
 * Combine toutes les fonctionnalités d'images en un seul bloc polyvalent
 * 
 * Modes disponibles:
 * - standard: Image simple
 * - parallax-scroll: Effet parallax au défilement
 * - parallax-fixed: Fond fixe (background-attachment: fixed)
 * - zoom: Zoom au survol
 * - comparison: Comparaison avant/après avec slider
 * - cover: Couverture avec overlay et texte
 */
registerBlockType("archi-graph/image-block", {
  apiVersion: 2,
  title: __("Image Block", "archi-graph"),
  description: __(
    "Bloc d'image polyvalent avec effets parallax, zoom, comparaison et plus",
    "archi-graph"
  ),
  icon: "format-image",
  category: "archi-graph",
  keywords: [
    __("image", "archi-graph"),
    __("parallax", "archi-graph"),
    __("photo", "archi-graph"),
    __("comparison", "archi-graph"),
    __("avant après", "archi-graph"),
  ],
  supports: {
    align: ["wide", "full"],
    spacing: {
      padding: true,
      margin: true,
    },
  },

  attributes: {
    // Mode d'affichage
    displayMode: {
      type: "string",
      default: "standard", // standard, parallax-scroll, parallax-fixed, zoom, comparison, cover
    },

    // Image principale
    imageUrl: {
      type: "string",
      default: "",
    },
    imageId: {
      type: "number",
    },
    imageAlt: {
      type: "string",
      default: "",
    },

    // Image secondaire (pour mode comparison)
    secondImageUrl: {
      type: "string",
      default: "",
    },
    secondImageId: {
      type: "number",
    },
    secondImageAlt: {
      type: "string",
      default: "",
    },

    // Dimensions
    heightMode: {
      type: "string",
      default: "auto", // auto, full-viewport, custom
    },
    customHeight: {
      type: "number",
      default: 600,
    },
    objectFit: {
      type: "string",
      default: "cover", // cover, contain, fill
    },

    // Effets Parallax
    parallaxSpeed: {
      type: "number",
      default: 0.5, // 0 = lent, 1 = rapide
    },

    // Overlay
    overlayEnabled: {
      type: "boolean",
      default: false,
    },
    overlayColor: {
      type: "string",
      default: "#000000",
    },
    overlayOpacity: {
      type: "number",
      default: 30,
    },

    // Texte superposé
    textEnabled: {
      type: "boolean",
      default: false,
    },
    textContent: {
      type: "string",
      default: "",
    },
    textPosition: {
      type: "string",
      default: "center", // center, top, bottom, top-left, top-right, bottom-left, bottom-right
    },
    textColor: {
      type: "string",
      default: "#ffffff",
    },
    textSize: {
      type: "number",
      default: 32, // en pixels
    },
    textWeight: {
      type: "string",
      default: "600", // 300, 400, 500, 600, 700, 800
    },
    textShadow: {
      type: "boolean",
      default: true,
    },
    textPadding: {
      type: "number",
      default: 40, // en pixels
    },
    textMaxWidth: {
      type: "number",
      default: 80, // en %
    },
    textAlign: {
      type: "string",
      default: "center", // left, center, right
    },

    // Mode Comparison
    comparisonOrientation: {
      type: "string",
      default: "vertical", // vertical, horizontal
    },
    comparisonInitialPosition: {
      type: "number",
      default: 50,
    },
    comparisonShowLabels: {
      type: "boolean",
      default: true,
    },
    comparisonBeforeLabel: {
      type: "string",
      default: __("Avant", "archi-graph"),
    },
    comparisonAfterLabel: {
      type: "string",
      default: __("Après", "archi-graph"),
    },
    comparisonHandleColor: {
      type: "string",
      default: "#ffffff",
    },

    // Alignement
    align: {
      type: "string",
      default: "full",
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      displayMode,
      imageUrl,
      imageId,
      imageAlt,
      secondImageUrl,
      secondImageId,
      secondImageAlt,
      heightMode,
      customHeight,
      objectFit,
      parallaxSpeed,
      overlayEnabled,
      overlayColor,
      overlayOpacity,
      textEnabled,
      textContent,
      textPosition,
      textColor,
      textSize,
      textWeight,
      textShadow,
      textPadding,
      textMaxWidth,
      textAlign,
      comparisonOrientation,
      comparisonInitialPosition,
      comparisonShowLabels,
      comparisonBeforeLabel,
      comparisonAfterLabel,
      comparisonHandleColor,
    } = attributes;

    const blockProps = useBlockProps({
      className: `archi-image-block-editor mode-${displayMode}`,
    });

    // Handlers
    const onSelectImage = (media) => {
      setAttributes({
        imageUrl: media.url,
        imageId: media.id,
        imageAlt: media.alt || "",
      });
    };

    const onSelectSecondImage = (media) => {
      setAttributes({
        secondImageUrl: media.url,
        secondImageId: media.id,
        secondImageAlt: media.alt || "",
      });
    };

    const onRemoveImage = () => {
      setAttributes({
        imageUrl: "",
        imageId: null,
        imageAlt: "",
      });
    };

    const onRemoveSecondImage = () => {
      setAttributes({
        secondImageUrl: "",
        secondImageId: null,
        secondImageAlt: "",
      });
    };

    // Mode label helper
    const getModeLabel = () => {
      const labels = {
        standard: __("Standard", "archi-graph"),
        "parallax-scroll": __("Parallax Scroll", "archi-graph"),
        "parallax-fixed": __("Fond Fixe", "archi-graph"),
        zoom: __("Zoom", "archi-graph"),
        comparison: __("Avant/Après", "archi-graph"),
        cover: __("Couverture", "archi-graph"),
      };
      return labels[displayMode] || displayMode;
    };

    return (
      <div {...blockProps}>
        <InspectorControls>
          {/* Mode d'affichage */}
          <PanelBody
            title={__("Mode d'affichage", "archi-graph")}
            initialOpen={true}
          >
            <SelectControl
              label={__("Type d'effet", "archi-graph")}
              value={displayMode}
              options={[
                {
                  label: __("Standard (image simple)", "archi-graph"),
                  value: "standard",
                },
                {
                  label: __("Parallax au défilement", "archi-graph"),
                  value: "parallax-scroll",
                },
                {
                  label: __("Fond fixe (parallax)", "archi-graph"),
                  value: "parallax-fixed",
                },
                {
                  label: __("Zoom au survol", "archi-graph"),
                  value: "zoom",
                },
                {
                  label: __("Comparaison avant/après", "archi-graph"),
                  value: "comparison",
                },
                {
                  label: __("Couverture avec overlay", "archi-graph"),
                  value: "cover",
                },
              ]}
              onChange={(value) => setAttributes({ displayMode: value })}
              help={__(
                "Choisissez le type d'effet pour l'image",
                "archi-graph"
              )}
            />

            {displayMode === "parallax-scroll" && (
              <RangeControl
                label={__("Vitesse du parallax", "archi-graph")}
                value={parallaxSpeed}
                onChange={(value) => setAttributes({ parallaxSpeed: value })}
                min={0}
                max={1}
                step={0.1}
                help={__("0 = très lent, 1 = rapide", "archi-graph")}
              />
            )}
          </PanelBody>

          {/* Dimensions */}
          <PanelBody title={__("Dimensions", "archi-graph")}>
            <SelectControl
              label={__("Mode de hauteur", "archi-graph")}
              value={heightMode}
              options={[
                {
                  label: __("Automatique (ratio image)", "archi-graph"),
                  value: "auto",
                },
                {
                  label: __("Pleine hauteur (100vh)", "archi-graph"),
                  value: "full-viewport",
                },
                {
                  label: __("Hauteur personnalisée", "archi-graph"),
                  value: "custom",
                },
              ]}
              onChange={(value) => setAttributes({ heightMode: value })}
            />

            {heightMode === "custom" && (
              <RangeControl
                label={__("Hauteur personnalisée (px)", "archi-graph")}
                value={customHeight}
                onChange={(value) => setAttributes({ customHeight: value })}
                min={200}
                max={1200}
                step={50}
              />
            )}

            <SelectControl
              label={__("Ajustement de l'image", "archi-graph")}
              value={objectFit}
              options={[
                {
                  label: __("Couvrir (cover)", "archi-graph"),
                  value: "cover",
                },
                {
                  label: __("Contenir (contain)", "archi-graph"),
                  value: "contain",
                },
                { label: __("Remplir (fill)", "archi-graph"), value: "fill" },
              ]}
              onChange={(value) => setAttributes({ objectFit: value })}
            />
          </PanelBody>

          {/* Overlay */}
          <PanelBody title={__("Overlay", "archi-graph")} initialOpen={false}>
            <ToggleControl
              label={__("Activer l'overlay", "archi-graph")}
              checked={overlayEnabled}
              onChange={(value) => setAttributes({ overlayEnabled: value })}
            />

            {overlayEnabled && (
              <>
                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Couleur de l'overlay", "archi-graph")}</label>
                  <input
                    type="color"
                    value={overlayColor}
                    onChange={(e) =>
                      setAttributes({ overlayColor: e.target.value })
                    }
                    style={{ width: "100%", height: "40px", marginTop: "5px" }}
                  />
                </div>
                <RangeControl
                  label={__("Opacité (%)", "archi-graph")}
                  value={overlayOpacity}
                  onChange={(value) =>
                    setAttributes({ overlayOpacity: value })
                  }
                  min={0}
                  max={100}
                  step={5}
                />
              </>
            )}
          </PanelBody>

          {/* Texte superposé */}
          <PanelBody
            title={__("Texte superposé", "archi-graph")}
            initialOpen={false}
          >
            <ToggleControl
              label={__("Afficher du texte", "archi-graph")}
              checked={textEnabled}
              onChange={(value) => setAttributes({ textEnabled: value })}
            />

            {textEnabled && (
              <>
                <SelectControl
                  label={__("Position du texte", "archi-graph")}
                  value={textPosition}
                  options={[
                    { label: __("Centre", "archi-graph"), value: "center" },
                    { label: __("Haut", "archi-graph"), value: "top" },
                    { label: __("Bas", "archi-graph"), value: "bottom" },
                    {
                      label: __("Haut gauche", "archi-graph"),
                      value: "top-left",
                    },
                    {
                      label: __("Haut droite", "archi-graph"),
                      value: "top-right",
                    },
                    {
                      label: __("Bas gauche", "archi-graph"),
                      value: "bottom-left",
                    },
                    {
                      label: __("Bas droite", "archi-graph"),
                      value: "bottom-right",
                    },
                  ]}
                  onChange={(value) => setAttributes({ textPosition: value })}
                />

                <RangeControl
                  label={__("Taille du texte (px)", "archi-graph")}
                  value={textSize}
                  onChange={(value) => setAttributes({ textSize: value })}
                  min={14}
                  max={100}
                  step={2}
                />

                <SelectControl
                  label={__("Épaisseur du texte", "archi-graph")}
                  value={textWeight}
                  options={[
                    { label: __("Ultra léger (300)", "archi-graph"), value: "300" },
                    { label: __("Normal (400)", "archi-graph"), value: "400" },
                    { label: __("Moyen (500)", "archi-graph"), value: "500" },
                    { label: __("Semi-gras (600)", "archi-graph"), value: "600" },
                    { label: __("Gras (700)", "archi-graph"), value: "700" },
                    { label: __("Extra-gras (800)", "archi-graph"), value: "800" },
                  ]}
                  onChange={(value) => setAttributes({ textWeight: value })}
                />

                <SelectControl
                  label={__("Alignement du texte", "archi-graph")}
                  value={textAlign}
                  options={[
                    { label: __("Gauche", "archi-graph"), value: "left" },
                    { label: __("Centre", "archi-graph"), value: "center" },
                    { label: __("Droite", "archi-graph"), value: "right" },
                  ]}
                  onChange={(value) => setAttributes({ textAlign: value })}
                />

                <RangeControl
                  label={__("Espacement depuis les bords (px)", "archi-graph")}
                  value={textPadding}
                  onChange={(value) => setAttributes({ textPadding: value })}
                  min={0}
                  max={100}
                  step={5}
                />

                <RangeControl
                  label={__("Largeur maximale du texte (%)", "archi-graph")}
                  value={textMaxWidth}
                  onChange={(value) => setAttributes({ textMaxWidth: value })}
                  min={30}
                  max={100}
                  step={5}
                />

                <ToggleControl
                  label={__("Ombre portée du texte", "archi-graph")}
                  checked={textShadow}
                  onChange={(value) => setAttributes({ textShadow: value })}
                  help={__("Améliore la lisibilité du texte sur l'image", "archi-graph")}
                />

                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Couleur du texte", "archi-graph")}</label>
                  <input
                    type="color"
                    value={textColor}
                    onChange={(e) =>
                      setAttributes({ textColor: e.target.value })
                    }
                    style={{ width: "100%", height: "40px", marginTop: "5px" }}
                  />
                </div>
              </>
            )}
          </PanelBody>

          {/* Options de comparaison */}
          {displayMode === "comparison" && (
            <PanelBody
              title={__("Options de comparaison", "archi-graph")}
              initialOpen={true}
            >
              <SelectControl
                label={__("Orientation", "archi-graph")}
                value={comparisonOrientation}
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
                onChange={(value) =>
                  setAttributes({ comparisonOrientation: value })
                }
              />

              <RangeControl
                label={__("Position initiale (%)", "archi-graph")}
                value={comparisonInitialPosition}
                onChange={(value) =>
                  setAttributes({ comparisonInitialPosition: value })
                }
                min={0}
                max={100}
                step={1}
              />

              <ToggleControl
                label={__("Afficher les étiquettes", "archi-graph")}
                checked={comparisonShowLabels}
                onChange={(value) =>
                  setAttributes({ comparisonShowLabels: value })
                }
              />

              {comparisonShowLabels && (
                <>
                  <div style={{ marginBottom: "10px" }}>
                    <label>{__("Étiquette 'Avant'", "archi-graph")}</label>
                    <input
                      type="text"
                      value={comparisonBeforeLabel}
                      onChange={(e) =>
                        setAttributes({ comparisonBeforeLabel: e.target.value })
                      }
                      style={{
                        width: "100%",
                        marginTop: "5px",
                        padding: "8px",
                      }}
                    />
                  </div>

                  <div style={{ marginBottom: "10px" }}>
                    <label>{__("Étiquette 'Après'", "archi-graph")}</label>
                    <input
                      type="text"
                      value={comparisonAfterLabel}
                      onChange={(e) =>
                        setAttributes({ comparisonAfterLabel: e.target.value })
                      }
                      style={{
                        width: "100%",
                        marginTop: "5px",
                        padding: "8px",
                      }}
                    />
                  </div>
                </>
              )}

              <div style={{ marginBottom: "10px" }}>
                <label>{__("Couleur de la poignée", "archi-graph")}</label>
                <input
                  type="color"
                  value={comparisonHandleColor}
                  onChange={(e) =>
                    setAttributes({ comparisonHandleColor: e.target.value })
                  }
                  style={{ width: "100%", height: "40px", marginTop: "5px" }}
                />
              </div>
            </PanelBody>
          )}

          {/* Accessibilité */}
          <PanelBody
            title={__("Accessibilité", "archi-graph")}
            initialOpen={false}
          >
            <div style={{ marginBottom: "10px" }}>
              <label>
                {__("Texte alternatif de l'image", "archi-graph")}
              </label>
              <input
                type="text"
                value={imageAlt}
                onChange={(e) => setAttributes({ imageAlt: e.target.value })}
                style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                placeholder={__("Description de l'image", "archi-graph")}
              />
            </div>

            {displayMode === "comparison" && secondImageUrl && (
              <div style={{ marginBottom: "10px" }}>
                <label>
                  {__("Texte alternatif image secondaire", "archi-graph")}
                </label>
                <input
                  type="text"
                  value={secondImageAlt}
                  onChange={(e) =>
                    setAttributes({ secondImageAlt: e.target.value })
                  }
                  style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                  placeholder={__("Description de l'image", "archi-graph")}
                />
              </div>
            )}
          </PanelBody>
        </InspectorControls>

        {/* Preview dans l'éditeur */}
        <div
          style={{
            border: "2px dashed #ccc",
            padding: "20px",
            borderRadius: "8px",
            background: "#f9f9f9",
          }}
        >
          {/* En-tête avec badge de mode */}
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              marginBottom: "15px",
            }}
          >
            <h3 style={{ margin: 0 }}>
              {__("Image Universelle", "archi-graph")}
            </h3>
            <div
              style={{
                background: "#3498db",
                color: "white",
                padding: "4px 10px",
                borderRadius: "12px",
                fontSize: "11px",
                fontWeight: "600",
              }}
            >
              {getModeLabel()}
            </div>
          </div>

          {/* Mode Comparison - deux images requises */}
          {displayMode === "comparison" ? (
            <>
              {!imageUrl || !secondImageUrl ? (
                <div
                  style={{
                    padding: "40px 20px",
                    textAlign: "center",
                    border: "2px dashed #ccc",
                    borderRadius: "8px",
                    background: "#fff",
                  }}
                >
                  <h4>{__("Mode Comparaison Avant/Après", "archi-graph")}</h4>
                  <p style={{ color: "#666", marginBottom: "20px" }}>
                    {__("Sélectionnez deux images à comparer", "archi-graph")}
                  </p>

                  {/* Image Avant */}
                  <div style={{ marginBottom: "20px" }}>
                    <h5>
                      {comparisonShowLabels && comparisonBeforeLabel
                        ? comparisonBeforeLabel
                        : __("Image Avant", "archi-graph")}
                    </h5>
                    {!imageUrl ? (
                      <MediaPlaceholder
                        icon="format-image"
                        labels={{
                          title: __("Image Avant", "archi-graph"),
                          instructions: __(
                            "Sélectionnez l'image AVANT",
                            "archi-graph"
                          ),
                        }}
                        onSelect={onSelectImage}
                        accept="image/*"
                        allowedTypes={["image"]}
                      />
                    ) : (
                      <div style={{ position: "relative" }}>
                        <img
                          src={imageUrl}
                          alt={imageAlt}
                          style={{ width: "100%", height: "auto" }}
                        />
                        <Button
                          onClick={onRemoveImage}
                          variant="secondary"
                          isDestructive
                          style={{ marginTop: "10px" }}
                        >
                          {__("Supprimer", "archi-graph")}
                        </Button>
                      </div>
                    )}
                  </div>

                  {/* Image Après */}
                  <div>
                    <h5>
                      {comparisonShowLabels && comparisonAfterLabel
                        ? comparisonAfterLabel
                        : __("Image Après", "archi-graph")}
                    </h5>
                    {!secondImageUrl ? (
                      <MediaPlaceholder
                        icon="format-image"
                        labels={{
                          title: __("Image Après", "archi-graph"),
                          instructions: __(
                            "Sélectionnez l'image APRÈS",
                            "archi-graph"
                          ),
                        }}
                        onSelect={onSelectSecondImage}
                        accept="image/*"
                        allowedTypes={["image"]}
                      />
                    ) : (
                      <div style={{ position: "relative" }}>
                        <img
                          src={secondImageUrl}
                          alt={secondImageAlt}
                          style={{ width: "100%", height: "auto" }}
                        />
                        <Button
                          onClick={onRemoveSecondImage}
                          variant="secondary"
                          isDestructive
                          style={{ marginTop: "10px" }}
                        >
                          {__("Supprimer", "archi-graph")}
                        </Button>
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                // Preview avec les deux images côte à côte
                <div>
                  <div
                    style={{
                      display: "grid",
                      gridTemplateColumns: "1fr 1fr",
                      gap: "10px",
                      marginBottom: "10px",
                    }}
                  >
                    <div>
                      <img
                        src={imageUrl}
                        alt={imageAlt}
                        style={{ width: "100%", height: "auto" }}
                      />
                      {comparisonShowLabels && (
                        <div
                          style={{
                            textAlign: "center",
                            padding: "5px",
                            background: "#fff",
                            marginTop: "5px",
                          }}
                        >
                          {comparisonBeforeLabel}
                        </div>
                      )}
                    </div>
                    <div>
                      <img
                        src={secondImageUrl}
                        alt={secondImageAlt}
                        style={{ width: "100%", height: "auto" }}
                      />
                      {comparisonShowLabels && (
                        <div
                          style={{
                            textAlign: "center",
                            padding: "5px",
                            background: "#fff",
                            marginTop: "5px",
                          }}
                        >
                          {comparisonAfterLabel}
                        </div>
                      )}
                    </div>
                  </div>
                  <p
                    style={{
                      textAlign: "center",
                      fontSize: "12px",
                      color: "#666",
                    }}
                  >
                    {__("Le slider sera interactif sur le frontend", "archi-graph")}
                  </p>
                </div>
              )}
            </>
          ) : (
            /* Modes standard - une seule image */
            <>
              {!imageUrl ? (
                <MediaPlaceholder
                  icon="format-image"
                  labels={{
                    title: __("Image", "archi-graph"),
                    instructions: __(
                      "Sélectionnez une image haute résolution",
                      "archi-graph"
                    ),
                  }}
                  onSelect={onSelectImage}
                  accept="image/*"
                  allowedTypes={["image"]}
                />
              ) : (
                <div style={{ position: "relative" }}>
                  {/* Preview de l'image */}
                  <div
                    style={{
                      position: "relative",
                      width: "100%",
                      height:
                        heightMode === "custom"
                          ? `${Math.min(customHeight, 400)}px`
                          : heightMode === "full-viewport"
                          ? "400px"
                          : "auto",
                      overflow: "hidden",
                      borderRadius: "4px",
                    }}
                  >
                    <img
                      src={imageUrl}
                      alt={imageAlt}
                      style={{
                        width: "100%",
                        height: "100%",
                        objectFit: objectFit,
                        display: "block",
                      }}
                    />

                    {/* Overlay preview */}
                    {overlayEnabled && (
                      <div
                        style={{
                          position: "absolute",
                          top: 0,
                          left: 0,
                          right: 0,
                          bottom: 0,
                          background: overlayColor,
                          opacity: overlayOpacity / 100,
                        }}
                      />
                    )}

                    {/* Texte preview */}
                    {textEnabled && (
                      <div
                        style={{
                          position: "absolute",
                          top: textPosition.includes("top")
                            ? `${textPadding}px`
                            : textPosition.includes("bottom")
                            ? "auto"
                            : "50%",
                          bottom: textPosition.includes("bottom")
                            ? `${textPadding}px`
                            : "auto",
                          left: textPosition.includes("left")
                            ? `${textPadding}px`
                            : textPosition.includes("right")
                            ? "auto"
                            : "50%",
                          right: textPosition.includes("right")
                            ? `${textPadding}px`
                            : "auto",
                          transform:
                            textPosition === "center"
                              ? "translate(-50%, -50%)"
                              : "none",
                          color: textColor,
                          fontSize: `${textSize}px`,
                          fontWeight: textWeight,
                          textAlign: textAlign,
                          textShadow: textShadow
                            ? "0 2px 8px rgba(0,0,0,0.5)"
                            : "none",
                          maxWidth: `${textMaxWidth}%`,
                          zIndex: 2,
                        }}
                      >
                        <RichText
                          tagName="div"
                          value={textContent}
                          onChange={(value) =>
                            setAttributes({ textContent: value })
                          }
                          placeholder={__(
                            "Ajoutez du texte ici...",
                            "archi-graph"
                          )}
                        />
                      </div>
                    )}
                  </div>

                  {/* Boutons d'action */}
                  <div
                    style={{
                      marginTop: "15px",
                      display: "flex",
                      gap: "10px",
                      flexWrap: "wrap",
                    }}
                  >
                    <MediaUpload
                      onSelect={onSelectImage}
                      allowedTypes={["image"]}
                      value={imageId}
                      render={({ open }) => (
                        <Button onClick={open} variant="secondary">
                          {__("Remplacer l'image", "archi-graph")}
                        </Button>
                      )}
                    />
                    <Button
                      onClick={onRemoveImage}
                      variant="secondary"
                      isDestructive
                    >
                      {__("Supprimer l'image", "archi-graph")}
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </div>
    );
  },

  save: () => null, // Server-side rendering
});
