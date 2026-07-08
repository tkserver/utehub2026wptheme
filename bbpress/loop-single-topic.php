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
$last_active_id   = utehub2026_get_topic_last_activity_id( $topic_id );
$last_author_id   = (int) get_post_field( 'post_author', $last_active_id );
$started_by_id    = (int) get_post_field( 'post_author', $topic_id );
$forum_url        = $topic_forum_id ? bbp_get_forum_permalink( $topic_forum_id ) : '';
$started_by_url   = bbp_get_topic_author_url( $topic_id );
$last_user_id     = $last_author_id ? $last_author_id : $started_by_id;
$last_author_name = get_the_author_meta( 'display_name', $last_user_id );
$last_author_url  = $last_active_id && $last_active_id !== $topic_id && function_exists( 'bbp_get_reply_author_url' )
    ? bbp_get_reply_author_url( $last_active_id )
    : $started_by_url;
$classes          = 'uh-topic';
$heat             = utehub2026_get_topic_heat( max( 0, $reply_count - 1 ) );

if ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) {
    $classes .= ' pinned';
}
?>

<article id="bbp-topic-<?php bbp_topic_id(); ?>" <?php post_class( $classes, $topic_id ); ?>>
    <div class="t-main">
        <a class="t-title" href="<?php echo esc_url( bbp_get_topic_permalink( $topic_id ) ); ?>"><?php bbp_topic_title( $topic_id ); ?></a>

        <div class="t-by">
            <a href="<?php echo esc_url( $started_by_url ); ?>">
                <?php echo utehub2026_render_avatar( $started_by_id, 20, array( 'class' => 't-by-av', 'name' => bbp_get_topic_author_display_name( $topic_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
            <span class="t-by-copy">
                Started by
                <a href="<?php echo esc_url( $started_by_url ); ?>"><?php echo esc_html( bbp_get_topic_author_display_name( $topic_id ) ); ?></a>
            </span>

            <div class="t-tags">
                <?php if ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) : ?>
                    <span class="chip pin"><?php echo utehub2026_get_svg( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Pinned</span>
                <?php endif; ?>

                <?php if ( $forum_title && $forum_url && ( ! bbp_is_single_forum() || $topic_forum_id !== $current_forum_id ) ) : ?>
                    <a class="chip cat" href="<?php echo esc_url( $forum_url ); ?>"><?php echo esc_html( $forum_title ); ?></a>
                <?php endif; ?>

                <?php if ( $heat ) : ?>
                    <span class="heat"><?php echo esc_html( str_repeat( '🔥', $heat ) ); ?></span>
                <?php endif; ?>
            </div>
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
        <a href="<?php echo esc_url( $last_author_url ); ?>">
            <?php echo utehub2026_render_avatar( $last_user_id, 38, array( 'name' => $last_author_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <div class="lp">
            <a class="who" href="<?php echo esc_url( $last_author_url ); ?>"><?php echo esc_html( $last_author_name ); ?></a>
            <span class="when"><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( utehub2026_get_relative_time( $last_active_id ) ); ?></span>
        </div>
    </div>
</article>
