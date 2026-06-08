<?php
/**
 * Main template.
 *
 * @package UteHub2026
 */

get_header();
?>
<div class="page-wrap page-wrap--single-column">
    <section class="page-card prose-card">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php the_content(); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No content found.</p>
        <?php endif; ?>
    </section>
</div>
<?php
get_footer();
