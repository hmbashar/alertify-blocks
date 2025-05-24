<?php 
/**
 * Plugin Name: Alertify Blocks – Advanced Notification Blocks
 * Description: A collection of beautiful alert/notice blocks for the WordPress block editor.
 * Version: 1.0.0
 * Author: Md Abul Bashar
 * Text Domain: alertify-blocks
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 8.0
 * Tested up to: 6.8
 * 
 * @since 1.0.0
 * @author Md Abul Bashar
 * @copyright Copyright (c) 2025, Md Abul Bashar
 * @package ALERTIFY\Includes
 */

namespace ALERTIFY;

if (!defined('ABSPATH')) {
    exit;
}

define('ALERTIFY_BLOCKS_VERSION', '1.0.0');
define('ALERTIFY_BLOCKS_DIR', plugin_dir_path(__FILE__));
define('ALERTIFY_BLOCKS_URL', plugin_dir_url(__FILE__));

// Check if the class exists before loading
if (!class_exists('\ALERTIFY\Includes\Loader')) {
    require_once ALERTIFY_BLOCKS_DIR . 'includes/class-alertify-blocks-loader.php';
}

// Initialize the loader only if the class exists
if (class_exists('\ALERTIFY\Includes\Loader')) {
    Includes\Loader::get_instance();
} else {
    // Log error or show admin notice if the class failed to load
    add_action('admin_notices', function() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e('Alertify Blocks: Required class could not be loaded.', 'alertify-blocks'); ?></p>
        </div>
        <?php
    });
}