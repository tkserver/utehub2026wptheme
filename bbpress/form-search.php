<?php
/**
 * Search form.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

if ( bbp_allow_search() ) : ?>
    <form role="search" method="get" class="search" id="bbp-search-form">
        <?php echo utehub2026_get_svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <label class="screen-reader-text" for="bbp_search"><?php esc_html_e( 'Search for:', 'bbpress' ); ?></label>
        <input type="hidden" name="action" value="bbp-search-request">
        <input type="text" value="<?php bbp_search_terms(); ?>" name="bbp_search" id="bbp_search" placeholder="Search topics...">
        <button type="submit"><?php esc_html_e( 'Search', 'bbpress' ); ?></button>
    </form>
<?php endif; ?>
