<?php
/**
 * Reply pagination.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="pager bbp-pagination">
    <div class="count bbp-pagination-count"><?php bbp_topic_pagination_count(); ?></div>
    <div class="pages bbp-pagination-links"><?php bbp_topic_pagination_links(); ?></div>
</div>
