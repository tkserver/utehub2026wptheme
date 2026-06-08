<?php
/**
 * Predict page template.
 *
 * @package UteHub2026
 */

get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php utehub2026_render_content_page_layout( array( 'context' => 'predict' ) ); ?>
<?php endwhile; ?>
<?php
get_footer();
