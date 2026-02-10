<?php
get_header(); ?>

<?php
// Get the featured image of the post
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="single-post-header" style="<?php echo $featured_image ? 'background-image: url(' . esc_url($featured_image) . ');' : ''; ?>">
    <div class="single-post-header-overlay">
        <h1><?php the_title(); ?></h1>
    </div>
</div>

<div class="single-post-meta">
    <p><?php echo get_the_date('d F Y'); ?> / <?php the_category(', '); ?></p>
</div>

<div class="single-post-container">
    <?php while (have_posts()) : the_post(); ?>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>