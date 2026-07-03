<?php
/**
 * BuddyPress member cover image header.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bp_before_member_header' );
?>

<div id="cover-image-container">
    <a id="header-cover-image" href="<?php bp_displayed_user_link(); ?>" aria-label="<?php esc_attr_e( 'View profile', 'buddypress' ); ?>"></a>

    <div id="item-header-cover-image">
        <div id="item-header-avatar">
            <a href="<?php bp_displayed_user_link(); ?>">
                <?php bp_displayed_user_avatar( 'type=full' ); ?>
            </a>
        </div>

        <div id="item-header-content">
            <?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
                <h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
            <?php endif; ?>

            <div id="item-buttons"><?php do_action( 'bp_member_header_actions' ); ?></div>

            <span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_user_last_activity( bp_displayed_user_id() ) ); ?>"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

            <?php do_action( 'bp_before_member_header_meta' ); ?>

            <div id="item-meta">
                <?php if ( bp_is_active( 'activity' ) ) : ?>
                    <div id="latest-update"><?php bp_activity_latest_update( bp_displayed_user_id() ); ?></div>
                <?php endif; ?>

                <?php do_action( 'bp_profile_header_meta' ); ?>
            </div>
        </div>
    </div>
</div>

<?php
do_action( 'bp_after_member_header' );
?>

<div id="template-notices" role="alert" aria-atomic="true">
    <?php do_action( 'template_notices' ); ?>
</div>
