<?php
/**
 * BuddyPress member friends.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="member-component-shell member-friends-shell">
    <div class="item-list-tabs no-ajax member-subnav" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
        <ul>
            <?php if ( bp_is_my_profile() ) : ?>
                <?php bp_get_options_nav(); ?>
            <?php endif; ?>

            <?php if ( ! bp_is_current_action( 'requests' ) ) : ?>
                <li id="members-order-select" class="last filter">
                    <label for="members-friends"><?php esc_html_e( 'Order By:', 'buddypress' ); ?></label>
                    <select id="members-friends">
                        <option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
                        <option value="newest"><?php esc_html_e( 'Newest Registered', 'buddypress' ); ?></option>
                        <option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
                        <?php do_action( 'bp_member_friends_order_options' ); ?>
                    </select>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="member-panel">
        <?php
        switch ( bp_current_action() ) :
            case '':
            case 'my-friends':
                do_action( 'bp_before_member_friends_content' );
                ?>

                <h2 class="bp-screen-reader-text">
                    <?php echo is_user_logged_in() ? esc_html__( 'My friends', 'buddypress' ) : esc_html__( 'Friends', 'buddypress' ); ?>
                </h2>

                <div
                    id="members-friends-list"
                    class="members friends"
                    data-user-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>"
                >
                    <?php bp_get_template_part( 'members/members-loop' ); ?>
                </div>

                <?php
                do_action( 'bp_after_member_friends_content' );
                break;

            case 'requests':
                bp_get_template_part( 'members/single/friends/requests' );
                break;

            default:
                bp_get_template_part( 'members/single/plugins' );
                break;
        endswitch;
        ?>
    </div>
</div>
