=== FFmpeg WASM for WordPress ===
Contributors: wordpressdotorg
Tags: media, video, gif, ffmpeg, wasm
Requires at least: 6.7
Tested up to: 7.1
Stable tag: 0.1.0
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides FFmpeg WASM for client-side media processing in the block editor.

== Description ==

This plugin provides the FFmpeg WebAssembly binary that enables the block
editor to convert animated GIFs to MP4/WebM videos during upload. This
dramatically reduces file sizes (5-10x smaller) while preserving the
GIF-like playback experience.

The plugin is invisible infrastructure — it has no admin UI or settings.
It is automatically installed by the block editor when needed.

**How it works:**

1. When you upload an animated GIF in the block editor, the editor detects it
2. If this plugin is installed, the GIF is converted to a looping MP4 video
3. The converted video autoplays, loops, and is muted — behaving identically to a GIF
4. File sizes are typically 5-10x smaller than the original GIF

**Requirements:**

* The site must serve pages with Cross-Origin Isolation headers for SharedArrayBuffer support
* The browser must support WebAssembly

== Installation ==

This plugin is typically installed automatically by the block editor when
an animated GIF upload is detected. You can also install it manually:

1. Upload the plugin files to `/wp-content/plugins/wp-ffmpeg-wasm/`
2. Activate the plugin through the 'Plugins' screen in WordPress

== Deployment ==

This plugin is automatically deployed to the WordPress.org plugin repository
when a new GitHub release is published, using the
[10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy) GitHub Action.

To enable deployment, add the following secrets to the GitHub repository settings:

* `SVN_USERNAME` – Your WordPress.org SVN username.
* `SVN_PASSWORD` – Your WordPress.org SVN password.

A ZIP file of the release is also automatically attached to the GitHub release.

== Changelog ==

= 0.1.0 =
* Initial release
* Includes FFmpeg core WASM binary for GIF-to-video conversion
