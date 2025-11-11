# New Gutenberg Blocks - Image Features

## Summary

Three new advanced Gutenberg blocks have been created for the Archi Graph theme, focusing on immersive image experiences with modern effects:

1. **Image Comparison Slider (Before/After)** - Interactive slider for comparing two images
2. **Full-Size Parallax Image** - Immersive full-screen images with advanced parallax effects

## 🎯 Block 1: Image Comparison Slider (Before/After)

**Block Name:** `archi-graph/image-comparison-slider`

### Features
- ✅ Interactive drag slider to compare two images
- ✅ Vertical (left/right) or horizontal (top/bottom) orientation
- ✅ Customizable initial slider position (0-100%)
- ✅ Multiple aspect ratios (16:9, 4:3, 1:1, 3:4, original)
- ✅ Customizable handle color
- ✅ Optional "Before" and "After" labels
- ✅ Touch and mouse support
- ✅ Smooth dragging animation
- ✅ Responsive design

### Usage
1. Add the "Comparaison Avant/Après" block in Gutenberg
2. Upload the "Before" image
3. Upload the "After" image
4. Customize settings in the sidebar:
   - Orientation (vertical/horizontal)
   - Initial position
   - Aspect ratio
   - Handle color
   - Labels

### Files Created
- **React Component:** `assets/js/blocks/image-comparison-slider.jsx`
- **PHP Renderer:** `inc/blocks/content/image-comparison-slider.php`
- **CSS Styles:** `assets/css/image-comparison-slider.css`
- **Bundle:** `dist/js/image-comparison-slider.bundle.js` (7.6 KiB)

### Attributes
```javascript
{
  beforeImageUrl: string,
  beforeImageId: number,
  beforeImageAlt: string,
  afterImageUrl: string,
  afterImageId: number,
  afterImageAlt: string,
  initialPosition: number (0-100),
  orientation: 'vertical' | 'horizontal',
  showLabels: boolean,
  beforeLabel: string (default: "Avant"),
  afterLabel: string (default: "Après"),
  aspectRatio: '16-9' | '4-3' | '1-1' | '3-4' | 'original',
  handleColor: string (hex color)
}
```

### CSS Classes
- `.archi-image-comparison-slider` - Main container
- `.orientation-vertical` / `.orientation-horizontal` - Orientation variants
- `.comparison-container` - Image wrapper
- `.before-image` / `.after-image` - Image layers
- `.comparison-slider-handle` - Draggable handle
- `.handle-circle` - Handle icon
- `.image-label` - Before/After labels

---

## 🎯 Block 2: Full-Size Parallax Image

**Block Name:** `archi-graph/fullsize-parallax-image`

### Features
- ✅ Full viewport height or custom height
- ✅ Multiple parallax effects:
  - Scroll parallax (with speed control)
  - Fixed background
  - Zoom on hover
  - No effect (static)
- ✅ Customizable overlay (color + opacity)
- ✅ Text overlay with 7 position options
- ✅ Object-fit options (cover, contain, fill)
- ✅ Performance-optimized with `requestAnimationFrame`
- ✅ Responsive with mobile optimizations
- ✅ Accessibility support (reduced motion)

### Usage
1. Add the "Image Pleine Taille Parallax" block in Gutenberg
2. Upload a high-resolution image
3. Customize in the sidebar:
   - Height mode (full viewport/custom/auto)
   - Parallax effect type
   - Speed (for scroll parallax)
   - Overlay settings
   - Text overlay
   - Text position

### Files Created
- **React Component:** `assets/js/blocks/fullsize-parallax-image.jsx`
- **PHP Renderer:** `inc/blocks/content/fullsize-parallax-image.php`
- **CSS Styles:** `assets/css/fullsize-parallax-image.css`
- **Bundle:** `dist/js/fullsize-parallax-image.bundle.js` (8.8 KiB)

### Attributes
```javascript
{
  imageUrl: string,
  imageId: number,
  imageAlt: string,
  heightMode: 'full-viewport' | 'custom' | 'auto',
  customHeight: number (px),
  parallaxEffect: 'scroll' | 'fixed' | 'zoom' | 'none',
  parallaxSpeed: number (0-1),
  overlayEnabled: boolean,
  overlayColor: string (hex),
  overlayOpacity: number (0-100),
  textEnabled: boolean,
  textContent: string (HTML),
  textPosition: 'center' | 'top' | 'bottom' | 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right',
  textColor: string (hex),
  enableZoom: boolean,
  objectFit: 'cover' | 'contain' | 'fill'
}
```

