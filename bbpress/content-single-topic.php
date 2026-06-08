<?php
/**
 * Single topic layout.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$topic_id       = bbp_get_topic_id();
$forum_id       = bbp_get_topic_forum_id( $topic_id );
$forum_title    = $forum_id ? get_the_title( $forum_id ) : '';
$last_active_id = (int) bbp_get_topic_last_active_id( $topic_id );
$last_author_id = (int) get_post_field( 'post_author', $last_active_id );
?>
<div class="uh-wrap">
    <section>
        <div class="crumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ute Hub</a>
            <span class="sep">›</span>
            <a href="<?php echo esc_url( function_exists( 'bbp_get_forum_archive_permalink' ) ? bbp_get_forum_archive_permalink() : home_url( '/forums/' ) ); ?>">Forums</a>
            <?php if ( $forum_id ) : ?>
                <span class="sep">›</span>
                <a href="<?php echo esc_url( get_permalink( $forum_id ) ); ?>"><?php echo esc_html( $forum_title ); ?></a>
            <?php endif; ?>
            <span class="sep">›</span>
            <span class="cur"><?php bbp_topic_title(); ?></span>
            <?php if ( function_exists( 'bbp_get_user_favorites_link' ) && is_user_logged_in() ) : ?>
                <span class="fav"><?php echo utehub2026_get_svg( 'favorite' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Favorite</span>
            <?php endif; ?>
        </div>

        <div class="thread-head">
            <h1><?php bbp_topic_title(); ?></h1>
            <div class="thread-meta">
                <span class="m"><?php echo utehub2026_get_svg( 'forum' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><b><?php echo esc_html( (int) bbp_get_topic_reply_count( $topic_id ) ); ?></b> replies</span>
                <span class="m"><?php echo utehub2026_get_svg( 'active' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><b><?php echo esc_html( (int) bbp_get_topic_voice_count( $topic_id ) ); ?></b> voices</span>
                <span class="m"><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Updated <b><?php echo esc_html( utehub2026_get_relative_time( $last_active_id ) ); ?></b> by <span style="color:var(--crimson);font-weight:600;margin-left:4px"><?php echo esc_html( get_the_author_meta( 'display_name', $last_author_id ) ); ?></span></span>
                <?php if ( $forum_title ) : ?><span class="chip cat mcat"><?php echo esc_html( $forum_title ); ?></span><?php endif; ?>
            </div>
        </div>

        <?php if ( bbp_has_replies() ) : ?>
            <div class="viewing"><?php bbp_topic_pagination_count(); ?></div>
        <?php endif; ?>

        <div id="bbpress-forums" class="bbpress-wrapper">
            <?php if ( bbp_show_lead_topic() ) : ?>
                <?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>
            <?php endif; ?>

            <?php if ( bbp_has_replies() ) : ?>
                <?php bbp_get_template_part( 'loop', 'replies' ); ?>
                <?php bbp_get_template_part( 'pagination', 'replies' ); ?>
            <?php endif; ?>

            <?php bbp_get_template_part( 'form', 'reply' ); ?>
        </div>
    </section>

    <?php utehub2026_render_right_rail( 'topic', $topic_id ); ?>
</div>
