<?php
/**
 * Search results loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bbp_template_before_search_results_loop' );

$template_dir = trailingslashit( get_stylesheet_directory() ) . 'bbpress/';
?>

<div class="forum-search-results">
    <?php while ( bbp_search_results() ) : bbp_the_search_result(); ?>
        <?php
        $post_type = get_post_type();

        if ( $post_type === bbp_get_reply_post_type() ) {
            include $template_dir . 'loop-search-reply.php';
        } elseif ( $post_type === bbp_get_topic_post_type() ) {
            include $template_dir . 'loop-search-topic.php';
        } elseif ( $post_type === bbp_get_forum_post_type() ) {
            include $template_dir . 'loop-search-forum.php';
        }
        ?>
    <?php endwhile; ?>
</div>

<?php do_action( 'bbp_template_after_search_results_loop' ); ?>
