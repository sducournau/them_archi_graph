import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls, MediaUpload, MediaPlaceholder, RichText, BlockControls, AlignmentToolbar } from "@wordpress/block-editor";
import { PanelBody, Button, RangeControl, SelectControl, ToggleControl, ToolbarGroup, ToolbarButton } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * Bloc Couverture avec Image
 * Similaire au bloc Cover WordPress natif avec overlay et texte
 */
registerBlockType("archi-graph/cover-block", {
  title: __("Couverture Image + Texte", "archi-graph"),
  description: __("Image de couverture avec overlay et texte superposé", "archi-graph"),
  icon: "cover-image",
  category: "archi-graph",
  supports: {
    align: ["wide", "full"],
    spacing: {
      padding: true,
      margin: true,
    },
  },
  attributes: {
    imageUrl: {
      type: "string",
      default: "",
    },
    imageId: {
      type: "number",
    },
    title: {
      type: "string",
      default: "",
    },
    subtitle: {
      type: "string",
      default: "",
    },
    overlayOpacity: {
      type: "number",
      default: 50,
    },
    overlayColor: {
      type: "string",
      default: "#000000",
    },
    minHeight: {
      type: "number",
      default: 400,
    },
    contentPosition: {
      type: "string",
      default: "center", // top, center, bottom
    },
    hasParallax: {
      type: "boolean",
      default: false,
    },
    parallaxSpeed: {
      type: "number",
      default: 0.5,
    },
    align: {
      type: "string",
      default: "full",
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      imageUrl,
      imageId,
      title,
      subtitle,
      overlayOpacity,
      overlayColor,
      minHeight,
      contentPosition,
      hasParallax,
      parallaxSpeed,
      align,
    } = attributes;

    const onSelectImage = (media) => {
      setAttributes({
        imageUrl: media.url,
        imageId: media.id,
      });
    };

    const onRemoveImage = () => {
      setAttributes({
        imageUrl: "",
        imageId: null,
      });
    };

    // Classes for the wrapper
    const wrapperClasses = [
      "archi-cover-block-editor",
      align && `align${align}`,
    ].filter(Boolean).join(" ");

    return (
      <div className={wrapperClasses}>
        <BlockControls>
          <ToolbarGroup>
            <ToolbarButton
              icon="align-center"
              label={__("Aligner au centre", "archi-graph")}
              onClick={() => setAttributes({ align: "center" })}
              isActive={align === "center"}
            />
            <ToolbarButton
              icon="align-full-width"
              label={__("Pleine largeur", "archi-graph")}
              onClick={() => setAttributes({ align: "full" })}
              isActive={align === "full"}
            />
          </ToolbarGroup>
        </BlockControls>

        <InspectorControls>
          <PanelBody title={__("Paramètres de l'image", "archi-graph")}>
            {imageUrl && (
              <Button onClick={onRemoveImage} variant="secondary" isDestructive>
                {__("Supprimer l'image", "archi-graph")}
              </Button>
            )}
          </PanelBody>

          <PanelBody title={__("Paramètres de l'overlay", "archi-graph")}>
            <RangeControl
              label={__("Opacité de l'overlay (%)", "archi-graph")}
              value={overlayOpacity}
              onChange={(value) => setAttributes({ overlayOpacity: value })}
              min={0}
              max={100}
              step={5}
            />
            <div style={{ marginBottom: "15px" }}>
              <label style={{ display: "block", marginBottom: "5px" }}>
                {__("Couleur de l'overlay", "archi-graph")}
              </label>
              <input
                type="color"
                value={overlayColor}
                onChange={(e) => setAttributes({ overlayColor: e.target.value })}
                style={{ width: "100%", height: "40px" }}
              />
            </div>
          </PanelBody>

          <PanelBody title={__("Paramètres de mise en page", "archi-graph")}>
            <RangeControl
              label={__("Hauteur minimale (px)", "archi-graph")}
              value={minHeight}
              onChange={(value) => setAttributes({ minHeight: value })}
              min={200}
              max={1000}
              step={50}
            />
            <SelectControl
              label={__("Position du contenu", "archi-graph")}
              value={contentPosition}
              options={[
                { label: __("Haut", "archi-graph"), value: "top" },
                { label: __("Centre", "archi-graph"), value: "center" },
                { label: __("Bas", "archi-graph"), value: "bottom" },
              ]}
              onChange={(value) => setAttributes({ contentPosition: value })}
            />
            <ToggleControl
              label={__("Effet parallax", "archi-graph")}
              checked={hasParallax}
              onChange={(value) => setAttributes({ hasParallax: value })}
              help={hasParallax ? __("Le parallax est activé", "archi-graph") : __("Activer pour un effet de profondeur au scroll", "archi-graph")}
            />
            {hasParallax && (
              <RangeControl
                label={__("Vitesse du parallax", "archi-graph")}
                value={parallaxSpeed}
                onChange={(value) => setAttributes({ parallaxSpeed: value })}
                min={0.1}
                max={1}
                step={0.1}
                help={__("0.3 = lent, 0.5 = moyen, 0.8 = rapide", "archi-graph")}
              />
            )}
          </PanelBody>
        </InspectorControls>

        {!imageUrl ? (
          <MediaPlaceholder
            icon="cover-image"
            labels={{
              title: __("Couverture Image + Texte", "archi-graph"),
              instructions: __(
                "Sélectionnez une image de fond pour la couverture (recommandé: pleine largeur)",
                "archi-graph"
              ),
            }}
            onSelect={onSelectImage}
            accept="image/*"
            allowedTypes={["image"]}
          />
        ) : (
          <div
            className={`archi-cover-preview ${hasParallax ? 'has-parallax' : ''} is-position-${contentPosition}`}
            style={{
              position: "relative",
              minHeight: `${minHeight}px`,
              backgroundImage: `url(${imageUrl})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
              width: align === "full" ? "100%" : "auto",
            }}
            data-parallax-speed={hasParallax ? parallaxSpeed : undefined}
          >
            {/* Overlay */}
            <div
              className="archi-cover-overlay"
              style={{
                position: "absolute",
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                backgroundColor: overlayColor,
                opacity: overlayOpacity / 100,
                zIndex: 0,
              }}
            />

            {/* Contenu */}
            <div
              className="archi-cover-content"
              style={{
                position: "relative",
                zIndex: 1,
                color: "white",
                padding: "40px",
                display: "flex",
                flexDirection: "column",
                justifyContent:
                  contentPosition === "top"
                    ? "flex-start"
                    : contentPosition === "bottom"
                    ? "flex-end"
                    : "center",
                alignItems: "center",
                minHeight: `${minHeight}px`,
                textAlign: "center",
                width: "100%",
                maxWidth: "1200px",
                margin: "0 auto",
              }}
            >
              <RichText
                tagName="h2"
                value={title}
                onChange={(value) => setAttributes({ title: value })}
                placeholder={__("Titre de la couverture...", "archi-graph")}
                style={{
                  fontSize: "2.5rem",
                  fontWeight: "bold",
                  marginBottom: "10px",
                  color: "white",
                  textShadow: "2px 2px 4px rgba(0,0,0,0.5)",
                }}
              />
              <RichText
                tagName="p"
                value={subtitle}
                onChange={(value) => setAttributes({ subtitle: value })}
                placeholder={__("Sous-titre optionnel...", "archi-graph")}
                style={{
                  fontSize: "1.25rem",
                  color: "white",
                  maxWidth: "800px",
                  textShadow: "1px 1px 3px rgba(0,0,0,0.5)",
                }}
              />
            </div>

            {/* Info badge */}
            {hasParallax && (
              <div
                style={{
                  position: "absolute",
                  bottom: "10px",
                  right: "10px",
                  background: "rgba(76, 175, 80, 0.9)",
                  color: "white",
                  padding: "5px 10px",
                  borderRadius: "4px",
                  fontSize: "12px",
                  zIndex: 2,
                }}
              >
                ✓ Parallax activé (speed: {parallaxSpeed})
              </div>
            )}

            {/* Boutons de contrôle */}
            <div style={{ position: "absolute", top: "10px", right: "10px", zIndex: 2 }}>
              <MediaUpload
                onSelect={onSelectImage}
                allowedTypes={["image"]}
                value={imageId}
                render={({ open }) => (
                  <Button
                    onClick={open}
                    variant="primary"
                    style={{ marginRight: "5px" }}
                  >
                    {__("Changer l'image", "archi-graph")}
                  </Button>
                )}
              />
            </div>
          </div>
        )}
      </div>
    );
  },

  save: () => null, // Server-side rendering
});
