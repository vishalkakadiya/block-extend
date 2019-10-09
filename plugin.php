<?php
/**
 * Plugin Name: VK AMP Validation Block
 * Description: Block to show total number of AMP errors.
 * Version: 0.1.0
 * Author: Vishal Kakadiya
 * Author URI: https://github.com/vishalkakadiya
 * Text Domain: vk-amp-validation-block
 */

namespace XWP\BlockExtend;

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

$block_extend_plugin = new BlockExtendPlugin( new Plugin( __FILE__ ) );

add_action( 'plugins_loaded', [ $block_extend_plugin, 'init' ] );
