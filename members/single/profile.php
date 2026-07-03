<?php
/**
 * BuddyPress member profile.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="member-component-shell member-profile-shell">
    <div class="item-list-tabs no-ajax member-subnav" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
        <ul>
            <?php bp_get_options_nav(); ?>
        </ul>
    </div>

    <?php do_action( 'bp_before_profile_content' ); ?>

    <div class="profile member-panel">
        <?php
        switch ( bp_current_action() ) :
            case 'edit':
                bp_get_template_part( 'members/single/profile/edit' );
                break;

            case 'change-avatar':
                bp_get_template_part( 'members/single/profile/change-avatar' );
                break;

            case 'change-cover-image':
                bp_get_template_part( 'members/single/profile/change-cover-image' );
                break;

            case '':
            case 'public':
                if ( bp_is_active( 'xprofile' ) ) {
                    bp_get_template_part( 'members/single/profile/profile-loop' );
                } else {
                    bp_get_template_part( 'members/single/profile/profile-wp' );
                }
                break;

            default:
                bp_get_template_part( 'members/single/plugins' );
                break;
        endswitch;
        ?>
    </div>

    <?php do_action( 'bp_after_profile_content' ); ?>
</div>
