<?php
/**
 * Search result card for a forum.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php bbp_forum_id(); ?>" class="forum-search-card forum-search-card-forum">
    <header class="forum-search-card-head">
        <div>
            <span class="forum-search-type"><?php esc_html_e( 'Forum', 'utehub2026' ); ?></span>
            <h2><a href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a></h2>
        </div>
        <a class="forum-search-permalink" href="<?php bbp_forum_permalink(); ?>">#<?php bbp_forum_id(); ?></a>
    </header>

    <div class="forum-search-content forum-search-forum-content">
        <div class="forum-search-date">
            <?php printf( esc_html__( 'Last updated %s', 'utehub2026' ), esc_html( bbp_get_forum_last_active_time() ) ); ?>
        </div>
        <?php bbp_forum_content(); ?>
    </div>
</article>
