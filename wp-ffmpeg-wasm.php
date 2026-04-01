<?php
/**
 * Plugin Name: FFmpeg WASM for WordPress
 * Plugin URI: https://github.com/WordPress/wp-ffmpeg-wasm
 * Description: Provides FFmpeg WASM for client-side media processing. Used by the block editor to convert animated GIFs to MP4/WebM videos during upload.
 * Version: 0.1.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: The WordPress Contributors
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-ffmpeg-wasm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Expose FFmpeg WASM asset URLs to the block editor.
 *
 * Sets window.__ffmpegWasmConfig on editor pages so the upload-media
 * package can locate the WASM binary and Emscripten JS glue without
 * relying on the import map.
 */
add_action( 'enqueue_block_editor_assets', 'wp_ffmpeg_wasm_enqueue_config' );

function wp_ffmpeg_wasm_enqueue_config() {
	$assets_url = plugin_dir_url( __FILE__ ) . 'assets/';

	wp_add_inline_script(
		'wp-block-editor',
		sprintf(
			'window.__ffmpegWasmConfig = { coreUrl: %s, wasmUrl: %s };',
			wp_json_encode( $assets_url . 'ffmpeg-core.js' ),
			wp_json_encode( $assets_url . 'ffmpeg-core.wasm' )
		),
		'before'
	);
}

/**
 * Register REST endpoint for mid-session URL discovery.
 *
 * When the plugin is installed and activated via REST API during an
 * editor session, the inline script above hasn't run yet. This endpoint
 * lets the editor fetch the WASM URLs after activation.
 */
add_action( 'rest_api_init', 'wp_ffmpeg_wasm_register_rest_route' );

function wp_ffmpeg_wasm_register_rest_route() {
	register_rest_route(
		'wp-ffmpeg-wasm/v1',
		'/config',
		array(
			'methods'             => 'GET',
			'callback'            => 'wp_ffmpeg_wasm_rest_config',
			'permission_callback' => function () {
				return current_user_can( 'upload_files' );
			},
		)
	);
}

function wp_ffmpeg_wasm_rest_config() {
	$assets_url = plugin_dir_url( __FILE__ ) . 'assets/';

	return array(
		'coreUrl' => $assets_url . 'ffmpeg-core.js',
		'wasmUrl' => $assets_url . 'ffmpeg-core.wasm',
	);
}
