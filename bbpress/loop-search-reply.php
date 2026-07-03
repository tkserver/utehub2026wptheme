<?php
/**
 * Search result card for a reply.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php bbp_reply_id(); ?>" class="forum-search-card forum-search-card-reply">
    <header class="forum-search-card-head">
        <div>
            <span class="forum-search-type"><?php esc_html_e( 'Reply', 'utehub2026' ); ?></span>
            <h2>
                <span><?php esc_html_e( 'In reply to:', 'utehub2026' ); ?></span>
                <a href="<?php bbp_reply_url(); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
            </h2>
        </div>
        <a class="forum-search-permalink" href="<?php bbp_reply_url(); ?>">#<?php bbp_reply_id(); ?></a>
    </header>

    <div class="forum-search-card-body">
        <aside class="forum-search-author">
            <?php bbp_reply_author_link( array( 'show_role' => true ) ); ?>
            <?php if ( bbp_is_user_keymaster() ) : ?>
                <div class="forum-search-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>
            <?php endif; ?>
        </aside>

        <div class="forum-search-content">
            <div class="forum-search-date"><?php bbp_reply_post_date(); ?></div>
            <?php bbp_reply_content(); ?>
        </div>
    </div>
</article>
