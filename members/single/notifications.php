<?php
/**
 * BuddyPress member notifications.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="member-component-shell member-notifications-shell">
    <div class="item-list-tabs no-ajax member-subnav" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
        <ul>
            <?php bp_get_options_nav(); ?>

            <li id="members-order-select" class="last filter">
                <?php bp_notifications_sort_order_form(); ?>
            </li>
        </ul>
    </div>

    <div class="member-panel">
        <?php
        switch ( bp_current_action() ) :
            case '':
            case 'unread':
                bp_get_template_part( 'members/single/notifications/unread' );
                break;

            case 'read':
                bp_get_template_part( 'members/single/notifications/read' );
                break;

            default:
                bp_get_template_part( 'members/single/plugins' );
                break;
        endswitch;
        ?>
    </div>
</div>
