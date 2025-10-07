<?php
/**
 * Header Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="wrapper">
    <header class="site-header">
        <div class="container">
            <div class="header-container">
                <div class="site-branding">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'youtuneai' ); ?>">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-icon"></span>
                        <span class="screen-reader-text"><?php _e( 'Menu', 'youtuneai' ); ?></span>
                    </button>
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ) );
                    ?>
                </nav>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <div class="header-cart">
                        <a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 2L7.17 4H3C1.9 4 1 4.9 1 6V19C1 20.1 1.9 21 3 21H21C22.1 21 23 20.1 23 19V6C23 4.9 22.1 4 21 4H16.83L15 2H9ZM9 4.83L9.83 6H14.17L15 4.83M12 9C9.24 9 7 11.24 7 14C7 16.76 9.24 19 12 19C14.76 19 17 16.76 17 14C17 11.24 14.76 9 12 9Z" fill="currentColor"/>
                            </svg>
                            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
