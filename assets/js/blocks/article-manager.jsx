/**
 * Bloc Gutenberg pour la gestion complète des articles
 * Compatible avec : post, archi_project, archi_illustration
 *
 * Ce bloc permet de gérer :
 * - Image featured
 * - Titre et description
 * - Métadonnées (auteur, date, type, etc.)
 * - Catégories et étiquettes
 * - Paramètres du nœud de visualisation (graphique)
 * - Champs personnalisés selon le type de post
 */

import { registerBlockType } from "@wordpress/blocks";
import {
  PanelBody,
  PanelRow,
  ToggleControl,
  SelectControl,
  ColorPicker,
  RangeControl,
  TextControl,
  TextareaControl,
  Button,
  Card,
  CardBody,
  CardHeader,
  Placeholder,
} from "@wordpress/components";
import {
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
  useBlockProps,
} from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";

const { Fragment } = wp.element;

registerBlockType("archi-graph/article-manager", {
  title: __("Gestionnaire d'Article", "archi-graph"),
  description: __(
    "Bloc complet pour gérer tous les aspects d'un article : métadonnées, visualisation, image, tags",
    "archi-graph"
  ),
  icon: "admin-settings",
  category: "archi-graph",
  keywords: [
    __("article", "archi-graph"),
    __("métadonnées", "archi-graph"),
    __("graphique", "archi-graph"),
  ],

  attributes: {
    // Affichage général
    showFeaturedImage: {
      type: "boolean",
      default: true,
    },
    showTitle: {
      type: "boolean",
      default: true,
    },
    showExcerpt: {
      type: "boolean",
      default: true,
    },
    showContent: {
      type: "boolean",
      default: false,
    },

    // Métadonnées
    showAuthor: {
      type: "boolean",
      default: true,
    },
    showDate: {
      type: "boolean",
      default: true,
    },
    showCategories: {
      type: "boolean",
      default: true,
    },
    showTags: {
      type: "boolean",
      default: true,
    },
    showPostType: {
      type: "boolean",
      default: true,
    },
    showWordCount: {
      type: "boolean",
      default: false,
    },

    // Style d'affichage
    layoutStyle: {
      type: "string",
      default: "card", // card, list, grid, minimal
    },
    imagePosition: {
      type: "string",
      default: "top", // top, left, right, background
    },

    // Sections spécifiques aux types de post
    showProjectDetails: {
      type: "boolean",
      default: true,
    },
    showIllustrationDetails: {
      type: "boolean",
      default: true,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const {
      showFeaturedImage,
      showTitle,
      showExcerpt,
      showContent,
      showAuthor,
      showDate,
      showCategories,
      showTags,
      showPostType,
      showWordCount,
      layoutStyle,
      imagePosition,
      showProjectDetails,
      showIllustrationDetails,
    } = attributes;

    // Récupérer les données de l'article actuel
    const postData = useSelect((select) => {
      const { getCurrentPost } = select("core/editor");
      const { getEditedPostAttribute, getEditedPostContent } =
        select("core/editor");

      return {
        post: getCurrentPost(),
        title: getEditedPostAttribute("title"),
        excerpt: getEditedPostAttribute("excerpt"),
        content: getEditedPostContent(),
        featuredMedia: getEditedPostAttribute("featured_media"),
        categories: getEditedPostAttribute("categories"),
        tags: getEditedPostAttribute("tags"),
        author: getEditedPostAttribute("author"),
        date: getEditedPostAttribute("date"),
        type: getEditedPostAttribute("type"),
      };
    }, []);

    return (
      <Fragment>
        <InspectorControls>
          {/* Paramètres d'affichage */}
          <PanelBody title={__("Affichage", "archi-graph")} initialOpen={true}>
            <ToggleControl
              label={__("Image à la une", "archi-graph")}
              checked={showFeaturedImage}
              onChange={(value) => setAttributes({ showFeaturedImage: value })}
            />
            <ToggleControl
              label={__("Titre", "archi-graph")}
              checked={showTitle}
              onChange={(value) => setAttributes({ showTitle: value })}
            />
            <ToggleControl
              label={__("Extrait", "archi-graph")}
              checked={showExcerpt}
              onChange={(value) => setAttributes({ showExcerpt: value })}
            />
            <ToggleControl
              label={__("Contenu complet", "archi-graph")}
              checked={showContent}
              onChange={(value) => setAttributes({ showContent: value })}
            />

            <SelectControl
              label={__("Style de mise en page", "archi-graph")}
              value={layoutStyle}
              options={[
                { label: __("Carte", "archi-graph"), value: "card" },
                { label: __("Liste", "archi-graph"), value: "list" },
                { label: __("Grille", "archi-graph"), value: "grid" },
                { label: __("Minimal", "archi-graph"), value: "minimal" },
              ]}
              onChange={(value) => setAttributes({ layoutStyle: value })}
            />

            {showFeaturedImage && (
              <SelectControl
                label={__("Position de l'image", "archi-graph")}
                value={imagePosition}
                options={[
                  { label: __("En haut", "archi-graph"), value: "top" },
                  { label: __("À gauche", "archi-graph"), value: "left" },
                  { label: __("À droite", "archi-graph"), value: "right" },
                  {
                    label: __("En arrière-plan", "archi-graph"),
                    value: "background",
                  },
                ]}
                onChange={(value) => setAttributes({ imagePosition: value })}
              />
            )}
          </PanelBody>

          {/* Métadonnées */}
          <PanelBody
            title={__("Métadonnées", "archi-graph")}
            initialOpen={true}
          >
            <ToggleControl
              label={__("Auteur", "archi-graph")}
              checked={showAuthor}
              onChange={(value) => setAttributes({ showAuthor: value })}
            />
            <ToggleControl
              label={__("Date de publication", "archi-graph")}
              checked={showDate}
              onChange={(value) => setAttributes({ showDate: value })}
            />
            <ToggleControl
              label={__("Type de post", "archi-graph")}
              checked={showPostType}
              onChange={(value) => setAttributes({ showPostType: value })}
            />
            <ToggleControl
              label={__("Nombre de mots", "archi-graph")}
              checked={showWordCount}
              onChange={(value) => setAttributes({ showWordCount: value })}
            />
          </PanelBody>

          {/* Taxonomies */}
          <PanelBody
            title={__("Catégories et Étiquettes", "archi-graph")}
            initialOpen={true}
          >
            <ToggleControl
              label={__("Catégories", "archi-graph")}
              checked={showCategories}
              onChange={(value) => setAttributes({ showCategories: value })}
            />
            <ToggleControl
              label={__("Étiquettes", "archi-graph")}
              checked={showTags}
              onChange={(value) => setAttributes({ showTags: value })}
            />
          </PanelBody>

          {/* Paramètres du graphique */}
          {/* Note: Les paramètres du graphique sont maintenant gérés par la meta box dans la sidebar */}

          {/* Détails spécifiques au type */}
          {postData && postData.type === "archi_project" && (
            <PanelBody
              title={__("Détails du Projet", "archi-graph")}
              initialOpen={false}
            >
              <ToggleControl
                label={__("Afficher les détails du projet", "archi-graph")}
                checked={showProjectDetails}
                onChange={(value) =>
                  setAttributes({ showProjectDetails: value })
                }
              />
            </PanelBody>
          )}

          {postData && postData.type === "archi_illustration" && (
            <PanelBody
              title={__("Détails de l'Illustration", "archi-graph")}
              initialOpen={false}
            >
              <ToggleControl
                label={__(
                  "Afficher les détails de l'illustration",
                  "archi-graph"
                )}
                checked={showIllustrationDetails}
                onChange={(value) =>
                  setAttributes({ showIllustrationDetails: value })
                }
              />
            </PanelBody>
          )}
        </InspectorControls>

        {/* Aperçu dans l'éditeur */}
        <div
          {...useBlockProps({
            className: `archi-manager archi-layout-${layoutStyle} archi-image-${imagePosition}`,
          })}
        >
          <Card>
            <CardHeader>
              <h3>📋 {__("Gestionnaire d'Article", "archi-graph")}</h3>
            </CardHeader>
            <CardBody>
              <p className="description">
                {__(
                  "Ce bloc affichera automatiquement toutes les informations de l'article selon vos paramètres.",
                  "archi-graph"
                )}
              </p>

              {/* Aperçu des paramètres actifs */}
              <div className="archi-settings-preview">
                <h4>{__("Paramètres actifs :", "archi-graph")}</h4>
                <ul>
                  {showFeaturedImage && (
                    <li>✓ {__("Image à la une", "archi-graph")}</li>
                  )}
                  {showTitle && <li>✓ {__("Titre", "archi-graph")}</li>}
                  {showExcerpt && <li>✓ {__("Extrait", "archi-graph")}</li>}
                  {showAuthor && <li>✓ {__("Auteur", "archi-graph")}</li>}
                  {showDate && <li>✓ {__("Date", "archi-graph")}</li>}
                  {showCategories && (
                    <li>✓ {__("Catégories", "archi-graph")}</li>
                  )}
                  {showTags && <li>✓ {__("Étiquettes", "archi-graph")}</li>}
                </ul>
              </div>

              <div
                style={{ marginTop: "15px", fontSize: "12px", color: "#666" }}
              >
                <p>
                  <strong>{__("Type de post :", "archi-graph")}</strong>{" "}
                  {postData && postData.type ? postData.type : __("N/A", "archi-graph")}
                </p>
                <p>
                  <strong>
                    {__("Style de mise en page :", "archi-graph")}
                  </strong>{" "}
                  {layoutStyle}
                </p>
              </div>
            </CardBody>
          </Card>
        </div>
      </Fragment>
    );
  },

  save: () => {
    // Le rendu côté serveur est géré par PHP
    return null;
  },
});
