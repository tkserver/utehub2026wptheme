<?php
/**
 * BuddyPress member settings.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="member-component-shell member-settings-shell">
    <div class="item-list-tabs no-ajax member-subnav" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
        <ul>
            <?php if ( bp_core_can_edit_settings() ) : ?>
                <?php bp_get_options_nav(); ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="member-panel">
        <?php
        switch ( bp_current_action() ) :
            case '':
            case 'general':
                bp_get_template_part( 'members/single/settings/general' );
                break;

            case 'notifications':
                bp_get_template_part( 'members/single/settings/notifications' );
                break;

            case 'capabilities':
                bp_get_template_part( 'members/single/settings/capabilities' );
                break;

            case 'delete-account':
                bp_get_template_part( 'members/single/settings/delete-account' );
                break;

            case 'profile':
                bp_get_template_part( 'members/single/settings/profile' );
                break;

            case 'data':
                bp_get_template_part( 'members/single/settings/data' );
                break;

            default:
                bp_get_template_part( 'members/single/plugins' );
                break;
        endswitch;
        ?>
    </div>
</div>
