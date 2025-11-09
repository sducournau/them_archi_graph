import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, Button, TextControl, SelectControl, ToggleControl, ColorPicker } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { Fragment } from "@wordpress/element";

/**
 * Bloc Graphique en Barres D3.js
 * Diagramme en barres interactif et personnalisable
 */
registerBlockType("archi-graph/d3-bar-chart", {
  title: __("Graphique en Barres (D3.js)", "archi-graph"),
  description: __("Diagramme en barres interactif avec D3.js", "archi-graph"),
  icon: "chart-bar",
  category: "archi-graph",
  keywords: [__("graphique", "archi-graph"), __("statistiques", "archi-graph"), __("d3", "archi-graph")],
  
  attributes: {
    title: {
      type: "string",
      default: __("Titre du graphique", "archi-graph"),
    },
    data: {
      type: "array",
      default: [
        { label: "Item 1", value: 30 },
        { label: "Item 2", value: 80 },
        { label: "Item 3", value: 45 },
        { label: "Item 4", value: 60 },
        { label: "Item 5", value: 20 },
      ],
    },
    orientation: {
      type: "string",
      default: "vertical", // vertical, horizontal
    },
    colorScheme: {
      type: "string",
      default: "blue", // blue, green, orange, purple, custom
    },
    customColor: {
      type: "string",
      default: "#3498db",
    },
    showValues: {
      type: "boolean",
      default: true,
    },
    showGrid: {
      type: "boolean",
      default: true,
    },
    height: {
      type: "number",
      default: 400,
    },
    animate: {
      type: "boolean",
      default: true,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      title,
      data,
      orientation,
      colorScheme,
      customColor,
      showValues,
      showGrid,
      height,
      animate,
    } = attributes;

    const addDataPoint = () => {
      const newData = [
        ...data,
        {
          label: `Item ${data.length + 1}`,
          value: 50,
        },
      ];
      setAttributes({ data: newData });
    };

    const updateDataPoint = (index, field, value) => {
      const newData = [...data];
      newData[index][field] = field === "value" ? parseFloat(value) || 0 : value;
      setAttributes({ data: newData });
    };

    const removeDataPoint = (index) => {
      setAttributes({ data: data.filter((_, i) => i !== index) });
    };

    const getColorForScheme = (scheme) => {
      const colors = {
        blue: "#3498db",
        green: "#2ecc71",
        orange: "#e67e22",
        purple: "#9b59b6",
        custom: customColor,
      };
      return colors[scheme] || colors.blue;
    };

    const maxValue = Math.max(...data.map((d) => d.value), 1);

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Paramètres du graphique", "archi-graph")}>
            <TextControl
              label={__("Titre", "archi-graph")}
              value={title}
              onChange={(value) => setAttributes({ title: value })}
            />
            <SelectControl
              label={__("Orientation", "archi-graph")}
              value={orientation}
              options={[
                { label: __("Verticale", "archi-graph"), value: "vertical" },
                { label: __("Horizontale", "archi-graph"), value: "horizontal" },
              ]}
              onChange={(value) => setAttributes({ orientation: value })}
            />
            <TextControl
              label={__("Hauteur (px)", "archi-graph")}
              type="number"
              value={height}
              onChange={(value) => setAttributes({ height: parseInt(value) || 400 })}
            />
            <ToggleControl
              label={__("Afficher les valeurs", "archi-graph")}
              checked={showValues}
              onChange={(value) => setAttributes({ showValues: value })}
            />
            <ToggleControl
              label={__("Afficher la grille", "archi-graph")}
              checked={showGrid}
              onChange={(value) => setAttributes({ showGrid: value })}
            />
            <ToggleControl
              label={__("Animation", "archi-graph")}
              checked={animate}
              onChange={(value) => setAttributes({ animate: value })}
            />
          </PanelBody>

          <PanelBody title={__("Couleurs", "archi-graph")} initialOpen={false}>
            <SelectControl
              label={__("Schéma de couleurs", "archi-graph")}
              value={colorScheme}
              options={[
                { label: __("Bleu", "archi-graph"), value: "blue" },
                { label: __("Vert", "archi-graph"), value: "green" },
                { label: __("Orange", "archi-graph"), value: "orange" },
                { label: __("Violet", "archi-graph"), value: "purple" },
                { label: __("Personnalisé", "archi-graph"), value: "custom" },
              ]}
              onChange={(value) => setAttributes({ colorScheme: value })}
            />
            {colorScheme === "custom" && (
              <div style={{ marginTop: "10px" }}>
                <label style={{ display: "block", marginBottom: "5px", fontWeight: "600" }}>
                  {__("Couleur personnalisée", "archi-graph")}
                </label>
                <ColorPicker
                  color={customColor}
                  onChangeComplete={(color) => setAttributes({ customColor: color.hex })}
                />
              </div>
            )}
          </PanelBody>

          <PanelBody title={__("Données", "archi-graph")} initialOpen={false}>
            <Button onClick={addDataPoint} variant="primary" style={{ marginBottom: "15px" }}>
              {__("+ Ajouter une donnée", "archi-graph")}
            </Button>
            {data.map((item, index) => (
              <div
                key={index}
                style={{
                  padding: "10px",
                  marginBottom: "10px",
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                  background: "#f9f9f9",
                }}
              >
                <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "10px" }}>
                  <strong>#{index + 1}</strong>
                  <Button onClick={() => removeDataPoint(index)} isDestructive isSmall>
                    {__("×", "archi-graph")}
                  </Button>
                </div>
                <TextControl
                  label={__("Label", "archi-graph")}
                  value={item.label}
                  onChange={(value) => updateDataPoint(index, "label", value)}
                />
                <TextControl
                  label={__("Valeur", "archi-graph")}
                  type="number"
                  value={item.value}
                  onChange={(value) => updateDataPoint(index, "value", value)}
                  step="0.1"
                />
              </div>
            ))}
          </PanelBody>
        </InspectorControls>

        <div
          style={{
            border: "2px dashed #ccc",
            padding: "20px",
            borderRadius: "8px",
            background: "#f9f9f9",
          }}
        >
          <h3 style={{ marginTop: 0 }}>{title}</h3>

          {/* Preview simplifié */}
          <div
            style={{
              width: "100%",
              height: `${Math.min(height, 400)}px`,
              background: "white",
              borderRadius: "4px",
              padding: "20px",
              display: "flex",
              flexDirection: orientation === "vertical" ? "row" : "column",
              alignItems: orientation === "vertical" ? "flex-end" : "flex-start",
              justifyContent: "space-around",
              gap: "10px",
            }}
          >
            {data.map((item, index) => (
              <div
                key={index}
                style={{
                  display: "flex",
                  flexDirection: orientation === "vertical" ? "column" : "row",
                  alignItems: "center",
                  gap: "5px",
                  flex: "1",
                }}
              >
                {orientation === "vertical" ? (
                  <>
                    {showValues && (
                      <div style={{ fontSize: "12px", fontWeight: "600" }}>
                        {item.value}
                      </div>
                    )}
                    <div
                      style={{
                        width: "100%",
                        height: `${(item.value / maxValue) * (Math.min(height, 400) - 80)}px`,
                        background: getColorForScheme(colorScheme),
                        borderRadius: "4px 4px 0 0",
                        transition: "all 0.3s ease",
                      }}
                    />
                    <div style={{ fontSize: "11px", textAlign: "center", wordBreak: "break-word" }}>
                      {item.label}
                    </div>
                  </>
                ) : (
                  <>
                    <div style={{ fontSize: "11px", minWidth: "60px", textAlign: "right" }}>
                      {item.label}
                    </div>
                    <div
                      style={{
                        width: `${(item.value / maxValue) * 70}%`,
                        height: "30px",
                        background: getColorForScheme(colorScheme),
                        borderRadius: "0 4px 4px 0",
                        transition: "all 0.3s ease",
                        display: "flex",
                        alignItems: "center",
                        paddingLeft: "8px",
                      }}
                    >
                      {showValues && (
                        <span style={{ fontSize: "12px", fontWeight: "600", color: "white" }}>
                          {item.value}
                        </span>
                      )}
                    </div>
                  </>
                )}
              </div>
            ))}
          </div>

          <div style={{ marginTop: "15px", fontSize: "11px", color: "#999", textAlign: "center" }}>
            {__("Graphique D3.js interactif sur le frontend", "archi-graph")} • {data.length} {__("points de données", "archi-graph")}
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});
