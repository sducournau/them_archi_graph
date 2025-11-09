import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls, MediaUpload, MediaPlaceholder } from "@wordpress/block-editor";
import { PanelBody, Button, RangeControl, SelectControl, ToggleControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * Bloc Comparaison d'Images Avant/Après
 * Slider interactif pour comparer deux images avec effet de glissement
 */
registerBlockType("archi-graph/image-comparison-slider", {
  title: __("Comparaison Avant/Après", "archi-graph"),
  description: __("Slider interactif pour comparer deux images (avant/après)", "archi-graph"),
  icon: "image-flip-horizontal",
  category: "archi-graph",
  keywords: [__("avant", "archi-graph"), __("après", "archi-graph"), __("slider", "archi-graph"), __("comparaison", "archi-graph")],
  
  attributes: {
    beforeImageUrl: {
      type: "string",
      default: "",
    },
    beforeImageId: {
      type: "number",
    },
    beforeImageAlt: {
      type: "string",
      default: "",
    },
    afterImageUrl: {
      type: "string",
      default: "",
    },
    afterImageId: {
      type: "number",
    },
    afterImageAlt: {
      type: "string",
      default: "",
    },
    initialPosition: {
      type: "number",
      default: 50, // Position initiale du slider (0-100%)
    },
    orientation: {
      type: "string",
      default: "vertical", // vertical ou horizontal
    },
    showLabels: {
      type: "boolean",
      default: true,
    },
    beforeLabel: {
      type: "string",
      default: __("Avant", "archi-graph"),
    },
    afterLabel: {
      type: "string",
      default: __("Après", "archi-graph"),
    },
    heightMode: {
      type: "string",
      default: "auto", // auto, custom, full-viewport
    },
    customHeight: {
      type: "number",
      default: 600,
    },
    aspectRatio: {
      type: "string",
      default: "16-9", // 16-9, 4-3, 1-1, original
    },
    handleColor: {
      type: "string",
      default: "#ffffff",
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      beforeImageUrl,
      beforeImageId,
      beforeImageAlt,
      afterImageUrl,
      afterImageId,
      afterImageAlt,
      initialPosition,
      orientation,
      showLabels,
      beforeLabel,
      afterLabel,
      heightMode,
      customHeight,
      aspectRatio,
      handleColor,
    } = attributes;

    const onSelectBeforeImage = (media) => {
      setAttributes({
        beforeImageUrl: media.url,
        beforeImageId: media.id,
        beforeImageAlt: media.alt || "",
      });
    };

    const onSelectAfterImage = (media) => {
      setAttributes({
        afterImageUrl: media.url,
        afterImageId: media.id,
        afterImageAlt: media.alt || "",
      });
    };

    const onRemoveBeforeImage = () => {
      setAttributes({
        beforeImageUrl: "",
        beforeImageId: null,
        beforeImageAlt: "",
      });
    };

    const onRemoveAfterImage = () => {
      setAttributes({
        afterImageUrl: "",
        afterImageId: null,
        afterImageAlt: "",
      });
    };

    return (
      <div className="archi-image-comparison-editor">
        <InspectorControls>
          <PanelBody title={__("Options de Hauteur", "archi-graph")}>
            <SelectControl
              label={__("Mode de Hauteur", "archi-graph")}
              value={heightMode}
              options={[
                { label: __("Automatique (aspect ratio)", "archi-graph"), value: "auto" },
                { label: __("Pleine hauteur d'écran", "archi-graph"), value: "full-viewport" },
                { label: __("Hauteur personnalisée", "archi-graph"), value: "custom" },
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
          </PanelBody>

          <PanelBody title={__("Paramètres du Slider", "archi-graph")}>
            <SelectControl
              label={__("Orientation", "archi-graph")}
              value={orientation}
              options={[
                { label: __("Vertical (gauche/droite)", "archi-graph"), value: "vertical" },
                { label: __("Horizontal (haut/bas)", "archi-graph"), value: "horizontal" },
              ]}
              onChange={(value) => setAttributes({ orientation: value })}
            />
            <RangeControl
              label={__("Position initiale (%)", "archi-graph")}
              value={initialPosition}
              onChange={(value) => setAttributes({ initialPosition: value })}
              min={0}
              max={100}
              step={1}
            />
            <SelectControl
              label={__("Ratio d'aspect", "archi-graph")}
              value={aspectRatio}
              options={[
                { label: __("16:9 (paysage)", "archi-graph"), value: "16-9" },
                { label: __("4:3 (standard)", "archi-graph"), value: "4-3" },
                { label: __("1:1 (carré)", "archi-graph"), value: "1-1" },
                { label: __("3:4 (portrait)", "archi-graph"), value: "3-4" },
                { label: __("Original", "archi-graph"), value: "original" },
              ]}
              onChange={(value) => setAttributes({ aspectRatio: value })}
              help={heightMode !== "auto" ? __("Le ratio d'aspect est ignoré en mode hauteur fixe", "archi-graph") : ""}
            />
            <div style={{ marginBottom: "10px" }}>
              <label>{__("Couleur de la poignée", "archi-graph")}</label>
              <input
                type="color"
                value={handleColor}
                onChange={(e) => setAttributes({ handleColor: e.target.value })}
                style={{ width: "100%", height: "40px", marginTop: "5px" }}
              />
            </div>
          </PanelBody>

          <PanelBody title={__("Étiquettes", "archi-graph")} initialOpen={false}>
            <ToggleControl
              label={__("Afficher les étiquettes", "archi-graph")}
              checked={showLabels}
              onChange={(value) => setAttributes({ showLabels: value })}
            />
            {showLabels && (
              <>
                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Étiquette Avant", "archi-graph")}</label>
                  <input
                    type="text"
                    value={beforeLabel}
                    onChange={(e) => setAttributes({ beforeLabel: e.target.value })}
                    style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                  />
                </div>
                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Étiquette Après", "archi-graph")}</label>
                  <input
                    type="text"
                    value={afterLabel}
                    onChange={(e) => setAttributes({ afterLabel: e.target.value })}
                    style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                  />
                </div>
              </>
            )}
          </PanelBody>

          <PanelBody title={__("Textes alternatifs", "archi-graph")} initialOpen={false}>
            {beforeImageUrl && (
              <div style={{ marginBottom: "10px" }}>
                <label>{__("Alt image Avant", "archi-graph")}</label>
                <input
                  type="text"
                  value={beforeImageAlt}
                  onChange={(e) => setAttributes({ beforeImageAlt: e.target.value })}
                  style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                />
              </div>
            )}
            {afterImageUrl && (
              <div style={{ marginBottom: "10px" }}>
                <label>{__("Alt image Après", "archi-graph")}</label>
                <input
                  type="text"
                  value={afterImageAlt}
                  onChange={(e) => setAttributes({ afterImageAlt: e.target.value })}
                  style={{ width: "100%", marginTop: "5px", padding: "8px" }}
                />
              </div>
            )}
          </PanelBody>
        </InspectorControls>

        <div className={`archi-comparison-editor orientation-${orientation} ${beforeImageUrl && afterImageUrl ? 'has-before-image' : ''}`}>
          
          {/* Preview container with split view */}
          {beforeImageUrl && afterImageUrl ? (
            <>
              <div className="archi-comparison-preview-container">
                {/* Before image side */}
                <div className="archi-comparison-preview-side before">
                  {showLabels && (
                    <div className="archi-comparison-preview-label">{beforeLabel}</div>
                  )}
                  <img
                    src={beforeImageUrl}
                    alt={beforeImageAlt}
                    style={{
                      width: "100%",
                      height: heightMode === "custom" ? `${customHeight}px` : "100%",
                      objectFit: "cover"
                    }}
                  />
                </div>

                {/* After image side */}
                <div className="archi-comparison-preview-side after">
                  {showLabels && (
                    <div className="archi-comparison-preview-label">{afterLabel}</div>
                  )}
                  <img
                    src={afterImageUrl}
                    alt={afterImageAlt}
                    style={{
                      width: "100%",
                      height: heightMode === "custom" ? `${customHeight}px` : "100%",
                      objectFit: "cover"
                    }}
                  />
                </div>
              </div>

              {/* Visual divider */}
              <div className="archi-comparison-divider"></div>

              {/* Info overlay */}
              <div className="archi-comparison-info-overlay">
                <strong>{__("Mode Édition:", "archi-graph")}</strong> {__("Le slider sera interactif sur le frontend", "archi-graph")}
                <br />
                <small>{__("Position initiale:", "archi-graph")} {initialPosition}% | {__("Orientation:", "archi-graph")} {orientation === "vertical" ? "↔" : "↕"}</small>
              </div>
            </>
          ) : (
            <div style={{
              border: "2px dashed #ccc",
              padding: "40px 20px",
              borderRadius: "8px",
              background: "#f9f9f9",
              textAlign: "center",
              minHeight: "400px",
              display: "flex",
              flexDirection: "column",
              justifyContent: "center"
            }}>
              <h3 style={{ marginTop: 0, marginBottom: "20px" }}>
                {__("Comparaison Avant/Après", "archi-graph")}
              </h3>

              {/* Image Avant */}
              <div style={{ marginBottom: "20px" }}>
            <h4 style={{ marginBottom: "10px" }}>
              {__("Image Avant", "archi-graph")} {showLabels && beforeLabel && `(${beforeLabel})`}
            </h4>
            {!beforeImageUrl ? (
              <MediaPlaceholder
                icon="format-image"
                labels={{
                  title: __("Image Avant", "archi-graph"),
                  instructions: __("Sélectionnez l'image AVANT", "archi-graph"),
                }}
                onSelect={onSelectBeforeImage}
                accept="image/*"
                allowedTypes={["image"]}
              />
            ) : (
              <div style={{ position: "relative" }}>
                <img 
                  src={beforeImageUrl} 
                  alt={beforeImageAlt} 
                  style={{ 
                    width: "100%", 
                    height: "auto", 
                    display: "block",
                    borderRadius: "4px"
                  }} 
                />
                <div style={{ marginTop: "10px", display: "flex", gap: "10px" }}>
                  <MediaUpload
                    onSelect={onSelectBeforeImage}
                    allowedTypes={["image"]}
                    value={beforeImageId}
                    render={({ open }) => (
                      <Button onClick={open} variant="secondary">
                        {__("Remplacer", "archi-graph")}
                      </Button>
                    )}
                  />
                  <Button onClick={onRemoveBeforeImage} variant="secondary" isDestructive>
                    {__("Supprimer", "archi-graph")}
                  </Button>
                </div>
              </div>
            )}
          </div>

          {/* Image Après */}
          <div>
            <h4 style={{ marginBottom: "10px" }}>
              {__("Image Après", "archi-graph")} {showLabels && afterLabel && `(${afterLabel})`}
            </h4>
            {!afterImageUrl ? (
              <MediaPlaceholder
                icon="format-image"
                labels={{
                  title: __("Image Après", "archi-graph"),
                  instructions: __("Sélectionnez l'image APRÈS", "archi-graph"),
                }}
                onSelect={onSelectAfterImage}
                accept="image/*"
                allowedTypes={["image"]}
              />
            ) : (
              <div style={{ position: "relative" }}>
                <img 
                  src={afterImageUrl} 
                  alt={afterImageAlt} 
                  style={{ 
                    width: "100%", 
                    height: "auto", 
                    display: "block",
                    borderRadius: "4px"
                  }} 
                />
                <div style={{ marginTop: "10px", display: "flex", gap: "10px" }}>
                  <MediaUpload
                    onSelect={onSelectAfterImage}
                    allowedTypes={["image"]}
                    value={afterImageId}
                    render={({ open }) => (
                      <Button onClick={open} variant="secondary">
                        {__("Remplacer", "archi-graph")}
                      </Button>
                    )}
                  />
                  <Button onClick={onRemoveAfterImage} variant="secondary" isDestructive>
                    {__("Supprimer", "archi-graph")}
                  </Button>
                </div>
              </div>
            )}
          </div>
            </div>
          )}
        </div>
      </div>
    );
  },

  save: () => null, // Server-side rendering
});
