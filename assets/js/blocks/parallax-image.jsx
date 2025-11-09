import { registerBlockType } from "@wordpress/blocks";
import { 
  InspectorControls, 
  MediaUpload, 
  MediaPlaceholder, 
  RichText,
  useBlockProps 
} from "@wordpress/block-editor";
import { PanelBody, Button, RangeControl, SelectControl, ToggleControl, Placeholder } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * Bloc Parallax Image Universel
 * Combine toutes les fonctionnalités de parallax en un seul bloc optimisé
 * - Fixed background (background-attachment: fixed)
 * - Scroll parallax (transform parallax)
 * - Zoom effects
 * - Full viewport ou hauteurs personnalisées
 * - Overlays et textes superposés
 */
registerBlockType("archi-graph/parallax-image", {
  title: __("Image Parallax", "archi-graph"),
  description: __("Image avec effets parallax avancés - fixed, scroll, zoom", "archi-graph"),
  icon: "format-image",
  category: "archi-graph",
  keywords: [
    __("parallax", "archi-graph"),
    __("image", "archi-graph"),
    __("scroll", "archi-graph"),
    __("immersif", "archi-graph"),
  ],

  attributes: {
    // Image
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

    // Dimensions
    heightMode: {
      type: "string",
      default: "custom", // full-viewport, custom, auto
    },
    customHeight: {
      type: "number",
      default: 600,
    },

    // Effet Parallax
    parallaxEffect: {
      type: "string",
      default: "fixed", // fixed, scroll, zoom, none
    },
    parallaxSpeed: {
      type: "number",
      default: 0.5, // Pour scroll parallax (0 = lent, 1 = rapide)
    },
    enableZoom: {
      type: "boolean",
      default: false, // Pour zoom effect
    },

    // Ajustement Image
    objectFit: {
      type: "string",
      default: "cover", // cover, contain, fill
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

    // Animation & Transitions
    transitionEnabled: {
      type: "boolean",
      default: true,
    },
    transitionDuration: {
      type: "number",
      default: 0.8, // secondes
    },
    transitionEasing: {
      type: "string",
      default: "ease-out", // ease, ease-in, ease-out, ease-in-out, linear
    },
    animationOnScroll: {
      type: "boolean",
      default: false,
    },
    animationEffect: {
      type: "string",
      default: "fade-in", // fade-in, slide-up, slide-down, zoom-in, none
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      imageUrl,
      imageId,
      imageAlt,
      heightMode,
      customHeight,
      parallaxEffect,
      parallaxSpeed,
      enableZoom,
      objectFit,
      overlayEnabled,
      overlayColor,
      overlayOpacity,
      textEnabled,
      textContent,
      textPosition,
      textColor,
      transitionEnabled,
      transitionDuration,
      transitionEasing,
      animationOnScroll,
      animationEffect,
    } = attributes;

    const onSelectImage = (media) => {
      setAttributes({
        imageUrl: media.url,
        imageId: media.id,
        imageAlt: media.alt || "",
      });
    };

    const onRemoveImage = () => {
      setAttributes({
        imageUrl: "",
        imageId: null,
        imageAlt: "",
      });
    };

    const getEffectLabel = () => {
      switch (parallaxEffect) {
        case "fixed":
          return __("Fond Fixe Actif", "archi-graph");
        case "scroll":
          return __("Parallax Scroll Actif", "archi-graph");
        case "zoom":
          return __("Zoom Actif", "archi-graph");
        default:
          return __("Pas d'effet", "archi-graph");
      }
    };

    return (
      <div {...useBlockProps({ className: "archi-parallax-image-editor" })}>
        <InspectorControls>
          {/* Dimensions */}
          <PanelBody title={__("Dimensions", "archi-graph")} initialOpen={true}>
            <SelectControl
              label={__("Mode de hauteur", "archi-graph")}
              value={heightMode}
              options={[
                { label: __("Pleine hauteur (100vh)", "archi-graph"), value: "full-viewport" },
                { label: __("Hauteur personnalisée", "archi-graph"), value: "custom" },
                { label: __("Automatique", "archi-graph"), value: "auto" },
              ]}
              onChange={(value) => setAttributes({ heightMode: value })}
            />
            {heightMode === "custom" && (
              <RangeControl
                label={__("Hauteur personnalisée (px)", "archi-graph")}
                value={customHeight}
                onChange={(value) => setAttributes({ customHeight: value })}
                min={300}
                max={1200}
                step={50}
              />
            )}
            <SelectControl
              label={__("Ajustement de l'image", "archi-graph")}
              value={objectFit}
              options={[
                { label: __("Couvrir (cover)", "archi-graph"), value: "cover" },
                { label: __("Contenir (contain)", "archi-graph"), value: "contain" },
                { label: __("Remplir (fill)", "archi-graph"), value: "fill" },
              ]}
              onChange={(value) => setAttributes({ objectFit: value })}
              help={__("Comment l'image s'adapte au conteneur", "archi-graph")}
            />
          </PanelBody>

          {/* Effet Parallax */}
          <PanelBody title={__("Effet Parallax", "archi-graph")} initialOpen={true}>
            <SelectControl
              label={__("Type d'effet", "archi-graph")}
              value={parallaxEffect}
              options={[
                { label: __("Fond fixe (background-attachment)", "archi-graph"), value: "fixed" },
                { label: __("Défilement parallax (transform)", "archi-graph"), value: "scroll" },
                { label: __("Zoom au survol", "archi-graph"), value: "zoom" },
                { label: __("Aucun effet", "archi-graph"), value: "none" },
              ]}
              onChange={(value) => setAttributes({ parallaxEffect: value })}
              help={__("Choisissez l'effet de mouvement", "archi-graph")}
            />

            {parallaxEffect === "scroll" && (
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

            {parallaxEffect === "zoom" && (
              <ToggleControl
                label={__("Zoom au survol", "archi-graph")}
                checked={enableZoom}
                onChange={(value) => setAttributes({ enableZoom: value })}
                help={__("Active l'animation de zoom au survol", "archi-graph")}
              />
            )}
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
                    onChange={(e) => setAttributes({ overlayColor: e.target.value })}
                    style={{ width: "100%", height: "40px", marginTop: "5px" }}
                  />
                </div>
                <RangeControl
                  label={__("Opacité de l'overlay (%)", "archi-graph")}
                  value={overlayOpacity}
                  onChange={(value) => setAttributes({ overlayOpacity: value })}
                  min={0}
                  max={100}
                  step={5}
                />
              </>
            )}
          </PanelBody>

          {/* Texte superposé */}
          <PanelBody title={__("Texte superposé", "archi-graph")} initialOpen={false}>
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
                    { label: __("Haut gauche", "archi-graph"), value: "top-left" },
                    { label: __("Haut droite", "archi-graph"), value: "top-right" },
                    { label: __("Bas gauche", "archi-graph"), value: "bottom-left" },
                    { label: __("Bas droite", "archi-graph"), value: "bottom-right" },
                  ]}
                  onChange={(value) => setAttributes({ textPosition: value })}
                />
                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Couleur du texte", "archi-graph")}</label>
                  <input
                    type="color"
                    value={textColor}
                    onChange={(e) => setAttributes({ textColor: e.target.value })}
                    style={{ width: "100%", height: "40px", marginTop: "5px" }}
                  />
                </div>
              </>
            )}
          </PanelBody>

          {/* Transitions & Animations */}
          <PanelBody title={__("Transitions & Animations", "archi-graph")} initialOpen={false}>
            <ToggleControl
              label={__("Activer les transitions", "archi-graph")}
              checked={transitionEnabled}
              onChange={(value) => setAttributes({ transitionEnabled: value })}
              help={__("Active les animations de transition sur l'image", "archi-graph")}
            />
            {transitionEnabled && (
              <>
                <RangeControl
                  label={__("Durée de transition (secondes)", "archi-graph")}
                  value={transitionDuration}
                  onChange={(value) => setAttributes({ transitionDuration: value })}
                  min={0.1}
                  max={3}
                  step={0.1}
                />
                <SelectControl
                  label={__("Type de transition", "archi-graph")}
                  value={transitionEasing}
                  options={[
                    { label: __("Linear (vitesse constante)", "archi-graph"), value: "linear" },
                    { label: __("Ease (naturel)", "archi-graph"), value: "ease" },
                    { label: __("Ease In (accélération)", "archi-graph"), value: "ease-in" },
                    { label: __("Ease Out (décélération)", "archi-graph"), value: "ease-out" },
                    { label: __("Ease In Out (fluide)", "archi-graph"), value: "ease-in-out" },
                  ]}
                  onChange={(value) => setAttributes({ transitionEasing: value })}
                />
              </>
            )}
            <ToggleControl
              label={__("Animation au défilement", "archi-graph")}
              checked={animationOnScroll}
              onChange={(value) => setAttributes({ animationOnScroll: value })}
              help={__("Anime l'image quand elle entre dans le viewport", "archi-graph")}
            />
            {animationOnScroll && (
              <SelectControl
                label={__("Effet d'animation", "archi-graph")}
                value={animationEffect}
                options={[
                  { label: __("Fondu (fade-in)", "archi-graph"), value: "fade-in" },
                  { label: __("Glissement haut (slide-up)", "archi-graph"), value: "slide-up" },
                  { label: __("Glissement bas (slide-down)", "archi-graph"), value: "slide-down" },
                  { label: __("Zoom (zoom-in)", "archi-graph"), value: "zoom-in" },
                  { label: __("Aucun", "archi-graph"), value: "none" },
                ]}
                onChange={(value) => setAttributes({ animationEffect: value })}
              />
            )}
          </PanelBody>

          {/* Accessibilité */}
          <PanelBody title={__("Accessibilité", "archi-graph")} initialOpen={false}>
            <div style={{ marginBottom: "10px" }}>
              <label>{__("Texte alternatif de l'image", "archi-graph")}</label>
              <input
                type="text"
                value={imageAlt}
                onChange={(e) => setAttributes({ imageAlt: e.target.value })}
                style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                placeholder={__("Description de l'image", "archi-graph")}
              />
            </div>
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
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              marginBottom: "15px",
            }}
          >
            <h3 style={{ margin: 0 }}>{__("Image Parallax", "archi-graph")}</h3>
            <div
              style={{
                background: parallaxEffect !== "none" ? "#4caf50" : "#999",
                color: "white",
                padding: "4px 10px",
                borderRadius: "12px",
                fontSize: "11px",
                fontWeight: "600",
              }}
            >
              {getEffectLabel()}
            </div>
          </div>

          {!imageUrl ? (
            <MediaPlaceholder
              icon="format-image"
              labels={{
                title: __("Image Parallax", "archi-graph"),
                instructions: __(
                  "Sélectionnez une image haute résolution pour un effet immersif",
                  "archi-graph"
                ),
              }}
              onSelect={onSelectImage}
              accept="image/*"
              allowedTypes={["image"]}
            />
          ) : (
            <div style={{ position: "relative" }}>
              <div
                style={{
                  position: "relative",
                  width: "100%",
                  height:
                    heightMode === "full-viewport"
                      ? "400px"
                      : heightMode === "custom"
                      ? `${Math.min(customHeight, 400)}px`
                      : "300px",
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

                {textEnabled && (
                  <div
                    style={{
                      position: "absolute",
                      top: textPosition.includes("top")
                        ? "40px"
                        : textPosition.includes("bottom")
                        ? "auto"
                        : "50%",
                      bottom: textPosition.includes("bottom") ? "40px" : "auto",
                      left: textPosition.includes("left")
                        ? "40px"
                        : textPosition.includes("right")
                        ? "auto"
                        : "50%",
                      right: textPosition.includes("right") ? "40px" : "auto",
                      transform: textPosition === "center" ? "translate(-50%, -50%)" : "none",
                      color: textColor,
                      fontSize: "18px",
                      fontWeight: "600",
                      textShadow: "0 2px 8px rgba(0,0,0,0.5)",
                      maxWidth: "80%",
                    }}
                  >
                    <RichText
                      tagName="div"
                      value={textContent}
                      onChange={(value) => setAttributes({ textContent: value })}
                      placeholder={__("Ajoutez du texte ici...", "archi-graph")}
                    />
                  </div>
                )}
              </div>

              <div
                style={{
                  marginTop: "15px",
                  display: "flex",
                  gap: "10px",
                  flexWrap: "wrap",
                  alignItems: "center",
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
                <Button onClick={onRemoveImage} variant="secondary" isDestructive>
                  {__("Supprimer", "archi-graph")}
                </Button>

                <div
                  style={{
                    marginLeft: "auto",
                    fontSize: "12px",
                    color: "#666",
                  }}
                >
                  {heightMode === "full-viewport" && __("100vh", "archi-graph")}
                  {heightMode === "custom" && `${customHeight}px`}
                  {heightMode === "auto" && __("Auto", "archi-graph")}
                  {" • "}
                  {objectFit}
                  {parallaxEffect === "scroll" && ` • speed: ${parallaxSpeed}`}
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    );
  },

  save: () => null, // Server-side rendering
});
