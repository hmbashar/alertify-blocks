<?php 
/**
 * Plugin Name: Alertify Blocks
 * Description: A collection of beautiful alert/notice blocks for the WordPress block editor.
 * Version: 1.0.0
 * Author: Md Abul Bashar
 * Text Domain: alertify-blocks
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALERTIFY_BLOCKS_VERSION', '1.0.0');
define('ALERTIFY_BLOCKS_DIR', plugin_dir_path(__FILE__));
define('ALERTIFY_BLOCKS_URL', plugin_dir_url(__FILE__));

require_once ALERTIFY_BLOCKS_DIR . 'includes/class-alertify-blocks-loader.php';

/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 */
function alertify_blocks_block_init() {
    // Initialize the loader
    $loader = new Alertify_Blocks_Loader();

    // Register blocks using manifest file
    if (function_exists('wp_register_block_types_from_metadata_collection')) {
        wp_register_block_types_from_metadata_collection(ALERTIFY_BLOCKS_DIR . 'blocks/build', ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php');
        return;
    }

    if (function_exists('wp_register_block_metadata_collection')) {
        wp_register_block_metadata_collection(ALERTIFY_BLOCKS_DIR . 'blocks/build', ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php');
    }

    $manifest_data = require ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php';
    foreach (array_keys($manifest_data) as $block_type) {
        register_block_type(ALERTIFY_BLOCKS_DIR . "blocks/build/{$block_type}");
    }
}
add_action('init', 'alertify_blocks_block_init');