<?php
/**
 * Lead topic card.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$topic_id   = bbp_get_topic_id();
$author_id  = (int) bbp_get_topic_author_id( $topic_id );
$post_num   = '#' . $topic_id;
$admin_html = utehub2026_get_topic_admin_links_html();
?>
<div class="posts">
    <div id="post-<?php echo esc_attr( $topic_id ); ?>" class="post-wrap">
        <article class="post op">
            <div class="post-top">
                <div class="ts">
                    <?php bbp_topic_post_date(); ?>
                    <?php echo utehub2026_render_vote_pills( $topic_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <div class="post-mod">
                    <?php echo $admin_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span class="post-num"><?php echo esc_html( $post_num ); ?></span>
                </div>
            </div>
            <div class="pauthor">
                <a class="pauthor-avatar" href="<?php echo esc_url( bbp_get_topic_author_url( $topic_id ) ); ?>"><?php echo utehub2026_render_avatar( $author_id, 84, array( 'name' => bbp_get_topic_author_display_name( $topic_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                <div class="pauthor-meta">
                    <div class="name"><a href="<?php echo esc_url( bbp_get_topic_author_url( $topic_id ) ); ?>"><?php bbp_topic_author_display_name( $topic_id ); ?></a></div>
                    <div class="rank"><?php bbp_topic_author_role(); ?></div>
                    <?php if ( current_user_can( 'moderate', $topic_id ) ) : ?>
                        <div class="ip"><?php bbp_author_ip( $topic_id ); ?></div>
                    <?php endif; ?>
                    <a class="ignore" href="#">Ignore user</a>
                </div>
            </div>
            <div class="pbody">
                <?php bbp_topic_content(); ?>
                <a class="reply-link" href="#new-reply-<?php echo esc_attr( $topic_id ); ?>"<?php echo is_user_logged_in() ? '' : ' data-login-required="1"'; ?>><?php echo utehub2026_get_svg( 'reply' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Reply</a>
            </div>
        </article>
    </div>
</div>
