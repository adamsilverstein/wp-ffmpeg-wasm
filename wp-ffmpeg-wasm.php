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
	$core_url = plugins_url( 'assets/ffmpeg-core.js', __FILE__ );

	wp_add_inline_script(
		'wp-block-editor',
		sprintf(
			'window.__ffmpegWasmConfig = { coreUrl: %s, wasmUrl: "" };',
			wp_json_encode( $core_url )
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
	return array(
		'coreUrl' => plugins_url( 'assets/ffmpeg-core.js', __FILE__ ),
		'wasmUrl' => '', // WASM is inlined in coreUrl as base64; this field is unused.
	);
}
