<?php
/**
 * Template Name: Submission Status
 * Template for checking form submission status
 */

get_header();
?>

<div class="archi-submission-status-page">
    <div class="archi-container">
        <header class="page-header">
            <h1><?php _e('Statut de votre soumission', 'archi-graph'); ?></h1>
            <p class="page-description">
                <?php _e('Vérifiez l\'état de votre projet ou illustration soumis.', 'archi-graph'); ?>
            </p>
        </header>
        
        <?php
        // Check for entry ID from URL
        $entry_id = isset($_GET['entry']) ? absint($_GET['entry']) : 0;
        $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
        
        if ($entry_id && $email) {
            archi_display_submission_status($entry_id, $email);
        } else {
            archi_display_submission_lookup_form();
        }
        ?>
    </div>
</div>

<style>
.archi-submission-status-page {
    padding: 60px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: calc(100vh - 200px);
}

.archi-container {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
    color: white;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.page-description {
    font-size: 1.2rem;
    opacity: 0.9;
}

.archi-status-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.archi-lookup-form {
    max-width: 500px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.submit-button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.submit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    margin: 20px 0;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-published {
    background: #d4edda;
    color: #155724;
}

.status-draft {
    background: #d1ecf1;
    color: #0c5460;
}

.submission-details {
    margin: 30px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.detail-item {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    min-width: 150px;
    color: #666;
}

.detail-value {
    flex: 1;
    color: #333;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.help-text {
    font-size: 0.9rem;
    color: #666;
    margin-top: 8px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}
</style>

<?php
get_footer();

/**
 * Display submission lookup form
 */
function archi_display_submission_lookup_form() {
    ?>
    <div class="archi-status-card">
        <h2 style="margin-bottom: 30px; text-align: center;">
            <?php _e('Vérifier votre soumission', 'archi-graph'); ?>
        </h2>
        
        <form method="get" action="" class="archi-lookup-form">
            <div class="form-group">
                <label for="entry_id">
                    <?php _e('Numéro de soumission', 'archi-graph'); ?>
                </label>
                <input 
                    type="number" 
                    id="entry_id" 
                    name="entry" 
                    placeholder="Ex: 123"
                    required
                >
                <p class="help-text">
                    <?php _e('Ce numéro vous a été envoyé par email après votre soumission.', 'archi-graph'); ?>
                </p>
            </div>
            
            <div class="form-group">
                <label for="email">
                    <?php _e('Votre adresse email', 'archi-graph'); ?>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="votre@email.com"
                    required
                >
            </div>
            
            <button type="submit" class="submit-button">
                <?php _e('Vérifier le statut', 'archi-graph'); ?>
            </button>
        </form>
        
        <div class="alert alert-info" style="margin-top: 30px;">
            <strong><?php _e('Note:', 'archi-graph'); ?></strong>
            <?php _e('Vos informations sont utilisées uniquement pour vérifier votre soumission et ne seront pas stockées.', 'archi-graph'); ?>
        </div>
    </div>
    <?php
}

/**
 * Display submission status
 */
function archi_display_submission_status($entry_id, $email) {
    // Find post by WPForms entry ID
    $posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => ['pending', 'draft', 'publish'],
        'meta_query' => [
            [
                'key' => '_archi_wpforms_entry_id',
                'value' => $entry_id,
                'compare' => '='
            ]
        ],
        'posts_per_page' => 1
    ]);
    
    if (empty($posts)) {
        ?>
        <div class="archi-status-card">
            <div class="alert alert-error">
                <h3><?php _e('Soumission introuvable', 'archi-graph'); ?></h3>
                <p><?php _e('Aucune soumission trouvée avec ce numéro. Veuillez vérifier les informations et réessayer.', 'archi-graph'); ?></p>
            </div>
            
            <div class="action-buttons">
                <a href="<?php echo esc_url(remove_query_arg(['entry', 'email'])); ?>" class="btn btn-primary">
                    <?php _e('← Nouvelle recherche', 'archi-graph'); ?>
                </a>
            </div>
        </div>
        <?php
        return;
    }
    
    $post = $posts[0];
    $contact_email = get_post_meta($post->ID, '_archi_contact_email', true);
    
    // Verify email matches
    if (strtolower($contact_email) !== strtolower($email)) {
        ?>
        <div class="archi-status-card">
            <div class="alert alert-error">
                <h3><?php _e('Erreur de vérification', 'archi-graph'); ?></h3>
                <p><?php _e('L\'adresse email ne correspond pas à celle de la soumission.', 'archi-graph'); ?></p>
            </div>
            
            <div class="action-buttons">
                <a href="<?php echo esc_url(remove_query_arg(['entry', 'email'])); ?>" class="btn btn-primary">
                    <?php _e('← Nouvelle recherche', 'archi-graph'); ?>
                </a>
            </div>
        </div>
        <?php
        return;
    }
    
    // Get status info
    $status_labels = [
        'pending' => __('En attente de révision', 'archi-graph'),
        'draft' => __('Brouillon', 'archi-graph'),
        'publish' => __('Publié', 'archi-graph')
    ];
    
    $status_classes = [
        'pending' => 'status-pending',
        'draft' => 'status-draft',
        'publish' => 'status-published'
    ];
    
    $status_icons = [
        'pending' => '⏳',
        'draft' => '📝',
        'publish' => '✅'
    ];
    
    $post_type_obj = get_post_type_object($post->post_type);
    
    ?>
    <div class="archi-status-card">
        <h2 style="margin-bottom: 20px; text-align: center;">
            <?php echo esc_html($post->post_title); ?>
        </h2>
        
        <div style="text-align: center;">
            <span class="status-badge <?php echo esc_attr($status_classes[$post->post_status]); ?>">
                <span><?php echo $status_icons[$post->post_status]; ?></span>
                <span><?php echo esc_html($status_labels[$post->post_status]); ?></span>
            </span>
        </div>
        
        <div class="submission-details">
            <div class="detail-item">
                <div class="detail-label"><?php _e('Type:', 'archi-graph'); ?></div>
                <div class="detail-value"><?php echo esc_html($post_type_obj->labels->singular_name); ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?php _e('Date de soumission:', 'archi-graph'); ?></div>
                <div class="detail-value"><?php echo esc_html(get_the_date('d/m/Y H:i', $post->ID)); ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><?php _e('Numéro d\'entrée:', 'archi-graph'); ?></div>
                <div class="detail-value">#<?php echo esc_html($entry_id); ?></div>
            </div>
            
            <?php if ($post->post_status === 'publish'): ?>
            <div class="detail-item">
                <div class="detail-label"><?php _e('Date de publication:', 'archi-graph'); ?></div>
                <div class="detail-value"><?php echo esc_html(get_the_date('d/m/Y H:i', $post->ID)); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($post->post_status === 'pending'): ?>
        <div class="alert alert-info">
            <p><strong><?php _e('Votre soumission est en cours de révision.', 'archi-graph'); ?></strong></p>
            <p><?php _e('Notre équipe examine votre projet. Vous recevrez un email dès qu\'il sera publié.', 'archi-graph'); ?></p>
        </div>
        <?php elseif ($post->post_status === 'publish'): ?>
        <div class="alert alert-info">
            <p><strong><?php _e('Félicitations ! Votre soumission est en ligne.', 'archi-graph'); ?></strong></p>
            <p><?php _e('Votre projet est maintenant visible sur notre site.', 'archi-graph'); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <?php if ($post->post_status === 'publish'): ?>
            <a href="<?php echo get_permalink($post->ID); ?>" class="btn btn-primary" target="_blank">
                <?php _e('Voir la publication →', 'archi-graph'); ?>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(remove_query_arg(['entry', 'email'])); ?>" class="btn btn-secondary">
                <?php _e('Nouvelle recherche', 'archi-graph'); ?>
            </a>
        </div>
    </div>
    <?php
}
