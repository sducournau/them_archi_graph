# 🚀 Quick Start Guide - Custom Gutenberg Blocks

## Getting Started in 5 Minutes

### Step 1: Access the Block Editor

1. Log in to your WordPress admin
2. Go to **Pages** → **Add New** (or edit an existing page)
3. You're now in the Gutenberg block editor

### Step 2: Find Archi-Graph Blocks

Click the **"+" button** (top left or click in the content area) and you'll see:

- **Search bar** at the top - type "archi" or "image" or "parallax"
- **Block categories** - scroll down to **"Archi Graph"**

All custom blocks are in the **"Archi Graph"** category.

---

## 📦 Available Blocks Quick Reference

### 1. 🎬 Fixed Background (Parallax Hero)

**Best for:** Opening hero sections with stunning parallax effect

**Quick Add:**
1. Click "+" → Search "Fixed Background"
2. Click "Select Image" → Choose from media library
3. Adjust settings in right sidebar:
   - Height slider (300-1000px)
   - Overlay opacity (0-100%)
   - Overlay color (color picker)
   - Content position (top/center/bottom)
4. Add text content (optional)

**💡 Pro Tip:** Use dark overlay (40-60% opacity) for better text contrast

---

### 2. 🖼️ Full-Width Image

**Best for:** Large architectural photos that need maximum impact

**Quick Add:**
1. Click "+" → Search "Image Pleine Largeur"
2. Select image from media library
3. Choose height mode in sidebar:
   - **Normal (70vh)** - Standard hero height
   - **Full (100vh)** - Entire viewport
   - **Half (50vh)** - Compact section
4. Add caption if needed

**💡 Pro Tip:** Use "Full" mode for hero images, "Normal" for content images

---

### 3. 📜 Sticky Scroll Section

**Best for:** Detailed presentations where image provides context for scrolling content

**Quick Add:**
1. Click "+" → Search "Sticky Scroll"
2. Select sticky image (stays fixed)
3. Add title and intro text
4. Click **"Add Item"** button for each step/feature
5. Fill in item titles and descriptions
6. Choose image position (left/right) in sidebar

**💡 Pro Tip:** Perfect for design process, project phases, or feature lists

---

### 4. 🎨 Images in Columns

**Best for:** Comparing multiple views or showcasing details

**Quick Add:**
1. Click "+" → Search "Images en Colonnes"
2. Choose 2 or 3 columns in sidebar
3. Click to open media library (gallery mode)
4. Select multiple images (hold Ctrl/Cmd)
5. Add individual captions

**💡 Pro Tip:** Use 3 columns for detail shots, 2 columns for before/after

---

### 5. 🏛️ Portrait Image

**Best for:** Vertical architectural photos (towers, facades, interior height)

**Quick Add:**
1. Click "+" → Search "Image Portrait"
2. Select vertical image
3. Add caption
4. Image auto-centers with limited width

**💡 Pro Tip:** Great for emphasizing vertical elements

---

### 6. 🎯 Cover Block

**Best for:** Text overlays on images, call-to-actions

**Quick Add:**
1. Click "+" → Search "Cover"
2. Select background image or color
3. Adjust overlay in sidebar
4. Add text content
5. Choose alignment

**💡 Pro Tip:** Use for section dividers with quotes or key messages

---

## 🎨 Common Workflow Examples

### Example 1: Project Presentation Page

```
1. Fixed Background (Hero with project title)
   ↓
2. Regular text block (Project description)
   ↓
3. Images in Columns (3 columns - different views)
   ↓
4. Sticky Scroll (Design process with 5 steps)
   ↓
5. Full-Width Image (Final result photo)
   ↓
6. Cover Block (Call-to-action or client testimonial)
```

### Example 2: Portfolio Homepage

```
1. Fixed Background (Welcome hero with parallax)
   ↓
2. Regular heading + paragraph (Introduction)
   ↓
3. Images in Columns (2 columns - Featured projects)
   ↓
4. Full-Width Image (Signature project)
   ↓
5. Sticky Scroll (Services/Expertise)
```

### Example 3: Single Project Page

```
1. Cover Block (Project title + hero image)
   ↓
2. Regular paragraph (Project overview)
   ↓
3. Images in Columns (3 columns - Exterior views)
   ↓
4. Portrait Image (Vertical element detail)
   ↓
5. Sticky Scroll (Project specifications)
   ↓
6. Full-Width Image (Interior panorama)
   ↓
7. Images in Columns (2 columns - Detail shots)
```

