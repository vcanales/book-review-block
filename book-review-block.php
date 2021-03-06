<?php
/**
 * Plugin Name: Book Review Block
 * Plugin URI: https://github.com/donnapep/book-review-block
 * Description: A Gutenberg block to add book details and a star rating to a book review post.
 * Author: Donna Peplinskie
 * Author URI: https://donnapeplinskie.com
 * Version: 1.4.0
 * Text Domain: book-review-block
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

class Book_Review_Block {
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object   $instance   A single instance of this class.
	 */
	private static $instance;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug    The plugin slug.
	 */
	private $slug;

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url    The base URL path of this plugin.
	 */
	private $url;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Registers the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    object    A single instance of this class.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new Book_Review_Block();
		}
	}

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function __construct() {
		$this->version = '1.4.0';
		$this->slug    = 'book-review-block';
		$this->url     = untrailingslashit( plugins_url( '/', __FILE__ ) );

		add_action( 'init', array( $this, 'init_block' ) );
	}

	/**
	 * Initializes the block.
	 *
	 * @since  1.1.2
	 * @access public
	 */
	public function init_block() {
		if ( function_exists( 'register_block_type' ) ) {
			register_block_type( 'book-review-block/book-review', array(
				'editor_script' => $this->slug,
				'editor_style'  => $this->slug . '-editor',
				'render_callback' => array( $this, 'render_book_review' ),
				'style' => $this->slug,
			) );

			add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}

		$this->register_meta_field( 'book_review_cover_url' );
		$this->register_meta_field( 'book_review_title' );
		$this->register_meta_field( 'book_review_series' );
		$this->register_meta_field( 'book_review_author' );
		$this->register_meta_field( 'book_review_genre' );
		$this->register_meta_field( 'book_review_publisher' );
		$this->register_meta_field( 'book_review_release_date' );
		$this->register_meta_field( 'book_review_format' );
		$this->register_meta_field( 'book_review_pages' );
		$this->register_meta_field( 'book_review_source' );
		$this->register_meta_field( 'book_review_rating' );
		$this->register_meta_field( 'book_review_summary' );
	}

		/**
	 * Enqueues block assets for use within Gutenberg.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_block_editor_assets() {
		$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

		// Scripts
		wp_enqueue_script(
			$this->slug,
			$this->url . '/build/index.js',
			array( 'wp-blocks', 'wp-components', 'wp-editor' ),
			$asset_file['version']
		);

		// Styles
		wp_enqueue_style(
			$this->slug . '-editor',
			$this->url . '/build/index.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);
	}

	/**
	 * Enqueues block assets for use within Gutenberg, as well as on the front-end.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_block_assets() {
		// Styles
		wp_enqueue_style(
			$this->slug,
			$this->url . '/build/style-index.css',
			array(),
			$this->version
		);
	}

	/**
	 * Renders the book review block.
	 *
	 * @since  1.1.2
	 * @param  array  $attributes Block attributes.
	 * @param  string $content    Block inner content.
	 * @return string Markup.
	 * @access public
	 */
	public function render_book_review( $attributes, $content ) {
		if ( ! empty( $content ) ) {
			return $content;
		}

		if ( in_the_loop() ) {
			$title = $this->get_post_meta( 'book_review_title' );
			$cover_url = $this->get_post_meta( 'book_review_cover_url' );
			$series = $this->get_post_meta( 'book_review_series' );
			$author = $this->get_post_meta( 'book_review_author' );
			$genre = $this->get_post_meta( 'book_review_genre' );
			$publisher = $this->get_post_meta( 'book_review_publisher' );
			$release_date = $this->get_post_meta( 'book_review_release_date' );
			$format = $this->get_post_meta( 'book_review_format' );
			$pages = $this->get_post_meta( 'book_review_pages' );
			$source = $this->get_post_meta( 'book_review_source' );
			$summary = $this->get_post_meta( 'book_review_summary' );
			$rating_html = array_map( array( $this, 'get_rating_html' ), array( 1, 2, 3, 4, 5 ) );

			if ( isset( $attributes ) && isset( $attributes['backgroundColor'] ) ) {
				$background_color = $attributes['backgroundColor'];
			} else {
				$background_color = '';
			}

			ob_start();
			include( 'partials/book-review.php' );
			return ob_get_clean();
		}
	}

	/**
	 * Registers a meta field.
	 *
	 * @since  1.1.2
	 * @param  string $meta_key   Meta field key.
	 * @param  bool   $is_integer true if the value of the meta field is an integer, false otherwise.
	 * @access private
	 */
	private function register_meta_field( $meta_key ) {
		register_meta( 'post', $meta_key, array(
			'show_in_rest' => true,
			'single' => true,
		) );
	}

	/**
	 * Retrieves post meta field.
	 *
	 * @since  1.1.2
	 * @param  string $meta_key Meta field key.
	 * @access private
	 */
	private function get_post_meta( $meta_key ) {
		return apply_filters(
			$meta_key,
			get_post_meta( get_the_ID(), $meta_key, true ),
			get_the_ID()
		);
	}

	/**
	 * Gets the HTML for the rating.
	 *
	 * @since  1.1.2
	 * @param  integer $rating Rating to compare to.
	 * @access private
	 */
	private function get_rating_html( $rating ) {
		$current_rating = floatval( $this->get_post_meta( 'book_review_rating' ) );
		$classname_whole = ( $current_rating >= ( $rating - 0.5 ) ) ? '' : 'is-rating-unfilled';
		$classname_half  = ( $current_rating >= $rating ) ? '' : 'is-rating-unfilled';

		$html = "<span>" .
			"<span>" .
				"<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>" .
					"<path class='{$classname_whole}' fill='#eba845' stroke='#eba845' d='M12,17.3l6.2,3.7l-1.6-7L22,9.2l-7.2-0.6L12,2L9.2,8.6L2,9.2L7.5,14l-1.6,7L12,17.3z' />" .
				"</svg>" .
			"</span>" .
			"<span>" .
				"<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>" .
					"<path class='{$classname_half}' fill='#eba845' stroke='#eba845' d='M12,17.3l6.2,3.7l-1.6-7L22,9.2l-7.2-0.6L12,2L9.2,8.6L2,9.2L7.5,14l-1.6,7L12,17.3z' />" .
				"</svg>" .
			"</span>" .
		"</span>";

		return $html;
	}
}

Book_Review_Block::register();
