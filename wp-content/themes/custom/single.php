<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
get_header();
?>
<div class="col-md-12">
    <section id="archive_header">
        <div class="col-md-12">
            <div class="breadcrumb"><?php get_breadcrumb(); ?></div>
        </div>
    </section>
</div>


<div class="container">
<article class="single_post">
    <div class="single-post-content">
        <?php
        while (have_posts()) : the_post();
            echo '<div class="single_inner_padding">';
            set_post_views($post->ID);
            $catName = array();
            $categories = get_the_category($post->ID);
            $tags = get_the_tags($post->ID);
            $getFormat = get_the_terms($post->ID, 'formats');
            $format = $getFormat[0]->slug;
            $video = get_post_meta(get_the_ID(), 'pluto_video_' . 'url');
            $audio = get_post_meta(get_the_ID(), 'pluto_audio_' . 'url');
            if (has_post_thumbnail()) {
                $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                $src = $src[0];
            } else {
                //$src = get_bloginfo('template_url') . '/images/no-thumbnail.jpg';
            }
            
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

            if ($format == 'quotes') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article class="format-quote">
                        <div class="post_title">
                            <h2><?php the_title() ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="card-cat">
                            <?php foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                            </div>
                        </div>
                        <div class="post_content" style="background-image: url('<?php echo $src ?>');">
                            <h2><?php the_content(); ?></h2>
                            <span class="quote-author">- <?php echo get_the_author_meta('display_name', $post->post_author) ?></span>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'movies') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article class="format-video">
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php /* Tassawer: Commented the below code. No need to show the featured image in case of post format: video 
                              if(isset($src) && !empty($src)) { ?>
                              <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                              <br><br><br>
                              <?php } */ ?>
                            <?php if (isset($video) && !empty($video)) { ?>
                                <!-- <h2>Video</h2> -->
                                <div id="video-type-check" class="<?php echo get_the_ID(); ?>" video-ext-check="mp4">
                                    <?php echo do_shortcode('[arve id="postvideo-'.get_the_ID().'" url="'.$video[0].'" thumbnail="'.$src.'" controls="yes" controlslist="nodownload noremoteplayback"]'); ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="cinema_mode" id="cinema" data-toggle="modal" data-target="#videoPopup">
                            <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                            <p>Cinema Mode</p>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'audio') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article class="format-audio">
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                            <?php if (isset($audio) && !empty($audio)) { ?>
                                <h5>Audio</h5>
                                <audio controls controlsList="nodownload">
                                    <source src="<?php echo $audio[0]; ?>" type="audio/mpeg">
                                </audio>
                            <?php } ?>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'podcasts') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article class="format-podcast">
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                            <?php if (isset($video) && !empty($video)) { ?>
                                <h5>Video</h5>
                                <div id="video-type-check" class="<?php echo get_the_ID(); ?>" video-ext-check="mp4">
                                    <?php echo do_shortcode('[arve id="postvideo-'.get_the_ID().'" url="'.$video[0].'" thumbnail="'.$src.'" controls="yes" controlslist="nodownload noremoteplayback"]'); ?>
                                </div>
                                <div class="cinema_mode" id="cinema" data-toggle="modal" data-target="#videoPopup">
                                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                                    <p>Cinema Mode</p>
                                </div>
                            <?php } ?>
                            <?php if (isset($audio) && !empty($audio)) { ?>
                                <h5>Audio</h5>
                                <audio controls controlsList="nodownload">
                                    <source src="<?php echo $audio[0]; ?>" type="audio/mpeg">
                                </audio>
                            <?php } ?>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'articles') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'gifs') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'photos') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                        </div>
                        <?php
                        $gallery = get_post_meta(get_the_ID(), 'pluto_gallery_' . 'gallery');
                        if (isset($gallery) && !empty($gallery)) { ?>
                            <div class="gallery_images">
                                <h5>Gallery</h5>
                                <div class="row">
                                    <?php foreach ($gallery[0] as $key => $value) {
                                        echo '<div class="col-md-2 col-sm-4 col-xs-6 gallery-thumb-th"> <a href="' . $value . '" data-key="'.$key.'" data-toggle="lightbox-th" data-gallery="th-gallery">
                                                <img src="' . wp_get_attachment_image_src($key, 'thumbnail')[0] . '" class="img-fluid" alt="' . get_the_title() . '">
                                            </a></div>';
                                    } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else if ($format == 'misc') { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                            <?php if (isset($video) && !empty($video)) { ?>
                                <h5>Video</h5>
                                <div id="video-type-check" class="<?php echo get_the_ID(); ?>" video-ext-check="mp4">
                                    <?php echo do_shortcode('[arve id="postvideo-'.get_the_ID().'" url="'.$video[0].'" thumbnail="'.$src.'" controls="yes" controlslist="nodownload noremoteplayback"]'); ?>
                                </div>
                                <div class="cinema_mode" id="cinema" data-toggle="modal" data-target="#videoPopup">
                                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                                    <p>Cinema Mode</p>
                                </div>
                            <?php } ?>
                            <?php if (isset($audio) && !empty($audio)) { ?>
                                <h5>Audio</h5>
                                <audio controls controlsList="nodownload">
                                    <source src="<?php echo $audio[0]; ?>" type="audio/mpeg">
                                </audio>
                            <?php } ?>
                        </div>
                        <?php $gallery = get_post_meta(get_the_ID(), 'pluto_gallery_' . 'gallery');
                        if (isset($gallery) && !empty($gallery)) { ?>
                            <div class="gallery_images">
                                <h5>Gallery</h5>
                                <div class="row">
                                    <?php foreach ($gallery[0] as $key => $value) {
                                        echo '<div class="col-md-2 col-sm-4 col-xs-6 gallery-thumb-th"> <a href="' . $value . '" data-key="'.$key.'" data-toggle="lightbox-th" data-gallery="th-gallery"><img src="' . wp_get_attachment_image_src($key, 'thumbnail')[0] . '" class="img-fluid" alt="' . get_the_title() . '"/></a></div>';
                                    } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } else { ?>
                <div class="col-md-12 <?php echo $filterCat; ?>">
                    <article>
                        <div class="post_title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="post_categories">
                            <div class="col-md-6" style="padding-left: 0;">
                                <div class="card-cat">
                                <?php foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php if (isset($tags) && !empty($tags)) { ?>
                            <div class="col-md-6" style="padding-right: 0;">
                                <div class="card-tags">Tags:
                                <?php foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . $tag->name . '</a>';
                                } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                                <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                                <?php if (intval($likes_count) > 0) { ?>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                <?php } ?>
                            </div> -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="feature_image">
                            <?php if (isset($src) && !empty($src)) { ?>
                                <img src="<?php echo $src ?>" alt="<?php echo get_the_title() ?>"/>
                            <?php } ?>
                        </div>
                        <div class="post_content">
                            <p><?php the_content(); ?></p>
                        </div>
                    </article>
                </div>
            <?php } ?>
                </div>
                <div class="clearfix"></div>
                <div class="post-like-meta">
                    <div class="post-like <?php echo $css_class; ?>" data-post_id="<?php echo $post->ID; ?>" data-action="<?php echo $data_action; ?>">
                        <span class="hearth-icon"><i class="fa fa-heart"></i></span>
                        <?php if (intval($likes_count) > 0) { ?>
                            <span class="like-count"><?php echo $likes_count; ?></span>
                        <?php } ?>
                    </div>
                </div>

        <?php endwhile; ?>
        <div class="clearfix"></div>
    </div>
</article>    
</div>

<div id="videoPopup" class="modal fade" role="dialog">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-dialog">
        <div class="modal-content-video" id="video-content">
            <?php echo do_shortcode('[arve id="modalvideo-'.get_the_ID().'" url="'.$video[0].'" thumbnail="'.$src.'" controls="yes" controlslist="nodownload noremoteplayback"]'); ?>
        </div>
    </div>
</div>

<?php
get_footer();