---

## ⚙️ Block Settings Cheat Sheet

### Fixed Background Block
- **Inspector Controls (Right Sidebar):**
  - ☑️ Enable Parallax (toggle)
  - 📏 Minimum Height (300-1000px)
  - 🎨 Overlay Opacity (0-100%)
  - 🌈 Overlay Color (color picker)
  - 📍 Content Position (top/center/bottom)

### Sticky Scroll Block
- **Inspector Controls:**
  - ◀️▶️ Image Position (left/right)
- **Content Area:**
  - ✏️ Title field
  - ✏️ Intro text area
  - ➕ "Add Item" button for list items

### Full-Width Image Block
- **Inspector Controls:**
  - 📐 Height Mode (normal/full-viewport/half-viewport)
  - 🔤 Alt Text field
  - 💬 Caption field

### Images in Columns Block
- **Inspector Controls:**
  - #️⃣ Columns (2 or 3)
- **Content Area:**
  - 🖼️ Gallery selector
  - ✏️ Caption for each image

---

## 🎯 Tips for Best Results

### Images
- ✅ **Use high-quality images** (min 1920px wide for full-width)
- ✅ **Optimize file size** before uploading (use WebP if possible)
- ✅ **Add alt text** for all images (accessibility + SEO)
- ✅ **Use consistent aspect ratios** in column layouts

### Parallax/Fixed Background
- ✅ **Choose simple backgrounds** (busy images can be distracting)
- ✅ **Test on mobile** (effect is disabled for performance)
- ✅ **Use overlay for text contrast** (40-60% works well)
- ✅ **Keep text short** (hero sections should be concise)

### Sticky Scroll
- ✅ **Image should provide context** for the content
- ✅ **Use 3-7 items** (too many = too much scrolling)
- ✅ **Write clear titles** for each item
- ✅ **Be concise** in descriptions

### Layout Composition
- ✅ **Alternate layouts** (don't stack same blocks)
- ✅ **Use white space** (add regular paragraph blocks between)
- ✅ **Create rhythm** (vary image sizes/heights)
- ✅ **Tell a story** (logical flow from top to bottom)

---

## 🐛 Troubleshooting

### Block doesn't appear in editor?
```bash
# Rebuild blocks
cd /path/to/theme
npm run build
```
Then refresh browser (Ctrl+F5)

### Parallax not working?
- Check if "Enable Parallax" toggle is ON
- Check if you're on mobile (disabled by default)
- Verify image uploaded successfully

### Sticky scroll not sticking?
- Check browser compatibility (position: sticky)
- Ensure enough content below to scroll
- On mobile, it converts to regular layout (intended)

### Images not showing?
- Verify image URLs are correct
- Check file permissions
- Clear browser cache
- Check WordPress media library

---

## 📱 Mobile Behavior

### Auto-Adaptations:
- ✅ **Parallax disabled** on screens <768px (battery savings)
- ✅ **Sticky scroll converts** to single column
- ✅ **Image columns stack** vertically
- ✅ **Reduced padding** for better mobile spacing

---

## 🎓 Learning Resources

### In Your Theme:
- 📄 **GUTENBERG-BLOCKS-ANALYSIS.md** - Complete technical guide
- 📄 **docs/NEW-GUTENBERG-BLOCKS.md** - Implementation details
- 📄 **template-blocks-demo.php** - Live examples (create page with this template)

### WordPress Resources:
- 📚 [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- 📚 [WordPress.org Support](https://wordpress.org/support/)

---

## ✨ Quick Keyboard Shortcuts

- **`/`** - Type to search for blocks
- **`Ctrl/Cmd + Shift + ,`** - Open block settings
- **`Ctrl/Cmd + Shift + D`** - Duplicate block
- **`Alt + Shift + N`** - Navigate to next block
- **`Alt + Shift + P`** - Navigate to previous block

---

## 🎉 Ready to Create!

You now know how to use all custom Gutenberg blocks. Start creating beautiful architectural presentations!

### Next Steps:
1. ✅ Create a new page
2. ✅ Try each block type
3. ✅ Use the demo template as reference
4. ✅ Build your first project showcase

**Have fun creating! 🚀**

---

**Need Help?** Check the documentation files or contact your theme developer.

**Last Updated:** November 8, 2025  
**Theme Version:** Archi-Graph Template
