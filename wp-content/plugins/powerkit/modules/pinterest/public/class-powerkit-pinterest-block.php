<?php
/**
 * Pinterest Board Block.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * Initialize Pinterest Board block.
 */
class Powerkit_Block_Pinterest_Board {

	/**
	 * Initialize
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'canvas_register_block_type', array( $this, 'register_block_type' ) );
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function init() {
		// Editor Scripts.
		wp_register_script(
			'powerkit-block-pinterest-board-editor-script',
			plugins_url( 'block/block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor', 'lodash', 'jquery', 'powerkit-pinterest' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'block/block.js' ),
			true
		);
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'          => 'canvas/pinterest-board',
			'title'         => esc_html__( 'Pinterest Board', 'powerkit' ),
			'description'   => '',
			'category'      => 'canvas',
			'keywords'      => array( 'pinterest', 'board' ),
			'icon'          => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M21 4.92857V19.0714C21 20.1362 20.1362 21 19.0714 21H9.20357C9.59732 20.3411 10.1036 19.3929 10.3045 18.6174C10.425 18.1554 10.9192 16.271 10.9192 16.271C11.2406 16.8857 12.1808 17.404 13.1812 17.404C16.1585 17.404 18.3 14.6679 18.3 11.2688C18.3 8.01027 15.6402 5.57143 12.217 5.57143C7.95804 5.57143 5.7 8.42813 5.7 11.542C5.7 12.9884 6.47143 14.7884 7.70089 15.3629C7.88973 15.4513 7.98616 15.4112 8.03036 15.2304C8.0625 15.0937 8.23125 14.4228 8.30357 14.1134C8.32768 14.0129 8.31563 13.9286 8.23527 13.8321C7.82946 13.3379 7.5 12.4299 7.5 11.5821C7.5 9.40446 9.14732 7.29911 11.9558 7.29911C14.3786 7.29911 16.0781 8.95045 16.0781 11.3129C16.0781 13.9808 14.7321 15.829 12.9763 15.829C12.008 15.829 11.2848 15.0295 11.5138 14.0451C11.7911 12.8719 12.3295 11.6063 12.3295 10.7585C12.3295 8.62902 9.29598 8.92232 9.29598 11.7629C9.29598 12.6348 9.58929 13.2295 9.58929 13.2295C8.32768 18.5652 8.13884 18.6335 8.4 20.9679L8.48839 21H4.92857C3.86384 21 3 20.1362 3 19.0714V4.92857C3 3.86384 3.86384 3 4.92857 3H19.0714C20.1362 3 21 3.86384 21 4.92857Z" />
				</svg>
			',
			'supports'      => array(
				'className'      => true,
				'anchor'         => true,
				'html'           => false,
				'canvasSpacings' => true,
			),
			'styles'        => array(),
			'location'      => array(),

			'sections'      => array(
				'general' => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
			),
			'layouts'       => array(),

			// Set fields just for add block attributes.
			// Editor render for this block is custom JSX
			// so we don't need to render fields automatically.
			'fields'        => array(
				array(
					'key'     => 'href',
					'label'   => esc_html__( 'Pinterest Board URL', 'powerkit' ),
					'type'    => 'text',
					'section' => 'general',
					'default' => '',
				),
			),
			'template'      => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_script' => 'powerkit-block-pinterest-board-editor-script',
		);

		return $blocks;
	}
}

new Powerkit_Block_Pinterest_Board();
