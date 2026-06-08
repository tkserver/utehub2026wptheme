<?php
/**
 * Topics loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bbp_template_before_topics_loop' );
?>

<div id="bbp-forum-<?php bbp_forum_id(); ?>" class="topics bbp-topics uh-topics-loop">
    <?php while ( bbp_topics() ) : bbp_the_topic(); ?>
        <?php bbp_get_template_part( 'loop', 'single-topic' ); ?>
    <?php endwhile; ?>
</div>

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
