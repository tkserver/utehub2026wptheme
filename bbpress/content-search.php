<?php
/**
 * Search results layout.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$search_terms = trim( bbp_get_search_terms() );
$has_results  = $search_terms ? bbp_has_search_results( array( 's' => $search_terms ) ) : bbp_has_search_results();
$template_dir = trailingslashit( get_stylesheet_directory() ) . 'bbpress/';
?>

<div id="bbpress-forums" class="bbpress-wrapper uh-wrap forum-search-page">
    <section class="forum-search-main">
        <div class="crumb forum-crumb">
            <?php bbp_breadcrumb(); ?>
        </div>

        <div class="forums-head forum-search-head">
            <div class="ttl">
                <span class="eye">Forum Search</span>
                <h1>
                    <?php if ( $search_terms ) : ?>
                        <?php printf( esc_html__( 'Search Results for "%s"', 'utehub2026' ), esc_html( $search_terms ) ); ?>
                    <?php else : ?>
                        <?php esc_html_e( 'Search Results', 'utehub2026' ); ?>
                    <?php endif; ?>
                </h1>
            </div>

            <div class="forums-head-tools">
                <?php include $template_dir . 'form-search.php'; ?>
            </div>
        </div>

        <?php bbp_set_query_name( bbp_get_search_rewrite_id() ); ?>

        <?php do_action( 'bbp_template_before_search' ); ?>

        <?php if ( $has_results ) : ?>
            <?php include $template_dir . 'pagination-search.php'; ?>
            <?php include $template_dir . 'loop-search.php'; ?>
            <?php include $template_dir . 'pagination-search.php'; ?>
        <?php else : ?>
            <div class="forum-search-empty">
                <?php bbp_get_template_part( 'feedback', 'no-search' ); ?>
            </div>
        <?php endif; ?>

        <?php do_action( 'bbp_template_after_search_results' ); ?>
    </section>

    <?php utehub2026_render_right_rail( 'archive' ); ?>
</div>
