<?php
/**
 * Template Name: Right Rail Page
 *
 * @package UteHub2026
 */

get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php utehub2026_render_content_page_layout( array( 'context' => 'page' ) ); ?>
<?php endwhile; ?>
<?php
get_footer();
