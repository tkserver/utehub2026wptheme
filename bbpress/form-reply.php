<?php
/**
 * Reply form.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

if ( bbp_current_user_can_access_create_reply_form() ) : ?>
    <div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form">
        <form id="new-post" name="new-post" method="post">
            <fieldset class="bbp-form">
                <legend><?php printf( esc_html__( 'Reply To: %s', 'bbpress' ), bbp_get_topic_title() ); ?></legend>

                <?php do_action( 'bbp_template_notices' ); ?>
                <?php bbp_get_template_part( 'form', 'anonymous' ); ?>
                <?php bbp_the_content( array( 'context' => 'reply', 'textarea_rows' => 8 ) ); ?>

                <?php if ( bbp_is_subscriptions_active() && ! bbp_is_anonymous() ) : ?>
                    <p>
                        <label>
                            <input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe"<?php bbp_form_topic_subscribed(); ?>>
                            <?php esc_html_e( 'Notify me of follow-up replies via email', 'bbpress' ); ?>
                        </label>
                    </p>
                <?php endif; ?>

                <div class="bbp-submit-wrapper">
                    <?php bbp_cancel_reply_to_link(); ?>
                    <button type="submit" id="bbp_reply_submit" name="bbp_reply_submit" class="button submit">Post Reply</button>
                </div>

                <?php bbp_reply_form_fields(); ?>
            </fieldset>
        </form>
    </div>
<?php elseif ( bbp_is_topic_closed() ) : ?>
    <div class="bbp-reply-form">
        <div class="bbp-template-notice"><ul><li><?php printf( esc_html__( 'The topic "%s" is closed to new replies.', 'bbpress' ), bbp_get_topic_title() ); ?></li></ul></div>
    </div>
<?php elseif ( ! is_user_logged_in() ) : ?>
    <div class="bbp-reply-form">
        <div class="bbp-template-notice"><ul><li><?php esc_html_e( 'You must be logged in to reply to this topic.', 'bbpress' ); ?></li></ul></div>
        <?php bbp_get_template_part( 'form', 'user-login' ); ?>
    </div>
<?php endif; ?>
