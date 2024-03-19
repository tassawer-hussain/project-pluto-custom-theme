<?php
if (!session_id()) {
    session_start();
    unset($_SESSION['seed']);
}
// Post Thumbnails
add_theme_support('post-thumbnails');
// Post Thumbnails Size
add_image_size('isotope_images', 600, 400, true);
// disable editor for posts
add_filter('use_block_editor_for_post', '__return_false', 10);

function getAllPosts() {
    global $post;
    $getPostsId = array();
    $prefix = 'pluto_featured_';
    $isotope_oldest = new WP_Query(
            array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'ASC',
        'posts_per_page' => -1,
        'paged' => '1',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => $prefix . 'checkbox',
                'value' => 'on',
                'compare' => 'NOT EXISTS',
            ),
        )
            )
    );
    $countAllposts = $isotope_oldest->found_posts;
    update_option('countAllposts', $countAllposts);
    if ($isotope_oldest->have_posts()) {
        $isotope_oldest_counter = 0;
        while ($isotope_oldest->have_posts()) : $isotope_oldest->the_post();
            $getPostsId[$isotope_oldest_counter] = $post->ID;
            $isotope_oldest_counter++;
        endwhile;
        update_option('getPostsId', $getPostsId);
    }
}

add_action('init', 'getAllPosts');

function getPopularPosts() {
    global $post;
    $prefix = 'pluto_featured_';
    $isotope_popular = new WP_Query(
            array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'meta_key' => 'post_views_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => $prefix . 'checkbox',
                'value' => 'on',
                'compare' => 'NOT EXISTS',
            ),
        )
            )
    );
    $countPopularPosts = $isotope_popular->found_posts;
    update_option('countPopularPosts', $countPopularPosts);
}

add_action('init', 'getPopularPosts');

/**
 * Formats Taxonomy Registration
 */
function create_post_formats_taxonomy() {
    // Add new taxonomy, make it hierarchical like categories
    $labels = array(
        'name' => _x('Formats', 'pluto'),
        'singular_name' => _x('Formats', 'pluto'),
        'search_items' => __('Search Posts'),
        'all_items' => __('All Posts Formats'),
        'parent_item' => __('Parent Format'),
        'parent_item_colon' => __('Parent Format:'),
        'edit_item' => __('Edit Format'),
        'update_item' => __('Update Format'),
        'add_new_item' => __('Add New Format'),
        'new_item_name' => __('New Format Name'),
        'menu_name' => __('Formats'),
    );
    // Now register the taxonomy
    register_taxonomy('formats', array('post'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_in_menu' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'formats'
        ),
        'show_in_rest' => true,
        'capabilities' => array(
            'edit_terms' => true,
        ),
    ));
}

add_action('init', 'create_post_formats_taxonomy');

/**
 * Use radio inputs instead of checkboxes for term checklists in specified taxonomies.
 */
function post_format_radio_checklist($args) {
    if (!empty($args['taxonomy']) && $args['taxonomy'] === 'formats') {
        if (empty($args['walker']) || is_a($args['walker'], 'Walker')) {
            if (!class_exists('Walker_Category_Radio_Checklist')) {

                /**
                 * Custom walker for switching checkbox inputs to radio.
                 *
                 * @see Walker_Category_Checklist
                 */
                class Walker_Category_Radio_Checklist extends Walker_Category_Checklist {

                    function walk($elements, $max_depth, $args = array()) {
                        $output = parent::walk($elements, $max_depth, $args);
                        $output = str_replace(
                                array('type="checkbox"', "type='checkbox'"), array('type="radio"', "type='radio'"), $output
                        );
                        return $output;
                    }

                }

            }
            $args['walker'] = new Walker_Category_Radio_Checklist;
        }
    }
    return $args;
}

add_filter('wp_terms_checklist_args', 'post_format_radio_checklist');

/**
 * CMB2 Custom Meta Fields
 */
