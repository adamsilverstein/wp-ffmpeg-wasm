/**
 * Build script for wp-ffmpeg-wasm.
 *
 * Inlines the FFmpeg WASM binary as a base64 data URL into the Emscripten
 * JS glue code, eliminating the need for separate .wasm file downloads
 * and avoiding issues with servers not serving .wasm with correct MIME types.
 *
 * Usage: node scripts/build.mjs
 */

import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const rootDir = join( __dirname, '..' );

const SEARCH = 'wasmBinaryFile="ffmpeg-core.wasm"';

// Read WASM binary and encode as base64.
const wasmPath = join( rootDir, 'src', 'ffmpeg-core.wasm' );
const wasmBuffer = readFileSync( wasmPath );
const base64 = wasmBuffer.toString( 'base64' );
const dataUrl = `data:application/wasm;base64,${ base64 }`;

// Read original Emscripten glue JS.
const corePath = join( rootDir, 'src', 'ffmpeg-core.js' );
const coreJs = readFileSync( corePath, 'utf-8' );

// Validate the search target exists exactly once.
const occurrences = coreJs.split( SEARCH ).length - 1;
if ( occurrences !== 1 ) {
	console.error(
		`Expected 1 occurrence of "${ SEARCH }", found ${ occurrences }`
	);
	process.exit( 1 );
}

// Replace the WASM filename with the inlined data URL.
const output = coreJs.replace( SEARCH, `wasmBinaryFile="${ dataUrl }"` );

// Write to assets/.
mkdirSync( join( rootDir, 'assets' ), { recursive: true } );
writeFileSync( join( rootDir, 'assets', 'ffmpeg-core.js' ), output );

const wasmMB = ( wasmBuffer.length / 1024 / 1024 ).toFixed( 1 );
const base64MB = ( base64.length / 1024 / 1024 ).toFixed( 1 );
const outputMB = ( output.length / 1024 / 1024 ).toFixed( 1 );

console.log( 'Build complete.' );
console.log( `  WASM size:   ${ wasmMB } MB` );
console.log( `  Base64 size: ${ base64MB } MB` );
console.log( `  Output JS:   ${ outputMB } MB` );
