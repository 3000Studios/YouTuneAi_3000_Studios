<?php
/**
 * Main Index Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <?php if ( have_posts() ) : ?>
            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <header class="post-header">
                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                <div class="post-meta">
                                    <span class="post-date">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <span class="post-author">
                                        <?php _e( 'by', 'youtuneai' ); ?> <?php the_author(); ?>
                                    </span>
                                </div>
                            </header>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php _e( 'Read More', 'youtuneai' ); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '← Previous', 'youtuneai' ),
                'next_text' => __( 'Next →', 'youtuneai' ),
            ) ); ?>

        <?php else : ?>
            <div class="no-posts">
                <h2><?php _e( 'Nothing Found', 'youtuneai' ); ?></h2>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'youtuneai' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
