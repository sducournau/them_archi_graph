/**
 * Comments Node Generator
 * 
 * Creates virtual nodes representing comments for articles in the graph
 * Each article with comments can have a separate comment node that links to it
 * 
 * @package ArchiGraph
 * @since 1.0.0
 */

(function() {
    'use strict';

/**
 * Generate virtual comment nodes for articles that have comments enabled
 * 
 * @param {Array} articles - Array of article objects from REST API
 * @return {Array} Array of virtual comment node objects
 */
function generateCommentsNodes(articles) {
    const commentsNodes = [];
    
    articles.forEach(article => {
        // Check if article has comments feature enabled
        if (article.comments && article.comments.show_as_node && article.comments.count > 0) {
            const commentNodeId = `comment-${article.id}`;
            
            const commentNode = {
                // Use string ID with prefix to avoid conflicts with post IDs
                id: commentNodeId,
                
                // Visual properties
                title: `${article.comments.count} ${article.comments.count === 1 ? 'commentaire' : 'commentaires'}`,
                excerpt: `Commentaires sur "${article.title}"`,
                
                // Link back to parent article
                permalink: `${article.permalink}#comments`,
                
                // Use comment icon or similar placeholder
                thumbnail: article.thumbnail, // Could be customized with comment icon
                thumbnail_large: article.thumbnail_large,
                
                // Mark as comment node type
                post_type: 'archi_comment_node',
                post_type_label: 'Commentaires',
                
                // Store parent article ID for linking
                parent_article_id: article.id,
                
                // Graph visualization parameters
                node_color: article.comments.node_color,
                node_size: Math.min(40 + (article.comments.count * 2), 80), // Size based on comment count (40-80px)
                node_shape: 'circle',
                node_opacity: 0.85,
                node_icon: '💬', // Comment emoji as icon
                
                // Visual styling
                node_border: 'solid',
                border_color: article.comments.node_color,
                
                // Animation effects
                hover_effect: 'pulse',
                entrance_animation: 'fade',
                
                // Priority
                priority_level: 'low', // Comments are secondary nodes
                
                // Visibility
                show_in_graph: '1',
                hide_links: '0',
                
                // Empty categories/tags (comments don't have taxonomy)
                categories: [],
                tags: [],
                
                // Metadata for identification
                is_comment_node: true,
                comments_count: article.comments.count,
                
                // Date from parent
                date: article.date
            };
            
            commentsNodes.push(commentNode);
        }
    });
    
    return commentsNodes;
}

/**
 * Generate links between comment nodes and their parent articles
 * 
 * @param {Array} commentsNodes - Array of comment node objects
 * @param {Array} allNodes - All nodes in the graph (including articles and comments)
 * @return {Array} Array of link objects
 */
function generateCommentsLinks(commentsNodes, allNodes) {
    allNodes = allNodes || [];
    const links = [];
    
    commentsNodes.forEach(commentNode => {
        // Find the parent article in all nodes
        const parentArticle = allNodes.find(node => node.id === commentNode.parent_article_id);
        
        if (parentArticle) {
            const link = {
                source: commentNode.id, // Comment node
                target: parentArticle.id, // Parent article
                
                // Link styling
                strength: 'medium',
                style: 'dashed', // Dashed to differentiate from content links
                color: commentNode.node_color,
                opacity: 0.6,
                
                // Link type identification
                link_type: 'comment',
                
                // Proximity score (lower than content links)
                proximity_score: 30
            };
            
            links.push(link);
        }
    });
    
    return links;
}

/**
 * Integrate comment nodes into existing graph data
 * Call this after loading article data from REST API
 * 
 * @param {Object} graphData - Object with nodes and links arrays
 * @return {Object} Updated graph data with comment nodes and links
 */
function integrateCommentsIntoGraph(graphData) {
    const nodes = graphData.nodes || [];
    const links = graphData.links || [];
    
    // Generate comment nodes
    const commentsNodes = generateCommentsNodes(nodes);
    
    // Combine all nodes
    const allNodes = nodes.concat(commentsNodes);
    
    // Generate comment links
    const commentsLinks = generateCommentsLinks(commentsNodes, allNodes);
    
    // Combine all links
    const allLinks = links.concat(commentsLinks);
    
    console.log('Comments Integration:', {
        original_nodes: nodes.length,
        comment_nodes: commentsNodes.length,
        total_nodes: allNodes.length,
        original_links: links.length,
        comment_links: commentsLinks.length,
        total_links: allLinks.length
    });
    
    return {
        nodes: allNodes,
        links: allLinks
    };
}

/**
 * Filter function to check if a node is a comment node
 * 
 * @param {Object} node - Node object
 * @return {Boolean} True if node is a comment node
 */
function isCommentNode(node) {
    return node.is_comment_node === true || node.post_type === 'archi_comment_node';
}

/**
 * Get parent article ID from a comment node
 * 
 * @param {Object} commentNode - Comment node object
 * @return {Number|null} Parent article ID or null
 */
function getCommentNodeParent(commentNode) {
    return isCommentNode(commentNode) ? commentNode.parent_article_id : null;
}

/**
 * Count total comments across all articles in graph
 * 
 * @param {Array} nodes - Array of all nodes
 * @return {Number} Total comment count
 */
function getTotalCommentsCount(nodes) {
    return nodes
        .filter(isCommentNode)
        .reduce(function(total, node) { return total + (node.comments_count || 0); }, 0);
}

// Make available globally for GraphManager
if (typeof window !== 'undefined') {
    window.generateCommentsNodes = generateCommentsNodes;
    window.generateCommentsLinks = generateCommentsLinks;
    window.integrateCommentsIntoGraph = integrateCommentsIntoGraph;
    window.isCommentNode = isCommentNode;
    window.getCommentNodeParent = getCommentNodeParent;
    window.getTotalCommentsCount = getTotalCommentsCount;
}

})();
