<?php
/**
 * BuddyPress activity entry.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bp_before_activity_entry' );

$activity_time_markup = '';
$activity_type = bp_get_activity_type();
$view_label    = 'activity_comment' === $activity_type || false !== strpos( $activity_type, 'forum' ) || false !== strpos( $activity_type, 'topic' ) || false !== strpos( $activity_type, 'reply' )
    ? __( 'View Topic', 'utehub2026' )
    : __( 'View Activity', 'utehub2026' );

if ( ! empty( buddypress()->activity->template->activity->date_recorded ) ) {
    $activity_time_markup = sprintf(
        '<span class="time-since" data-livestamp="%1$s">%2$s</span>',
        esc_attr( bp_core_get_iso8601_date( buddypress()->activity->template->activity->date_recorded ) ),
        esc_html( bp_core_time_since( buddypress()->activity->template->activity->date_recorded ) )
    );
}
?>

<li class="<?php bp_activity_css_class(); ?> activity-feed-item" id="activity-<?php bp_activity_id(); ?>">
    <div class="activity-avatar">
        <a href="<?php bp_activity_user_link(); ?>">
            <?php bp_activity_avatar(); ?>
        </a>
    </div>

    <div class="activity-content">
        <div class="activity-header">
            <?php bp_activity_action( array( 'no_timestamp' => true ) ); ?>
            <?php if ( $activity_time_markup ) : ?>
                <span class="activity-when"><?php echo $activity_time_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <?php endif; ?>
        </div>

        <?php if ( bp_activity_has_content() ) : ?>
            <div class="activity-inner">
                <?php bp_get_template_part( 'activity/type-parts/content', bp_activity_type_part() ); ?>
            </div>
        <?php endif; ?>

        <?php do_action( 'bp_activity_entry_content' ); ?>

        <div class="activity-meta">
            <a href="<?php bp_activity_thread_permalink(); ?>" class="button view bp-secondary-action"><?php echo esc_html( $view_label ); ?></a>
        </div>
    </div>

    <?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>
        <div class="activity-comments">
            <?php bp_activity_comments(); ?>

            <?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
                <form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
                    <div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
                    <div class="ac-reply-content">
                        <div class="ac-textarea">
                            <label for="ac-input-<?php bp_activity_id(); ?>" class="bp-screen-reader-text"><?php esc_html_e( 'Comment', 'buddypress' ); ?></label>
                            <textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
                        </div>
                        <input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'buddypress' ); ?>" />
                        <a href="<?php bp_activity_comment_cancel_url(); ?>" class="ac-reply-cancel"><?php esc_html_e( 'Cancel', 'buddypress' ); ?></a>
                        <input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />

                        <?php do_action( 'bp_activity_entry_comments' ); ?>
                        <?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment_' . bp_get_activity_id() ); ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</li>

<?php do_action( 'bp_after_activity_entry' ); ?>
