<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/CMB2/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

/**
 * Conditionally displays a metabox when used as a callback in the 'show_on_cb' cmb2_box parameter
 *
 * @param  CMB2 $cmb CMB2 object.
 *
 * @return bool      True if metabox should show
 */
function yourprefix_show_if_front_page( $cmb ) {
	// Don't show this metabox if it's not the front page template.
	if ( get_option( 'page_on_front' ) !== $cmb->object_id ) {
		return false;
	}
	return true;
}

/**
 * Conditionally displays a field when used as a callback in the 'show_on_cb' field parameter
 *
 * @param  CMB2_Field $field Field object.
 *
 * @return bool              True if metabox should show
 */
function yourprefix_hide_if_no_cats( $field ) {
	// Don't show this field if not in the cats category.
	if ( ! has_tag( 'cats', $field->object_id ) ) {
		return false;
	}
	return true;
}

/**
 * Manually render a field.
 *
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object.
 */
function yourprefix_render_row_cb( $field_args, $field ) {
	$classes     = $field->row_classes();
	$id          = $field->args( 'id' );
	$label       = $field->args( 'name' );
	$name        = $field->args( '_name' );
	$value       = $field->escaped_value();
	$description = $field->args( 'description' );
	?>
	<div class="custom-field-row <?php echo esc_attr( $classes ); ?>">
		<p><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></p>
		<p><input id="<?php echo esc_attr( $id ); ?>" type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo $value; ?>"/></p>
		<p class="description"><?php echo esc_html( $description ); ?></p>
	</div>
	<?php
}

/**
 * Manually render a field column display.
 *
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object.
 */
function yourprefix_display_text_small_column( $field_args, $field ) {
	?>
	<div class="custom-column-display <?php echo esc_attr( $field->row_classes() ); ?>">
		<p><?php echo $field->escaped_value(); ?></p>
		<p class="description"><?php echo esc_html( $field->args( 'description' ) ); ?></p>
	</div>
	<?php
}

/**
 * Conditionally displays a message if the $post_id is 2
 *
 * @param  array      $field_args Array of field parameters.
 * @param  CMB2_Field $field      Field object.
 */
function yourprefix_before_row_if_2( $field_args, $field ) {
	if ( 2 == $field->object_id ) {
		echo '<p>Testing <b>"before_row"</b> parameter (on $post_id 2)</p>';
	} else {
		echo '<p>Testing <b>"before_row"</b> parameter (<b>NOT</b> on $post_id 2)</p>';
	}
}

add_action( 'cmb2_admin_init', 'like_view_register_metabox' );
function like_view_register_metabox() {
	$prefix = 'pluto_view_';
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Like & View Count Metabox', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Total Views', 'cmb2' ),
		'desc' => esc_html__( 'How many time this post views so far', 'cmb2' ),
		'id'   => 'post_views_count',
		'type' => 'text_small',
                'default' => 0,
                'attributes'  => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
                    'type' => 'number',
                    'pattern' => '\d*',
                ),                
                'sanitization_cb' => 'absint',
                'escape_cb'       => 'absint',
            
	) );
        $cmb_demo->add_field( array(
		'name' => esc_html__( 'Total Likes', 'cmb2' ),
		'desc' => esc_html__( 'Total likes on this post', 'cmb2' ),
		'id'   => 'post_likes_count',
		'type' => 'text_small',
                'default' => 0,
                'attributes'  => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
                    'type' => 'number',
                    'pattern' => '\d*',
                ),                
                'sanitization_cb' => 'absint',
                'escape_cb'       => 'absint',
	) );
        
}

add_action( 'cmb2_admin_init', 'featured_register_metabox' );
function featured_register_metabox() {
	$prefix = 'pluto_featured_';
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Featured Metabox', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Featured Post?', 'cmb2' ),
		'desc' => esc_html__( '', 'cmb2' ),
		'id'   => $prefix . 'checkbox',
		'type' => 'checkbox',
	) );
	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Features Slide Image', 'cmb2' ),
		'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
		'id'   => $prefix . 'image',
		'type' => 'file',
	) );
	$cmb_demo->add_field( array(
		'name' => esc_html__( 'External Link', 'cmb2' ),
		'desc' => esc_html__( 'Enter external link.', 'cmb2' ),
		'id'   => $prefix . 'external_link',
		'type' => 'file',
	) );
}

add_action( 'cmb2_admin_init', 'video_register_metabox' );
function video_register_metabox() {
	$prefix = 'pluto_video_';
	$video = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Video Section', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$video->add_field( array(
		'name' => esc_html__( 'Video URL', 'cmb2' ),
		'desc' => esc_html__( 'Only video format e.g .mp4', 'cmb2' ),
		'id'   => $prefix . 'url',
		'type' => 'file',
	) );
}

add_action( 'cmb2_admin_init', 'audio_register_metabox' );
function audio_register_metabox() {
	$prefix = 'pluto_audio_';
	$video = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Audio Section', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$video->add_field( array(
		'name' => esc_html__( 'Audio URL', 'cmb2' ),
		'desc' => esc_html__( 'Only audio format e.g .mp3', 'cmb2' ),
		'id'   => $prefix . 'url',
		'type' => 'file',
	) );
}

add_action( 'cmb2_admin_init', 'gallery_register_metabox' );
function gallery_register_metabox() {
	$prefix = 'pluto_gallery_';
	$video = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Photo Gallery Section', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$video->add_field( array(
		'name' => esc_html__( 'Image Gallery', 'cmb2' ),
		'desc' => esc_html__( 'Only jpg/png format', 'cmb2' ),
		'id'   => $prefix . 'gallery',
		'type' => 'file_list',
	) );
}

add_action( 'cmb2_admin_init', 'autho_register_metabox' );
function autho_register_metabox() {
	$prefix = 'pluto_author_';
	$author = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Author Section', 'cmb2' ),
		'object_types'  => array( 'post' ),
	) );	
	$author->add_field( array(
		'name' => esc_html__( 'Author Name', 'cmb2' ),
		'desc' => esc_html__( 'Enter author name of the quote.', 'cmb2' ),
		'id'   => $prefix . 'author',
		'type' => 'text',
	) );
}