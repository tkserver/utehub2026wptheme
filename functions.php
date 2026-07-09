<?php

defined( 'ABSPATH' ) || exit;

add_filter( 'bp_email_use_wp_mail', '__return_true' );
add_filter( 'show_admin_bar', '__return_false' );
add_filter( 'body_class', 'utehub2026_filter_body_classes' );
add_action( 'template_redirect', 'utehub2026_ensure_server_keys_exist', 1 );
add_action( 'init', 'utehub2026_stop_heartbeat', 1 );
add_action( 'wp_head', 'utehub2026_dark_mode_inline_style', 1 );

function utehub2026_ensure_server_keys_exist() {
    if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
        $_SERVER['HTTP_HOST'] = '';
    }
}

function utehub2026_stop_heartbeat() {
    wp_deregister_script( 'heartbeat' );
}

function utehub2026_filter_body_classes( $classes ) {
    $classes[] = 'utehub-theme-contract';
    $classes[] = 'utehub-theme-surface';

    return $classes;
}

function utehub2026_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    register_nav_menus(
        array(
            'header-menu' => __( 'Header Menu', 'utehub2026' ),
        )
    );
}
add_action( 'after_setup_theme', 'utehub2026_setup' );
add_action( 'customize_register', 'utehub2026_customize_register' );

function utehub2026_keep_verbose_page_rules_first( $rules ) {
    $permalink_structure = (string) get_option( 'permalink_structure' );

    if ( ! str_starts_with( $permalink_structure, '/%postname%' ) ) {
        return $rules;
    }

    $page_rules = array();
    $other_rules = array();

    foreach ( $rules as $regex => $query ) {
        if ( is_string( $query ) && str_contains( $query, 'pagename=$matches' ) ) {
            $page_rules[ $regex ] = $query;
        } else {
            $other_rules[ $regex ] = $query;
        }
    }

    if ( empty( $page_rules ) ) {
        return $rules;
    }

    return $page_rules + $other_rules;
}
add_filter( 'rewrite_rules_array', 'utehub2026_keep_verbose_page_rules_first', 1000 );

function utehub2026_enqueue_assets() {
    $style_path    = get_stylesheet_directory() . '/style.css';
    $style_version = file_exists( $style_path ) ? (string) filemtime( $style_path ) : wp_get_theme()->get( 'Version' );
    $script_path   = get_stylesheet_directory() . '/assets/nav.js';
    $script_version = file_exists( $script_path ) ? (string) filemtime( $script_path ) : $style_version;

    wp_enqueue_style(
        'utehub2026-fonts',
        'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Graduate&display=swap',
        array(),
        null
    );

    wp_enqueue_style( 'utehub2026-style', get_stylesheet_uri(), array( 'utehub2026-fonts' ), $style_version );

    wp_enqueue_script(
        'utehub2026-nav',
        get_template_directory_uri() . '/assets/nav.js',
        array(),
        $script_version,
        true
    );

    wp_localize_script(
        'utehub2026-nav',
        'UteHubThemeConfig',
        array(
            'storageKey'  => 'utehub2026-theme',
            'eventName'   => 'utehub:themechange',
            'bodyClassPrefix' => 'theme-',
            'bodyThemeAttribute' => 'data-theme',
            'rootThemeAttribute' => 'data-theme',
        )
    );

    if ( function_exists( 'bp_is_members_directory' ) && bp_is_members_directory() ) {
        $members_script_path    = get_stylesheet_directory() . '/assets/members-directory.js';
        $members_script_version = file_exists( $members_script_path ) ? (string) filemtime( $members_script_path ) : $script_version;

        wp_enqueue_script(
            'utehub2026-members-directory',
            get_template_directory_uri() . '/assets/members-directory.js',
            array(),
            $members_script_version,
            true
        );

        wp_localize_script(
            'utehub2026-members-directory',
            'UteHubMembersDirectory',
            array(
                'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
                'membersUrl' => function_exists( 'bp_get_members_directory_permalink' ) ? bp_get_members_directory_permalink() : home_url( '/members/' ),
            )
        );
    }

    if ( function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory() ) {
        $activity_script_path    = get_stylesheet_directory() . '/assets/activity-directory.js';
        $activity_script_version = file_exists( $activity_script_path ) ? (string) filemtime( $activity_script_path ) : $script_version;

        wp_enqueue_script(
            'utehub2026-activity-directory',
            get_template_directory_uri() . '/assets/activity-directory.js',
            array(),
            $activity_script_version,
            true
        );

        wp_localize_script(
            'utehub2026-activity-directory',
            'UteHubActivityDirectory',
            array(
                'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
                'activityUrl' => function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : home_url( '/activity/' ),
            )
        );
    }

    if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
        $member_script_path    = get_stylesheet_directory() . '/assets/member-single.js';
        $member_script_version = file_exists( $member_script_path ) ? (string) filemtime( $member_script_path ) : $script_version;

        wp_enqueue_script(
            'utehub2026-member-single',
            get_template_directory_uri() . '/assets/member-single.js',
            array(),
            $member_script_version,
            true
        );

        wp_localize_script(
            'utehub2026-member-single',
            'UteHubMemberSingle',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'utehub2026_enqueue_assets' );

function utehub2026_members_ajax_querystring( $query_string, $object ) {
    if ( 'members' !== $object || ! bp_is_post_request() || empty( $_POST['action'] ) || 'members_filter' !== $_POST['action'] ) {
        return $query_string;
    }

    $allowed_filters = array( 'active', 'newest', 'alphabetical', 'random', 'popular', 'online' );
    $filter          = isset( $_POST['filter'] ) ? sanitize_key( wp_unslash( $_POST['filter'] ) ) : 'active';
    $scope           = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'all';
    $page            = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $search_terms    = isset( $_POST['search_terms'] ) ? sanitize_text_field( wp_unslash( $_POST['search_terms'] ) ) : '';
    $query_args      = array(
        'type' => in_array( $filter, $allowed_filters, true ) ? $filter : 'active',
        'page' => max( 1, $page ),
    );

    if ( 'personal' === $scope && is_user_logged_in() ) {
        $query_args['user_id'] = bp_loggedin_user_id();
    }

    if ( 'friends' === $scope ) {
        $displayed_user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;

        if ( $displayed_user_id ) {
            $query_args['user_id'] = $displayed_user_id;
        }
    }

    if ( '' !== $search_terms ) {
        $query_args['search_terms'] = $search_terms;
    }

    return build_query( $query_args );
}

function utehub2026_members_ajax_template_loader() {
    if ( ! bp_is_post_request() ) {
        wp_die();
    }

    $object = isset( $_POST['object'] ) ? sanitize_key( wp_unslash( $_POST['object'] ) ) : '';

    if ( 'members' !== $object || ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'members' ) ) {
        wp_die();
    }

    add_filter( 'bp_ajax_querystring', 'utehub2026_members_ajax_querystring', 20, 2 );

    if ( function_exists( 'bp_update_is_directory' ) && ! bp_current_action() ) {
        bp_update_is_directory( true, 'members' );
    }

    bp_get_template_part( 'members/members-loop' );
    wp_die();
}
add_action( 'wp_ajax_members_filter', 'utehub2026_members_ajax_template_loader' );
add_action( 'wp_ajax_nopriv_members_filter', 'utehub2026_members_ajax_template_loader' );

function utehub2026_register_buddypress_cover_image_feature() {
    if ( ! function_exists( 'bp_set_theme_compat_feature' ) ) {
        return;
    }

    bp_set_theme_compat_feature(
        'legacy',
        array(
            'name'     => 'cover_image',
            'settings' => array(
                'components'   => array( 'members', 'groups' ),
                'width'        => 1300,
                'height'       => 252,
                'callback'     => 'bp_legacy_theme_cover_image',
                'theme_handle' => 'utehub2026-style',
            ),
        )
    );
}
add_action( 'bp_after_setup_theme', 'utehub2026_register_buddypress_cover_image_feature', 20 );

function utehub2026_buddypress_cover_image_settings( $settings ) {
    $settings['components']   = array( 'members', 'groups' );
    $settings['width']        = 1300;
    $settings['height']       = 252;
    $settings['callback']     = 'bp_legacy_theme_cover_image';
    $settings['theme_handle'] = 'utehub2026-style';

    return $settings;
}
add_filter( 'bp_before_members_cover_image_settings_parse_args', 'utehub2026_buddypress_cover_image_settings' );
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'utehub2026_buddypress_cover_image_settings' );

function utehub2026_cover_photo_nav_label( $nav_html ) {
    return str_replace( 'Change Cover Image', 'Change Cover Photo', $nav_html );
}
add_filter( 'bp_get_options_nav_change-cover-image', 'utehub2026_cover_photo_nav_label' );

function utehub2026_hide_member_groups_nav() {
    if ( utehub2026_is_member_groups_tab_enabled() ) {
        return;
    }

    if ( function_exists( 'bp_core_remove_nav_item' ) ) {
        bp_core_remove_nav_item( 'groups' );
    }
}
add_action( 'bp_setup_nav', 'utehub2026_hide_member_groups_nav', 100 );

