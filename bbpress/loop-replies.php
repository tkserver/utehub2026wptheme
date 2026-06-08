<?php
/**
 * Replies loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="posts">
    <?php while ( bbp_replies() ) : bbp_the_reply(); ?>
        <?php bbp_get_template_part( 'loop', 'single-reply' ); ?>
    <?php endwhile; ?>
</div>
