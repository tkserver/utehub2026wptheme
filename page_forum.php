<?php
/**
 * Forum page template.
 *
 * @package UteHub2026
 */

get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php utehub2026_render_topics_feed( get_permalink() ); ?>
<?php endwhile; ?>
<?php
get_footer();
