<?php
/**
 * Template Name: Blocks Demo - Showcase
 * Description: Demonstrates all custom Gutenberg blocks for architectural presentation
 * 
 * This template is designed to showcase the full capabilities of Archi-Graph custom blocks.
 * Use this as a reference when creating portfolio pages.
 */

get_header();
?>

<article id="blocks-demo" class="archi-blocks-showcase">
    <div class="entry-content">
        
        <!-- Instructions Section -->
        <section class="demo-instructions" style="padding: 2rem; background: #f8f9fa; border-left: 4px solid #3498db; margin-bottom: 3rem;">
            <h2>🎨 Archi-Graph Custom Blocks Demo</h2>
            <p><strong>This page demonstrates all available custom Gutenberg blocks.</strong></p>
            <p>To use these blocks in your pages:</p>
            <ol>
                <li>Edit any page in the Gutenberg editor</li>
                <li>Click the "+" button to add a block</li>
                <li>Look for the <strong>"Archi Graph"</strong> category</li>
                <li>Select the block you want to use</li>
            </ol>
            <p><em>Note: This template is for demonstration purposes. Delete this section when creating actual content.</em></p>
        </section>

        <!-- 
        ============================================
        BLOCK 1: FIXED BACKGROUND / PARALLAX
        ============================================
        This block creates a hero section with parallax scrolling effect.
        The background image stays fixed while content scrolls over it.
        -->
        
        <!-- wp:archi-graph/fixed-background {
            "imageUrl":"https://images.unsplash.com/photo-1511818966892-d7d671e672a2?w=1920",
            "minHeight":600,
            "overlayOpacity":40,
            "overlayColor":"#000000",
            "content":"<h1>Welcome to Our Architectural Portfolio</h1><p>Experience modern design through immersive visual storytelling</p>",
            "contentPosition":"center",
            "enableParallax":true
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #e8f4f8; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Fixed Background Block (Parallax Effect)</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/fixed-background</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ Parallax scrolling effect (background-attachment: fixed)</li>
                <li>✅ Adjustable height (300px - 1000px)</li>
                <li>✅ Customizable overlay with color and opacity</li>
                <li>✅ Content positioning (top/center/bottom)</li>
                <li>✅ Toggle parallax on/off</li>
                <li>✅ Mobile-optimized (parallax disabled on small screens)</li>
            </ul>
            <p><strong>Perfect for:</strong> Hero sections, visual separators, project introductions</p>
        </div>

        <!-- 
        ============================================
        BLOCK 2: FULL-WIDTH IMAGE
        ============================================
        Display images that span the entire width of the screen.
        -->
        
        <!-- wp:archi-graph/image-full-width {
            "imageUrl":"https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1920",
            "imageAlt":"Modern architectural facade",
            "caption":"Contemporary facade design - Project Example",
            "heightMode":"normal"
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #fff3cd; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Full-Width Image Block</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/image-full-width</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ Three height modes: Normal (70vh), Full (100vh), Half (50vh)</li>
                <li>✅ Alt text for accessibility</li>
                <li>✅ Optional caption</li>
                <li>✅ Lazy loading for performance</li>
            </ul>
            <p><strong>Perfect for:</strong> Showcasing large architectural photos, project highlights</p>
        </div>

        <!-- 
        ============================================
        BLOCK 3: STICKY SCROLL SECTION
        ============================================
        Image stays fixed on one side while content scrolls on the other.
        Ideal for detailed project presentations.
        -->
        
        <!-- wp:archi-graph/sticky-scroll {
            "imageUrl":"https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800",
            "imagePosition":"left",
            "title":"Our Design Process",
            "content":"We follow a comprehensive approach to architectural design, ensuring every detail meets the highest standards.",
            "items":[
                {"title":"Research & Discovery","description":"Understanding the site, context, and client vision through comprehensive analysis."},
                {"title":"Conceptual Design","description":"Developing initial concepts and exploring spatial relationships."},
                {"title":"Design Development","description":"Refining the design with detailed drawings and material selections."},
                {"title":"Documentation","description":"Creating comprehensive construction documents."},
                {"title":"Construction Administration","description":"Overseeing the build to ensure design intent is maintained."}
            ]
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #d4edda; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Sticky Scroll Block</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/sticky-scroll</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ Image stays fixed (sticky) while content scrolls</li>
                <li>✅ Configurable image position (left/right)</li>
                <li>✅ Title and introductory content</li>
                <li>✅ Dynamic list of items with animations</li>
                <li>✅ FadeInUp animations with progressive delays</li>
                <li>✅ Fully responsive (single column on mobile)</li>
            </ul>
            <p><strong>Perfect for:</strong> Project process descriptions, feature lists, detailed presentations</p>
        </div>

        <!-- 
        ============================================
        BLOCK 4: IMAGES IN COLUMNS
        ============================================
        Display 2 or 3 images side-by-side in full width.
        -->
        
        <!-- wp:archi-graph/images-columns {
            "columns":3,
            "images":[
                {"url":"https://images.unsplash.com/photo-1600607687644-aac4c3eac7f4?w=600","alt":"","caption":"Interior Detail"},
                {"url":"https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=600","alt":"","caption":"Structural Design"},
                {"url":"https://images.unsplash.com/photo-1600607688960-e095ff83135b?w=600","alt":"","caption":"Material Palette"}
            ]
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #f8d7da; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Images in Columns Block</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/images-columns</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ 2 or 3 column layouts</li>
                <li>✅ Individual captions per image</li>
                <li>✅ Gallery mode selection</li>
                <li>✅ Responsive grid</li>
            </ul>
            <p><strong>Perfect for:</strong> Showcasing multiple project views, detail shots, material palettes</p>
        </div>

        <!-- 
        ============================================
        BLOCK 5: PORTRAIT IMAGE
        ============================================
        Centered vertical images with limited width.
        -->
        
        <!-- wp:archi-graph/image-portrait {
            "imageUrl":"https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=1200",
            "imageAlt":"Vertical architectural element",
            "caption":"Tower detail - Vertical emphasis in design"
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #cce5ff; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Portrait Image Block</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/image-portrait</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ Centered alignment</li>
                <li>✅ Limited width for vertical images</li>
                <li>✅ Alt text and caption support</li>
                <li>✅ Clean, focused presentation</li>
            </ul>
            <p><strong>Perfect for:</strong> Portrait-oriented photos, vertical architectural elements, detail shots</p>
        </div>

        <!-- 
        ============================================
        BLOCK 6: COVER BLOCK
        ============================================
        Enhanced cover block with text overlay.
        -->
        
        <!-- wp:archi-graph/cover-block {
            "imageUrl":"https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=1920",
            "overlayOpacity":50,
            "overlayColor":"#2c3e50",
            "content":"<h2>Award-Winning Design</h2><p>Recognized for innovation and sustainability</p>",
            "contentPosition":"center",
            "minHeight":400
        } /-->
        
        <div class="demo-block-info" style="padding: 2rem; background: #e2d4f7; margin: 2rem 0; border-radius: 8px;">
            <h3>☝️ Cover Block</h3>
            <p><strong>Block ID:</strong> <code>archi-graph/cover-block</code></p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>✅ Image or color background</li>
                <li>✅ Adjustable overlay</li>
                <li>✅ Text positioning</li>
                <li>✅ Height control</li>
            </ul>
            <p><strong>Perfect for:</strong> Call-to-action sections, highlighted content, testimonials</p>
        </div>

        <!-- Summary Section -->
        <section class="demo-summary" style="padding: 3rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; margin: 3rem 0;">
            <h2 style="color: white; margin-bottom: 1.5rem;">🎯 Block Selection Guide</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: white; font-size: 1.2rem; margin-bottom: 0.5rem;">📸 For Hero Sections</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Use: <strong>Fixed Background</strong> or <strong>Cover Block</strong></p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: white; font-size: 1.2rem; margin-bottom: 0.5rem;">📖 For Storytelling</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Use: <strong>Sticky Scroll</strong> with process steps</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: white; font-size: 1.2rem; margin-bottom: 0.5rem;">🖼️ For Galleries</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Use: <strong>Images in Columns</strong> (2 or 3)</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: white; font-size: 1.2rem; margin-bottom: 0.5rem;">🏛️ For Large Photos</h3>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Use: <strong>Full-Width Image</strong> (adjustable height)</p>
                </div>
            </div>
        </section>

        <!-- Technical Notes -->
        <section class="demo-technical" style="padding: 2rem; background: #f1f3f5; border-radius: 8px; margin: 3rem 0;">
            <h2>🔧 Technical Notes</h2>
            
            <h3>Block Category</h3>
            <p>All custom blocks are registered under the <strong>"Archi Graph"</strong> category in the Gutenberg editor.</p>
            
            <h3>Performance</h3>
            <ul>
                <li>✅ All images use lazy loading</li>
                <li>✅ Parallax effects disabled on mobile for battery life</li>
                <li>✅ CSS animations use GPU acceleration</li>
                <li>✅ Compiled JavaScript bundles are minified</li>
            </ul>
            
            <h3>Responsive Design</h3>
            <ul>
                <li>Desktop (>1024px): Full effects enabled</li>
                <li>Tablet (768px-1024px): Optimized spacing</li>
                <li>Mobile (<768px): Single column, simplified effects</li>
            </ul>
            
            <h3>Browser Support</h3>
            <ul>
                <li>✅ Chrome/Edge (Chromium)</li>
                <li>✅ Firefox</li>
                <li>✅ Safari</li>
                <li>✅ Modern mobile browsers</li>
                <li>⚠️ Graceful degradation for older browsers</li>
            </ul>
            
            <h3>Accessibility</h3>
            <ul>
                <li>✅ Alt text support for all images</li>
                <li>✅ Proper heading hierarchy</li>
                <li>✅ Keyboard navigation</li>
                <li>✅ Screen reader friendly</li>
            </ul>
        </section>

        <!-- Documentation Links -->
        <section class="demo-links" style="padding: 2rem; background: white; border: 2px solid #e0e0e0; border-radius: 8px; margin: 3rem 0;">
            <h2>📚 Documentation</h2>
            <p>For more detailed information, see:</p>
            <ul>
                <li><strong>Full Analysis:</strong> <code>GUTENBERG-BLOCKS-ANALYSIS.md</code> - Complete technical documentation</li>
                <li><strong>Implementation Guide:</strong> <code>docs/NEW-GUTENBERG-BLOCKS.md</code> - Development details</li>
                <li><strong>GitHub Instructions:</strong> <code>.github/copilot-instructions.md</code> - Coding guidelines</li>
            </ul>
            
            <h3>Need Help?</h3>
            <p>If you encounter issues:</p>
            <ol>
                <li>Check the documentation files</li>
                <li>Ensure webpack compiled successfully: <code>npm run build</code></li>
                <li>Clear browser cache</li>
                <li>Check WordPress debug log if enabled</li>
            </ol>
        </section>

    </div>
</article>

<?php
get_footer();
?>
