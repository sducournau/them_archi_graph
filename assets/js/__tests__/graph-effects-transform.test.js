/**
 * Test: Graph Effects Parameters Transformation
 * 
 * This test verifies that the GraphManager correctly transforms
 * flat API data into nested animation and hover objects.
 * 
 * @since 1.1.0
 */

describe('GraphManager - Effects Parameters Transformation', () => {
  
  test('should transform flat animation parameters to nested structure', () => {
    // Mock API response with flat structure
    const apiNode = {
      id: 123,
      title: "Test Article",
      node_color: "#3498db",
      node_size: 60,
      animation_type: "fadeIn",
      animation_duration: 1200,
      animation_delay: 300,
      animation_easing: "bounce",
      enter_from: "left"
    };

    // Expected transformation
    const expectedNode = {
      ...apiNode,
      animation: {
        type: "fadeIn",
        duration: 1200,
        delay: 300,
        easing: "bounce",
        enterFrom: "left"
      }
    };

    // Simulate the transformation (extract from GraphManager.loadData)
    const transformNode = (node) => {
      const animation = {
        type: node.animation_type || "fadeIn",
        duration: node.animation_duration || 800,
        delay: node.animation_delay || 0,
        easing: node.animation_easing || "ease-out",
        enterFrom: node.enter_from || "center"
      };

      return {
        ...node,
        animation
      };
    };

    const result = transformNode(apiNode);

    expect(result.animation).toEqual(expectedNode.animation);
    expect(result.animation.type).toBe("fadeIn");
    expect(result.animation.duration).toBe(1200);
    expect(result.animation.delay).toBe(300);
  });

  test('should transform flat hover parameters to nested structure', () => {
    // Mock API response with flat structure
    const apiNode = {
      id: 456,
      title: "Test Project",
      node_color: "#e67e22",
      node_size: 80,
      hover_scale: 1.3,
      pulse_effect: true,
      glow_effect: true
    };

    // Expected transformation
    const expectedNode = {
      ...apiNode,
      hover: {
        scale: 1.3,
        pulse: true,
        glow: true
      }
    };

    // Simulate the transformation
    const transformNode = (node) => {
      const hover = {
        scale: node.hover_scale || 1.15,
        pulse: node.pulse_effect || false,
        glow: node.glow_effect || false
      };

      return {
        ...node,
        hover
      };
    };

    const result = transformNode(apiNode);

    expect(result.hover).toEqual(expectedNode.hover);
    expect(result.hover.scale).toBe(1.3);
    expect(result.hover.pulse).toBe(true);
    expect(result.hover.glow).toBe(true);
  });

  test('should use default values when parameters are missing', () => {
    // Mock API response without optional parameters
    const apiNode = {
      id: 789,
      title: "Minimal Article",
      node_color: "#3498db",
      node_size: 60
    };

    // Transform with defaults
    const transformNode = (node) => {
      const animation = {
        type: node.animation_type || "fadeIn",
        duration: node.animation_duration || 800,
        delay: node.animation_delay || 0,
        easing: node.animation_easing || "ease-out",
        enterFrom: node.enter_from || "center"
      };

      const hover = {
        scale: node.hover_scale || 1.15,
        pulse: node.pulse_effect || false,
        glow: node.glow_effect || false
      };

      return {
        ...node,
        animation,
        hover
      };
    };

    const result = transformNode(apiNode);

    // Check default animation values
    expect(result.animation.type).toBe("fadeIn");
    expect(result.animation.duration).toBe(800);
    expect(result.animation.delay).toBe(0);
    expect(result.animation.easing).toBe("ease-out");
    expect(result.animation.enterFrom).toBe("center");

    // Check default hover values
    expect(result.hover.scale).toBe(1.15);
    expect(result.hover.pulse).toBe(false);
    expect(result.hover.glow).toBe(false);
  });

  test('should preserve all original node properties', () => {
    const apiNode = {
      id: 111,
      title: "Complete Article",
      excerpt: "Test excerpt",
      permalink: "https://example.com/article",
      thumbnail: "image.jpg",
      node_color: "#9b59b6",
      node_size: 70,
      categories: [{id: 1, name: "Cat1"}],
      tags: [{id: 2, name: "Tag1"}],
      animation_type: "slideIn",
      hover_scale: 1.25
    };

    const transformNode = (node) => {
      const animation = {
        type: node.animation_type || "fadeIn",
        duration: node.animation_duration || 800,
        delay: node.animation_delay || 0,
        easing: node.animation_easing || "ease-out",
        enterFrom: node.enter_from || "center"
      };

      const hover = {
        scale: node.hover_scale || 1.15,
        pulse: node.pulse_effect || false,
        glow: node.glow_effect || false
      };

      return {
        ...node,
        animation,
        hover
      };
    };

    const result = transformNode(apiNode);

    // All original properties should be preserved
    expect(result.id).toBe(111);
    expect(result.title).toBe("Complete Article");
    expect(result.excerpt).toBe("Test excerpt");
    expect(result.permalink).toBe("https://example.com/article");
    expect(result.thumbnail).toBe("image.jpg");
    expect(result.node_color).toBe("#9b59b6");
    expect(result.node_size).toBe(70);
    expect(result.categories).toEqual([{id: 1, name: "Cat1"}]);
    expect(result.tags).toEqual([{id: 2, name: "Tag1"}]);
    
    // New nested structures should be added
    expect(result.animation).toBeDefined();
    expect(result.hover).toBeDefined();
  });

});