if (file_exists(dirname(__FILE__) . '/cmb2/init.php')) {
    require_once dirname(__FILE__) . '/cmb2/init.php';
    require_once dirname(__FILE__) . '/cmb2/metabox.php';
}

/**
 * Add New Column For Featured Post
 */
function featuedPostColumnValue($column_name, $post_ID) {
    $prefix = 'pluto_featured_';
    if ($column_name == 'featured') {
        $featured = get_post_meta($post_ID, $prefix . 'checkbox');
        if ($featured) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }
}

add_action('manage_posts_custom_column', 'featuedPostColumnValue', 10, 2);

/**
 * New Column Name
 */
function featuedPostColumn($defaults) {
    $defaults['featured'] = 'Featured';
    return $defaults;
}

add_filter('manage_posts_columns', 'featuedPostColumn');

/**
 * Breadcrumbs for frontend
 */
function get_breadcrumb() {
    echo '<a class="btn btn-default" href="' . home_url() . '" rel="nofollow">Home</a>';
    if (is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        if (is_single()) {
            echo '<span>' . get_the_title() . '</span>';
        }
    }
}

/**
 * Load more posts using ajax
 */
function more_post_ajax() {
    global $post;
    $prefix = 'pluto_featured_';
    $paged = $_POST['page'];
    $order = $_POST['order'];
    if ($order == 'oldest') {
        $isotope_oldest = new WP_Query(
                array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'posts_per_page' => 9,
            'paged' => $paged,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $prefix . 'checkbox',
                    'value' => 'on',
                    'compare' => 'NOT EXISTS',
                ),
            )
                )
        );

        if ($isotope_oldest->have_posts()) {
            while ($isotope_oldest->have_posts()) : $isotope_oldest->the_post();
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

                if ($format == 'quotes') {
                    ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-quote">
                            <div class="post_title">
                                <i class="fa fa-comment"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                                <i class="fa fa-quote-left"></i>
                                <h2><?php echo strip_tags(get_the_content()) ?></h2>
                                <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'movie') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-video">
                            <div class="post_title">
                                <i class="fa fa-film"></i>
                                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read_more">Watch Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'audio') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-audio">
                            <div class="post_title">
                                <i class="fa fa-file-audio-o"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">Listen Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'podcast') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-podcast">
                            <div class="post_title">
                                <i class="fa fa-podcast"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">View Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'articles') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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

                <?php } else if ($format == 'gifs') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'photos') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="image-link">
                                <div class="feature_image">
                                    <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
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
                <?php } else { ?>
                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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
            wp_reset_postdata();
        } else {
            
        }
    } if ($order == 'newest') {
        $isotope_oldest = new WP_Query(
                array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 9,
            'paged' => $paged,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $prefix . 'checkbox',
                    'value' => 'on',
                    'compare' => 'NOT EXISTS',
                ),
            )
                )
        );
        if ($isotope_oldest->have_posts()) {
            while ($isotope_oldest->have_posts()) : $isotope_oldest->the_post();
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

                if ($format == 'quotes') {
                    ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-quote">
                            <div class="post_title">
                                <i class="fa fa-comment"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                                <i class="fa fa-quote-left"></i>
                                <h2><?php echo strip_tags(get_the_content()) ?></h2>
                                <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'movie') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-video">
                            <div class="post_title">
                                <i class="fa fa-film"></i>
                                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read_more">Watch Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'audio') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-audio">
                            <div class="post_title">
                                <i class="fa fa-file-audio-o"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">Listen Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'podcast') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-podcast">
                            <div class="post_title">
                                <i class="fa fa-podcast"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">View Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'articles') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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

                <?php } else if ($format == 'gifs') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'photos') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="image-link">
                                <div class="feature_image">
                                    <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
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
                <?php } else { ?>
                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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
            wp_reset_postdata();
        } else {
            
        }
    } if ($order == 'popular') {
        $isotope_oldest = new WP_Query(
                array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'posts_per_page' => 9,
            'paged' => $paged,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $prefix . 'checkbox',
                    'value' => 'on',
                    'compare' => 'NOT EXISTS',
                ),
            )
                )
        );
        if ($isotope_oldest->have_posts()) {
            while ($isotope_oldest->have_posts()) : $isotope_oldest->the_post();
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

                if ($format == 'quotes') {
                    ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-quote">
                            <div class="post_title">
                                <i class="fa fa-comment"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                                <i class="fa fa-quote-left"></i>
                                <h2><?php echo strip_tags(get_the_content()) ?></h2>
                                <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'movie') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-video">
                            <div class="post_title">
                                <i class="fa fa-film"></i>
                                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read_more">Watch Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'audio') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-audio">
                            <div class="post_title">
                                <i class="fa fa-file-audio-o"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">Listen Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'podcast') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-podcast">
                            <div class="post_title">
                                <i class="fa fa-podcast"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">View Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'articles') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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

                <?php } else if ($format == 'gifs') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'photos') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="image-link">
                                <div class="feature_image">
                                    <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
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
                <?php } else { ?>
                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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
            wp_reset_postdata();
        } else {
            
        }
    } else if ($order == 'random') {
        header("Content-Type: text/html");
        $dont_duplicate = array();
        $duplicatePosts = get_option('duplicatePosts');

        $isotope = new WP_Query(
                array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 9,
            'paged' => $paged,
            //'post__not_in' => $duplicatePosts,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $prefix . 'checkbox',
                    'value' => 'on',
                    'compare' => 'NOT EXISTS',
                ),
            )
                )
        );
        if ($isotope->have_posts()) {
            $original_posts = (array) $isotope->posts;
            shuffle($original_posts);
            $shuffled_posts = json_decode(json_encode($original_posts), FALSE);
            $isotope->posts = $shuffled_posts;
            $postCounter = 0;
            while ($isotope->have_posts()) : $isotope->the_post();
                $dont_duplicate[$postCounter] = $post->ID;
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

                if ($format == 'quotes') {
                    ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-quote">
                            <div class="post_title">
                                <i class="fa fa-comment"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                                <i class="fa fa-quote-left"></i>
                                <h2><?php echo strip_tags(get_the_content()) ?></h2>
                                <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'movie') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-video">
                            <div class="post_title">
                                <i class="fa fa-film"></i>
                                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo $src; ?>" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read_more">Watch Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'audio') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-audio">
                            <div class="post_title">
                                <i class="fa fa-file-audio-o"></i>
                                <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="read_more">Listen Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'podcast') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article class="format-podcast">
                            <div class="post_title">
                                <i class="fa fa-podcast"></i>
                                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>">
                                <div class="feature_image">
                                    <img src="<?php echo get_bloginfo('template_url') ?>/timthumb.php?src=<?php echo $src; ?>&amp;h=350&amp;w=350&amp;zc=1" alt="<?php echo get_the_title() ?>"/>
                                </div>
                            </a>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read_more">View Now</a>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'articles') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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

                            <?php } else if ($format == 'gifs') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><?php echo get_the_title() ?></h2>
                            </div>
                            <div class="feature_image">
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            </div>
                            <div class="post_content">
                                <p><?php echo wp_trim_words(get_the_content(), 30, '...') ?></p>
                            </div>
                            <div class="post_categories">
                    <?php
                    foreach ($categories as $category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                    }
                    ?>
                            </div>
                        </article>
                    </div>

                <?php } else if ($format == 'photos') { ?>

                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="image-link">
                                <div class="feature_image">
                                    <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
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
                            <?php } else if ($format == 'misc') { ?>
                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
                        <article>
                            <div class="post_title">
                                <i class="fa fa-picture-o"></i>
                                <h2><a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?></a></h2>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="image-link">
                                <div class="feature_image">
                                    <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
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
                            <?php } else { ?>
                    <div class="col-md-4 col-sm-12 col-xs-12 padding-right <?php echo $filterCat; ?>">
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
                $postCounter++;
            endwhile;
            update_option('duplicatePosts', $dont_duplicate);
            wp_reset_postdata();
        } else {
            
        }
    }
    die();
}

