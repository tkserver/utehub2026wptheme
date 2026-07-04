<?php
/**
 * Default page template.
 *
 * @package UteHub2026
 */

get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php if ( utehub2026_content_has_forum_layout( get_the_ID() ) ) : ?>
        <?php utehub2026_render_topics_feed( get_permalink() ); ?>
    <?php else : ?>
        <?php utehub2026_render_content_page_layout( array( 'context' => 'page' ) ); ?>
    <?php endif; ?>
<?php endwhile; ?>
<?php
get_footer();
