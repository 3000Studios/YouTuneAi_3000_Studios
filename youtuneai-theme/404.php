<?php
/**
 * 404 Error Page Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main error-404">
    <div class="container">
        <div class="error-content text-center">
            <h1 class="error-title">404</h1>
            <h2 class="error-subtitle"><?php _e( 'Page Not Found', 'youtuneai' ); ?></h2>
            <p class="error-description">
                <?php _e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'youtuneai' ); ?>
            </p>
            
            <div class="error-search">
                <?php get_search_form(); ?>
            </div>
            
            <div class="error-actions">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                    <?php _e( 'Go to Homepage', 'youtuneai' ); ?>
                </a>
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-secondary">
                        <?php _e( 'Browse Shop', 'youtuneai' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
