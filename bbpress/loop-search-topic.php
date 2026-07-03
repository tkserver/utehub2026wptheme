<?php
/**
 * Search result card for a topic.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php bbp_topic_id(); ?>" class="forum-search-card forum-search-card-topic">
    <header class="forum-search-card-head">
        <div>
            <span class="forum-search-type"><?php esc_html_e( 'Topic', 'utehub2026' ); ?></span>
            <h2><a href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a></h2>
            <div class="forum-search-location">
                <?php esc_html_e( 'In forum', 'utehub2026' ); ?>
                <a href="<?php bbp_forum_permalink( bbp_get_topic_forum_id() ); ?>"><?php bbp_forum_title( bbp_get_topic_forum_id() ); ?></a>
            </div>
        </div>
        <a class="forum-search-permalink" href="<?php bbp_topic_permalink(); ?>">#<?php bbp_topic_id(); ?></a>
    </header>

    <div class="forum-search-card-body">
        <aside class="forum-search-author">
            <?php bbp_topic_author_link( array( 'show_role' => true ) ); ?>
            <?php if ( bbp_is_user_keymaster() ) : ?>
                <div class="forum-search-ip"><?php bbp_author_ip( bbp_get_topic_id() ); ?></div>
            <?php endif; ?>
        </aside>

        <div class="forum-search-content">
            <div class="forum-search-date"><?php bbp_topic_post_date( bbp_get_topic_id() ); ?></div>
            <?php bbp_topic_content(); ?>
        </div>
    </div>
</article>
