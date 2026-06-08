<?php
/**
 * Single topic card.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$topic_id         = bbp_get_topic_id();
$current_forum_id = bbp_get_forum_id();
$topic_forum_id   = bbp_get_topic_forum_id( $topic_id );
$forum_title      = $topic_forum_id ? get_the_title( $topic_forum_id ) : '';
$reply_count      = (int) bbp_get_topic_post_count( $topic_id );
$voice_count      = (int) bbp_get_topic_voice_count( $topic_id );
$last_active_id   = (int) bbp_get_topic_last_active_id( $topic_id );
$last_author_id   = (int) get_post_field( 'post_author', $last_active_id ? $last_active_id : $topic_id );
$started_by_id    = (int) get_post_field( 'post_author', $topic_id );
$classes          = 'uh-topic';
$heat             = utehub2026_get_topic_heat( max( 0, $reply_count - 1 ) );

if ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) {
    $classes .= ' pinned';
}
?>

<article id="bbp-topic-<?php bbp_topic_id(); ?>" <?php post_class( $classes, $topic_id ); ?>>
    <div class="t-main">
        <div class="t-tags">
            <?php if ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) : ?>
                <span class="chip pin"><?php echo utehub2026_get_svg( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Pinned</span>
            <?php endif; ?>

            <?php if ( $forum_title && ( ! bbp_is_single_forum() || $topic_forum_id !== $current_forum_id ) ) : ?>
                <span class="chip cat"><?php echo esc_html( $forum_title ); ?></span>
            <?php endif; ?>

            <?php if ( $heat ) : ?>
                <span class="heat"><?php echo esc_html( str_repeat( '🔥', $heat ) ); ?></span>
            <?php endif; ?>
        </div>

        <a class="t-title" href="<?php echo esc_url( bbp_get_topic_permalink( $topic_id ) ); ?>"><?php echo esc_html( bbp_get_topic_title( $topic_id ) ); ?></a>

        <div class="t-by">
            Started by
            <a href="<?php echo esc_url( bbp_get_topic_author_url( $topic_id ) ); ?>"><?php echo esc_html( bbp_get_topic_author_display_name( $topic_id ) ); ?></a>
        </div>

        <?php if ( bbp_get_topic_pagination() ) : ?>
            <div class="t-pages"><?php bbp_topic_pagination(); ?></div>
        <?php endif; ?>
    </div>

    <div class="t-stats">
        <div class="stat"><b><?php echo esc_html( $voice_count ); ?></b><span>Voices</span></div>
        <div class="stat"><b><?php echo esc_html( $reply_count ); ?></b><span>Posts</span></div>
    </div>

    <div class="t-last">
        <?php echo utehub2026_render_avatar( $last_author_id ? $last_author_id : $started_by_id, 38, array( 'name' => get_the_author_meta( 'display_name', $last_author_id ? $last_author_id : $started_by_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="lp">
            <span class="who"><?php echo esc_html( get_the_author_meta( 'display_name', $last_author_id ? $last_author_id : $started_by_id ) ); ?></span>
            <span class="when"><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( utehub2026_get_relative_time( $last_active_id ? $last_active_id : $topic_id ) ); ?></span>
        </div>
    </div>
</article>
