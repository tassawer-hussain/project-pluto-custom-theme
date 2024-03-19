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

// remove version from head
remove_action('wp_head', 'wp_generator');

// remove version from rss
add_filter('the_generator', '__return_empty_string');

// remove version from scripts and styles
function shapeSpace_remove_version_scripts_styles($src) {
	if (strpos($src, 'ver=')) {
		$src = remove_query_arg('ver', $src);
	}
	return $src;
}
add_filter('style_loader_src', 'shapeSpace_remove_version_scripts_styles', 9999);
add_filter('script_loader_src', 'shapeSpace_remove_version_scripts_styles', 9999);

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
 * Custom Taxonomy - Formats
 */
if (file_exists(get_stylesheet_directory() . '/inc/custom-taxonomy-formats.php')) {
    require_once ( get_stylesheet_directory() . '/inc/custom-taxonomy-formats.php' );
}

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
        echo ($featured) ? 'Yes' : 'No';
    }

    if ($column_name == 'total_view') {
        $counts = get_post_meta($post_ID, 'post_views_count');
        echo (isset($counts)) ? $counts[0] : 0;
    }
    if ($column_name == 'total_like') {
        $likes = get_post_meta($post_ID, 'post_likes_count');
        echo (isset($likes) && !empty($likes)) ? $likes[0] : 0;
    }
}
add_action('manage_posts_custom_column', 'featuedPostColumnValue', 10, 2);

/**
 * New Column Name
 */
function featuedPostColumn($defaults) {
    $defaults['featured'] = 'Featured';
    $defaults['total_view'] = 'Views';
    $defaults['total_like'] = 'Likes';
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

/*
 * Helper function to retrieve the posts accordingly
 */
function th_get_relevant_posts($_paged, $queryArgs) {
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 9,
        'paged' => $_paged,
        'orderby' => $queryArgs['orderby'],
        'order' => $queryArgs['order'],
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'pluto_featured_checkbox',
                'value' => 'on',
                'compare' => 'NOT EXISTS',
            ),
        )
    );

    if ($queryArgs['meta_key'] != 'none') {
        $args['meta_key'] = $queryArgs['meta_key'];
    }
    
    if ($queryArgs['is_format'] != '') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'formats',
                'field' => 'term_id',
                'terms' => $queryArgs['is_format'],
            ),
        );
    }

    $all_posts_list = new WP_Query($args);
    return $all_posts_list;
}

/**
 * Load more posts using ajax
 */
