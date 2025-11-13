import React, { useState, useEffect } from 'react';

/**
 * GraphEditorPanel Component
 * 
 * Separate UI component for graph edit mode functionality
 * Extracted from GraphContainer.jsx to improve code organization
 * 
 * @param {Object} props
 * @param {boolean} props.isEditMode - Whether edit mode is active
 * @param {Function} props.onEditModeChange - Callback when edit mode changes
 * @param {Object} props.selectedNode - Currently selected node
 * @param {Function} props.onSavePositions - Callback to save node positions
 * @param {Function} props.onToggleNodeVisibility - Callback to toggle node visibility
 * @param {Function} props.onUpdateNodeParams - Callback to update node parameters
 * @param {Object} props.config - Editor configuration from WordPress
 */
const GraphEditorPanel = ({
  isEditMode = false,
  onEditModeChange,
  selectedNode = null,
  onSavePositions,
  onToggleNodeVisibility,
  onUpdateNodeParams,
  config = {}
}) => {
  const [showPanel, setShowPanel] = useState(false);
  const [showAdvancedParams, setShowAdvancedParams] = useState(false);
  const [nodeParams, setNodeParams] = useState({
    shape: 'circle',
    color: '#3498db',
    size: 60,
    icon: '',
    badge: ''
  });

  // Check if user can edit
  const canEdit = config.canEdit || false;
  const strings = config.strings || {};

  // Update node params when selected node changes
  useEffect(() => {
    if (selectedNode) {
      setNodeParams({
        shape: selectedNode.node_shape || 'circle',
        color: selectedNode.node_color || '#3498db',
        size: selectedNode.node_size || 80,
        icon: selectedNode.node_icon || '',
        badge: selectedNode.node_badge || ''
      });
    }
  }, [selectedNode]);

  // Don't render if user cannot edit
  if (!canEdit) {
    return null;
  }

  /**
   * Toggle edit mode
   */
  const handleEditModeToggle = (enabled) => {
    onEditModeChange(enabled);
  };

  /**
   * Apply advanced parameters
   */
  const handleApplyParams = () => {
    if (selectedNode && onUpdateNodeParams) {
      onUpdateNodeParams(selectedNode.id, nodeParams);
    }
    setShowAdvancedParams(false);
  };

  /**
   * Toggle node visibility
   */
  const handleToggleVisibility = () => {
    if (selectedNode && onToggleNodeVisibility) {
      onToggleNodeVisibility(selectedNode.id);
    }
  };

  return (
    <>
      {/* Floating edit mode toggle button */}
      {!showPanel && (
        <button
          className="archi-editor-toggle-btn"
          onClick={() => setShowPanel(true)}
          title={strings.editMode || 'Edit Mode'}
          aria-label="Open edit panel"
        >
          ✏️
        </button>
      )}

      {/* Editor panel */}
      {showPanel && (
        <div className="archi-editor-panel">
          {/* Header */}
          <div className="archi-editor-header">
            <h3>🎨 {strings.editMode || 'Edit Mode'}</h3>
            <button
              className="archi-editor-close"
              onClick={() => setShowPanel(false)}
              aria-label="Close"
            >
              ×
            </button>
          </div>

          <div className="archi-editor-body">
            {/* Edit mode toggle */}
            <div className="archi-editor-section">
              <label className="archi-toggle-label">
                <input
                  type="checkbox"
                  checked={isEditMode}
                  onChange={(e) => handleEditModeToggle(e.target.checked)}
                />
                <span className="archi-toggle-slider"></span>
                <span className="archi-toggle-text">
                  {strings.enableEditing || 'Enable Editing'}
                </span>
              </label>
            </div>

            {/* Tools section - shown when edit mode is active */}
            {isEditMode && (
              <div className="archi-editor-section archi-tools-section">
                <h4>{strings.tools || 'Tools'}</h4>
                
                <button
                  className="archi-editor-btn archi-btn-save"
                  onClick={onSavePositions}
                >
                  💾 {strings.savePositions || 'Save Positions'}
                </button>

                <p className="archi-help-text">
                  {strings.dragHelp || 'Drag nodes to reposition them'}
                </p>
              </div>
            )}

            {/* Selected node section */}
            {isEditMode && selectedNode && (
              <div className="archi-editor-section archi-node-section">
                <h4>{strings.selectedNode || 'Selected Node'}</h4>
                
                <div className="archi-node-info">
                  <p className="archi-node-title">{selectedNode.title}</p>
                  <p className="archi-node-id">ID: {selectedNode.id}</p>
                  <p className="archi-node-type">
                    Type: {selectedNode.post_type}
                  </p>
                </div>

                <button
                  className="archi-editor-btn archi-btn-toggle-visibility"
                  onClick={handleToggleVisibility}
                >
                  👁️ {strings.toggleVisibility || 'Toggle Visibility'}
                </button>

                <button
                  className="archi-editor-btn archi-btn-edit-params"
                  onClick={() => setShowAdvancedParams(!showAdvancedParams)}
                >
                  ⚙️ {strings.advancedParams || 'Advanced Parameters'}
                </button>
              </div>
            )}

            {/* Advanced parameters section */}
            {showAdvancedParams && selectedNode && (
              <div className="archi-editor-section archi-params-section">
                <h4>{strings.advancedParams || 'Advanced Parameters'}</h4>

                <label>
                  {strings.shape || 'Shape'}:
                  <select
                    value={nodeParams.shape}
                    onChange={(e) => setNodeParams({...nodeParams, shape: e.target.value})}
                  >
                    <option value="circle">Circle</option>
                    <option value="square">Square</option>
                    <option value="diamond">Diamond</option>
                    <option value="triangle">Triangle</option>
                    <option value="star">Star</option>
                    <option value="hexagon">Hexagon</option>
                  </select>
                </label>

                <label>
                  {strings.color || 'Color'}:
                  <input
                    type="color"
                    value={nodeParams.color}
                    onChange={(e) => setNodeParams({...nodeParams, color: e.target.value})}
                  />
                </label>

                <label>
                  {strings.size || 'Size'}:
                  <input
                    type="range"
                    min="40"
                    max="120"
                    step="5"
                    value={nodeParams.size}
                    onChange={(e) => setNodeParams({...nodeParams, size: parseInt(e.target.value)})}
                  />
                  <span>{nodeParams.size}px</span>
                </label>

                <label>
                  {strings.icon || 'Icon'}:
                  <input
                    type="text"
                    placeholder="🏗️"
                    maxLength="2"
                    value={nodeParams.icon}
                    onChange={(e) => setNodeParams({...nodeParams, icon: e.target.value})}
                  />
                </label>

                <label>
                  {strings.badge || 'Badge'}:
                  <select
                    value={nodeParams.badge}
                    onChange={(e) => setNodeParams({...nodeParams, badge: e.target.value})}
                  >
                    <option value="">None</option>
                    <option value="new">New</option>
                    <option value="featured">Featured</option>
                    <option value="hot">Hot</option>
                    <option value="updated">Updated</option>
                  </select>
                </label>

                <div className="archi-params-actions">
                  <button
                    className="archi-editor-btn archi-btn-apply-params"
                    onClick={handleApplyParams}
                  >
                    ✅ {strings.apply || 'Apply'}
                  </button>

                  <button
                    className="archi-editor-btn archi-btn-cancel-params"
                    onClick={() => setShowAdvancedParams(false)}
                  >
                    ❌ {strings.cancel || 'Cancel'}
                  </button>
                </div>
              </div>
            )}

            {/* Status/Help */}
            {isEditMode && (
              <div className="archi-editor-status">
                <p className="archi-help-text">
                  💡 {strings.editModeHelp || 'Click on nodes to select them. Drag to reposition.'}
                </p>
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
};

export default GraphEditorPanel;
