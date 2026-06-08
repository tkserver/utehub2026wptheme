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
        <div class="page-wrap page-wrap--single-column">
            <article class="page-card prose-card">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </article>
        </div>
    <?php endif; ?>
<?php endwhile; ?>
<?php
get_footer();
