import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, Button, TextControl, DatePicker, ColorPicker } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { Fragment } from "@wordpress/element";

/**
 * Bloc Timeline D3.js
 * Timeline horizontale interactive pour projets et événements
 */
registerBlockType("archi-graph/d3-timeline", {
  title: __("Timeline (D3.js)", "archi-graph"),
  description: __("Timeline horizontale interactive avec D3.js", "archi-graph"),
  icon: "calendar-alt",
  category: "archi-graph",
  keywords: [__("timeline", "archi-graph"), __("chronologie", "archi-graph"), __("temps", "archi-graph")],
  
  attributes: {
    title: {
      type: "string",
      default: __("Chronologie du projet", "archi-graph"),
    },
    events: {
      type: "array",
      default: [
        { id: 1, title: "Début du projet", date: "2024-01-15", description: "", color: "#3498db" },
        { id: 2, title: "Phase de conception", date: "2024-03-20", description: "", color: "#2ecc71" },
        { id: 3, title: "Début des travaux", date: "2024-06-01", description: "", color: "#e67e22" },
        { id: 4, title: "Livraison", date: "2024-12-15", description: "", color: "#9b59b6" },
      ],
    },
    height: {
      type: "number",
      default: 300,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const { title, events, height } = attributes;

    const addEvent = () => {
      const newEvent = {
        id: Date.now(),
        title: __("Nouvel événement", "archi-graph"),
        date: new Date().toISOString().split('T')[0],
        description: "",
        color: "#3498db",
      };
      setAttributes({ events: [...events, newEvent] });
    };

    const updateEvent = (id, field, value) => {
      const newEvents = events.map((event) =>
        event.id === id ? { ...event, [field]: value } : event
      );
      setAttributes({ events: newEvents });
    };

    const removeEvent = (id) => {
      setAttributes({ events: events.filter((e) => e.id !== id) });
    };

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Paramètres", "archi-graph")}>
            <TextControl
              label={__("Titre", "archi-graph")}
              value={title}
              onChange={(value) => setAttributes({ title: value })}
            />
            <TextControl
              label={__("Hauteur (px)", "archi-graph")}
              type="number"
              value={height}
              onChange={(value) => setAttributes({ height: parseInt(value) || 300 })}
            />
          </PanelBody>

          <PanelBody title={__("Événements", "archi-graph")} initialOpen={false}>
            <Button onClick={addEvent} variant="primary" style={{ marginBottom: "15px" }}>
              {__("+ Ajouter un événement", "archi-graph")}
            </Button>
            {events.map((event, index) => (
              <div
                key={event.id}
                style={{
                  padding: "10px",
                  marginBottom: "10px",
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                  background: "#f9f9f9",
                }}
              >
                <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "10px" }}>
                  <strong>{index + 1}. {event.title}</strong>
                  <Button onClick={() => removeEvent(event.id)} isDestructive isSmall>
                    ×
                  </Button>
                </div>
                <TextControl
                  label={__("Titre", "archi-graph")}
                  value={event.title}
                  onChange={(value) => updateEvent(event.id, "title", value)}
                />
                <TextControl
                  label={__("Date (YYYY-MM-DD)", "archi-graph")}
                  type="date"
                  value={event.date}
                  onChange={(value) => updateEvent(event.id, "date", value)}
                />
                <div style={{ marginBottom: "10px" }}>
                  <label>{__("Description", "archi-graph")}</label>
                  <textarea
                    value={event.description}
                    onChange={(e) => updateEvent(event.id, "description", e.target.value)}
                    style={{ width: "100%", padding: "8px", minHeight: "50px" }}
                  />
                </div>
                <div>
                  <label>{__("Couleur", "archi-graph")}</label>
                  <input
                    type="color"
                    value={event.color}
                    onChange={(e) => updateEvent(event.id, "color", e.target.value)}
                    style={{ width: "100%", height: "40px" }}
                  />
                </div>
              </div>
            ))}
          </PanelBody>
        </InspectorControls>

        <div style={{ border: "2px dashed #ccc", padding: "20px", borderRadius: "8px", background: "#f9f9f9" }}>
          <h3 style={{ marginTop: 0 }}>{title}</h3>
          <div style={{ width: "100%", height: `${Math.min(height, 300)}px`, background: "white", borderRadius: "4px", padding: "20px", position: "relative", overflow: "hidden" }}>
            {/* Timeline preview */}
            <div style={{ position: "absolute", top: "50%", left: "5%", right: "5%", height: "2px", background: "#ddd" }}></div>
            {events.sort((a, b) => new Date(a.date) - new Date(b.date)).map((event, index) => (
              <div
                key={event.id}
                style={{
                  position: "absolute",
                  left: `${5 + (index / Math.max(events.length - 1, 1)) * 90}%`,
                  top: "50%",
                  transform: "translate(-50%, -50%)",
                }}
              >
                <div style={{ width: "12px", height: "12px", borderRadius: "50%", background: event.color, margin: "0 auto" }}></div>
                <div style={{ fontSize: "10px", marginTop: "10px", textAlign: "center", whiteSpace: "nowrap" }}>
                  {event.title}
                </div>
              </div>
            ))}
          </div>
          <div style={{ marginTop: "10px", fontSize: "11px", color: "#999", textAlign: "center" }}>
            {__("Timeline interactive D3.js", "archi-graph")} • {events.length} {__("événements", "archi-graph")}
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null,
});
