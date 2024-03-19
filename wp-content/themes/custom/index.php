<?php get_header(); ?>
<!-- Featured Posts -->

<section id="featuredSlider">

    <h4 class="featured-heading">Featured</h4>
    <div class="slider">
        <?php
        $prefix = 'pluto_featured_';
        $loop = new WP_Query(
            array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'posts_per_page' => -1,
                'meta_query' => array(
                'relation' => 'AND',
                    array(
                        'key' => $prefix . 'checkbox',
                        'value' => 'on',
                        'compare' => 'EXISTS',
                    ),
                )
            )
        );
        if ($loop->have_posts()) :
            while ($loop->have_posts()) : $loop->the_post();
                $featured_image = get_post_meta(get_the_ID(), $prefix . 'image');
                $image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
                $featuredPost = get_post_meta(get_the_ID(), $prefix . 'checkbox');
                $categories = get_the_category($post->ID);
                $getFormat = get_the_terms($post->ID, 'formats');
                $format = $getFormat[0]->slug;
                $externalLink = get_post_meta(get_the_ID(), $prefix . 'external_link');
                
                if (isset($externalLink) && !empty($externalLink)) {
                    $link = $externalLink[0];
                } else {
                    $link = get_the_permalink();
                }

                if ($format == 'quotes') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <div class="featured_fader"></div>
                        <div class="feature_details">
                            <span class="feature_title"><?php the_title(); ?></span>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'movies') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'audio') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'podcasts') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'articles') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'stories') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'gifs') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <div class="featured_fader"></div>	
                        <div class="feature_details">
                            <span class="feature_title"><?php the_title(); ?></span>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if ($format == 'photos') { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="inner_slide">
                        <?php if (isset($featured_image) && !empty($featured_image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $featured_image[0]; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else if (isset($image) && !empty($image)) { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php echo $image; ?>&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } else { ?>
                            <img src="<?php bloginfo('template_url'); ?>/timthumb.php?src=<?php bloginfo('template_url'); ?>/images/no-thumbnail.jpg&amp;h=350&amp;w=255&amp;zc=1" alt="<?php the_title(); ?>"/>
                        <?php } ?>
                        <a href="<?php echo $link; ?>">
                            <div class="featured_fader"></div>	
                        </a>
                        <div class="feature_details">
                            <a href="<?php echo $link; ?>" class="feature_title"><?php the_title(); ?></a>
                            <ul class="featured-categories">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php }
            endwhile;
        endif;
        wp_reset_postdata();
        ?>
    </div>
</section>

<div class="clearfix"></div>
<!-- Filters -->
<div class="col-md-12">
    <div id="filters" class="panel panel-default">
        <div class="panel-body">
            <div class="form-inline">
                <div class="form-group homeButton">
                    <a href="<?php echo site_url(); ?>" class="btn btn-default">Home</a>
                </div>
                <div class="form-group orderButton">
                    <span class="order-text">ORDER BY:</span>
                    <button class="btn btn-default thorderbybtn" data-meta_key="post_views_count" data-orderby="meta_value_num" data-order="DESC">Most Popular</button>
                    <button class="btn btn-default thorderbybtn" data-meta_key="none" data-orderby="date" data-order="DESC">Newest</button>
                    <button class="btn btn-default thorderbybtn" data-meta_key="none" data-orderby="date" data-order="ASC">Oldest</button>
                </div>
                <div class="form-group formatButton">
                    <?php
                    $taxonomies = get_terms(array(
                        'taxonomy' => 'formats',
                        'hide_empty' => true
                    ));
                    if (!is_wp_error($taxonomies)) { ?>
                        <div class="form-group formats">
                            <div class="format-text">FORMATS:</div>
                            <?php
                            foreach ($taxonomies as $term) {
                                switch ($term->name) {
                                    case 'Quotes':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/quotation.png" alt="" /></span>';
                                        break;
                                    case 'Articles':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/article.png" alt="" /></span>';
                                        break;
                                    case 'Stories':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/story.png" alt="" /></span>';
                                        break;
                                    case 'Gifs':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/gif.png" alt="" /></span>';
                                        break;
                                    case 'Misc':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/misc.png" alt="" /></span>';
                                        break;
                                    case 'Podcasts':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/podcast.png" alt="" /></span>';
                                        break;
                                    case 'Audio':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/audio.png" alt="" /></span>';
                                        break;
                                    case 'Photos':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/photo.png" alt="" /></span>';
                                        break;
                                    case 'Movies':
                                        echo '<span data-toggle="tooltip" title="' . $term->name . '"><img class="formats-img" data-termid="' . $term->term_id . '" src="' . get_bloginfo('template_url') . '/images/movie.png" alt="" /></span>';
                                        break;
                                }
                            } ?>
                        </div>
                        <div class="form-group">
                            <span class="clear-filter-btn"><i class="fa fa-times"></i> <span>Clear Filters</span></span>
                        </div>
                    <?php } ?>
                </div>


                <div class="form-group pull-right categoryButton">
                    <div class="dropdown">
                        <?php $parent_terms = get_terms('category', array('parent' => 0, 'orderby' => 'slug', 'hide_empty' => false)); ?>
                        <div class="form-group">
                            <select class="form-control" id="categoryPage">
                                echo '<option value="">- Select Category -</option>';
                                <?php foreach ($parent_terms as $pterm) {
                                    echo '<option value="' . get_term_link($pterm) . '" class="optionGroup">' . $pterm->name . '</option>';
                                    $terms = get_terms('category', array('parent' => $pterm->term_id, 'orderby' => 'slug', 'hide_empty' => false));
                                    foreach ($terms as $term) {
                                        echo '<option value="' . get_term_link($term) . '" class="optionChild">&nbsp;&nbsp;&nbsp;' . $term->name . '</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clear-fix"></div>
<section id="isotope_posts" class="home_posts">

    <!-- Isotope Listings Default -->
    <div class="isotope">

    </div>

    <div class="clear-fix"></div>
    
    <div class="load_more th_load_more" id="th_load_more">
        <div class="loaderPosts"><img src="<?php echo site_url(); ?>/wp-content/themes/custom/images/loader-red.gif"></div>
    </div>
    
    <div class="no_posts th_no_posts" id="th_no_posts">
        <div class="col-md-12">
            <p>NO MORE POSTS.</p>
        </div>
    </div>

</section>

<?php get_footer(); ?>