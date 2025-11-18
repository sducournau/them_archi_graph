<?php
/**
 * Template des commentaires - Design harmonisé avec le livre d'or
 * 
 * Template personnalisé pour afficher les commentaires avec un design unifié
 * cohérent avec page-guestbook.php et le système de feedback global.
 *
 * @package ArchiGraph
 * @since 1.1.0
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area archi-feedback-section">
    
    <?php if (have_comments()) : ?>
        <h2 class="comments-title archi-feedback-title">
            <span class="comment-icon">💬</span>
            <?php
            $comment_count = get_comments_number();
            printf(
                _n(
                    '%s commentaire',
                    '%s commentaires',
                    $comment_count,
                    'archi-graph'
                ),
                number_format_i18n($comment_count)
            );
            ?>
        </h2>

        <ol class="commentlist">
            <?php
            wp_list_comments([
                'style'       => 'li',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'archi_comment_callback',
            ]);
            ?>
        </ol>

        <?php
        // Pagination des commentaires
        if (get_comment_pages_count() > 1 && get_option('page_comments')) :
            ?>
            <nav class="comment-navigation archi-feedback-pagination" role="navigation">
                <h3 class="screen-reader-text"><?php _e('Navigation des commentaires', 'archi-graph'); ?></h3>
                <div class="nav-links">
                    <?php
                    paginate_comments_links([
                        'prev_text' => '<span class="nav-prev">&larr; ' . __('Précédents', 'archi-graph') . '</span>',
                        'next_text' => '<span class="nav-next">' . __('Suivants', 'archi-graph') . ' &rarr;</span>',
                    ]);
                    ?>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; // have_comments() ?>

    <?php
    // Message si commentaires fermés mais il y en a
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
        ?>
        <p class="no-comments archi-feedback-info-message">
            <span class="icon">ℹ️</span>
            <?php _e('Les commentaires sont fermés.', 'archi-graph'); ?>
        </p>
    <?php endif; ?>

    <?php
    // Formulaire de commentaire avec design unifié
    if (comments_open()) :
        comment_form([
            'title_reply'          => __('Laisser un commentaire', 'archi-graph'),
            'title_reply_to'       => __('Répondre à %s', 'archi-graph'),
            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title archi-feedback-title"><span class="comment-icon">✍️</span>',
            'title_reply_after'    => '</h3>',
            'cancel_reply_before'  => '<span class="cancel-comment-reply">',
            'cancel_reply_after'   => '</span>',
            'cancel_reply_link'    => __('Annuler la réponse', 'archi-graph'),
            'class_form'           => 'archi-feedback-form comment-form',
            'class_submit'         => 'submit-button archi-feedback-submit',
            'label_submit'         => __('Publier le commentaire', 'archi-graph'),
            'submit_button'        => '<button type="submit" id="%2$s" class="%3$s">%4$s</button>',
            
            // Champs personnalisés
            'fields' => [
                'author' => '<p class="comment-form-author">' .
                            '<label for="author">' . __('Nom', 'archi-graph') . ' <span class="required">*</span></label>' .
                            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required /></p>',
                
                'email'  => '<p class="comment-form-email">' .
                            '<label for="email">' . __('Email', 'archi-graph') . ' <span class="required">*</span></label>' .
                            '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" required /></p>',
                
                'url'    => '<p class="comment-form-url">' .
                            '<label for="url">' . __('Site web', 'archi-graph') . '</label>' .
                            '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" /></p>',
            ],
            
            'comment_field' => '<p class="comment-form-comment">' .
                               '<label for="comment">' . __('Commentaire', 'archi-graph') . ' <span class="required">*</span></label>' .
                               '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required></textarea>' .
                               '<span class="comment-note">' . __('Maximum 65 000 caractères', 'archi-graph') . '</span>' .
                               '</p>',
            
            'comment_notes_before' => '<p class="comment-notes">' .
                                      '<span class="icon">💡</span>' .
                                      sprintf(
                                          __('Votre adresse email ne sera pas publiée. Les champs obligatoires sont indiqués avec %s', 'archi-graph'),
                                          '<span class="required">*</span>'
                                      ) .
                                      '</p>',
            
            'comment_notes_after'  => '',
        ]);
    endif;
    ?>

</div><!-- #comments -->
