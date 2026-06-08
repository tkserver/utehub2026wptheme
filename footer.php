<?php
/**
 * Theme footer.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;
?>
    </main>
    <footer id="colophon" class="site-footer">
        <div class="footer-wrap">
            <?php if ( is_active_sidebar( 'footer-left' ) ) : ?>
                <div><?php dynamic_sidebar( 'footer-left' ); ?></div>
            <?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-center' ) ) : ?>
                <div><?php dynamic_sidebar( 'footer-center' ); ?></div>
            <?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-right' ) ) : ?>
                <div><?php dynamic_sidebar( 'footer-right' ); ?></div>
            <?php endif; ?>
        </div>
    </footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
