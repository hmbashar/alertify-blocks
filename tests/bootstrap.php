<?php
/**
 * PHPUnit bootstrap for Alertify Blocks.
 *
 * Stubs out the WordPress functions and constants the plugin uses so the
 * class under test can be loaded without a full WordPress install.
 */

define( 'ABSPATH', '/tmp/fake-abspath/' );
define( 'ALERTIFY_BLOCKS_VERSION', '1.0.1' );
define( 'ALERTIFY_BLOCKS_DIR', dirname( __DIR__ ) . '/' );
define( 'ALERTIFY_BLOCKS_URL', 'https://example.com/wp-content/plugins/alertify-blocks/' );

// --- WordPress function stubs ---

if ( ! function_exists( 'add_action' ) ) {
    function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
}

if ( ! function_exists( 'register_block_type' ) ) {
    function register_block_type( $block_type, $args = [] ) {
        return true;
    }
}

if ( ! function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
    // Not defined by default — tests can define it when needed.
}

if ( ! function_exists( 'wp_register_block_metadata_collection' ) ) {
    // Not defined by default — tests can define it when needed.
}

// Autoload plugin classes.
require_once dirname( __DIR__ ) . '/includes/class-alertify-blocks-loader.php';
