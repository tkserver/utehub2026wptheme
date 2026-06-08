<?php
/**
 * Forum archive layout.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$sections = utehub2026_get_forum_archive_sections();
?>
<div class="uh-wrap forums-archive-page">
    <section>
        <div class="crumb forum-crumb">
            <?php bbp_breadcrumb(); ?>
        </div>

        <div class="forums-head">
            <div class="ttl">
                <span class="eye">The Boards</span>
                <h1><?php post_type_archive_title(); ?></h1>
            </div>
            <div class="forums-head-tools">
                <div class="forums-subscribe"><?php bbp_forum_subscription_link(); ?></div>
                <form role="search" method="get" class="search" id="bbp-search-form">
                    <?php echo utehub2026_get_svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <label class="screen-reader-text" for="bbp_search"><?php esc_html_e( 'Search for:', 'bbpress' ); ?></label>
                    <input type="hidden" name="action" value="bbp-search-request">
                    <input type="text" value="" name="bbp_search" id="bbp_search" placeholder="Search topics...">
                    <button type="submit"><?php esc_html_e( 'Search', 'bbpress' ); ?></button>
                </form>
            </div>
        </div>

        <?php do_action( 'bbp_template_before_forums_index' ); ?>

        <?php if ( $sections ) : ?>
            <?php foreach ( $sections as $section ) : ?>
                <div class="forum-section">
                    <h2 class="forum-section-title"><?php echo esc_html( $section['label'] ); ?></h2>

                    <div class="forum-cards">
                        <?php foreach ( $section['items'] as $forum ) : ?>
                            <?php $card = utehub2026_get_forum_archive_card_data( $forum ); ?>
                            <article class="forum-card">
                                <div class="forum-card-icon">
                                    <?php echo utehub2026_get_svg( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>

                                <div class="forum-card-main">
                                    <h3><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>

                                    <?php if ( $card['description'] ) : ?>
                                        <p><?php echo esc_html( $card['description'] ); ?></p>
                                    <?php endif; ?>

                                    <?php if ( $card['subforums'] ) : ?>
                                        <div class="forum-card-subforums">
                                            <?php foreach ( $card['subforums'] as $subforum ) : ?>
                                                <a class="forum-pill" href="<?php echo esc_url( $subforum['url'] ); ?>">
                                                    <span><?php echo esc_html( $subforum['title'] ); ?></span>
                                                    <small><?php echo esc_html( number_format_i18n( $subforum['topics'] ) ); ?> · <?php echo esc_html( number_format_i18n( $subforum['posts'] ) ); ?></small>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="forum-card-stats">
                                    <div class="forum-stat">
                                        <strong><?php echo esc_html( number_format_i18n( $card['topics'] ) ); ?></strong>
                                        <span>Topics</span>
                                    </div>
                                    <div class="forum-stat">
                                        <strong><?php echo esc_html( number_format_i18n( $card['posts'] ) ); ?></strong>
                                        <span>Posts</span>
                                    </div>
                                </div>

                                <div class="forum-card-last">
                                    <?php if ( $card['last_author_id'] ) : ?>
                                        <?php echo utehub2026_render_avatar( $card['last_author_id'], 40, array( 'name' => $card['last_author'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <div class="forum-card-last-meta">
                                            <a href="<?php echo esc_url( $card['last_url'] ); ?>"><?php echo esc_html( $card['last_author'] ); ?></a>
                                            <span><?php echo utehub2026_get_svg( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $card['last_when'] ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <?php bbp_get_template_part( 'feedback', 'no-forums' ); ?>
        <?php endif; ?>

        <?php do_action( 'bbp_template_after_forums_index' ); ?>
    </section>

    <?php utehub2026_render_right_rail( 'archive' ); ?>
</div>
