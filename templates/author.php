<?php get_header(); ?>

<?php 
$author = get_queried_object();
$image_id = get_user_meta($author->ID, 'author_profile_image_id', true);
$profile_image = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
$bio = get_user_meta($author->ID, 'author_custom_bio', true);

// Configuration options
$layout = get_option('cap_author_page_layout', 'top');
$image_size = get_option('cap_author_image_size', 200);
$show_image = get_option('cap_author_show_image', 1);
$show_bio = get_option('cap_author_show_bio', 1);
$show_social = get_option('cap_author_show_social', 1);
$show_email = get_option('cap_author_show_email', 0);
$show_website = get_option('cap_author_show_website', 0);

// Get all social media links (centralized)
$social_links = [];
foreach (array_keys(CAP_Plugin::get_social_networks()) as $network) {
    $social_links[$network] = get_user_meta($author->ID, 'author_' . $network, true);
}

// Check if profile card should be displayed
$has_profile_content = ($show_image) || ($show_social) || ($show_email && $author->user_email) || ($show_website && $author->user_url) || ($layout === 'top' && $show_bio && $bio);
?>

<div class="author-page-container layout-<?php echo esc_attr($layout); ?>">

    <?php if ($has_profile_content): ?>
    <div class="author-profile-wrapper">
        <div class="author-profile">

            <h1 class="author-name"><?php echo esc_html($author->display_name); ?></h1>

            <?php if($show_image): ?>
                <?php if($profile_image): ?>
                    <img class="author-profile-image" src="<?php echo esc_url($profile_image); ?>" alt="<?php echo esc_attr($author->display_name); ?>" style="max-width: <?php echo intval($image_size); ?>px !important;">
                <?php else: ?>
                    <?php echo get_avatar($author->ID, intval($image_size), '', esc_attr($author->display_name), ['class' => 'author-profile-image']); ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($show_social): ?>
                <div class="author-social-links">
                    <?php foreach ($social_links as $network => $url): ?>
                        <?php if ($url): ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                <?php 
                                $svg = CAP()->frontend->get_svg_icon($network);
                                echo $svg ? $svg : '<img src="' . esc_url(CAP_PLUGIN_URL . 'templates/images/' . $network . '.svg') . '" alt="' . esc_attr(ucfirst($network)) . '" class="social-icon">'; 
                                ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="author-contact-info">
                 <?php if ($show_email && $author->user_email): ?>
                    <p class="author-email">
                        <a href="mailto:<?php echo esc_attr($author->user_email); ?>"><?php echo esc_html($author->user_email); ?></a>
                    </p>
                <?php endif; ?>

                <?php if ($show_website && $author->user_url): ?>
                    <p class="author-website">
                        <a href="<?php echo esc_url($author->user_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html(str_replace(['http://', 'https://'], '', $author->user_url)); ?></a>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($layout === 'top' && $show_bio && $bio): ?>
                <div class="author-bio"><?php echo do_shortcode(wpautop($bio)); ?></div>
            <?php endif; ?>

        </div>
    </div>
    <?php endif; ?>

    <div class="author-content-wrapper">
        <?php if (!$has_profile_content): ?>
             <h1 class="author-name" style="margin-top: 0; margin-bottom: 20px;"><?php echo esc_html($author->display_name); ?></h1>
        <?php endif; ?>

        <?php if ($layout !== 'top' && $show_bio && $bio): ?>
            <div class="author-bio" style="margin-bottom: 30px; text-align: left;"><?php echo do_shortcode(wpautop($bio)); ?></div>
        <?php endif; ?>

        <h2><?php echo esc_html(get_option('cap_label_articles_by', __('Articles by', 'custom-author-profile'))); ?> <?php echo esc_html($author->display_name); ?>:</h2>

    <?php if (have_posts()) : ?>

        <div class="blog-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" class="blog-grid-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="blog-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <h3 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

                    <div class="entry-excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="read-more"><?php echo esc_html(get_option('cap_label_read_more', __('Read more', 'custom-author-profile'))); ?></a>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php echo paginate_links(); ?>
        </div>

    <?php else : ?>
        <p><?php echo esc_html(get_option('cap_label_no_posts', __('No posts found.', 'custom-author-profile'))); ?></p>
    <?php endif; ?>
    </div>

</div>

<?php get_footer(); ?>