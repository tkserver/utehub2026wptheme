<?php
/**
 * BuddyPress member cover image header.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$cover_image = '';

if ( function_exists( 'bp_attachments_get_attachment' ) ) {
    $cover_image = bp_attachments_get_attachment(
        'url',
        array(
            'object_dir' => 'members',
            'item_id'    => bp_displayed_user_id(),
        )
    );
}

do_action( 'bp_before_member_header' );
?>

<div id="cover-image-container">
    <a
        id="header-cover-image"
        href="<?php bp_displayed_user_link(); ?>"
        aria-label="<?php esc_attr_e( 'View profile', 'buddypress' ); ?>"
        <?php if ( $cover_image ) : ?>
            style="background-image: url('<?php echo esc_url( $cover_image ); ?>');"
        <?php endif; ?>
    ></a>

    <div id="item-header-cover-image">
        <div id="item-header-avatar">
            <a href="<?php bp_displayed_user_link(); ?>">
                <?php bp_displayed_user_avatar( 'type=full' ); ?>
            </a>
        </div>

        <div id="item-header-content">
            <h1 class="member-display-name"><?php bp_displayed_user_fullname(); ?></h1>

            <?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
                <div class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></div>
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
