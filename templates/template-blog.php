<?php
/*
Template Name: Blog Archive
*/
get_header(); ?>

<?php
// Custom blog header image logic
$desktop_id = get_post_meta(get_the_ID(), '_cap_blog_header_image_desktop', true);
$mobile_id  = get_post_meta(get_the_ID(), '_cap_blog_header_image_mobile', true);

$desktop_url = $desktop_id ? wp_get_attachment_url($desktop_id) : get_the_post_thumbnail_url(get_the_ID(), 'full');
$mobile_url  = $mobile_id  ? wp_get_attachment_url($mobile_id)  : $desktop_url; // fallback
?>
<style>
@media (min-width: 768px) {
    .blog-header {
        background-image: url('<?php echo esc_url($desktop_url); ?>');
    }
}
@media (max-width: 767px) {
    .blog-header {
        background-image: url('<?php echo esc_url($mobile_url); ?>');
    }
}
</style>
<div class="blog-header">
    <div class="blog-header-overlay">
        <h1><?php the_title(); ?></h1>
    </div>
</div>

<div class="blog-container">
    <?php
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $posts_per_page = get_option('cap_posts_per_page', 12);

    $blog_query = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => $posts_per_page > 0 ? $posts_per_page : 12,
        'paged' => $paged
    ]);

    if ($blog_query->have_posts()) : ?>

        <div class="blog-grid">
            <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-grid-item'); ?>>

                <?php if(has_post_thumbnail()): ?>
                    <div class="blog-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>

                <div class="entry-meta">
                    <?php echo get_the_date(); ?> | <?php the_category(', '); ?>
                </div>

                <div class="entry-excerpt">
                    <?php the_excerpt(); ?>
                </div>

                <a href="<?php the_permalink(); ?>" class="read-more"><?php echo esc_html(get_option('cap_label_read_more', 'Citeste mai departe')); ?></a>
            </article>

            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php
            echo paginate_links([
                'total' => $blog_query->max_num_pages,
                'prev_text' => esc_html(get_option('cap_label_prev', '« Inapoi')),
                'next_text' => esc_html(get_option('cap_label_next', 'Inainte »')),
            ]);
            ?>
        </div>

    <?php else : ?>
        <p>No posts found.</p>
    <?php endif; wp_reset_postdata(); ?>

</div>

<?php get_footer(); ?>