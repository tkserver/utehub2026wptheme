<?php
/**
 * Single forum layout.
 *
 * @package UteHub2026
 */

defined( 'ABSPATH' ) || exit;

$forum_id     = bbp_get_forum_id();
$forum_post   = get_post( $forum_id );
$forum_card   = $forum_post instanceof WP_Post ? utehub2026_get_forum_archive_card_data( $forum_post ) : array();
$description  = ! empty( $forum_card['description'] ) ? $forum_card['description'] : '';
$subforums    = ! empty( $forum_card['subforums'] ) ? $forum_card['subforums'] : array();
$has_topics   = ! bbp_is_forum_category() && bbp_has_topics();
?>

<div class="uh-wrap forum-page">
    <section>
        <div class="crumb forum-crumb">
            <?php bbp_breadcrumb(); ?>
        </div>

        <div class="forums-head forum-page-head">
            <div class="ttl">
                <span class="eye">Forum</span>
                <h1><?php bbp_forum_title(); ?></h1>
            </div>

            <div class="forums-head-tools">
                <?php if ( ! bbp_is_forum_category() ) : ?>
                    <div class="forums-subscribe"><?php bbp_forum_subscription_link(); ?></div>
                <?php endif; ?>
                <?php bbp_get_template_part( 'form', 'search' ); ?>
            </div>
        </div>

        <?php do_action( 'bbp_template_before_single_forum' ); ?>

        <?php if ( post_password_required() ) : ?>
            <?php bbp_get_template_part( 'form', 'protected' ); ?>
        <?php else : ?>
            <?php if ( $description || ! empty( $forum_card ) ) : ?>
                <div class="forum-page-summary">
                    <?php if ( $description ) : ?>
                        <p class="forum-page-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>

                    <?php if ( ! empty( $forum_card ) ) : ?>
                        <div class="forum-page-stats">
                            <div class="forum-stat">
                                <strong><?php echo esc_html( number_format_i18n( (int) $forum_card['topics'] ) ); ?></strong>
                                <span>Topics</span>
                            </div>
                            <div class="forum-stat">
                                <strong><?php echo esc_html( number_format_i18n( (int) $forum_card['posts'] ) ); ?></strong>
                                <span>Posts</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( $subforums ) : ?>
                <div class="forum-page-subforums">
                    <?php foreach ( $subforums as $subforum ) : ?>
                        <a class="forum-pill" href="<?php echo esc_url( $subforum['url'] ); ?>">
                            <span><?php echo esc_html( $subforum['title'] ); ?></span>
                            <small><?php echo esc_html( number_format_i18n( (int) $subforum['topics'] ) ); ?> · <?php echo esc_html( number_format_i18n( (int) $subforum['posts'] ) ); ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( $has_topics ) : ?>
                <div class="viewing"><?php bbp_forum_pagination_count(); ?></div>

                <div class="uh-feed forum-topic-list">
                    <?php bbp_get_template_part( 'loop', 'topics' ); ?>
                    <?php bbp_get_template_part( 'pagination', 'topics' ); ?>
                </div>

                <?php bbp_get_template_part( 'form', 'topic' ); ?>
            <?php elseif ( ! bbp_is_forum_category() ) : ?>
                <?php bbp_get_template_part( 'feedback', 'no-topics' ); ?>
                <?php bbp_get_template_part( 'form', 'topic' ); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action( 'bbp_template_after_single_forum' ); ?>
    </section>

    <?php utehub2026_render_right_rail( 'archive' ); ?>
</div>