function utehub2026_member_screen_template() {
    return 'members/single/home';
}
add_filter( 'bbp_member_forums_screen_topics', 'utehub2026_member_screen_template' );
add_filter( 'bbp_member_forums_screen_replies', 'utehub2026_member_screen_template' );
add_filter( 'bbp_member_forums_screen_engagements', 'utehub2026_member_screen_template' );
add_filter( 'bbp_member_forums_screen_favorites', 'utehub2026_member_screen_template' );
add_filter( 'bbp_member_forums_screen_subscriptions', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_general_settings', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_notification_settings', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_capabilities', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_delete_account', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_data', 'utehub2026_member_screen_template' );
add_filter( 'bp_settings_screen_xprofile', 'utehub2026_member_screen_template' );

function utehub2026_notifications_delete_all_for_current_tab() {
    if ( ! function_exists( 'bp_is_notifications_component' ) || ! bp_is_notifications_component() ) {
        return;
    }

    if ( ! bp_is_current_action( 'read' ) && ! bp_is_current_action( 'unread' ) ) {
        return;
    }

    $action = isset( $_POST['notification_bulk_action'] ) ? sanitize_key( wp_unslash( $_POST['notification_bulk_action'] ) ) : '';

    if ( 'delete-all' !== $action ) {
        return;
    }

    $nonce = isset( $_POST['notifications_bulk_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['notifications_bulk_nonce'] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'notifications_bulk_nonce' ) ) {
        bp_core_add_message( __( 'There was a problem deleting your notifications.', 'buddypress' ), 'error' );
        return;
    }

    $user_id = bp_displayed_user_id();

    if ( ! $user_id || ( (int) $user_id !== (int) bp_loggedin_user_id() && ! current_user_can( 'bp_manage' ) ) ) {
        bp_core_add_message( __( 'You do not have permission to delete these notifications.', 'buddypress' ), 'error' );
        return;
    }

    $is_new  = bp_is_current_action( 'unread' ) ? 1 : 0;
    $deleted = BP_Notifications_Notification::delete(
        array(
            'user_id' => $user_id,
            'is_new'  => $is_new,
        )
    );

    if ( false === $deleted ) {
        bp_core_add_message( __( 'There was a problem deleting your notifications.', 'buddypress' ), 'error' );
    } else {
        bp_core_add_message( sprintf( _n( '%s notification deleted.', '%s notifications deleted.', (int) $deleted, 'buddypress' ), number_format_i18n( (int) $deleted ) ) );
    }

    $redirect = bp_is_current_action( 'unread' ) ? bp_get_notifications_unread_permalink( $user_id ) : bp_get_notifications_read_permalink( $user_id );

    bp_core_redirect( $redirect );
}
add_action( 'bp_actions', 'utehub2026_notifications_delete_all_for_current_tab', 9 );

function utehub2026_activity_ajax_querystring( $query_string, $object ) {
    if ( 'activity' !== $object || ! bp_is_post_request() || empty( $_POST['action'] ) || 'activity_widget_filter' !== $_POST['action'] ) {
        return $query_string;
    }

    $filter       = isset( $_POST['filter'] ) ? sanitize_key( wp_unslash( $_POST['filter'] ) ) : '-1';
    $scope        = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'all';
    $search_terms = isset( $_POST['search_terms'] ) ? sanitize_text_field( wp_unslash( $_POST['search_terms'] ) ) : '';
    $query_args   = array(
        'page' => 1,
    );

    if ( '' !== $filter && '-1' !== $filter ) {
        $query_args['type']   = $filter;
        $query_args['action'] = $filter;
    }

    if ( 'all' !== $scope ) {
        $query_args['scope'] = $scope;
    }

    if ( '' !== $search_terms ) {
        $query_args['search_terms'] = $search_terms;
    }

    return build_query( $query_args );
}

function utehub2026_get_activity_ajax_feed_url( $scope ) {
    switch ( $scope ) {
        case 'friends':
            return is_user_logged_in() ? bp_loggedin_user_url( bp_members_get_path_chunks( array( bp_get_activity_slug(), bp_get_friends_slug(), array( 'feed' ) ) ) ) : bp_get_sitewide_activity_feed_link();
        case 'groups':
            return is_user_logged_in() ? bp_loggedin_user_url( bp_members_get_path_chunks( array( bp_get_activity_slug(), bp_get_groups_slug(), array( 'feed' ) ) ) ) : bp_get_sitewide_activity_feed_link();
        case 'favorites':
            return is_user_logged_in() ? bp_loggedin_user_url( bp_members_get_path_chunks( array( bp_get_activity_slug(), 'favorites', array( 'feed' ) ) ) ) : bp_get_sitewide_activity_feed_link();
        case 'mentions':
            return is_user_logged_in() ? bp_loggedin_user_url( bp_members_get_path_chunks( array( bp_get_activity_slug(), 'mentions', array( 'feed' ) ) ) ) : bp_get_sitewide_activity_feed_link();
        default:
            return bp_get_sitewide_activity_feed_link();
    }
}

function utehub2026_activity_ajax_template_loader() {
    if ( ! bp_is_post_request() || ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
        wp_send_json_error();
    }

    $scope = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'all';

    add_filter( 'bp_ajax_querystring', 'utehub2026_activity_ajax_querystring', 20, 2 );

    ob_start();
    bp_get_template_part( 'activity/activity-loop' );
    $contents = ob_get_clean();

    wp_send_json(
        array(
            'contents' => $contents,
            'feed_url' => utehub2026_get_activity_ajax_feed_url( $scope ),
        )
    );
}
add_action( 'wp_ajax_activity_widget_filter', 'utehub2026_activity_ajax_template_loader', 20 );
add_action( 'wp_ajax_nopriv_activity_widget_filter', 'utehub2026_activity_ajax_template_loader', 20 );

function utehub2026_customize_register( $wp_customize ) {
    $wp_customize->add_section(
        'utehub2026_home_copy',
        array(
            'title'       => __( 'Home Copy', 'utehub2026' ),
            'priority'    => 44,
            'description' => __( 'Manage the welcome text shown above the homepage topic feed.', 'utehub2026' ),
        )
    );

    $wp_customize->add_setting(
        'utehub2026_home_welcome_messages',
        array(
            'default'           => "Welcome to the Hub",
            'sanitize_callback' => 'utehub2026_sanitize_multiline_text',
        )
    );

    $wp_customize->add_control(
        'utehub2026_home_welcome_messages',
        array(
            'label'       => __( 'Welcome Messages', 'utehub2026' ),
            'description' => __( 'Enter one message per line. The rotation mode below controls how they are used.', 'utehub2026' ),
            'section'     => 'utehub2026_home_copy',
            'type'        => 'textarea',
        )
    );

    $wp_customize->add_setting(
        'utehub2026_home_welcome_rotation',
        array(
            'default'           => 'daily',
            'sanitize_callback' => 'utehub2026_sanitize_home_welcome_rotation',
        )
    );

    $wp_customize->add_control(
        'utehub2026_home_welcome_rotation',
        array(
            'label'   => __( 'Welcome Message Rotation', 'utehub2026' ),
            'section' => 'utehub2026_home_copy',
            'type'    => 'select',
            'choices' => array(
                'static' => __( 'Use first message only', 'utehub2026' ),
                'daily'  => __( 'Rotate daily', 'utehub2026' ),
                'random' => __( 'Random on each page load', 'utehub2026' ),
            ),
        )
    );

    $wp_customize->add_setting(
        'utehub2026_home_topics_heading',
        array(
            'default'           => 'Latest Topics',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'utehub2026_home_topics_heading',
        array(
            'label'       => __( 'Topics Heading', 'utehub2026' ),
            'description' => __( 'Main heading shown above the homepage topic feed.', 'utehub2026' ),
            'section'     => 'utehub2026_home_copy',
            'type'        => 'text',
        )
    );

    $wp_customize->add_section(
        'utehub2026_member_profiles',
        array(
            'title'       => __( 'Member Profiles', 'utehub2026' ),
            'priority'    => 45,
            'description' => __( 'Toggle BuddyPress profile features shown by the theme.', 'utehub2026' ),
        )
    );

    $wp_customize->add_setting(
        'utehub2026_show_member_groups_tab',
        array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        )
    );

    $wp_customize->add_control(
        'utehub2026_show_member_groups_tab',
        array(
            'label'       => __( 'Show Groups tab on member profiles', 'utehub2026' ),
            'description' => __( 'Keep the BuddyPress Groups component active for compatibility while hiding the profile tab when groups are not part of the site experience.', 'utehub2026' ),
            'section'     => 'utehub2026_member_profiles',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'utehub2026_show_member_whats_new',
        array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        )
    );

    $wp_customize->add_control(
        'utehub2026_show_member_whats_new',
        array(
            'label'       => __( 'Show "What\'s New" composer on profiles', 'utehub2026' ),
            'description' => __( 'Allow members to post activity updates from their profile activity tab.', 'utehub2026' ),
            'section'     => 'utehub2026_member_profiles',
            'type'        => 'checkbox',
        )
    );

    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return;
    }

    $wp_customize->add_section(
        'utehub2026_home_feed',
        array(
            'title'       => __( 'Home Feed', 'utehub2026' ),
            'priority'    => 46,
            'description' => __( 'Choose which forum pills appear above the homepage topic feed.', 'utehub2026' ),
        )
    );

    $choices = utehub2026_get_forum_choices();

    for ( $index = 1; $index <= 3; $index++ ) {
        $wp_customize->add_setting(
            'utehub2026_home_feed_forum_' . $index,
            array(
                'default'           => 0,
                'sanitize_callback' => 'absint',
            )
        );

        $wp_customize->add_control(
            'utehub2026_home_feed_forum_' . $index,
            array(
                'label'   => sprintf( __( 'Forum Pill %d', 'utehub2026' ), $index ),
                'section' => 'utehub2026_home_feed',
                'type'    => 'select',
                'choices' => $choices,
            )
        );
    }

    $wp_customize->add_section(
        'utehub2026_forum_features',
        array(
            'title'       => __( 'Forum Features', 'utehub2026' ),
            'priority'    => 47,
            'description' => __( 'Toggle optional bbPress features shown on topic pages.', 'utehub2026' ),
        )
    );

    $wp_customize->add_setting(
        'utehub2026_show_favorite_topic',
        array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        )
    );

    $wp_customize->add_control(
        'utehub2026_show_favorite_topic',
        array(
            'label'   => __( 'Show "Favorite" link on topics', 'utehub2026' ),
            'section' => 'utehub2026_forum_features',
            'type'    => 'checkbox',
        )
    );

    $wp_customize->add_section(
        'utehub2026_dark_mode',
        array(
            'title'       => __( 'Dark Mode', 'utehub2026' ),
            'priority'    => 48,
            'description' => __( 'Configure dark mode behavior for the theme.', 'utehub2026' ),
        )
    );

    $wp_customize->add_setting(
        'utehub2026_dark_mode_default',
        array(
            'default'           => 'auto',
            'sanitize_callback' => 'utehub2026_sanitize_dark_mode_default',
        )
    );

    $wp_customize->add_control(
        'utehub2026_dark_mode_default',
        array(
            'label'   => __( 'Default Mode', 'utehub2026' ),
            'section' => 'utehub2026_dark_mode',
            'type'    => 'select',
            'choices' => array(
                'auto'   => __( 'Follow system preference', 'utehub2026' ),
                'light'  => __( 'Light mode', 'utehub2026' ),
                'dark'   => __( 'Dark mode', 'utehub2026' ),
            ),
        )
    );
}

