<?php
/**
 * BuddyPress member activity.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="member-activity-shell">
    <div class="item-list-tabs no-ajax member-activity-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
        <ul>
            <?php bp_get_options_nav(); ?>

            <li id="activity-filter-select" class="last">
                <label for="activity-filter-by"><?php esc_html_e( 'Show', 'utehub2026' ); ?></label>
                <select id="activity-filter-by">
                    <option value="-1"><?php esc_html_e( 'Everything', 'utehub2026' ); ?></option>

                    <?php bp_activity_show_filters(); ?>
                    <?php do_action( 'bp_member_activity_filter_options' ); ?>
                </select>
            </li>
        </ul>
    </div>

    <?php do_action( 'bp_before_member_activity_post_form' ); ?>

    <?php
    if ( function_exists( 'utehub2026_is_member_whats_new_enabled' ) && utehub2026_is_member_whats_new_enabled() && is_user_logged_in() && bp_is_my_profile() && ( ! bp_current_action() || bp_is_current_action( 'just-me' ) ) ) {
        bp_get_template_part( 'activity/post-form' );
    }
    ?>

    <?php do_action( 'bp_after_member_activity_post_form' ); ?>
    <?php do_action( 'bp_before_member_activity_content' ); ?>

    <div class="activity" aria-live="polite" aria-atomic="true" aria-relevant="all">
        <?php bp_get_template_part( 'activity/activity-loop' ); ?>
    </div>

    <?php do_action( 'bp_after_member_activity_content' ); ?>
</div>
