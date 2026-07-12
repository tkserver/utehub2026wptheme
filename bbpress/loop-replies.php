<?php
/**
 * Replies loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="posts">
    <?php if ( function_exists( 'bbp_thread_replies' ) && bbp_thread_replies() ) : ?>
        <?php bbp_list_replies( array( 'style' => 'div' ) ); ?>
    <?php else : ?>
        <?php while ( bbp_replies() ) : bbp_the_reply(); ?>
            <?php bbp_get_template_part( 'loop', 'single-reply' ); ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
