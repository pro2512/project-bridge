<?php
/**
 * Main template file.
 *
 * @package BridgeBootstrapMinimal
 */

get_header();
?>
<main class="site-main container" id="content">
    <?php if (have_posts()) : ?>
        <?php $bridge_post_index = 0; ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                <header>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="entry-meta"><?php echo esc_html(get_the_date()); ?></p>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <figure class="entry-media">
                        <?php
                        the_post_thumbnail(
                            'large',
                            [
                                'class'         => 'entry-image',
                                'loading'       => 0 === $bridge_post_index ? 'eager' : 'lazy',
                                'fetchpriority' => 0 === $bridge_post_index ? 'high' : 'auto',
                                'decoding'      => 0 === $bridge_post_index ? 'sync' : 'async',
                            ]
                        );
                        ?>
                    </figure>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php $bridge_post_index++; ?>
        <?php endwhile; ?>

        <nav class="posts-pagination" aria-label="<?php esc_attr_e('Posts', 'bridge-bootstrap-minimal'); ?>">
            <span><?php next_posts_link(__('&larr; Older Posts', 'bridge-bootstrap-minimal')); ?></span>
            <span><?php previous_posts_link(__('Newer Posts &rarr;', 'bridge-bootstrap-minimal')); ?></span>
        </nav>
    <?php else : ?>
        <section class="empty-state" role="status">
            <?php esc_html_e('No content found yet.', 'bridge-bootstrap-minimal'); ?>
        </section>
    <?php endif; ?>
</main>
<?php
get_footer();