function utehub2026_is_member_groups_tab_enabled() {
    return (bool) get_theme_mod( 'utehub2026_show_member_groups_tab', false );
}

function utehub2026_is_member_whats_new_enabled() {
    return (bool) get_theme_mod( 'utehub2026_show_member_whats_new', false );
}

function utehub2026_is_favorite_topic_enabled() {
    return (bool) get_theme_mod( 'utehub2026_show_favorite_topic', true );
}

function utehub2026_sanitize_multiline_text( $value ) {
    $lines = preg_split( '/\r\n|\r|\n/', (string) $value );
    $lines = array_map( 'sanitize_text_field', $lines );
    $lines = array_filter(
        array_map( 'trim', $lines ),
        static function( $line ) {
            return '' !== $line;
        }
    );

    if ( empty( $lines ) ) {
        return 'Welcome to the Hub';
    }

    return implode( "\n", $lines );
}

function utehub2026_sanitize_home_welcome_rotation( $value ) {
    $value   = sanitize_key( (string) $value );
    $allowed = array( 'static', 'daily', 'random' );

    return in_array( $value, $allowed, true ) ? $value : 'daily';
}

function utehub2026_sanitize_dark_mode_default( $value ) {
    $value   = sanitize_key( (string) $value );
    $allowed = array( 'auto', 'light', 'dark' );

    return in_array( $value, $allowed, true ) ? $value : 'auto';
}

function utehub2026_get_dark_mode_default() {
    return get_theme_mod( 'utehub2026_dark_mode_default', 'auto' );
}

function utehub2026_dark_mode_inline_style() {
    $default = utehub2026_get_dark_mode_default();
    $storage_key = 'utehub2026-theme';
    ?><script>(function(){var t=localStorage.getItem('<?php echo $storage_key; ?>');if(t==='dark'||t==='light'){document.documentElement.setAttribute("data-theme",t)}else if(!t&&<?php echo 'auto' === $default ? 'true' : 'false'; ?>&&window.matchMedia('(prefers-color-scheme: dark)').matches){document.documentElement.setAttribute("data-theme","dark")}document.documentElement.style.setProperty("--dm","<?php echo 'auto' === $default ? 'match' : 'override'; ?>")})();</script><?php
}

function utehub2026_get_home_welcome_messages() {
    $raw = (string) get_theme_mod( 'utehub2026_home_welcome_messages', "Welcome to the Hub" );
    $messages = preg_split( '/\r\n|\r|\n/', $raw );
    $messages = array_filter(
        array_map( 'trim', array_map( 'sanitize_text_field', (array) $messages ) ),
        static function( $message ) {
            return '' !== $message;
        }
    );

    if ( empty( $messages ) ) {
        return array( 'Welcome to the Hub' );
    }

    return array_values( $messages );
}

function utehub2026_get_home_welcome_message() {
    $messages = utehub2026_get_home_welcome_messages();
    $count    = count( $messages );

    if ( 1 === $count ) {
        return $messages[0];
    }

    $mode = utehub2026_sanitize_home_welcome_rotation( get_theme_mod( 'utehub2026_home_welcome_rotation', 'daily' ) );

    if ( 'random' === $mode ) {
        return $messages[ wp_rand( 0, $count - 1 ) ];
    }

    if ( 'daily' === $mode ) {
        $day_index = (int) gmdate( 'z', current_time( 'timestamp', true ) );
        return $messages[ $day_index % $count ];
    }

    return $messages[0];
}

function utehub2026_get_home_topics_heading() {
    $heading = sanitize_text_field( (string) get_theme_mod( 'utehub2026_home_topics_heading', 'Latest Topics' ) );

    return '' !== $heading ? $heading : 'Latest Topics';
}

function utehub2026_register_sidebars() {
    $rail_sidebars = array(
        'front-page-sidebar' => 'Standard Right Rail',
        'page-sidebar'       => 'Page Right Rail',
        'topic-sidebar'      => 'Topic Right Rail',
        'pickem-sidebar'     => 'Pick Em Right Rail',
        'predict-sidebar'    => 'Predict Right Rail',
        'schedules-sidebar'  => 'Schedules Right Rail',
        'chat-sidebar'       => 'Chat Right Rail',
    );

    foreach ( $rail_sidebars as $id => $label ) {
        register_sidebar(
            array(
                'name'          => __( $label, 'utehub2026' ),
                'id'            => $id,
                'before_widget' => '<section id="%1$s" class="panel widget %2$s"><div class="panel-b">',
                'after_widget'  => '</div></section>',
                'before_title'  => '</div><h2 class="panel-h widget-title">',
                'after_title'   => '</h2><div class="panel-b">',
            )
        );
    }

    $footer_sidebars = array(
        'footer-left'   => 'Footer Left',
        'footer-center' => 'Footer Center',
        'footer-right'  => 'Footer Right',
    );

    foreach ( $footer_sidebars as $id => $label ) {
        register_sidebar(
            array(
                'name'          => __( $label, 'utehub2026' ),
                'id'            => $id,
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title screen-reader-text">',
                'after_title'   => '</h2>',
            )
        );
    }
}
add_action( 'widgets_init', 'utehub2026_register_sidebars' );

function utehub2026_get_right_rail_sidebar_id( $context = '' ) {
    if ( 'full' === $context || 'none' === $context ) {
        return '';
    }

    if ( 'page' === $context ) {
        return 'page-sidebar';
    }

    if ( 'topic' === $context ) {
        return 'topic-sidebar';
    }

    if ( 'pickem' === $context ) {
        return 'pickem-sidebar';
    }

    if ( 'predict' === $context ) {
        return 'predict-sidebar';
    }

    if ( 'schedules' === $context ) {
        return 'schedules-sidebar';
    }

    if ( 'chat' === $context ) {
        return 'chat-sidebar';
    }

    $request_path = wp_parse_url( home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '/' ) ), PHP_URL_PATH );
    $request_path = is_string( $request_path ) ? trim( $request_path, '/' ) : '';

    if ( preg_match( '#(^|/)pick-em(/|$)#', $request_path ) ) {
        return 'pickem-sidebar';
    }

    if ( preg_match( '#(^|/)predict(/|$)#', $request_path ) ) {
        return 'predict-sidebar';
    }

    if ( preg_match( '#(^|/)schedules(/|$)#', $request_path ) ) {
        return 'schedules-sidebar';
    }

    if ( preg_match( '#(^|/)chat(/|$)#', $request_path ) || preg_match( '#(^|/)tkchat(/|$)#', $request_path ) ) {
        return 'chat-sidebar';
    }

    return 'front-page-sidebar';
}

function utehub2026_render_content_page_layout( $args = array() ) {
    $args = wp_parse_args(
        $args,
        array(
            'context'     => 'page',
            'full_width'  => false,
            'title'       => get_the_title(),
            'show_title'  => true,
            'card_class'  => 'page-card prose-card',
        )
    );

    $args['card_class'] .= ' page-context-' . sanitize_html_class( (string) $args['context'] );

    if ( $args['full_width'] ) {
        echo '<div class="page-wrap">';
        echo '<article class="' . esc_attr( $args['card_class'] ) . '">';
        if ( $args['show_title'] ) {
            echo '<h1 class="page-title">' . esc_html( $args['title'] ) . '</h1>';
        }
        the_content();
        echo '</article>';
        echo '</div>';
        return;
    }

    echo '<div class="uh-wrap">';
    echo '<section>';
    echo '<article class="' . esc_attr( $args['card_class'] ) . '">';
    if ( $args['show_title'] ) {
        echo '<h1 class="page-title">' . esc_html( $args['title'] ) . '</h1>';
    }
    the_content();
    echo '</article>';
    echo '</section>';
    utehub2026_render_right_rail( $args['context'] );
    echo '</div>';
}

