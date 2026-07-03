<?php
/**
 * Search pagination.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bbp_template_before_pagination_loop' );
?>

<div class="pager bbp-pagination forum-search-pagination">
    <div class="count bbp-pagination-count"><?php bbp_search_pagination_count(); ?></div>
    <div class="pages bbp-pagination-links"><?php bbp_search_pagination_links(); ?></div>
</div>

<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
