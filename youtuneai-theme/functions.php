<?php
/**
 * YouTuneAi Theme Functions
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */
function youtuneai_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'custom-header' );
    add_theme_support( 'custom-background' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ) );

    // WooCommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'youtuneai' ),
        'footer'  => __( 'Footer Menu', 'youtuneai' ),
    ) );

    // Add support for editor styles
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );

    // Add support for responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Add support for wide and full alignment
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'youtuneai_setup' );

/**
 * Register Widget Areas
 */
function youtuneai_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'youtuneai' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Main sidebar widget area', 'youtuneai' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    // Footer widget areas
    $footer_widget_areas = 4;
    for ( $i = 1; $i <= $footer_widget_areas; $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( __( 'Footer Widget Area %d', 'youtuneai' ), $i ),
            'id'            => 'footer-' . $i,
            'description'   => sprintf( __( 'Footer widget area %d', 'youtuneai' ), $i ),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );
    }
}
add_action( 'widgets_init', 'youtuneai_widgets_init' );

/**
 * Enqueue Scripts and Styles
 */
function youtuneai_scripts() {
    // Main stylesheet
    wp_enqueue_style( 'youtuneai-style', get_stylesheet_uri(), array(), '1.0.0' );

    // Custom CSS
    wp_enqueue_style( 'youtuneai-custom', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.0.0' );

    // Google Fonts
    wp_enqueue_style( 'youtuneai-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', array(), null );

    // Main JavaScript
    wp_enqueue_script( 'youtuneai-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true );

    // Localize script for AJAX
    wp_localize_script( 'youtuneai-main', 'youtuneaiAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'youtuneai-nonce' ),
    ) );

    // Comments reply
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'youtuneai_scripts' );

/**
 * Custom Post Types
 */
function youtuneai_register_post_types() {
    // AI Features Post Type
    register_post_type( 'ai_feature', array(
        'labels' => array(
            'name'               => __( 'AI Features', 'youtuneai' ),
            'singular_name'      => __( 'AI Feature', 'youtuneai' ),
            'add_new'            => __( 'Add New', 'youtuneai' ),
            'add_new_item'       => __( 'Add New AI Feature', 'youtuneai' ),
            'edit_item'          => __( 'Edit AI Feature', 'youtuneai' ),
            'new_item'           => __( 'New AI Feature', 'youtuneai' ),
            'view_item'          => __( 'View AI Feature', 'youtuneai' ),
            'search_items'       => __( 'Search AI Features', 'youtuneai' ),
            'not_found'          => __( 'No AI Features found', 'youtuneai' ),
            'not_found_in_trash' => __( 'No AI Features found in Trash', 'youtuneai' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'menu_icon'    => 'dashicons-lightbulb',
        'rewrite'      => array( 'slug' => 'ai-features' ),
    ) );

    // Tutorials Post Type
    register_post_type( 'tutorial', array(
        'labels' => array(
            'name'               => __( 'Tutorials', 'youtuneai' ),
            'singular_name'      => __( 'Tutorial', 'youtuneai' ),
            'add_new'            => __( 'Add New', 'youtuneai' ),
            'add_new_item'       => __( 'Add New Tutorial', 'youtuneai' ),
            'edit_item'          => __( 'Edit Tutorial', 'youtuneai' ),
            'new_item'           => __( 'New Tutorial', 'youtuneai' ),
            'view_item'          => __( 'View Tutorial', 'youtuneai' ),
            'search_items'       => __( 'Search Tutorials', 'youtuneai' ),
            'not_found'          => __( 'No Tutorials found', 'youtuneai' ),
            'not_found_in_trash' => __( 'No Tutorials found in Trash', 'youtuneai' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'    => 'dashicons-welcome-learn-more',
        'rewrite'      => array( 'slug' => 'tutorials' ),
    ) );
}
add_action( 'init', 'youtuneai_register_post_types' );

/**
 * Custom Taxonomies
 */
function youtuneai_register_taxonomies() {
    register_taxonomy( 'ai_category', 'ai_feature', array(
        'labels' => array(
            'name'          => __( 'AI Categories', 'youtuneai' ),
            'singular_name' => __( 'AI Category', 'youtuneai' ),
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'ai-category' ),
    ) );
}
add_action( 'init', 'youtuneai_register_taxonomies' );

/**
 * Theme Customizer
 */
function youtuneai_customize_register( $wp_customize ) {
    // Hero Section
    $wp_customize->add_section( 'youtuneai_hero', array(
        'title'    => __( 'Hero Section', 'youtuneai' ),
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'hero_title', array(
        'default'           => __( 'Transform Your Music with AI', 'youtuneai' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_title', array(
        'label'   => __( 'Hero Title', 'youtuneai' ),
        'section' => 'youtuneai_hero',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'hero_subtitle', array(
        'default'           => __( 'AI-powered tools for musicians and creators', 'youtuneai' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );

    $wp_customize->add_control( 'hero_subtitle', array(
        'label'   => __( 'Hero Subtitle', 'youtuneai' ),
        'section' => 'youtuneai_hero',
        'type'    => 'textarea',
    ) );

    // Revenue Settings
    $wp_customize->add_section( 'youtuneai_revenue', array(
        'title'    => __( 'Revenue Settings', 'youtuneai' ),
        'priority' => 40,
    ) );

    $wp_customize->add_setting( 'enable_ads', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );

    $wp_customize->add_control( 'enable_ads', array(
        'label'   => __( 'Enable Ads', 'youtuneai' ),
        'section' => 'youtuneai_revenue',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'google_adsense_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'google_adsense_id', array(
        'label'       => __( 'Google AdSense ID', 'youtuneai' ),
        'section'     => 'youtuneai_revenue',
        'type'        => 'text',
        'description' => __( 'Enter your Google AdSense publisher ID', 'youtuneai' ),
    ) );

    $wp_customize->add_setting( 'affiliate_disclosure', array(
        'default'           => __( 'This site contains affiliate links', 'youtuneai' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );

    $wp_customize->add_control( 'affiliate_disclosure', array(
        'label'   => __( 'Affiliate Disclosure', 'youtuneai' ),
        'section' => 'youtuneai_revenue',
        'type'    => 'textarea',
    ) );
}
add_action( 'customize_register', 'youtuneai_customize_register' );

/**
 * Add Google AdSense to header
 */
function youtuneai_add_adsense() {
    if ( get_theme_mod( 'enable_ads', false ) && get_theme_mod( 'google_adsense_id' ) ) {
        $adsense_id = esc_attr( get_theme_mod( 'google_adsense_id' ) );
        echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . $adsense_id . '" crossorigin="anonymous"></script>';
    }
}
add_action( 'wp_head', 'youtuneai_add_adsense' );

/**
 * WooCommerce Customizations
 */
function youtuneai_woocommerce_setup() {
    // Disable WooCommerce default styles
    add_filter( 'woocommerce_enqueue_styles', '__return_false' );
}
add_action( 'after_setup_theme', 'youtuneai_woocommerce_setup' );

/**
 * Custom excerpt length
 */
function youtuneai_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'youtuneai_excerpt_length' );

/**
 * Custom excerpt more
 */
function youtuneai_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'youtuneai_excerpt_more' );

/**
 * Add custom body classes
 */
function youtuneai_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'home-page';
    }
    if ( class_exists( 'WooCommerce' ) && is_shop() ) {
        $classes[] = 'shop-page';
    }
    return $classes;
}
add_filter( 'body_class', 'youtuneai_body_classes' );

/**
 * Revenue Tracking Shortcode
 */
function youtuneai_revenue_tracking() {
    ob_start();
    ?>
    <div class="revenue-widget">
        <h3><?php _e( 'Revenue Dashboard', 'youtuneai' ); ?></h3>
        <?php if ( class_exists( 'WooCommerce' ) ): ?>
            <div class="revenue-stats">
                <p><?php _e( 'Track your earnings with WooCommerce integration', 'youtuneai' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'revenue_tracking', 'youtuneai_revenue_tracking' );

/**
 * AI Features Shortcode
 */
function youtuneai_ai_features( $atts ) {
    $atts = shortcode_atts( array(
        'count' => 3,
    ), $atts );

    $query = new WP_Query( array(
        'post_type'      => 'ai_feature',
        'posts_per_page' => intval( $atts['count'] ),
    ) );

    if ( ! $query->have_posts() ) {
        return '';
    }

    ob_start();
    ?>
    <div class="ai-features-grid">
        <?php while ( $query->have_posts() ): $query->the_post(); ?>
            <div class="ai-feature-card">
                <?php if ( has_post_thumbnail() ): ?>
                    <div class="feature-image">
                        <?php the_post_thumbnail( 'medium' ); ?>
                    </div>
                <?php endif; ?>
                <h3><?php the_title(); ?></h3>
                <div class="feature-excerpt">
                    <?php the_excerpt(); ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                    <?php _e( 'Learn More', 'youtuneai' ); ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'ai_features', 'youtuneai_ai_features' );

/**
 * Security enhancements
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Performance optimizations
 */
function youtuneai_defer_scripts( $tag, $handle, $src ) {
    $defer_scripts = array( 'youtuneai-main' );
    
    if ( in_array( $handle, $defer_scripts ) ) {
        return '<script src="' . $src . '" defer></script>' . "\n";
    }
    
    return $tag;
}
add_filter( 'script_loader_tag', 'youtuneai_defer_scripts', 10, 3 );

/**
 * Include revenue tracking functionality
 */
require_once get_template_directory() . '/inc/revenue-tracking.php';
