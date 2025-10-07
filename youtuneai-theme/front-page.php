<?php
/**
 * Front Page Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <?php echo esc_html( get_theme_mod( 'hero_title', __( 'Transform Your Music with AI', 'youtuneai' ) ) ); ?>
                </h1>
                <p class="hero-subtitle">
                    <?php echo esc_html( get_theme_mod( 'hero_subtitle', __( 'AI-powered tools for musicians and creators', 'youtuneai' ) ) ); ?>
                </p>
                <div class="hero-cta">
                    <a href="#features" class="btn btn-primary">
                        <?php _e( 'Get Started', 'youtuneai' ); ?>
                    </a>
                    <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn-secondary">
                        <?php _e( 'Learn More', 'youtuneai' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">
                <?php _e( 'Powerful AI Features', 'youtuneai' ); ?>
            </h2>
            
            <div class="features-grid">
                <?php
                $features = array(
                    array(
                        'icon'  => '🎵',
                        'title' => __( 'AI Music Generation', 'youtuneai' ),
                        'desc'  => __( 'Create unique music tracks with advanced AI algorithms tailored to your style.', 'youtuneai' ),
                    ),
                    array(
                        'icon'  => '🎤',
                        'title' => __( 'Voice Enhancement', 'youtuneai' ),
                        'desc'  => __( 'Enhance vocal quality with AI-powered audio processing and effects.', 'youtuneai' ),
                    ),
                    array(
                        'icon'  => '🎸',
                        'title' => __( 'Instrument Separation', 'youtuneai' ),
                        'desc'  => __( 'Isolate and extract individual instruments from any audio track.', 'youtuneai' ),
                    ),
                    array(
                        'icon'  => '🎹',
                        'title' => __( 'MIDI Generation', 'youtuneai' ),
                        'desc'  => __( 'Convert audio to MIDI and create custom arrangements instantly.', 'youtuneai' ),
                    ),
                    array(
                        'icon'  => '🎧',
                        'title' => __( 'Mastering Suite', 'youtuneai' ),
                        'desc'  => __( 'Professional mastering powered by AI for radio-ready sound quality.', 'youtuneai' ),
                    ),
                    array(
                        'icon'  => '🎬',
                        'title' => __( 'Content Analysis', 'youtuneai' ),
                        'desc'  => __( 'Analyze your content and get AI-powered recommendations for improvement.', 'youtuneai' ),
                    ),
                );

                foreach ( $features as $feature ) :
                ?>
                    <div class="feature-card">
                        <div class="feature-icon"><?php echo $feature['icon']; ?></div>
                        <h3 class="feature-title"><?php echo esc_html( $feature['title'] ); ?></h3>
                        <p class="feature-description"><?php echo esc_html( $feature['desc'] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Shop Section (if WooCommerce is active) -->
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <section class="woocommerce-section">
            <div class="container">
                <h2 class="section-title">
                    <?php _e( 'Premium Tools & Resources', 'youtuneai' ); ?>
                </h2>
                <p class="text-center mb-4">
                    <?php _e( 'Explore our curated selection of AI-powered tools, plugins, and resources for musicians.', 'youtuneai' ); ?>
                </p>
                
                <?php
                echo do_shortcode( '[products limit="4" columns="4" orderby="popularity"]' );
                ?>
                
                <div class="text-center mt-4">
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-primary">
                        <?php _e( 'View All Products', 'youtuneai' ); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- AI Features Post Type Section -->
    <?php
    $ai_features_query = new WP_Query( array(
        'post_type'      => 'ai_feature',
        'posts_per_page' => 3,
    ) );

    if ( $ai_features_query->have_posts() ) :
    ?>
        <section class="ai-features-showcase">
            <div class="container">
                <h2 class="section-title">
                    <?php _e( 'Latest AI Innovations', 'youtuneai' ); ?>
                </h2>
                
                <div class="features-grid">
                    <?php while ( $ai_features_query->have_posts() ) : $ai_features_query->the_post(); ?>
                        <article class="feature-card">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="feature-image">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="feature-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <p class="feature-description">
                                <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php _e( 'Explore Feature', 'youtuneai' ); ?>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <!-- Blog Posts Section -->
    <?php
    $blog_query = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
    ) );

    if ( $blog_query->have_posts() ) :
    ?>
        <section class="blog-section">
            <div class="container">
                <h2 class="section-title">
                    <?php _e( 'Latest from Our Blog', 'youtuneai' ); ?>
                </h2>
                
                <div class="posts-grid">
                    <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                        <article <?php post_class( 'post-card' ); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="post-content">
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                </div>
                                <p class="post-excerpt">
                                    <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                                </p>
                                <a href="<?php the_permalink(); ?>" class="btn btn-secondary">
                                    <?php _e( 'Read More', 'youtuneai' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn-primary">
                        <?php _e( 'View All Posts', 'youtuneai' ); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="section-title">
                <?php _e( 'Ready to Transform Your Music?', 'youtuneai' ); ?>
            </h2>
            <p class="hero-subtitle">
                <?php _e( 'Join thousands of creators using AI to enhance their music production workflow.', 'youtuneai' ); ?>
            </p>
            <div class="hero-cta">
                <a href="#" class="btn btn-primary">
                    <?php _e( 'Start Free Trial', 'youtuneai' ); ?>
                </a>
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-secondary">
                        <?php _e( 'Browse Products', 'youtuneai' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