function more_post_ajax() {
    global $post;
    $paged = $_POST['page'];
    $queryArgs = $_POST['queryArgs'];

    if (isset($queryArgs) && !empty($queryArgs)) {
        $isotope_items = th_get_relevant_posts($paged, $queryArgs);
    }
    
    if ($isotope_items->have_posts()) {
        
        if($queryArgs['is_random'] == 'yes') {
            $original_posts = (array) $isotope_items->posts;
            shuffle($original_posts);
            $shuffled_posts = json_decode(json_encode($original_posts), FALSE);
            $isotope_items->posts = $shuffled_posts;
        }
        
        while ($isotope_items->have_posts()) : $isotope_items->the_post();
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

            $css_class = '';
            $data_action = '';
            $likes_count = get_post_meta($post->ID, 'post_likes_count', true);
            $cookie_name = 'th_like_' . $post->ID;
            if (isset($_COOKIE[$cookie_name])) {
                $css_class = "has-voted";
                $data_action = "unvote";
            } else {
                $css_class = "has-not-voted";
                $data_action = "vote";
            }
			
            if ($format == 'quotes') { 
				$getPostContent = get_the_content();
				$postwithbreaks = wpautop( $getPostContent, true );
                $prefix = 'pluto_author_';
                $authorName = get_post_meta($post->ID, $prefix . 'author');
			?>
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
                    <article class="format-quote">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/quotation-w.png'; ?>" alt="" />
                            </div>
                            <h2><?php echo get_the_title() ?></h2>
                        </div>
                        <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                            <i class="fa fa-quote-left"></i>
                            <h2><?php echo $postwithbreaks; ?></h2>
                            <?php if (isset($authorName) && !empty($authorName)) { ?>
                                <span class="quote-author">- <?php echo $authorName[0]; ?></span>
                            <?php } ?>
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
                    <article class="format-video">
                        <div class="post_title">
                            <div class="icon">
                                <img class="post-format-img" src="<?php echo get_bloginfo('template_url') . '/images/movie-w.png'; ?>" alt="" />
                            </div>
                            <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
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
                        <a href="<?php the_permalink(); ?>" class="read_more">Watch Now</a>
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
                <div class="col-md-4 col-sm-12 col-xs-12 padding-right">
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
            <?php }
        endwhile;
        wp_reset_postdata();
    } else {
        echo "No post found";
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

add_action( 'pre_get_posts', function( $query ) {

    // Check that it is the query we want to change: front-end search query
    if( ! is_admin() && $query->is_search() ) {

        // Change the query parameters
        $query->set( 'posts_per_page', -1 );

    }

} );


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

//add_filter('posts_search', 'search_by_title_only', 500, 2);

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
    wp_register_style('nunito-google-font', 'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&display=swap');
    // Register JS
    wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('slick', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('isotope', get_template_directory_uri() . '/js/isotope.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('isotopeimagesloaded', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), '5.2.4', true);
    wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '5.2.4', true);
    // Call CSS
    wp_enqueue_style('bootstrap');
    wp_enqueue_style('font-awesome');
    wp_enqueue_style('style');
    wp_enqueue_style('nunito-google-font');
    // Register JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap');
    wp_enqueue_script('slick');
    wp_enqueue_script('isotope');
    wp_enqueue_script('isotopeimagesloaded');
    wp_enqueue_script('custom');
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

function handle_post_likes() {
    $post_id = $_POST['post_id'];
    $vote_action = $_POST['vote_action'];

    if ($post_id && $vote_action) {
        switch ($vote_action) {
            case 'vote':
                echo wp_send_json(array('status' => 200, 'message' => os_vote_do_vote($post_id)));
                break;
            case 'unvote':
                echo wp_send_json(array('status' => 200, 'message' => os_vote_do_unvote($post_id)));
                break;
        }
    } else {
        echo wp_send_json(array('status' => 422, 'message' => 'Invalid data supplied'));
    }
    wp_die();



    $likes_count = get_post_meta($post_id, 'post_likes_count', true);

    if (empty($likes_count)) {
        $likes_count = 0;
    }
    $likes_count = intval($likes_count);
    $likes_count++;

    update_post_meta($post_id, 'post_likes_count', $likes_count);

    echo $likes_count;
    die();
}

add_action('wp_ajax_nopriv_handle_post_likes', 'handle_post_likes');
add_action('wp_ajax_handle_post_likes', 'handle_post_likes');

function os_vote_do_vote($post_id) {

    $likes_count = get_post_meta($post_id, 'post_likes_count', true);
    if (empty($likes_count)) {
        $likes_count = 0;
    }
    $likes_count = intval($likes_count);
    $likes_count++;

    update_post_meta($post_id, 'post_likes_count', $likes_count);

    $cookie_expire_on = time() + 60 * 60 * 24 * 30;
    setcookie('th_like_' . $post_id, $post_id, $cookie_expire_on, '/');

    return 'voted';
}

function os_vote_do_unvote($post_id) {
    $likes_count = get_post_meta($post_id, 'post_likes_count', true);
    $likes_count = intval($likes_count);
    $likes_count--;

    update_post_meta($post_id, 'post_likes_count', $likes_count);

    setcookie('th_like_' . $post_id, $post_id, 1, '/');

    return 'unvoted';
}
