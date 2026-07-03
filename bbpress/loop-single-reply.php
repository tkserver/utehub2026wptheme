<?php
/**
 * Single reply card.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$reply_id      = bbp_get_reply_id();
$author_id     = (int) bbp_get_reply_author_id( $reply_id );
$depth         = utehub2026_get_reply_depth( $reply_id );
$parent_author = utehub2026_get_reply_parent_author_name( $reply_id );
$wrap_class    = 'post-wrap';
$admin_html    = utehub2026_get_reply_admin_links_html();

if ( $depth > 0 ) {
    $wrap_class .= ' nest-' . min( 4, $depth );

    if ( utehub2026_is_reply_branch_end( $reply_id ) ) {
        $wrap_class .= ' branch-end';
    }
}
?>
<div class="<?php echo esc_attr( $wrap_class ); ?>">
    <article class="post">
        <div class="post-top">
            <div class="ts">
                <?php bbp_reply_post_date(); ?>
                <?php echo utehub2026_render_vote_pills( $reply_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
            <div class="post-mod">
                <?php echo $admin_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <span class="post-num">#<?php echo esc_html( $reply_id ); ?></span>
            </div>
        </div>
        <div class="pauthor">
            <a class="pauthor-avatar" href="<?php echo esc_url( bbp_get_reply_author_url( $reply_id ) ); ?>"><?php echo utehub2026_render_avatar( $author_id, 84, array( 'name' => bbp_get_reply_author_display_name( $reply_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
            <div class="pauthor-meta">
                <div class="name"><a href="<?php echo esc_url( bbp_get_reply_author_url( $reply_id ) ); ?>"><?php bbp_reply_author_display_name( $reply_id ); ?></a></div>
                <div class="rank"><?php bbp_reply_author_role( array( 'reply_id' => $reply_id ) ); ?></div>
                <?php if ( current_user_can( 'moderate', $reply_id ) ) : ?>
                    <div class="ip"><?php bbp_author_ip( $reply_id ); ?></div>
                <?php endif; ?>
                <a class="ignore" href="#">Ignore user</a>
            </div>
        </div>
        <div class="pbody">
            <?php if ( $parent_author ) : ?>
                <div class="replyto"><?php echo utehub2026_get_svg( 'reply' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>In reply to <a href="#reply-<?php echo esc_attr( $reply_id ); ?>"><?php echo esc_html( $parent_author ); ?></a></div>
            <?php endif; ?>
            <?php bbp_reply_content(); ?>
            <a class="reply-link" href="#new-reply-<?php echo esc_attr( bbp_get_topic_id() ); ?>"<?php echo is_user_logged_in() ? '' : ' data-login-required="1"'; ?>><?php echo utehub2026_get_svg( 'reply' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Reply</a>
        </div>
    </article>
</div>
