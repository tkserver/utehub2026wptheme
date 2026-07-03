<?php
/**
 * BuddyPress members directory.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'bp_before_directory_members_page' );
?>

<div class="uh-wrap members-page">
    <section>
        <div id="buddypress" class="members-directory-shell">
            <?php do_action( 'bp_before_directory_members' ); ?>
            <?php do_action( 'bp_before_directory_members_content' ); ?>

            <form action="" method="post" id="members-directory-form" class="dir-form members-directory-form">
                <div class="forums-head members-head">
                    <div class="ttl">
                        <span class="eye">The Community</span>
                        <h1><?php esc_html_e( 'Members', 'utehub2026' ); ?></h1>
                    </div>

                    <div class="members-head-tools">
                        <div id="members-dir-search" class="search dir-search" role="search" data-bp-search="members">
                            <?php echo utehub2026_get_svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <label for="<?php bp_search_input_name(); ?>" class="screen-reader-text"><?php bp_search_placeholder(); ?></label>
                            <input type="search" name="<?php echo esc_attr( bp_core_get_component_search_query_arg() ); ?>" id="<?php bp_search_input_name(); ?>" placeholder="<?php bp_search_placeholder(); ?>" />
                            <button type="submit"><?php esc_html_e( 'Search', 'buddypress' ); ?></button>
                        </div>
                    </div>
                </div>

                <?php do_action( 'bp_before_directory_members_tabs' ); ?>

                <div class="members-toolbar">
                    <div class="item-list-tabs members-tabs" aria-label="<?php esc_attr_e( 'Members directory main navigation', 'buddypress' ); ?>" role="navigation">
                        <ul>
                            <li class="selected" id="members-all" data-bp-scope="all" data-bp-object="members"><a href="<?php bp_members_directory_permalink(); ?>"><?php printf( esc_html__( 'All Members %s', 'buddypress' ), '<span>' . esc_html( bp_get_total_member_count() ) . '</span>' ); ?></a></li>

                            <?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
                                <li id="members-personal" data-bp-scope="personal" data-bp-object="members"><a href="<?php bp_loggedin_user_link( array( bp_get_friends_slug(), 'my-friends' ) ); ?>"><?php printf( esc_html__( 'My Friends %s', 'buddypress' ), '<span>' . esc_html( bp_get_total_friend_count( bp_loggedin_user_id() ) ) . '</span>' ); ?></a></li>
                            <?php endif; ?>

                            <?php do_action( 'bp_members_directory_member_types' ); ?>
                        </ul>
                    </div>

                    <div class="item-list-tabs members-subnav" id="subnav" aria-label="<?php esc_attr_e( 'Members directory secondary navigation', 'buddypress' ); ?>" role="navigation">
                        <ul>
                            <?php do_action( 'bp_members_directory_member_sub_types' ); ?>

                            <li id="members-order-select" class="last filter">
                                <label for="members-order-by"><?php esc_html_e( 'Order By:', 'buddypress' ); ?></label>
                                <select id="members-order-by" data-bp-filter="members">
                                    <option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
                                    <option value="newest"><?php esc_html_e( 'Newest Registered', 'buddypress' ); ?></option>

                                    <?php if ( bp_is_active( 'xprofile' ) ) : ?>
                                        <option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>
                                    <?php endif; ?>

                                    <?php do_action( 'bp_members_directory_order_options' ); ?>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>

                <h2 class="screen-reader-text"><?php esc_html_e( 'Members directory', 'buddypress' ); ?></h2>

                <div id="members-dir-list" class="members members-directory-results dir-list" data-bp-list="members">
                    <?php bp_get_template_part( 'members/members-loop' ); ?>
                </div>

                <?php do_action( 'bp_directory_members_content' ); ?>
                <?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>
                <?php do_action( 'bp_after_directory_members_content' ); ?>
            </form>

            <?php do_action( 'bp_after_directory_members' ); ?>
        </div>
    </section>

    <?php utehub2026_render_right_rail( 'archive' ); ?>
</div>

<?php do_action( 'bp_after_directory_members_page' ); ?>

<?php
get_footer();