function utehub2026_is_bbpress() {
    return function_exists( 'is_bbpress' ) && is_bbpress();
}

function utehub2026_content_has_forum_layout( $post = null ) {
    $post = get_post( $post );

    if ( ! $post instanceof WP_Post ) {
        return false;
    }

    if ( utehub2026_is_bbpress() ) {
        return true;
    }

    $content = (string) $post->post_content;

    if ( preg_match( '/\[bbp[^\]]*\]/i', $content ) ) {
        return true;
    }

    return false;
}

function utehub2026_get_svg( $icon ) {
    static $icons = array(
        'home'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"/><path d="M5 9v11h14V9"/></svg>',
        'mobile'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 19h2"/></svg>',
        'forum'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        'chevron'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>',
        'search'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>',
        'clock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'account'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 12 0v1"/></svg>',
        'poll'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10M12 20V4M20 20v-6"/></svg>',
        'active'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20a6 6 0 0 1 12 0M15 20a5 5 0 0 1 6-4.5"/></svg>',
        'favorite'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18l-5.9 3 1.2-6.5L2.5 9.9 9 9z"/></svg>',
        'plus'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>',
        'reply'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 17l-6-5 6-5"/><path d="M3 12h12a6 6 0 0 1 6 6v1"/></svg>',
        'pin'       => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 3l1 6 4 3-6 1-3 8-3-8-6-1 4-3 1-6z"/></svg>',
        'thumb-up'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M2 10h4v11H2zM22 11a2 2 0 0 0-2-2h-5l1-4a2 2 0 0 0-4-1l-4 7v10h11a2 2 0 0 0 2-1.7l1.5-7z"/></svg>',
        'thumb-down'=> '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 14h-4V3h4zM2 13a2 2 0 0 0 2 2h5l-1 4a2 2 0 0 0 4 1l4-7V3H5a2 2 0 0 0-2 1.7L1.5 12z"/></svg>',
        'target'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>',
        'trophy'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3h8v3a4 4 0 0 1-4 4 4 4 0 0 1-4-4z"/><path d="M6 5H4a2 2 0 0 0 2 5h2"/><path d="M18 5h2a2 2 0 0 1-2 5h-2"/><path d="M12 10v5"/><path d="M8 21h8"/><path d="M9 15h6v6H9z"/></svg>',
        'megaphone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11v2a2 2 0 0 0 2 2h2l5 4V5l-5 4H5a2 2 0 0 0-2 2z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M18.5 6.5a8.5 8.5 0 0 1 0 11"/></svg>',
        'gavel'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 6l4 4"/><path d="M7 13l4 4"/><path d="M5 15l10-10 4 4-10 10z"/><path d="M3 21h12"/></svg>',
        'chat'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/></svg>',
    );

    return $icons[ $icon ] ?? '';
}

function utehub2026_get_brand_url() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        if ( $logo ) {
            return $logo;
        }
    }

    return get_template_directory_uri() . '/assets/utehub-wordmark-dark.png';
}

function utehub2026_get_nav_icon_name( $label ) {
    $normalized = strtolower( trim( wp_strip_all_tags( (string) $label ) ) );

    $map = array(
        'home'       => 'home',
        'mobile app' => 'mobile',
        'forum'      => 'forum',
        'hub'        => 'chevron',
    );

    return $map[ $normalized ] ?? '';
}

function utehub2026_get_default_nav_items() {
    $forum_url = function_exists( 'bbp_get_forum_archive_permalink' ) ? bbp_get_forum_archive_permalink() : home_url( '/forums/' );
    $forum_kids = array(
        array(
            'label'   => 'Latest Topics',
            'url'     => $forum_url,
            'icon'    => '',
            'current' => utehub2026_is_bbpress() && ( bbp_is_topic_archive() || bbp_is_forum_archive() ),
        ),
    );

    if ( function_exists( 'bbp_get_forum_post_type' ) ) {
        $forums = get_posts(
            array(
                'post_type'      => bbp_get_forum_post_type(),
                'post_parent'    => 0,
                'post_status'    => bbp_get_public_status_id(),
                'posts_per_page' => 4,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC',
            )
        );

        foreach ( $forums as $forum ) {
            $forum_kids[] = array(
                'label'   => get_the_title( $forum ),
                'url'     => get_permalink( $forum ),
                'icon'    => '',
                'current' => function_exists( 'bbp_get_forum_id' ) && (int) bbp_get_forum_id() === (int) $forum->ID,
            );
        }
    }

    $hub_children = array();
    $hub_pages    = array(
        'Members'  => home_url( '/members/' ),
        'Messages' => home_url( '/messages/' ),
        'Pick Em'  => home_url( '/pick-em/' ),
    );

    foreach ( $hub_pages as $label => $url ) {
        $hub_children[] = array(
            'label'   => $label,
            'url'     => $url,
            'icon'    => '',
            'current' => untrailingslashit( home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '/' ) ) ) === untrailingslashit( $url ),
        );
    }

    return array(
        array(
            'label'  => 'Home',
            'url'    => home_url( '/' ),
            'icon'   => 'home',
            'active' => is_front_page() || is_home(),
        ),
        array(
            'label'  => 'Mobile App',
            'url'    => home_url( '/mobile-app/' ),
            'icon'   => 'mobile',
            'active' => is_page( 'mobile-app' ),
        ),
        array(
            'label'  => 'Forum',
            'url'    => $forum_url,
            'icon'   => 'forum',
            'active' => utehub2026_is_bbpress(),
            'children' => $forum_kids,
        ),
        array(
            'label'  => 'Hub',
            'url'    => home_url( '/hub/' ),
            'icon'   => 'chevron',
            'active' => is_page( 'hub' ),
            'children' => $hub_children,
        ),
    );
}

function utehub2026_get_primary_nav_items() {
    if ( ! has_nav_menu( 'header-menu' ) ) {
        return utehub2026_get_default_nav_items();
    }

    $locations = get_nav_menu_locations();
    if ( empty( $locations['header-menu'] ) ) {
        return utehub2026_get_default_nav_items();
    }

    $menu_items = wp_get_nav_menu_items( $locations['header-menu'] );
    if ( empty( $menu_items ) || is_wp_error( $menu_items ) ) {
        return utehub2026_get_default_nav_items();
    }

    $indexed = array();

    foreach ( $menu_items as $item ) {
        $indexed[ $item->ID ] = array(
            'id'       => (int) $item->ID,
            'parent'   => (int) $item->menu_item_parent,
            'label'    => $item->title,
            'url'      => $item->url,
            'icon'     => 0 === (int) $item->menu_item_parent ? utehub2026_get_nav_icon_name( $item->title ) : '',
            'active'   => in_array( 'current-menu-item', $item->classes, true ) || in_array( 'current-menu-ancestor', $item->classes, true ) || in_array( 'current-menu-parent', $item->classes, true ),
            'children' => array(),
            'order'    => (int) $item->menu_order,
        );
    }

    $tree = array();

    foreach ( $indexed as $id => $item ) {
        if ( $item['parent'] && isset( $indexed[ $item['parent'] ] ) ) {
            $indexed[ $item['parent'] ]['children'][] = &$indexed[ $id ];
        } else {
            $tree[] = &$indexed[ $id ];
        }
    }

    usort(
        $tree,
        static function ( $left, $right ) {
            return $left['order'] <=> $right['order'];
        }
    );

    return $tree;
}

