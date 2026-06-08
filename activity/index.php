<?php
/**
 * BuddyPress activity directory.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

get_header();

do_action( 'bp_before_directory_activity' );
?>

<div class="page-wrap activity-page-wrap">
    <section class="activity-page-shell">
        <div id="buddypress" class="activity-directory-shell">
            <?php do_action( 'bp_before_directory_activity_content' ); ?>

            <div class="feedhead activity-head">
                <div class="ttl">
                    <span class="eye"><?php esc_html_e( "What's Happening", 'utehub2026' ); ?></span>
                    <h1><?php esc_html_e( 'Site-Wide Activity', 'utehub2026' ); ?></h1>
                </div>

                <div id="activity-dir-search" class="search dir-search" role="search">
                    <?php echo utehub2026_get_svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <form action="" method="get" id="search-activity-form">
                        <label for="activity_search" class="screen-reader-text"><?php esc_html_e( 'Search activity', 'utehub2026' ); ?></label>
                        <input type="text" name="s" id="activity_search" placeholder="<?php esc_attr_e( 'Search activity...', 'utehub2026' ); ?>" />
                        <button type="submit"><?php esc_html_e( 'Search', 'buddypress' ); ?></button>
                    </form>
                </div>
            </div>

            <div class="activity-toolbar">
                <div class="item-list-tabs activity-type-tabs activity-tabs" aria-label="<?php esc_attr_e( 'Sitewide activities navigation', 'buddypress' ); ?>" role="navigation">
                    <ul>
                        <?php do_action( 'bp_before_activity_type_tab_all' ); ?>

                        <li class="selected" id="activity-all">
                            <a href="<?php bp_activity_directory_permalink(); ?>">
                                <?php
                                printf(
                                    esc_html__( 'All Members %s', 'buddypress' ),
                                    '<span>' . esc_html( bp_get_total_member_count() ) . '</span>'
                                );
                                ?>
                            </a>
                        </li>

                        <?php if ( is_user_logged_in() ) : ?>
                            <?php do_action( 'bp_before_activity_type_tab_friends' ); ?>

                            <?php if ( bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
                                <li id="activity-friends">
                                    <a href="<?php bp_loggedin_user_link( array( bp_get_activity_slug(), bp_get_friends_slug() ) ); ?>">
                                        <?php
                                        printf(
                                            esc_html__( 'My Friends %s', 'buddypress' ),
                                            '<span>' . esc_html( bp_get_total_friend_count( bp_loggedin_user_id() ) ) . '</span>'
                                        );
                                        ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php do_action( 'bp_before_activity_type_tab_groups' ); ?>

                            <?php if ( bp_is_active( 'groups' ) && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>
                                <li id="activity-groups">
                                    <a href="<?php bp_loggedin_user_link( array( bp_get_activity_slug(), bp_get_groups_slug() ) ); ?>">
                                        <?php
                                        printf(
                                            esc_html__( 'My Groups %s', 'buddypress' ),
                                            '<span>' . esc_html( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) . '</span>'
                                        );
                                        ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php do_action( 'bp_before_activity_type_tab_favorites' ); ?>

                            <?php if ( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) : ?>
                                <li id="activity-favorites">
                                    <a href="<?php bp_loggedin_user_link( array( bp_get_activity_slug(), 'favorites' ) ); ?>">
                                        <?php
                                        printf(
                                            esc_html__( 'My Favorites %s', 'buddypress' ),
                                            '<span>' . esc_html( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) . '</span>'
                                        );
                                        ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ( bp_activity_do_mentions() ) : ?>
                                <?php do_action( 'bp_before_activity_type_tab_mentions' ); ?>

                                <li id="activity-mentions">
                                    <a href="<?php bp_loggedin_user_link( array( bp_get_activity_slug(), 'mentions' ) ); ?>">
                                        <?php esc_html_e( 'Mentions', 'buddypress' ); ?>
                                        <?php if ( bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ) : ?>
                                            <span>
                                                <?php
                                                $new_mentions_count = bp_get_total_mention_count_for_user( bp_loggedin_user_id() );
                                                echo esc_html( $new_mentions_count );
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php do_action( 'bp_activity_type_tabs' ); ?>
                    </ul>
                </div>
            </div>

            <div class="activity-subnav-row">
                <div class="item-list-tabs no-ajax activity-rss-nav" id="activity-rss-nav" aria-label="<?php esc_attr_e( 'Activity feed navigation', 'buddypress' ); ?>" role="navigation">
                    <ul>
                        <?php if ( bp_activity_is_feed_enable( 'sitewide' ) ) : ?>
                            <li class="feed">
                                <a href="<?php bp_sitewide_activity_feed_link(); ?>" class="bp-tooltip" data-bp-tooltip="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>" aria-label="<?php esc_attr_e( 'RSS Feed', 'buddypress' ); ?>">
                                    <?php esc_html_e( 'RSS', 'buddypress' ); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="item-list-tabs no-ajax activity-filter-nav" id="subnav" aria-label="<?php esc_attr_e( 'Activity secondary navigation', 'buddypress' ); ?>" role="navigation">
                    <ul>
                        <?php do_action( 'bp_activity_syndication_options' ); ?>

                        <li id="activity-filter-select" class="last">
                            <label for="activity-filter-by"><?php esc_html_e( 'Show', 'utehub2026' ); ?></label>
                            <select id="activity-filter-by">
                                <option value="-1"><?php esc_html_e( 'Everything', 'utehub2026' ); ?></option>
                                <?php bp_activity_show_filters(); ?>
                                <?php do_action( 'bp_activity_filter_options' ); ?>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>

            <div id="template-notices" role="alert" aria-atomic="true">
                <?php do_action( 'template_notices' ); ?>
            </div>

            <?php do_action( 'bp_before_directory_activity_list' ); ?>

            <div class="activity" aria-live="polite" aria-atomic="true" aria-relevant="all">
                <?php bp_get_template_part( 'activity/activity-loop' ); ?>
            </div>

            <?php do_action( 'bp_after_directory_activity_list' ); ?>
            <?php do_action( 'bp_directory_activity_content' ); ?>
            <?php do_action( 'bp_after_directory_activity_content' ); ?>
        </div>
    </section>
</div>

<?php
do_action( 'bp_after_directory_activity' );
get_footer();
