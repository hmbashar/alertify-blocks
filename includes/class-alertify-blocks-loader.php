<?php

if (!defined('ABSPATH')) {
    exit;
}

class Alertify_Blocks_Loader {

    public function __construct() {
        // Register block editor assets
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    public function register_blocks() {
        // Register Notice Box block
        register_block_type(
            ALERTIFY_BLOCKS_DIR . 'build/blocks/notice-box'
        );
    }

    public function enqueue_editor_assets() {
        // You can enqueue extra editor JS/CSS if needed in the future
    }
}
