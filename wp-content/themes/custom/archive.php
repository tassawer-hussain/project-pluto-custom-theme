<?php
get_header();
?>

<div class="container-fluid">
    <section id="archive_header">
        <div class="col-md-12">
            <h2><strong>Category:</strong> <?php single_cat_title(); ?></h2>
            <a class="btn btn-default" href="<?php echo site_url(); ?>"><i class="fa fa-caret-left" aria-hidden="true"></i> Back to Home</a>
        </div>
    </section>
</div>


<section id="isotope_posts">
    <!-- Isotope Listings -->

    <?php
    global $post;
    $prefix = 'pluto_featured_';
    $catName = '';
    if (have_posts()) {
        echo '<div class="isotope">';
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
                //$src = get_bloginfo('template_url') . '/images/no-thumbnail.jpg';
                $src = '';
            }
            $filterCat = '';
            foreach ($categories as $category) {
                $filterCat .= $category->slug . ' ';
            }
            
            $css_class = '';
            $data_action = '';
            $likes_count = get_post_meta($post->ID, 'post_likes_count', true);
            $cookie_name = 'th_like_'.$post->ID;
            if(isset($_COOKIE[$cookie_name])) {
                $css_class = "has-voted";
                $data_action = "unvote";
            } else {
                $css_class = "has-not-voted";
                $data_action = "vote";
            }
            
            //if ( empty( $pluto_featured_checkbox ) ) {
            if ($format == 'quotes') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article class="format-quote">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/quotation-w.png'; ?>" alt="" />
                            </div>
                            <h2><?php echo get_the_title() ?></h2>
                        </div>
                        <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                            <i class="fa fa-quote-left"></i>
                            <h2><?php echo strip_tags(get_the_content()) ?></h2>
                            <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                        </div>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'movies') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article class="format-video">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/movie-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="feature_image">
                                <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php echo get_the_permalink(); ?>" class="read_more">Watch Now</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'audio') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article class="format-audio">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/audio-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="feature_image">
                                <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php echo get_the_permalink(); ?>" class="read_more">Listen Now</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'podcasts') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article class="format-podcast">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/podcast-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="feature_image">
                                <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php echo get_the_permalink(); ?>" class="read_more">View Now</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'articles') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/article-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php the_permalink(); ?>" class="image-link">
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read_more">Read More</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if($format == 'stories') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/story-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php the_permalink(); ?>" class="image-link">
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read_more">Read More</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'gifs') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/gif-w.png'; ?>" alt="" />
                            </div>
                            <h2><?php echo get_the_title() ?></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <div class="feature_image">
                            <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                        </div>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'photos') { ?>
                <div class="col-md-4 padding-right <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/photo-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php the_permalink(); ?>" class="image-link">
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read_more">Read More</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php } else { ?>
                <div class="col-md-4 padding-right">
                    <article>
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/misc-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                        </div>
                        <?php if (isset($src) && !empty($src)) { ?>
                        <a href="<?php the_permalink(); ?>" class="image-link">
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                        </a>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read_more">Read More</a>
                        <div class="post_categories listing">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                            } ?>
                            </div>
                            <div class="post-like listing <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php
            }
        //}
        endwhile;
        echo '</div>';
    } else { ?>
        <div class="no_post_found">
            <h2>No Post Found.</h2>
            <p>The post you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <form method="GET" action="<?php echo site_url(); ?>" autocomplete="off">  
                <input type="text" autocomplete="off" class="search-field" placeholder="Search" value="" name="s" title="Search for:">
                <input type="submit" class="search-submit" value="Search">
            </form>
        </div>
<?php } ?>

</section>

<?php get_footer(); ?>