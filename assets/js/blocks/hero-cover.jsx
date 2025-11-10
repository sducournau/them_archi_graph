/**
 * Hero Cover Block - Editor Interface
 * Bloc de couverture pleine page pour l'en-tête des articles
 */

import { registerBlockType } from "@wordpress/blocks";
import {
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";
import {
  PanelBody,
  ToggleControl,
  SelectControl,
  RangeControl,
  Button,
  ColorPicker,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { Fragment } from "@wordpress/element";

registerBlockType("archi-graph/hero-cover", {
  title: __("Hero Cover", "archi-graph"),
  description: __(
    "Image de couverture pleine page pour l'en-tête des articles",
    "archi-graph"
  ),
  icon: "cover-image",
  category: "archi-graph",
  keywords: [__("hero", "archi-graph"), __("cover", "archi-graph"), __("header", "archi-graph")],
  supports: {
    align: ["full"],
    anchor: true,
  },

  attributes: {
    imageUrl: { type: "string", default: "" },
    imageId: { type: "number" },
    imageAlt: { type: "string", default: "" },
    useFeaturedImage: { type: "boolean", default: true },

    heightMode: { type: "string", default: "full-viewport" },
    customHeight: { type: "number", default: 800 },

    overlayColor: { type: "string", default: "#000000" },
    overlayOpacity: { type: "number", default: 40 },

    showTitle: { type: "boolean", default: true },
    showExcerpt: { type: "boolean", default: true },
    showMeta: { type: "boolean", default: true },
    showCategories: { type: "boolean", default: true },
    showScrollIndicator: { type: "boolean", default: true },

    contentPosition: { type: "string", default: "center" },
    textAlign: { type: "string", default: "center" },
    textColor: { type: "string", default: "#ffffff" },

    enableParallax: { type: "boolean", default: true },
    parallaxSpeed: { type: "number", default: 0.5 },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      imageUrl,
      imageId,
      useFeaturedImage,
      heightMode,
      customHeight,
      overlayColor,
      overlayOpacity,
      showTitle,
      showExcerpt,
      showMeta,
      showCategories,
      showScrollIndicator,
      contentPosition,
      textAlign,
      textColor,
      enableParallax,
      parallaxSpeed,
    } = attributes;

    // Récupérer les données du post
    const postData = useSelect((select) => {
      const { getCurrentPost } = select("core/editor");
      const { getMedia } = select("core");
      const post = getCurrentPost();

      let featuredImageUrl = "";
      if (post && post.featured_media) {
        const media = getMedia(post.featured_media);
        if (media) {
          featuredImageUrl = media.source_url;
        }
      }

      return {
        title: post?.title || __("Titre de l'article", "archi-graph"),
        excerpt: post?.excerpt || __("Extrait de l'article...", "archi-graph"),
        categories: post?.categories || [],
        featuredImageUrl,
      };
    }, []);

    // Déterminer l'image à afficher
    const displayImageUrl = useFeaturedImage && postData.featuredImageUrl
      ? postData.featuredImageUrl
      : imageUrl;

    // Styles pour la preview
    const containerStyle = {
      position: "relative",
      minHeight: heightMode === "full-viewport" ? "600px" : `${customHeight}px`,
      overflow: "hidden",
      backgroundColor: "#000",
    };

    const imageStyle = {
      position: "absolute",
      top: 0,
      left: 0,
      width: "100%",
      height: "100%",
      objectFit: "cover",
    };

    const overlayStyle = {
      position: "absolute",
      top: 0,
      left: 0,
      width: "100%",
      height: "100%",
      backgroundColor: overlayColor,
      opacity: overlayOpacity / 100,
    };

    const contentStyle = {
      position: "relative",
      zIndex: 3,
      height: "100%",
      display: "flex",
      alignItems: contentPosition === "top" ? "flex-start" : contentPosition === "bottom" ? "flex-end" : "center",
      justifyContent: "center",
      padding: "60px 40px",
      color: textColor,
      textAlign: textAlign,
    };

    return (
      <Fragment>
        <InspectorControls>
          {/* Image */}
          <PanelBody title={__("Image", "archi-graph")} initialOpen={true}>
            <ToggleControl
              label={__("Utiliser l'image à la une", "archi-graph")}
              checked={useFeaturedImage}
              onChange={(value) => setAttributes({ useFeaturedImage: value })}
            />

            {!useFeaturedImage && (
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) =>
                    setAttributes({
                      imageUrl: media.url,
                      imageId: media.id,
                      imageAlt: media.alt,
                    })
                  }
                  allowedTypes={["image"]}
                  value={imageId}
                  render={({ open }) => (
                    <Button onClick={open} variant="secondary">
                      {imageUrl
                        ? __("Changer l'image", "archi-graph")
                        : __("Sélectionner une image", "archi-graph")}
                    </Button>
                  )}
                />
              </MediaUploadCheck>
            )}
          </PanelBody>

          {/* Dimensions */}
          <PanelBody title={__("Dimensions", "archi-graph")}>
            <SelectControl
              label={__("Hauteur", "archi-graph")}
              value={heightMode}
              options={[
                { label: __("Plein écran (100vh)", "archi-graph"), value: "full-viewport" },
                { label: __("Personnalisée", "archi-graph"), value: "custom" },
              ]}
              onChange={(value) => setAttributes({ heightMode: value })}
            />

            {heightMode === "custom" && (
              <RangeControl
                label={__("Hauteur (px)", "archi-graph")}
                value={customHeight}
                onChange={(value) => setAttributes({ customHeight: value })}
                min={400}
                max={1200}
                step={50}
              />
            )}
          </PanelBody>

          {/* Overlay */}
          <PanelBody title={__("Overlay", "archi-graph")}>
            <p>{__("Couleur de l'overlay", "archi-graph")}</p>
            <ColorPicker
              color={overlayColor}
              onChangeComplete={(value) =>
                setAttributes({ overlayColor: value.hex })
              }
              disableAlpha
            />

            <RangeControl
              label={__("Opacité (%)", "archi-graph")}
              value={overlayOpacity}
              onChange={(value) => setAttributes({ overlayOpacity: value })}
              min={0}
              max={100}
              step={5}
            />
          </PanelBody>

          {/* Contenu */}
          <PanelBody title={__("Contenu à afficher", "archi-graph")}>
            <ToggleControl
              label={__("Afficher les catégories", "archi-graph")}
              checked={showCategories}
              onChange={(value) => setAttributes({ showCategories: value })}
            />
            <ToggleControl
              label={__("Afficher le titre", "archi-graph")}
              checked={showTitle}
              onChange={(value) => setAttributes({ showTitle: value })}
            />
            <ToggleControl
              label={__("Afficher l'extrait", "archi-graph")}
              checked={showExcerpt}
              onChange={(value) => setAttributes({ showExcerpt: value })}
            />
            <ToggleControl
              label={__("Afficher les métadonnées", "archi-graph")}
              checked={showMeta}
              onChange={(value) => setAttributes({ showMeta: value })}
            />
            <ToggleControl
              label={__("Afficher l'indicateur de défilement", "archi-graph")}
              checked={showScrollIndicator}
              onChange={(value) => setAttributes({ showScrollIndicator: value })}
            />
          </PanelBody>

          {/* Position */}
          <PanelBody title={__("Position et alignement", "archi-graph")}>
            <SelectControl
              label={__("Position verticale", "archi-graph")}
              value={contentPosition}
              options={[
                { label: __("Haut", "archi-graph"), value: "top" },
                { label: __("Centre", "archi-graph"), value: "center" },
                { label: __("Bas", "archi-graph"), value: "bottom" },
              ]}
              onChange={(value) => setAttributes({ contentPosition: value })}
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

            <p>{__("Couleur du texte", "archi-graph")}</p>
            <ColorPicker
              color={textColor}
              onChangeComplete={(value) => setAttributes({ textColor: value.hex })}
              disableAlpha
            />
          </PanelBody>

          {/* Parallax */}
          <PanelBody title={__("Effet parallax", "archi-graph")}>
            <ToggleControl
              label={__("Activer le parallax", "archi-graph")}
              checked={enableParallax}
              onChange={(value) => setAttributes({ enableParallax: value })}
            />

            {enableParallax && (
              <RangeControl
                label={__("Vitesse du parallax", "archi-graph")}
                value={parallaxSpeed}
                onChange={(value) => setAttributes({ parallaxSpeed: value })}
                min={0.1}
                max={1}
                step={0.1}
              />
            )}
          </PanelBody>
        </InspectorControls>

        {/* Preview */}
        <div style={containerStyle}>
          {displayImageUrl && (
            <img src={displayImageUrl} alt="" style={imageStyle} />
          )}

          <div style={overlayStyle}></div>

          <div style={contentStyle}>
            <div style={{ maxWidth: "1200px" }}>
              {showCategories && (
                <div style={{ marginBottom: "20px" }}>
                  <span
                    style={{
                      padding: "6px 16px",
                      background: "rgba(255,255,255,0.2)",
                      borderRadius: "20px",
                      fontSize: "14px",
                    }}
                  >
                    {__("Catégorie", "archi-graph")}
                  </span>
                </div>
              )}

              {showTitle && (
                <h1
                  style={{
                    fontSize: "3rem",
                    fontWeight: "800",
                    margin: "0 0 24px",
                  }}
                >
                  {postData.title}
                </h1>
              )}

              {showExcerpt && (
                <p style={{ fontSize: "1.25rem", margin: "0 0 32px" }}>
                  {postData.excerpt}
                </p>
              )}

              {showMeta && (
                <div style={{ fontSize: "16px", display: "flex", gap: "24px", flexWrap: "wrap", justifyContent: textAlign }}>
                  <span>{__("Auteur", "archi-graph")}</span>
                  <span>{__("Date", "archi-graph")}</span>
                </div>
              )}
            </div>
          </div>

          {showScrollIndicator && (
            <div
              style={{
                position: "absolute",
                bottom: "40px",
                left: "50%",
                transform: "translateX(-50%)",
                textAlign: "center",
                color: textColor,
              }}
            >
              <div
                style={{
                  width: "24px",
                  height: "40px",
                  border: `2px solid ${textColor}`,
                  borderRadius: "12px",
                  margin: "0 auto 12px",
                }}
              ></div>
              <div style={{ fontSize: "12px" }}>{__("Défiler", "archi-graph")}</div>
            </div>
          )}
        </div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});
