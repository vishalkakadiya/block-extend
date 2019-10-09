<?php

namespace XWP\BlockExtend;

/**
 * Plugin Router.
 */
class BlockExtendPlugin {

	/**
	 * Plugin interface.
	 *
	 * @var XWP\BlockExtend\Plugin
	 */
	protected $plugin;

	/**
	 * Setup the plugin instance.
	 *
	 * @param XWP\BlockExtend\Plugin $plugin Instance of the plugin abstraction.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );

		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Load our block assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'xwp-block-extend-js',
			$this->plugin->asset_url( 'js/dist/editor.js' ),
			[
				'lodash',
				'react',
				'wp-block-editor',
			],
			$this->plugin->asset_version()
		);
	}

	/**
	 * Register block.
	 */
	public function register_block() {

		register_block_type(
			'vk/amp-validate-block',
			[
				'render_callback' => [ $this, 'render_block' ],
			]
		);
	}

	/**
	 * Render block output on front-end.
	 *
	 * @param array $attributes Block attributes.
	 * @param string $content Block content.
	 *
	 * @return mixed|string|void
	 */
	public function render_block( $attributes, $content ) {

		if ( ! class_exists( 'AMP_Theme_Support' ) ) {
			return __( 'AMP plugin needed in this block to load AMP errors!', 'vk-amp-validation-block' );
		}

		$query = new \WP_Query(
			[
				'post_type'              => 'amp_validated_url',
				'post_status'            => 'publish',
				'no_rows_found'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
			]
		);

		$total_validated_urls = 0;
		if ( ! empty( $query->posts ) && is_array( $query->posts ) ) {
			$total_validated_urls = count( $query->posts );
		}

		// Reset global query.
		wp_reset_postdata();

		$markup = sprintf( '<h3>%s</h3>', __( 'AMP Validation Statistics', 'vk-amp-validation-block' ) );

		$markup .= sprintf(
			'<p>%1$s %2$d %3$s</p>',
			__( 'There are', 'vk-amp-validation-block' ),
			$total_validated_urls,
			__( 'validated URLs.', 'vk-amp-validation-block' )
		);

		$errors_count = wp_count_terms( 'amp_validation_error' );

		if ( ! is_wp_error( $errors_count ) && ! empty( $errors_count ) ) {

			$markup .= sprintf(
				'<p>%1$s %2$d %3$s</p>',
				__( 'There are ', 'vk-amp-validation-block' ),
				$errors_count,
				__( 'validation errors.', 'vk-amp-validation-block' )
			);
		}

//		if ( true === $attributes['showMode'] ) {

			$mode = \AMP_Theme_Support::get_support_mode();

			$markup .= sprintf(
				'<p>%1$s: <span class="vk-amp-capitalize">%2$s</span></p>',
				__( 'Website Mode', 'vk-amp-validation-block' ),
				$mode
			);
//		}

		return $markup;
	}
}