### CSS Classes
- `.archi-fullsize-parallax-image` - Main container
- `.height-full-viewport` / `.height-custom` / `.height-auto` - Height modes
- `.parallax-scroll` / `.parallax-fixed` / `.parallax-zoom` / `.parallax-none` - Effect types
- `.fullsize-image-container` - Image wrapper
- `.parallax-image` - The actual image
- `.image-overlay` - Overlay layer
- `.image-text` - Text overlay
- `.text-position-*` - Text position modifiers

---

## 🔧 Technical Implementation

### Webpack Configuration
Updated `webpack.config.js` to include the new block entry points:
```javascript
entry: {
  // ... existing blocks
  "image-comparison-slider": "./assets/js/blocks/image-comparison-slider.jsx",
  "fullsize-parallax-image": "./assets/js/blocks/fullsize-parallax-image.jsx",
}
```

### Blocks Loader
Updated `inc/blocks/_loader.php` to:
1. Register new block scripts with WordPress dependencies
2. Enqueue new CSS files for both blocks
3. Include scripts in editor and frontend

### CSS Files Enqueued
- `assets/css/image-comparison-slider.css` - Before/After slider styles
- `assets/css/fullsize-parallax-image.css` - Full-size parallax styles

### Build Output
```
✅ image-comparison-slider.bundle.js (7.6 KiB)
✅ fullsize-parallax-image.bundle.js (8.8 KiB)
```

---

## 📱 Responsive Design

### Image Comparison Slider
- Touch support for mobile devices
- Smaller handle size on mobile (40px vs 48px)
- Adjusted label positioning
- Optimized for both portrait and landscape

### Full-Size Parallax Image
- Reduced height on tablets (70vh) and mobile (60vh)
- Parallax effects disabled on mobile for performance
- Fixed backgrounds converted to absolute positioning
- Simplified text positions on small screens
- Respects `prefers-reduced-motion` for accessibility

---

## 🎨 Customization Examples

### Before/After Slider - Architectural Renovation
```
Before: Old building facade
After: Renovated modern facade
Orientation: Vertical
Initial Position: 50%
Aspect Ratio: 16:9
Handle Color: #3498db
Labels: "Avant" / "Après"
```

### Full-Size Parallax - Portfolio Hero
```
Image: High-res project photo
Height: Full viewport (100vh)
Parallax Effect: Scroll (speed: 0.5)
Overlay: Black at 30% opacity
Text: "Architecture Moderne"
Text Position: Center
Text Color: White
```

---

## 🚀 Performance Optimizations

1. **Lazy Loading:** Images use `loading="lazy"` attribute
2. **RequestAnimationFrame:** Smooth parallax animations
3. **Throttling:** Scroll events are throttled for performance
4. **CSS Will-Change:** Optimized for GPU acceleration
5. **Mobile Optimizations:** Effects reduced/disabled on mobile
6. **Code Splitting:** Separate bundles for each block

---

## ✅ Browser Compatibility

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)
- ⚠️ IE11 not supported (uses modern CSS features)

---

## 📋 Next Steps

1. ✅ **Test in Gutenberg Editor** - Verify blocks appear and work correctly
2. ✅ **Test on Frontend** - Check rendering and interactions
3. ✅ **Test Responsive** - Verify mobile/tablet behavior
4. ✅ **Test Performance** - Check loading times and animations
5. 📝 **Add to Theme Documentation** - Update user guides

---

## 🐛 Troubleshooting

### Blocks Don't Appear in Editor
1. Clear WordPress cache
2. Rebuild assets: `npm run build`
3. Check browser console for JavaScript errors
4. Verify PHP files in `inc/blocks/content/` are present

### Parallax Effect Not Working
1. Check that JavaScript is enabled
2. Verify the parallax effect is set to "scroll" not "none"
3. Check browser console for errors
4. Test in a different browser

### Images Not Loading
1. Verify image URLs are correct
2. Check file permissions
3. Test with different image formats (JPG, PNG, WebP)
4. Check browser console for 404 errors

---

## 📚 Documentation References

- WordPress Block Editor Handbook: https://developer.wordpress.org/block-editor/
- React Components: https://react.dev/
- CSS Custom Properties: https://developer.mozilla.org/en-US/docs/Web/CSS/--*
- Intersection Observer API: https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API

---

**Created:** November 8, 2025  
**Author:** GitHub Copilot  
**Theme:** Archi Graph Template  
**Version:** 1.0.5
