<?php
/**
 * BuddyPress members loop.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bp_before_members_loop' );
?>

<?php if ( bp_get_current_member_type() ) : ?>
    <p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

    <div id="pag-top" class="pager members-pager pagination">
        <div class="count pag-count" id="member-dir-count-top"><?php bp_members_pagination_count(); ?></div>
        <div class="pages pagination-links" id="member-dir-pag-top"><?php bp_members_pagination_links(); ?></div>
    </div>

    <?php do_action( 'bp_before_directory_members_list' ); ?>

    <div id="members-list" class="members-list" aria-live="assertive" aria-relevant="all">
        <?php while ( bp_members() ) : bp_the_member(); ?>
            <article <?php bp_member_class( array( 'member-card' ) ); ?>>
                <div class="member-card-main">
                    <div class="member-card-avatar">
                        <a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( array( 'type' => 'full', 'width' => 60, 'height' => 60 ) ); ?></a>
                    </div>

                    <div class="member-card-body">
                        <div class="member-card-title-row">
                            <a class="member-card-name" href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

                            <?php if ( bp_member_is_loggedin_user() ) : ?>
                                <span class="member-chip you"><?php esc_html_e( 'You', 'utehub2026' ); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="member-card-meta">
                            <span class="member-last-active"><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php bp_member_last_active(); ?></span>
                        </div>

                        <?php if ( bp_get_member_latest_update() ) : ?>
                            <div class="member-card-update"><?php bp_member_latest_update(); ?></div>
                        <?php endif; ?>

                        <?php do_action( 'bp_directory_members_item' ); ?>
                    </div>
                </div>

                <div class="member-card-action">
                    <?php do_action( 'bp_directory_members_actions' ); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <?php do_action( 'bp_after_directory_members_list' ); ?>
    <?php bp_member_hidden_fields(); ?>

    <div id="pag-bottom" class="pager members-pager pagination">
        <div class="count pag-count" id="member-dir-count-bottom"><?php bp_members_pagination_count(); ?></div>
        <div class="pages pagination-links" id="member-dir-pag-bottom"><?php bp_members_pagination_links(); ?></div>
    </div>

<?php else : ?>

    <div id="message" class="bbp-template-notice info"><ul><li><?php esc_html_e( 'Sorry, no members were found.', 'buddypress' ); ?></li></ul></div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
