<?php
/**
 * Footer Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */
?>

    <footer class="site-footer">
        <div class="container">
            <?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || 
                       is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) : ?>
                <div class="footer-widgets">
                    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                        <?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
                            <div class="footer-widget-area">
                                <?php dynamic_sidebar( 'footer-' . $i ); ?>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'youtuneai' ); ?>">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                    ) );
                    ?>
                </nav>
            <?php endif; ?>

            <div class="footer-bottom">
                <p class="copyright">
                    &copy; <?php echo date( 'Y' ); ?> 
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                    <?php _e( '- All Rights Reserved', 'youtuneai' ); ?>
                </p>
                
                <?php if ( get_theme_mod( 'enable_ads', false ) && get_theme_mod( 'affiliate_disclosure' ) ) : ?>
                    <p class="affiliate-disclosure">
                        <?php echo esc_html( get_theme_mod( 'affiliate_disclosure' ) ); ?>
                    </p>
                <?php endif; ?>
                
                <p class="theme-credit">
                    <?php _e( 'Powered by', 'youtuneai' ); ?> 
                    <a href="https://3000studios.com" target="_blank" rel="noopener">3000Studios</a>
                </p>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
