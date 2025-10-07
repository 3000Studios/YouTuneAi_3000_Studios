<?php
/**
 * Single Post Template
 *
 * @package YouTuneAi
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main single-post">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span class="posted-on">
                            <?php echo get_the_date(); ?>
                        </span>
                        <span class="author">
                            <?php _e( 'by', 'youtuneai' ); ?> 
                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                                <?php the_author(); ?>
                            </a>
                        </span>
                        <?php if ( has_category() ) : ?>
                            <span class="categories">
                                <?php the_category( ', ' ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <?php if ( has_tag() ) : ?>
                    <footer class="entry-footer">
                        <div class="post-tags">
                            <?php the_tags( '<span class="tags-label">' . __( 'Tags:', 'youtuneai' ) . '</span> ', ', ' ); ?>
                        </div>
                    </footer>
                <?php endif; ?>

                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </article>

            <?php
            the_post_navigation( array(
                'prev_text' => '<span class="nav-subtitle">' . __( 'Previous:', 'youtuneai' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __( 'Next:', 'youtuneai' ) . '</span> <span class="nav-title">%title</span>',
            ) );
            ?>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
