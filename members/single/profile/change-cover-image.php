<?php
/**
 * BuddyPress member profile cover image editor.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>

<h2><?php esc_html_e( 'Change Cover Photo', 'buddypress' ); ?></h2>

<?php do_action( 'bp_before_profile_edit_cover_image' ); ?>

<p><?php esc_html_e( 'Your cover photo customizes the header of your profile.', 'buddypress' ); ?></p>

<?php bp_attachments_get_template_part( 'cover-images/index' ); ?>

<?php do_action( 'bp_after_profile_edit_cover_image' ); ?>
