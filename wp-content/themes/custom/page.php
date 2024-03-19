<?php
get_header();
?>

<section id="archive_header">
    <div class="col-md-12">
        <h2><strong>Category:</strong> <?php single_cat_title(); ?></h2>
        <a class="btn btn-default" href="<?php echo site_url(); ?>"><i class="fa fa-caret-left" aria-hidden="true"></i> Back to Home</a>
    </div>
</section>

<section id="isotope_posts">
    <!-- Isotope Listings -->

    <?php
    global $post;
    $prefix = 'pluto_featured_';
    $catName = '';

    if (have_posts()) {

        while (have_posts()) : the_post();
            $pluto_featured_checkbox = get_post_meta($post->ID, 'pluto_featured_checkbox', true);

            $catName = array();
            $categories = get_the_category($post->ID);
            $getFormat = get_the_terms($post->ID, 'formats');
            $format = $getFormat[0]->slug;
            if (has_post_thumbnail()) {
                $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                $src = $src[0];
            } else {
                $src = get_bloginfo('template_url') . '/images/no-thumbnail.jpg';
            }
            $filterCat = '';
            foreach ($categories as $category) {
                $filterCat .= $category->slug . ' ';
            }
            if (empty($pluto_featured_checkbox)) {
                ?>

                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <i class="fa fa-file-text-o"></i>
                            <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="image-link">
                            <div class="feature_image">
                                <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read_more">Read More</a>
                        <div class="post_categories">
                            <?php
                            foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            }
                            ?>
                        </div>
                    </article>
                </div>
            <?php
            }

        endwhile;
        echo '</div>';
    } else {
        ?>
        <div class="no_post_found">
            <h2>No Post Found.</h2>
            <p>The post you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <form method="GET" action="<?php echo site_url(); ?>" autocomplete="off">  
                <input type="text" autocomplete="off" class="search-field" placeholder="Search" value="" name="s" title="Search for:">
                <input type="submit" class="search-submit" value="Search">
            </form>
        </div>
<?php }
?>


</section>


<?php
get_footer();
?>