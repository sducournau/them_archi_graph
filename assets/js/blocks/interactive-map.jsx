import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls, RichText } from "@wordpress/block-editor";
import { PanelBody, Button, RangeControl, SelectControl, TextControl, ToggleControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { Fragment, useEffect, useRef } from "@wordpress/element";

/**
 * Bloc Carte Interactive (Leaflet.js)
 * Carte interactive avec marqueurs personnalisables
 */
registerBlockType("archi-graph/interactive-map", {
  title: __("Carte Interactive", "archi-graph"),
  description: __("Carte interactive avec marqueurs et popups personnalisables", "archi-graph"),
  icon: "location-alt",
  category: "archi-graph",
  keywords: [__("carte", "archi-graph"), __("map", "archi-graph"), __("géolocalisation", "archi-graph")],
  
  attributes: {
    latitude: {
      type: "number",
      default: 48.8566, // Paris
    },
    longitude: {
      type: "number",
      default: 2.3522,
    },
    zoom: {
      type: "number",
      default: 13,
    },
    height: {
      type: "number",
      default: 400,
    },
    mapStyle: {
      type: "string",
      default: "osm", // osm, osm-fr, terrain, satellite
    },
    markers: {
      type: "array",
      default: [],
    },
    showControls: {
      type: "boolean",
      default: true,
    },
    enableScroll: {
      type: "boolean",
      default: true,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      latitude,
      longitude,
      zoom,
      height,
      mapStyle,
      markers,
      showControls,
      enableScroll,
    } = attributes;

    const mapRef = useRef(null);
    const mapInstanceRef = useRef(null);
    const markersLayerRef = useRef(null);

    // Initialiser la carte Leaflet dans l'éditeur
    useEffect(() => {
      if (!mapRef.current || !window.L) {
        return;
      }

      // Détruire la carte existante si elle existe
      if (mapInstanceRef.current) {
        mapInstanceRef.current.remove();
      }

      // Créer la nouvelle carte
      const map = window.L.map(mapRef.current, {
        center: [latitude, longitude],
        zoom: zoom,
        scrollWheelZoom: enableScroll,
        zoomControl: showControls,
      });

      // Ajouter le fond de carte selon le style
      let tileUrl = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
      let attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

      if (mapStyle === "osm-fr") {
        tileUrl = "https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png";
      } else if (mapStyle === "terrain") {
        tileUrl = "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png";
        attribution = '&copy; <a href="https://opentopomap.org">OpenTopoMap</a>';
      } else if (mapStyle === "satellite") {
        tileUrl = "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
        attribution = "&copy; Esri";
      }

      window.L.tileLayer(tileUrl, {
        attribution: attribution,
        maxZoom: 18,
      }).addTo(map);

      mapInstanceRef.current = map;

      // Nettoyer au démontage
      return () => {
        if (mapInstanceRef.current) {
          mapInstanceRef.current.remove();
          mapInstanceRef.current = null;
        }
      };
    }, []);

    // Mettre à jour la vue de la carte quand les paramètres changent
    useEffect(() => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.setView([latitude, longitude], zoom);
      }
    }, [latitude, longitude, zoom]);

    // Mettre à jour les marqueurs
    useEffect(() => {
      if (!mapInstanceRef.current || !window.L) {
        return;
      }

      // Supprimer les anciens marqueurs
      if (markersLayerRef.current) {
        mapInstanceRef.current.removeLayer(markersLayerRef.current);
      }

      // Créer un nouveau groupe de marqueurs
      const markersLayer = window.L.layerGroup();

      markers.forEach((marker) => {
        const icon = window.L.divIcon({
          className: "custom-marker",
          html: `<div style="background-color: ${marker.color}; width: 25px; height: 25px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
          iconSize: [25, 25],
          iconAnchor: [12, 24],
        });

        const leafletMarker = window.L.marker([marker.lat, marker.lng], { icon });

        if (marker.title || marker.description) {
          let popupContent = "";
          if (marker.title) {
            popupContent += `<strong>${marker.title}</strong>`;
          }
          if (marker.description) {
            popupContent += `<br>${marker.description}`;
          }
          leafletMarker.bindPopup(popupContent);
        }

        markersLayer.addLayer(leafletMarker);
      });

      markersLayer.addTo(mapInstanceRef.current);
      markersLayerRef.current = markersLayer;
    }, [markers]);

    const addMarker = () => {
      const newMarker = {
        id: Date.now(),
        lat: latitude,
        lng: longitude,
        title: __("Nouveau marqueur", "archi-graph"),
        description: "",
        color: "#3498db",
      };
      setAttributes({ markers: [...markers, newMarker] });
    };

    const updateMarker = (id, field, value) => {
      const newMarkers = markers.map((marker) =>
        marker.id === id ? { ...marker, [field]: value } : marker
      );
      setAttributes({ markers: newMarkers });
    };

    const removeMarker = (id) => {
      setAttributes({ markers: markers.filter((m) => m.id !== id) });
    };

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Paramètres de la carte", "archi-graph")}>
            <TextControl
              label={__("Latitude", "archi-graph")}
              type="number"
              value={latitude}
              onChange={(value) => setAttributes({ latitude: parseFloat(value) })}
              step="0.0001"
            />
            <TextControl
              label={__("Longitude", "archi-graph")}
              type="number"
              value={longitude}
              onChange={(value) => setAttributes({ longitude: parseFloat(value) })}
              step="0.0001"
            />
            <RangeControl
              label={__("Zoom", "archi-graph")}
              value={zoom}
              onChange={(value) => setAttributes({ zoom: value })}
              min={1}
              max={18}
              step={1}
            />
            <RangeControl
              label={__("Hauteur (px)", "archi-graph")}
              value={height}
              onChange={(value) => setAttributes({ height: value })}
              min={200}
              max={800}
              step={50}
            />
            <SelectControl
              label={__("Style de carte", "archi-graph")}
              value={mapStyle}
              options={[
                { label: __("OpenStreetMap", "archi-graph"), value: "osm" },
                { label: __("OpenStreetMap France", "archi-graph"), value: "osm-fr" },
                { label: __("Terrain", "archi-graph"), value: "terrain" },
                { label: __("Satellite", "archi-graph"), value: "satellite" },
              ]}
              onChange={(value) => setAttributes({ mapStyle: value })}
            />
            <ToggleControl
              label={__("Afficher les contrôles", "archi-graph")}
              checked={showControls}
              onChange={(value) => setAttributes({ showControls: value })}
            />
            <ToggleControl
              label={__("Activer le zoom à la molette", "archi-graph")}
              checked={enableScroll}
              onChange={(value) => setAttributes({ enableScroll: value })}
            />
          </PanelBody>

          <PanelBody title={__("Marqueurs", "archi-graph")} initialOpen={false}>
            <Button onClick={addMarker} variant="primary" style={{ marginBottom: "15px" }}>
              {__("+ Ajouter un marqueur", "archi-graph")}
            </Button>
            {markers.length === 0 && (
              <p style={{ color: "#666", fontStyle: "italic" }}>
                {__("Aucun marqueur. Ajoutez-en un pour commencer.", "archi-graph")}
              </p>
            )}
            {markers.map((marker, index) => (
              <div
                key={marker.id}
                style={{
                  padding: "15px",
                  marginBottom: "10px",
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                  background: "#f9f9f9",
                }}
              >
                <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "10px" }}>
                  <strong>{__("Marqueur", "archi-graph")} {index + 1}</strong>
                  <Button
                    onClick={() => removeMarker(marker.id)}
                    isDestructive
                    isSmall
                  >
                    {__("Supprimer", "archi-graph")}
                  </Button>
                </div>
                <TextControl
                  label={__("Titre", "archi-graph")}
                  value={marker.title}
                  onChange={(value) => updateMarker(marker.id, "title", value)}
                />
                <TextControl
                  label={__("Latitude", "archi-graph")}
                  type="number"
                  value={marker.lat}
                  onChange={(value) => updateMarker(marker.id, "lat", parseFloat(value))}
                  step="0.0001"
                />
                <TextControl
                  label={__("Longitude", "archi-graph")}
                  type="number"
                  value={marker.lng}
                  onChange={(value) => updateMarker(marker.id, "lng", parseFloat(value))}
                  step="0.0001"
                />
                <div style={{ marginBottom: "10px" }}>
                  <label style={{ display: "block", marginBottom: "5px", fontWeight: "600" }}>
                    {__("Description", "archi-graph")}
                  </label>
                  <textarea
                    value={marker.description}
                    onChange={(e) => updateMarker(marker.id, "description", e.target.value)}
                    style={{ width: "100%", padding: "8px", minHeight: "60px" }}
                    placeholder={__("Description du marqueur...", "archi-graph")}
                  />
                </div>
                <div style={{ marginBottom: "10px" }}>
                  <label style={{ display: "block", marginBottom: "5px", fontWeight: "600" }}>
                    {__("Couleur du marqueur", "archi-graph")}
                  </label>
                  <input
                    type="color"
                    value={marker.color}
                    onChange={(e) => updateMarker(marker.id, "color", e.target.value)}
                    style={{ width: "100%", height: "40px" }}
                  />
                </div>
              </div>
            ))}
          </PanelBody>
        </InspectorControls>

        <div
          style={{
            border: "2px solid #0073aa",
            padding: "20px",
            borderRadius: "8px",
            background: "#fff",
          }}
        >
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "15px" }}>
            <h3 style={{ margin: 0 }}>{__("🗺️ Carte Interactive", "archi-graph")}</h3>
            <div style={{ fontSize: "12px", color: "#666" }}>
              {markers.length} {__("marqueur(s)", "archi-graph")}
            </div>
          </div>

          {/* Vraie carte Leaflet dans l'éditeur */}
          <div
            ref={mapRef}
            style={{
              width: "100%",
              height: `${height}px`,
              borderRadius: "4px",
              overflow: "hidden",
              border: "1px solid #ddd",
            }}
          />

          <div style={{ marginTop: "15px", fontSize: "12px", color: "#666", textAlign: "center" }}>
            {__("📍 Lat:", "archi-graph")} {latitude.toFixed(4)}, 
            {__(" Lng:", "archi-graph")} {longitude.toFixed(4)}, 
            {__(" Zoom:", "archi-graph")} {zoom} · 
            {__("Style:", "archi-graph")} {mapStyle}
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});