function utehub2026_render_nav_item( $item, $depth = 0 ) {
    $has_children = ! empty( $item['children'] );
    $li_classes   = array( 'menu-item' );

    if ( $has_children ) {
        $li_classes[] = 'menu-item-has-children';
    }

    if ( ! empty( $item['active'] ) ) {
        $li_classes[] = 'current-menu-item';
    }

    $link_class = 0 === $depth ? 'nav-link' : 'submenu-link';
    if ( ! empty( $item['active'] ) ) {
        $link_class .= ' active';
    }

    echo '<li class="' . esc_attr( implode( ' ', $li_classes ) ) . '">';
    echo '<a class="' . esc_attr( $link_class ) . '" href="' . esc_url( $item['url'] ) . '">';

    if ( 0 === $depth && ! empty( $item['icon'] ) ) {
        echo utehub2026_get_svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    echo '<span>' . esc_html( $item['label'] ) . '</span>';

    if ( 0 === $depth && $has_children ) {
        echo '<span class="submenu-indicator">' . utehub2026_get_svg( 'chevron' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    echo '</a>';

    if ( 0 === $depth && $has_children ) {
        echo '<button class="submenu-toggle" type="button" aria-expanded="false" aria-label="' . esc_attr( sprintf( __( 'Toggle %s submenu', 'utehub2026' ), $item['label'] ) ) . '">';
        echo utehub2026_get_svg( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</button>';
    }

    if ( $has_children ) {
        echo '<ul class="sub-menu">';
        foreach ( $item['children'] as $child ) {
            utehub2026_render_nav_item( $child, $depth + 1 );
        }
        echo '</ul>';
    }

    echo '</li>';
}

function utehub2026_render_primary_nav() {
    $items = utehub2026_get_primary_nav_items();

    echo '<nav class="nav" aria-label="Primary">';
    echo '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '"><img src="' . esc_url( utehub2026_get_brand_url() ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"></a>';
    echo '<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">';
    echo '<span class="screen-reader-text">' . esc_html__( 'Toggle navigation', 'utehub2026' ) . '</span>';
    echo '<span class="nav-toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>';
    echo '</button>';
    echo '<button class="theme-toggle" type="button" aria-label="Toggle dark mode">';
    echo '<svg class="theme-icon-light" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>';
    echo '<svg class="theme-icon-dark" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
    echo '</button>';
    echo '<ul class="links" id="primary-menu">';

    foreach ( $items as $item ) {
        utehub2026_render_nav_item( $item );
    }

    echo '</ul>';
    echo '</nav>';
}

function utehub2026_get_initials( $name ) {
    $name  = trim( wp_strip_all_tags( (string) $name ) );
    $parts = preg_split( '/\s+/', $name );
    $bits  = array();

    foreach ( $parts as $part ) {
        if ( '' !== $part ) {
            $bits[] = strtoupper( mb_substr( $part, 0, 1 ) );
        }
        if ( count( $bits ) >= 2 ) {
            break;
        }
    }

    return $bits ? implode( '', $bits ) : 'U';
}

function utehub2026_get_color_class( $seed ) {
    $classes = array( 'c1', 'c2', 'c3', 'c4', 'c5', 'c6' );
    $index   = absint( crc32( (string) $seed ) ) % count( $classes );

    return $classes[ $index ];
}

function utehub2026_render_avatar( $user_id = 0, $size = 48, $args = array() ) {
    $defaults = array(
        'class'           => '',
        'prefer_app_icon' => false,
        'name'            => '',
    );
    $args     = wp_parse_args( $args, $defaults );

    $name = $args['name'];
    if ( ! $name && $user_id ) {
        $user = get_userdata( $user_id );
        $name = $user ? $user->display_name : '';
    }

    $classes = trim( 'av ' . utehub2026_get_color_class( $user_id ? $user_id : $name ) . ' ' . $args['class'] );
    $style   = 'style="width:' . (int) $size . 'px;height:' . (int) $size . 'px"';

    if ( $args['prefer_app_icon'] ) {
        return '<span class="' . esc_attr( $classes ) . '" ' . $style . '><img src="' . esc_url( get_template_directory_uri() . '/assets/utehub-app-icon.png' ) . '" alt=""></span>';
    }

    if ( $user_id ) {
        $avatar = get_avatar_url( $user_id, array( 'size' => $size * 2 ) );
        if ( $avatar ) {
            return '<span class="' . esc_attr( $classes ) . '" ' . $style . '><img src="' . esc_url( $avatar ) . '" alt="' . esc_attr( $name ) . '"></span>';
        }
    }

    return '<span class="' . esc_attr( $classes ) . '" ' . $style . '>' . esc_html( utehub2026_get_initials( $name ) ) . '</span>';
}

function utehub2026_get_like_data( $post_id ) {
    $post_id = (int) $post_id;

    if ( function_exists( 'tk_api_get_like_data' ) ) {
        $data      = tk_api_get_like_data( $post_id );
        $post_type = get_post_type( $post_id );

        if ( $data && ! empty( $data->post_type ) && $post_type && $data->post_type !== $post_type ) {
            return null;
        }

        return $data;
    }

    return null;
}

function utehub2026_render_vote_pills( $post_id ) {
    $post_id       = (int) $post_id;
    $data          = utehub2026_get_like_data( $post_id );
    $likes         = $data && isset( $data->like_count ) ? (int) $data->like_count : 0;
    $dislikes      = $data && isset( $data->dislike_count ) ? (int) $data->dislike_count : 0;
    $current_user  = get_current_user_id();
    $liked_users   = $data && ! empty( $data->like_user_ids ) ? array_map( 'intval', (array) $data->like_user_ids ) : array();
    $disliked_users= $data && ! empty( $data->dislike_user_ids ) ? array_map( 'intval', (array) $data->dislike_user_ids ) : array();

    if ( function_exists( 'tklike_user_logged_in_optimized' ) ) {
        $post_type        = get_post_type( $post_id );
        $like_counter     = $likes > 0 ? number_format_i18n( $likes ) : '';
        $dislike_counter  = $dislikes > 0 ? number_format_i18n( $dislikes ) : '';
        $user_liked       = in_array( $current_user, $liked_users, true );
        $user_disliked    = in_array( $current_user, $disliked_users, true );
        $like_class       = 'likeButton';
        $dislike_class    = 'dislikeButton';
        $is_logged_in     = 'yes' === tklike_user_logged_in_optimized();

        if ( $is_logged_in && $user_liked ) {
            $like_class    .= ' liked';
            $dislike_class .= ' dislike_disabled';
        } elseif ( $is_logged_in && $user_disliked ) {
            $like_class    .= ' like_disabled';
            $dislike_class .= ' liked';
        } elseif ( ! $is_logged_in ) {
            $like_class    .= ' disabled';
            $dislike_class .= ' disabled';
        }

        $button_tag = $is_logged_in ? 'a' : 'span';
        $href       = $is_logged_in ? ' href="javascript:void(0);"' : '';
        $data_attrs = $is_logged_in ? ' data-item-id="' . esc_attr( $post_id ) . '" data-user-id="' . esc_attr( $current_user ) . '" data-post-type="' . esc_attr( $post_type ) . '"' : '';

        return '<span class="vote tk-like-buttons" data-item-id="' . esc_attr( $post_id ) . '"><' . $button_tag . $href . ' id="likeButton_' . esc_attr( $post_id ) . '" class="' . esc_attr( $like_class ) . '"' . $data_attrs . '></' . $button_tag . '><span id="like_count_' . esc_attr( $post_id ) . '" class="like_count">' . esc_html( $like_counter ) . '</span><' . $button_tag . $href . ' id="dislikeButton_' . esc_attr( $post_id ) . '" class="' . esc_attr( $dislike_class ) . '"' . $data_attrs . '></' . $button_tag . '><span id="dislike_count_' . esc_attr( $post_id ) . '" class="dislike_count">' . esc_html( $dislike_counter ) . '</span></span>';
    }

    $up_class   = in_array( $current_user, $liked_users, true ) ? 'vb up active' : 'vb up';
    $down_class = in_array( $current_user, $disliked_users, true ) ? 'vb down active' : 'vb down';

    return '<span class="vote"><span class="' . esc_attr( $up_class ) . '">' . utehub2026_get_svg( 'thumb-up' ) . '<span>' . esc_html( $likes ) . '</span></span><span class="' . esc_attr( $down_class ) . '">' . utehub2026_get_svg( 'thumb-down' ) . ( $dislikes ? '<span>' . esc_html( $dislikes ) . '</span>' : '' ) . '</span></span>';
}

function utehub2026_delete_mismatched_like_row( $post_id, $post_type = '' ) {
    global $wpdb;

    $post_id   = (int) $post_id;
    $post_type = $post_type ? $post_type : get_post_type( $post_id );

    if ( ! $post_id || ! $post_type ) {
        return;
    }

    $table_name = $wpdb->prefix . 'tkLike_data';
    $row        = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, post_type FROM $table_name WHERE item_id = %d LIMIT 1",
            $post_id
        )
    );

    if ( $row && ! empty( $row->post_type ) && $row->post_type !== $post_type ) {
        $wpdb->delete( $table_name, array( 'id' => (int) $row->id ), array( '%d' ) );
        wp_cache_delete( 'tklike_data_' . $post_id );
    }
}

function utehub2026_register_tklike_ajax_guard() {
    if ( function_exists( 'tklike_handle_like_ajax' ) ) {
        remove_action( 'wp_ajax_tklike_handle_like', 'tklike_handle_like_ajax' );
        remove_action( 'wp_ajax_nopriv_tklike_handle_like', 'tklike_handle_like_ajax' );

        add_action( 'wp_ajax_tklike_handle_like', 'utehub2026_handle_tklike_ajax' );
        add_action( 'wp_ajax_nopriv_tklike_handle_like', 'utehub2026_handle_tklike_ajax' );
    }
}
add_action( 'init', 'utehub2026_register_tklike_ajax_guard' );

function utehub2026_handle_tklike_ajax() {
    check_ajax_referer( 'tklike-nonce', 'nonce' );

    $item_id     = isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : 0;
    $user_id     = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
    $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';
    $post_type   = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';
    $real_type   = $item_id ? get_post_type( $item_id ) : '';

    if ( ! $item_id || ! $user_id || ! $real_type || ! in_array( $action_type, array( 'like', 'unlike', 'dislike', 'undislike' ), true ) ) {
        wp_send_json_error( array( 'message' => 'Invalid like request.' ), 400 );
    }

    $post_type = $post_type && $post_type === $real_type ? $post_type : $real_type;
    utehub2026_delete_mismatched_like_row( $item_id, $post_type );

    if ( function_exists( 'tklike_update_like_data' ) ) {
        wp_send_json( tklike_update_like_data( $item_id, $user_id, $action_type, $post_type ) );
    }

    wp_send_json_error( array( 'message' => 'Like plugin unavailable.' ), 500 );
}

function utehub2026_is_topic_activity_post( $post_id, $topic_id ) {
    $post_id  = (int) $post_id;
    $topic_id = (int) $topic_id;

    if ( ! $post_id || ! $topic_id || ! get_post( $post_id ) ) {
        return false;
    }

    if ( $post_id === $topic_id ) {
        return true;
    }

    if ( function_exists( 'bbp_get_reply_post_type' ) && bbp_get_reply_post_type() === get_post_type( $post_id ) ) {
        $reply_topic_id = function_exists( 'bbp_get_reply_topic_id' ) ? (int) bbp_get_reply_topic_id( $post_id ) : 0;
        return $reply_topic_id === $topic_id || (int) wp_get_post_parent_id( $post_id ) === $topic_id;
    }

    return false;
}

function utehub2026_get_topic_last_activity_id( $topic_id ) {
    $topic_id = (int) $topic_id;

    if ( ! $topic_id ) {
        return 0;
    }

    $last_active_id = function_exists( 'bbp_get_topic_last_active_id' ) ? (int) bbp_get_topic_last_active_id( $topic_id ) : 0;
    if ( utehub2026_is_topic_activity_post( $last_active_id, $topic_id ) ) {
        return $last_active_id;
    }

    $last_reply_id = function_exists( 'bbp_get_topic_last_reply_id' ) ? (int) bbp_get_topic_last_reply_id( $topic_id ) : 0;
    if ( utehub2026_is_topic_activity_post( $last_reply_id, $topic_id ) ) {
        return $last_reply_id;
    }

    return $topic_id;
}

function utehub2026_get_relative_time( $post_id ) {
    $timestamp = get_post_time( 'U', true, $post_id );
    if ( ! $timestamp ) {
        return '';
    }

    return human_time_diff( $timestamp, current_time( 'timestamp' ) ) . ' ago';
}

function utehub2026_get_topic_heat( $reply_count ) {
    $reply_count = (int) $reply_count;
    if ( $reply_count >= 30 ) {
        return 5;
    }
    if ( $reply_count >= 20 ) {
        return 4;
    }
    if ( $reply_count >= 10 ) {
        return 3;
    }
    if ( $reply_count >= 6 ) {
        return 2;
    }
    if ( $reply_count >= 4 ) {
        return 1;
    }

    return 0;
}

function utehub2026_get_forum_tabs() {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    $tabs   = array();
    $tabs[] = array(
        'label'   => 'All',
        'url'     => function_exists( 'bbp_get_forum_archive_permalink' ) ? bbp_get_forum_archive_permalink() : home_url( '/forums/' ),
        'current' => bbp_is_forum_archive() || bbp_is_topic_archive(),
    );

    $forums = get_posts(
        array(
            'post_type'      => bbp_get_forum_post_type(),
            'post_parent'    => 0,
            'post_status'    => bbp_get_public_status_id(),
            'posts_per_page' => 3,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        )
    );

    foreach ( $forums as $forum ) {
        $tabs[] = array(
            'label'   => get_the_title( $forum ),
            'url'     => get_permalink( $forum ),
            'current' => function_exists( 'bbp_get_forum_id' ) && (int) bbp_get_forum_id() === (int) $forum->ID,
        );
    }

    return $tabs;
}

function utehub2026_get_forum_descendant_ids( $forum_id ) {
    $forum_id = (int) $forum_id;
    if ( ! $forum_id || ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    $forum_ids = get_posts(
        array(
            'post_type'      => bbp_get_forum_post_type(),
            'post_status'    => bbp_get_public_status_id(),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    $matches = array( $forum_id );

    foreach ( $forum_ids as $candidate_id ) {
        $candidate_id = (int) $candidate_id;
        if ( $candidate_id === $forum_id ) {
            continue;
        }

        $ancestors = get_post_ancestors( $candidate_id );
        if ( in_array( $forum_id, array_map( 'intval', $ancestors ), true ) ) {
            $matches[] = $candidate_id;
        }
    }

    return array_values( array_unique( array_map( 'intval', $matches ) ) );
}

function utehub2026_get_root_forum_posts() {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    return get_posts(
        array(
            'post_type'      => bbp_get_forum_post_type(),
            'post_parent'    => 0,
            'post_status'    => bbp_get_public_status_id(),
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        )
    );
}

function utehub2026_get_all_forum_posts() {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    return get_posts(
        array(
            'post_type'      => bbp_get_forum_post_type(),
            'post_status'    => bbp_get_public_status_id(),
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        )
    );
}

function utehub2026_get_forum_choice_label( $forum_id ) {
    $forum_id = (int) $forum_id;
    if ( ! $forum_id ) {
        return '';
    }

    $title     = get_the_title( $forum_id );
    $ancestors = array_reverse( array_map( 'intval', get_post_ancestors( $forum_id ) ) );

    if ( empty( $ancestors ) ) {
        return $title;
    }

    $parts = array();

    foreach ( $ancestors as $ancestor_id ) {
        $ancestor_title = get_the_title( $ancestor_id );
        if ( $ancestor_title ) {
            $parts[] = $ancestor_title;
        }
    }

    $parts[] = $title;

    return implode( ' > ', $parts );
}

function utehub2026_get_forum_choices() {
    $choices = array(
        0 => __( 'Select a forum', 'utehub2026' ),
    );

    foreach ( utehub2026_get_all_forum_posts() as $forum ) {
        $choices[ (int) $forum->ID ] = utehub2026_get_forum_choice_label( (int) $forum->ID );
    }

    return $choices;
}

function utehub2026_get_home_feed_forum_ids() {
    $selected_ids = array();

    for ( $index = 1; $index <= 3; $index++ ) {
        $forum_id = (int) get_theme_mod( 'utehub2026_home_feed_forum_' . $index, 0 );
        if ( $forum_id > 0 ) {
            $selected_ids[] = $forum_id;
        }
    }

    $selected_ids = array_values( array_unique( array_filter( array_map( 'intval', $selected_ids ) ) ) );

    if ( count( $selected_ids ) >= 3 ) {
        return array_slice( $selected_ids, 0, 3 );
    }

    foreach ( utehub2026_get_root_forum_posts() as $forum ) {
        $forum_id = (int) $forum->ID;
        if ( in_array( $forum_id, $selected_ids, true ) ) {
            continue;
        }

        $selected_ids[] = $forum_id;

        if ( count( $selected_ids ) >= 3 ) {
            break;
        }
    }

    return array_slice( $selected_ids, 0, 3 );
}

function utehub2026_get_recent_topics_tabs( $base_url = '' ) {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    $base_url = $base_url ? $base_url : home_url( '/recent-posts/' );
    $current  = utehub2026_get_current_recent_topics_tab();
    $tabs     = array(
        array(
            'key'     => 'all',
            'label'   => 'All',
            'url'     => remove_query_arg( 'forum_tab', $base_url ),
            'current' => 'all' === $current,
        ),
    );

    foreach ( utehub2026_get_home_feed_forum_ids() as $forum_id ) {
        $forum = get_post( $forum_id );

        if ( ! $forum instanceof WP_Post ) {
            continue;
        }

        $key    = $forum->post_name;
        $tabs[] = array(
            'key'     => $key,
            'label'   => get_the_title( $forum ),
            'forum'   => (int) $forum->ID,
            'url'     => add_query_arg( 'forum_tab', $key, $base_url ),
            'current' => $current === $key,
        );
    }

    $tabs[] = array(
        'key'     => 'hot',
        'label'   => 'Hot',
        'url'     => add_query_arg( 'forum_tab', 'hot', $base_url ),
        'current' => 'hot' === $current,
    );

    return $tabs;
}

function utehub2026_get_current_recent_topics_tab() {
    if ( isset( $_GET['forum_tab'] ) ) {
        $tab = sanitize_key( wp_unslash( $_GET['forum_tab'] ) );

        return '' !== $tab ? $tab : 'all';
    }

    if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
        $query_string = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_QUERY );

        if ( $query_string ) {
            parse_str( $query_string, $query_args );

            if ( ! empty( $query_args['forum_tab'] ) ) {
                $tab = sanitize_key( (string) $query_args['forum_tab'] );

                return '' !== $tab ? $tab : 'all';
            }
        }
    }

    return 'all';
}

function utehub2026_get_recent_topics_query_args( $paged = 1 ) {
    $paged      = max( 1, (int) $paged );
    $search     = isset( $_GET['bbp_search'] ) ? sanitize_text_field( wp_unslash( $_GET['bbp_search'] ) ) : '';
    $tab_key    = utehub2026_get_current_recent_topics_tab();
    $topic_type = function_exists( 'bbp_get_topic_post_type' ) ? bbp_get_topic_post_type() : 'topic';
    $status     = function_exists( 'bbp_get_public_status_id' ) ? bbp_get_public_status_id() : 'publish';

    $args = array(
        'post_type'           => $topic_type,
        'post_status'         => $status,
        'posts_per_page'      => 8,
        'paged'               => $paged,
        'ignore_sticky_posts' => false,
        'orderby'             => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        ),
    );

    if ( '' !== $search ) {
        $args['s'] = $search;
    }

    if ( 'hot' === $tab_key ) {
        $args['meta_key'] = '_bbp_reply_count';
        $args['orderby']  = array(
            'meta_value_num' => 'DESC',
            'date'           => 'DESC',
        );
        $args['meta_query'] = array(
            array(
                'key'     => '_bbp_reply_count',
                'value'   => 1,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ),
        );

        return $args;
    }

    foreach ( utehub2026_get_recent_topics_tabs() as $tab ) {
        if ( empty( $tab['forum'] ) || $tab_key !== $tab['key'] ) {
            continue;
        }

        $forum_ids = utehub2026_get_forum_descendant_ids( (int) $tab['forum'] );
        if ( ! empty( $forum_ids ) ) {
            $args['post_parent__in'] = $forum_ids;
        }
        break;
    }

    return $args;
}

function utehub2026_render_recently_active( $limit = 12 ) {
    $post_types = array();
    if ( function_exists( 'bbp_get_topic_post_type' ) ) {
        $post_types[] = bbp_get_topic_post_type();
    }
    if ( function_exists( 'bbp_get_reply_post_type' ) ) {
        $post_types[] = bbp_get_reply_post_type();
    }
    if ( empty( $post_types ) ) {
        $post_types = array( 'post' );
    }

    $recent_posts = get_posts(
        array(
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'posts_per_page' => 30,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        )
    );

    $seen    = array();
    $authors = array();

    foreach ( $recent_posts as $post_id ) {
        $author_id = (int) get_post_field( 'post_author', $post_id );
        if ( ! $author_id || isset( $seen[ $author_id ] ) ) {
            continue;
        }

        $seen[ $author_id ] = true;
        $authors[]          = $author_id;

        if ( count( $authors ) >= $limit ) {
            break;
        }
    }

    if ( empty( $authors ) ) {
        echo '<div class="recent-members-empty">No recent activity yet.</div>';
        return;
    }

    echo '<div class="recent-members">';
    foreach ( $authors as $index => $author_id ) {
        $prefer_icon = 0 === $index;
        echo utehub2026_render_avatar( $author_id, 40, array( 'prefer_app_icon' => $prefer_icon ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    echo '</div>';
}

function utehub2026_get_forums_archive_top_level_forums() {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    return get_posts(
        array(
            'post_type'              => bbp_get_forum_post_type(),
            'post_parent'            => 0,
            'post_status'            => bbp_get_public_status_id(),
            'posts_per_page'         => -1,
            'orderby'                => 'menu_order title',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        )
    );
}

function utehub2026_get_forum_archive_section_key( WP_Post $forum, $index = 0 ) {
    $slug = sanitize_title( $forum->post_name );

    if ( in_array( $slug, array( 'sports', 'misc', 'professional-sports' ), true ) || $index < 3 ) {
        return 'sports';
    }

    return 'community';
}

function utehub2026_get_forum_archive_sections() {
    $sections = array(
        'sports'    => array(
            'label' => 'Sports',
            'items' => array(),
        ),
        'community' => array(
            'label' => 'Community',
            'items' => array(),
        ),
    );

    foreach ( utehub2026_get_forums_archive_top_level_forums() as $index => $forum ) {
        $sections[ utehub2026_get_forum_archive_section_key( $forum, $index ) ]['items'][] = $forum;
    }

    return array_values(
        array_filter(
            $sections,
            static function ( $section ) {
                return ! empty( $section['items'] );
            }
        )
    );
}

function utehub2026_get_forum_archive_icon( WP_Post $forum ) {
    $map = array(
        'sports'              => 'target',
        'misc'                => 'home',
        'professional-sports' => 'trophy',
        'hubinfo'             => 'megaphone',
        'politics'            => 'gavel',
        'byutds'              => 'chat',
    );

    return $map[ sanitize_title( $forum->post_name ) ] ?? 'forum';
}

function utehub2026_get_forum_archive_subforums( $forum_id ) {
    if ( ! function_exists( 'bbp_get_forum_post_type' ) ) {
        return array();
    }

    $subforums = get_posts(
        array(
            'post_type'              => bbp_get_forum_post_type(),
            'post_parent'            => (int) $forum_id,
            'post_status'            => bbp_get_public_status_id(),
            'posts_per_page'         => -1,
            'orderby'                => 'menu_order title',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        )
    );

    return array_map(
        static function ( $subforum ) {
            $subforum_id = (int) $subforum->ID;

            return array(
                'id'     => $subforum_id,
                'title'  => get_the_title( $subforum_id ),
                'url'    => get_permalink( $subforum_id ),
                'topics' => function_exists( 'bbp_get_forum_topic_count' ) ? (int) bbp_get_forum_topic_count( $subforum_id, true ) : 0,
                'posts'  => function_exists( 'bbp_get_forum_post_count' ) ? (int) bbp_get_forum_post_count( $subforum_id, true ) : 0,
            );
        },
        $subforums
    );
}

function utehub2026_get_forum_archive_card_data( WP_Post $forum ) {
    $forum_id       = (int) $forum->ID;
    $last_active_id = function_exists( 'bbp_get_forum_last_active_id' ) ? (int) bbp_get_forum_last_active_id( $forum_id ) : 0;
    $last_author_id = $last_active_id ? (int) get_post_field( 'post_author', $last_active_id ) : 0;
    $description    = function_exists( 'bbp_get_forum_content' ) ? wp_strip_all_tags( bbp_get_forum_content( $forum_id ) ) : '';

    return array(
        'id'             => $forum_id,
        'title'          => get_the_title( $forum_id ),
        'url'            => get_permalink( $forum_id ),
        'description'    => $description,
        'topics'         => function_exists( 'bbp_get_forum_topic_count' ) ? (int) bbp_get_forum_topic_count( $forum_id, true ) : 0,
        'posts'          => function_exists( 'bbp_get_forum_post_count' ) ? (int) bbp_get_forum_post_count( $forum_id, true ) : 0,
        'subforums'      => utehub2026_get_forum_archive_subforums( $forum_id ),
        'last_active_id' => $last_active_id,
        'last_author_id' => $last_author_id,
        'last_author'    => $last_author_id ? get_the_author_meta( 'display_name', $last_author_id ) : '',
        'last_when'      => $last_active_id ? utehub2026_get_relative_time( $last_active_id ) : '',
        'last_url'       => $last_active_id ? get_permalink( $last_active_id ) : '',
        'icon'           => utehub2026_get_forum_archive_icon( $forum ),
    );
}

function utehub2026_get_forum_archive_stats() {
    $topic_count = function_exists( 'bbp_get_topic_post_type' ) ? (int) wp_count_posts( bbp_get_topic_post_type() )->publish : 0;
    $reply_count = function_exists( 'bbp_get_reply_post_type' ) ? (int) wp_count_posts( bbp_get_reply_post_type() )->publish : 0;
    $users       = count_users();

    return array(
        'topics'  => $topic_count,
        'posts'   => $topic_count + $reply_count,
        'members' => ! empty( $users['total_users'] ) ? (int) $users['total_users'] : 0,
    );
}

function utehub2026_render_right_rail( $context = 'archive', $topic_id = 0 ) {
    $sidebar_id  = utehub2026_get_right_rail_sidebar_id( $context );
    $has_widgets = $sidebar_id && is_active_sidebar( $sidebar_id );

    if ( ! $has_widgets ) {
        return;
    }

    echo '<aside class="rail">';

    if ( is_active_sidebar( $sidebar_id ) ) {
        dynamic_sidebar( $sidebar_id );
    }

    echo '</aside>';
}

function utehub2026_render_topics_feed( $base_url = '' ) {
    $base_url        = $base_url ? $base_url : home_url( '/' );
    $paged           = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
    $tabs            = utehub2026_get_recent_topics_tabs( $base_url );
    $search          = isset( $_GET['bbp_search'] ) ? sanitize_text_field( wp_unslash( $_GET['bbp_search'] ) ) : '';
    $current_forum   = utehub2026_get_current_recent_topics_tab();
    $topic_query     = new WP_Query( utehub2026_get_recent_topics_query_args( $paged ) );
    $count_text      = '';
    $pagination_links = '';

    if ( $topic_query->have_posts() ) {
        $from = ( ( $paged - 1 ) * (int) $topic_query->query_vars['posts_per_page'] ) + 1;
        $to   = min( $topic_query->found_posts, $from + (int) $topic_query->post_count - 1 );
        $count_text = sprintf(
            'Viewing %1$s topics - %2$s through %3$s (of %4$s total)',
            number_format_i18n( (int) $topic_query->post_count ),
            number_format_i18n( $from ),
            number_format_i18n( $to ),
            number_format_i18n( (int) $topic_query->found_posts )
        );
    }

    $pagination_links = paginate_links(
        array(
            'base'      => trailingslashit( $base_url ) . '%_%',
            'format'    => 'page/%#%/',
            'current'   => $paged,
            'total'     => max( 1, (int) $topic_query->max_num_pages ),
            'mid_size'  => 1,
            'end_size'  => 1,
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'type'      => 'plain',
            'add_args'  => array_filter(
                array(
                    'bbp_search' => $search,
                    'forum_tab'  => 'all' !== $current_forum ? $current_forum : '',
                )
            ),
        )
    );

    ?>
    <div class="uh-wrap">
        <section>
            <div class="feedhead">
                <div class="ttl">
                    <span class="eye"><?php echo esc_html( utehub2026_get_home_welcome_message() ); ?></span>
                    <h1><?php echo esc_html( utehub2026_get_home_topics_heading() ); ?></h1>
                </div>
                <form role="search" method="get" class="search" action="<?php echo esc_url( $base_url ); ?>">
                    <?php echo utehub2026_get_svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <label class="screen-reader-text" for="bbp_search"><?php esc_html_e( 'Search for:', 'utehub2026' ); ?></label>
                    <?php if ( '' !== $current_forum ) : ?>
                        <input type="hidden" name="forum_tab" value="<?php echo esc_attr( $current_forum ); ?>">
                    <?php endif; ?>
                    <input type="text" value="<?php echo esc_attr( $search ); ?>" name="bbp_search" id="bbp_search" placeholder="Search topics...">
                    <button type="submit"><?php esc_html_e( 'Search', 'utehub2026' ); ?></button>
                </form>
            </div>

            <?php if ( $tabs ) : ?>
                <div class="tabs">
                    <?php foreach ( $tabs as $tab ) : ?>
                        <a class="tab <?php echo ! empty( $tab['current'] ) ? 'current' : ''; ?> <?php echo 'hot' === $tab['key'] ? 'hot' : ''; ?>" href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo 'hot' === $tab['key'] ? '🔥🔥 ' : ''; ?><?php echo esc_html( $tab['label'] ); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="uh-feed">
                <?php if ( $topic_query->have_posts() ) : ?>
                    <div class="topics">
                        <?php while ( $topic_query->have_posts() ) : $topic_query->the_post(); ?>
                            <?php
                            $topic_id       = get_the_ID();
                            $forum_id       = function_exists( 'bbp_get_topic_forum_id' ) ? (int) bbp_get_topic_forum_id( $topic_id ) : (int) wp_get_post_parent_id( $topic_id );
                            $forum_title    = $forum_id ? get_the_title( $forum_id ) : '';
                            $reply_count    = function_exists( 'bbp_get_topic_post_count' ) ? (int) bbp_get_topic_post_count( $topic_id ) : 0;
                            $voice_count    = function_exists( 'bbp_get_topic_voice_count' ) ? (int) bbp_get_topic_voice_count( $topic_id ) : 0;
                            $last_active_id = utehub2026_get_topic_last_activity_id( $topic_id );
                            $last_author_id = (int) get_post_field( 'post_author', $last_active_id );
                            $started_by_id  = (int) get_post_field( 'post_author', $topic_id );
                            $forum_url      = $forum_id ? ( function_exists( 'bbp_get_forum_permalink' ) ? bbp_get_forum_permalink( $forum_id ) : get_permalink( $forum_id ) ) : '';
                            $started_by_url = function_exists( 'bbp_get_topic_author_url' ) ? bbp_get_topic_author_url( $topic_id ) : get_author_posts_url( $started_by_id );
                            $last_user_id   = $last_author_id ? $last_author_id : $started_by_id;
                            $last_user_name = get_the_author_meta( 'display_name', $last_user_id );
                            $last_user_url  = $last_active_id && $last_active_id !== $topic_id && function_exists( 'bbp_get_reply_author_url' )
                                ? bbp_get_reply_author_url( $last_active_id )
                                : $started_by_url;
                            $heat           = utehub2026_get_topic_heat( max( 0, $reply_count - 1 ) );
                            $classes        = 'uh-topic';

                            if ( function_exists( 'bbp_is_topic_sticky' ) && ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) ) {
                                $classes .= ' pinned';
                            }
                            ?>
                            <article <?php post_class( $classes, $topic_id ); ?>>
                                <div class="t-main">
                                    <a class="t-title" href="<?php echo esc_url( get_permalink( $topic_id ) ); ?>"><?php echo esc_html( get_the_title( $topic_id ) ); ?></a>
                                    <div class="t-by">
                                        <a href="<?php echo esc_url( $started_by_url ); ?>">
                                            <?php echo utehub2026_render_avatar( $started_by_id, 20, array( 'class' => 't-by-av', 'name' => get_the_author_meta( 'display_name', $started_by_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </a>
                                        <span class="t-by-copy">
                                            Started by <a href="<?php echo esc_url( $started_by_url ); ?>"><?php echo esc_html( function_exists( 'bbp_get_topic_author_display_name' ) ? bbp_get_topic_author_display_name( $topic_id ) : get_the_author_meta( 'display_name', $started_by_id ) ); ?></a>
                                        </span>
                                        <div class="t-tags">
                                            <?php if ( function_exists( 'bbp_is_topic_sticky' ) && ( bbp_is_topic_sticky( $topic_id ) || bbp_is_topic_super_sticky( $topic_id ) ) ) : ?>
                                                <span class="chip pin"><?php echo utehub2026_get_svg( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Pinned</span>
                                            <?php endif; ?>
                                            <?php if ( $forum_title && $forum_url ) : ?>
                                                <a class="chip cat" href="<?php echo esc_url( $forum_url ); ?>"><?php echo esc_html( $forum_title ); ?></a>
                                            <?php endif; ?>
                                            <?php if ( $heat ) : ?>
                                                <span class="heat"><?php echo esc_html( str_repeat( '🔥', $heat ) ); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="t-stats">
                                    <div class="stat"><b><?php echo esc_html( $voice_count ); ?></b><span>Voices</span></div>
                                    <div class="stat"><b><?php echo esc_html( $reply_count ); ?></b><span>Posts</span></div>
                                </div>

                                <div class="t-last">
                                    <a href="<?php echo esc_url( $last_user_url ); ?>">
                                        <?php echo utehub2026_render_avatar( $last_user_id, 38, array( 'name' => $last_user_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </a>
                                    <div class="lp">
                                        <a class="who" href="<?php echo esc_url( $last_user_url ); ?>"><?php echo esc_html( $last_user_name ); ?></a>
                                        <span class="when"><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( utehub2026_get_relative_time( $last_active_id ) ); ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="bbp-template-notice"><ul><li>No topics found.</li></ul></div>
                <?php endif; ?>

                <?php if ( $count_text || $pagination_links ) : ?>
                    <div class="pager uh-pager">
                        <?php if ( $count_text ) : ?>
                            <div class="count uh-count"><?php echo esc_html( $count_text ); ?></div>
                        <?php endif; ?>
                        <?php if ( $pagination_links ) : ?>
                            <div class="pages uh-pages"><?php echo wp_kses_post( $pagination_links ); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php utehub2026_render_right_rail( 'archive' ); ?>
    </div>
    <?php
}

function utehub2026_get_reply_depth( $reply_id ) {
    $depth     = 0;
    $parent_id = function_exists( 'bbp_get_reply_to' ) ? (int) bbp_get_reply_to( $reply_id ) : 0;

    while ( $parent_id && $depth < 4 ) {
        ++$depth;
        $parent_id = function_exists( 'bbp_get_reply_to' ) ? (int) bbp_get_reply_to( $parent_id ) : 0;
    }

    return $depth;
}

function utehub2026_get_reply_parent_author_name( $reply_id ) {
    if ( ! function_exists( 'bbp_get_reply_to' ) ) {
        return '';
    }

    $parent_id = (int) bbp_get_reply_to( $reply_id );
    if ( ! $parent_id ) {
        return '';
    }

    return get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $parent_id ) );
}

function utehub2026_is_reply_branch_end( $reply_id ) {
    if ( ! function_exists( 'bbp_get_reply_to' ) || ! function_exists( 'bbp_get_reply_topic_id' ) ) {
        return false;
    }

    $reply_id       = (int) $reply_id;
    $current_parent = (int) bbp_get_reply_to( $reply_id );
    $topic_id       = (int) bbp_get_reply_topic_id( $reply_id );
    $menu_order     = (int) get_post_field( 'menu_order', $reply_id );

    if ( ! $topic_id ) {
        return false;
    }

    $next_replies = get_posts(
        array(
            'post_type'              => bbp_get_reply_post_type(),
            'post_parent'            => $topic_id,
            'posts_per_page'         => -1,
            'orderby'                => 'menu_order',
            'order'                  => 'ASC',
            'fields'                 => 'ids',
            'post_status'            => 'any',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        )
    );

    if ( empty( $next_replies ) ) {
        return true;
    }

    foreach ( $next_replies as $maybe_next_reply_id ) {
        $maybe_next_reply_id = (int) $maybe_next_reply_id;

        if ( (int) get_post_field( 'menu_order', $maybe_next_reply_id ) <= $menu_order ) {
            continue;
        }

        $next_parent = (int) bbp_get_reply_to( $maybe_next_reply_id );

        if ( $next_parent === $reply_id || $next_parent === $current_parent ) {
            return false;
        }

        return true;
    }

    return true;
}

function utehub2026_get_topic_admin_links_html() {
    if ( ! function_exists( 'bbp_get_topic_admin_links' ) ) {
        return '';
    }

    return bbp_get_topic_admin_links(
        array(
            'sep'    => '',
            'before' => '',
            'after'  => '',
        )
    );
}

function utehub2026_get_reply_admin_links_html() {
    if ( ! function_exists( 'bbp_get_reply_admin_links' ) ) {
        return '';
    }

    return bbp_get_reply_admin_links(
        array(
            'sep'    => '',
            'before' => '',
            'after'  => '',
        )
    );
}