add_action('wp_ajax_nopriv_more_post_ajax', 'more_post_ajax');
add_action('wp_ajax_more_post_ajax', 'more_post_ajax');

// function add_my_orderby_filter( $query ) {
//     if ( $query->is_home ) {
//         add_filter('posts_orderby', 'orderby_last_modified');
//     }
// }
// add_action( 'pre_get_posts', 'add_my_orderby_filter' );



function search_by_title_only($search, $wp_query) {
    global $wpdb;
    if (empty($search)) {
        return $search; // skip processing - no search term in query
    }
    $q = $wp_query->query_vars;
    $n = !empty($q['exact']) ? '' : '%';
    $search = $searchand = '';
    foreach ((array) $q['search_terms'] as $term) {
        $term = esc_sql($wpdb->esc_like($term));
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
    if (!empty($search)) {
        $search = " AND ({$search}) ";
        if (!is_user_logged_in())
            $search .= " AND ($wpdb->posts.post_password = '') ";
    }
    return $search;
}

add_filter('posts_search', 'search_by_title_only', 500, 2);

function set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        $count = 0;
        delete_post_meta($postID, $count_key);
        update_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

add_action('wp_head', 'set_post_views');

/**
 * Styles and Scripts Enqueue
 */
function add_isotope() {
    // Register CSS
    wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_register_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css');
    wp_register_style('style', get_template_directory_uri() . '/css/style.css');
    wp_register_style('ekko-lightbox', get_template_directory_uri() . '/css/ekko-lightbox.css');
    wp_register_style('nunito-google-font', 'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&display=swap');
    // Register JS
    wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('slick', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('isotope', get_template_directory_uri() . '/js/isotope.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('isotopeimagesloaded', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '5.2.4', true);
    wp_register_script('ekko-lightbox', get_template_directory_uri() . '/js/ekko-lightbox.min.js', array('jquery'), '5.2.4', true);
    // Call CSS
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('font-awesome');
    wp_enqueue_style('style');
    wp_enqueue_style('ekko-lightbox');
    wp_enqueue_style('nunito-google-font');
    // Register JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap');
    wp_enqueue_script('slick');
    wp_enqueue_script('isotope');
    wp_enqueue_script('isotopeimagesloaded');
    wp_enqueue_script('custom');
    wp_enqueue_script('ekko-lightbox');
    // For Ajax Call in JS file
    wp_localize_script('custom', 'ajax_call', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'site_url' => site_url(),
        'current_page' => '1',
        'oldest_current_page' => '1',
        'newest_current_page' => '1',
        'popular_current_page' => '1',
        'count' => get_option('countAllposts'),
        'count_popular' => get_option('countPopularPosts'),
            )
    );
}

add_action('wp_enqueue_scripts', 'add_isotope');

//add_filter('wp_insert_post_data', 'post_data_validator', '99');
function post_data_validator($data) {
    if ($data['post_type'] == 'post') {
        // If post data is invalid then
        $data['post_status'] = 'draft';
        add_filter('redirect_post_location', 'post_redirect_filter', '99');
    }
    return $data;
}

function post_redirect_filter($location) {
    remove_filter('redirect_post_location', __FILTER__, '99');
    return add_query_arg('format', 1, $location);
}

add_action('admin_notices', 'my_post_admin_notices');

function my_post_admin_notices() {
    if (!isset($_GET['format']))
        return;
    if ($_GET['format'] == '1') {
        $message = '<p><strong>You must select a format to publish your post. Your Post is saved as draft but it can not be published.</strong></p>';
    }
    echo '<div id="notice" class="error"><p>' . $message . '</p></div>';
}
