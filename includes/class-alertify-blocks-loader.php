<?php
namespace ALERTIFY\Includes;

/**
 * Class Loader
 * 
 * Handles the loading and initialization of Alertify blocks.
 * 
 * @package ALERTIFY\Includes
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Loader {
    /**
     * Instance of the loader.
     *
     * @var Loader
     */
    private static $instance = null;

    /**
     * Get the singleton instance.
     *
     * @return Loader
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the loader.
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks.
     *
     * @return void
     */
    private function init_hooks() {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    /**
     * Register all blocks.
     */
    public function register_blocks() {
        if (function_exists('wp_register_block_types_from_metadata_collection')) {
            wp_register_block_types_from_metadata_collection(
                ALERTIFY_BLOCKS_DIR . 'blocks/build',
                ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php'
            );
            return;
        }

        if (function_exists('wp_register_block_metadata_collection')) {
            wp_register_block_metadata_collection(
                ALERTIFY_BLOCKS_DIR . 'blocks/build',
                ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php'
            );
        }

        $manifest_data = require ALERTIFY_BLOCKS_DIR . 'blocks/build/blocks-manifest.php';
        foreach (array_keys($manifest_data) as $block_type) {
            register_block_type(ALERTIFY_BLOCKS_DIR . "blocks/build/{$block_type}");
        }
    }

    /**
     * Enqueue editor-specific assets.
     */
    public function enqueue_editor_assets() {
        // Add editor-specific styles or scripts here
        return;
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue_frontend_assets() {
        // Add frontend styles or scripts here
        return;
    }

    /**
     * Prevent cloning of the instance.
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Prevent unserializing of the instance.
     *
     * @return void
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}

