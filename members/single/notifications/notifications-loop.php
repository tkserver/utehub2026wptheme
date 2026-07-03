<?php
/**
 * BuddyPress member notifications loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<form action="" method="post" id="notifications-bulk-management">
    <table class="notifications">
        <thead>
            <tr>
                <th class="icon"></th>
                <th class="bulk-select-all">
                    <input id="select-all-notifications" type="checkbox">
                    <label class="bp-screen-reader-text" for="select-all-notifications"><?php esc_html_e( 'Select all', 'buddypress' ); ?></label>
                </th>
                <th class="title"><?php esc_html_e( 'Notification', 'buddypress' ); ?></th>
                <th class="date"><?php esc_html_e( 'Date Received', 'buddypress' ); ?></th>
                <th class="actions"><?php esc_html_e( 'Actions', 'buddypress' ); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php while ( bp_the_notifications() ) : bp_the_notification(); ?>
                <tr>
                    <td></td>
                    <td class="bulk-select-check">
                        <label for="<?php bp_the_notification_id(); ?>">
                            <input id="<?php bp_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php bp_the_notification_id(); ?>" class="notification-check">
                            <span class="bp-screen-reader-text"><?php esc_html_e( 'Select this notification', 'buddypress' ); ?></span>
                        </label>
                    </td>
                    <td class="notification-description"><?php bp_the_notification_description(); ?></td>
                    <td class="notification-since"><?php bp_the_notification_time_since(); ?></td>
                    <td class="notification-actions"><?php bp_the_notification_action_links(); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="notifications-options-nav">
        <label class="bp-screen-reader-text" for="notification-select"><?php esc_html_e( 'Select Bulk Action', 'buddypress' ); ?></label>
        <select name="notification_bulk_action" id="notification-select">
            <option value="" selected="selected"><?php esc_html_e( 'Bulk Actions', 'buddypress' ); ?></option>

            <?php if ( bp_is_current_action( 'unread' ) ) : ?>
                <option value="read"><?php esc_html_e( 'Mark read', 'buddypress' ); ?></option>
                <option value="delete-all"><?php esc_html_e( 'Delete all unread', 'buddypress' ); ?></option>
            <?php elseif ( bp_is_current_action( 'read' ) ) : ?>
                <option value="unread"><?php esc_html_e( 'Mark unread', 'buddypress' ); ?></option>
                <option value="delete-all"><?php esc_html_e( 'Delete all read', 'buddypress' ); ?></option>
            <?php endif; ?>

            <option value="delete"><?php esc_html_e( 'Delete selected', 'buddypress' ); ?></option>
        </select>
        <input type="submit" id="notification-bulk-manage" class="button action" value="<?php esc_attr_e( 'Apply', 'buddypress' ); ?>">
    </div>

    <?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
</form>
