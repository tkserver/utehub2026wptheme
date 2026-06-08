<?php
/**
 * Topic pagination.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$bbp = bbpress();

if ( empty( $bbp->topic_query ) ) {
    return;
}

$topic_query = $bbp->topic_query;
$current     = max( 1, (int) bbp_get_paged() );
$total_pages = max( 1, (int) $topic_query->max_num_pages );

$pagination_links = paginate_links(
    array(
        'base'      => get_pagenum_link( 1 ) . '%_%',
        'format'    => user_trailingslashit( bbp_get_paged_slug() . '/%#%/' ),
        'current'   => $current,
        'total'     => $total_pages,
        'mid_size'  => 1,
        'end_size'  => 1,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
        'type'      => 'plain',
    )
);
?>
<div class="pager bbp-pagination">
    <div class="count bbp-pagination-count"><?php bbp_forum_pagination_count(); ?></div>
    <?php if ( $pagination_links ) : ?>
        <div class="pages bbp-pagination-links"><?php echo wp_kses_post( $pagination_links ); ?></div>
    <?php endif; ?>
</div>
