<?php

/**
 * Plugin Name: Flashcard Plugin
 * Description: A custom plugin to manage flashcards and categories.
 * Version: 1.0
 * Author: Free language School
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define Plugin Paths
define('FLASHCARD_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__)); // Path to plugin directory
define('FLASHCARD_PLUGIN_URL', plugin_dir_url(__FILE__));       // URL to plugin directory

// Include Necessary Files
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-db.php';
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-shortcode.php';
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-category.php';
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-frontend.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-form-handler.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-file-handler.php';

// Activation Hook
register_activation_hook(__FILE__, 'flashcard_plugin_activate');
function flashcard_plugin_activate()
{
    require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-db.php';
    Flashcard_DB::create_tables(); // Create necessary tables
    Flashcard_DB::insert_mock_data(); // Insert mock data
}

// Deactivation Hook
register_deactivation_hook(__FILE__, 'flashcard_plugin_deactivate');
function flashcard_plugin_deactivate()
{
    // Add any deactivation cleanup code if needed
}

// Uninstall Hook
register_uninstall_hook(__FILE__, 'flashcard_plugin_uninstall');
function flashcard_plugin_uninstall()
{
    require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-db.php';
    Flashcard_DB::drop_tables(); // Drop all plugin-related tables
}

// Enqueue Scripts and Styles
add_action('wp_enqueue_scripts', 'flashcard_enqueue_scripts');
function flashcard_enqueue_scripts()
{
    wp_enqueue_style('flashcard-frontend-style', FLASHCARD_PLUGIN_URL . 'assets/css/frontend-style.css', array(), '1.0', 'all');
    wp_enqueue_script('flashcard-frontend-script', FLASHCARD_PLUGIN_URL . 'assets/js/frontend-script.js', array('jquery'), '1.0', true);
}

add_action('admin_enqueue_scripts', 'flashcard_enqueue_admin_scripts');
function flashcard_enqueue_admin_scripts()
{
    wp_enqueue_style('flashcard-admin-style', FLASHCARD_PLUGIN_URL . 'assets/css/admin-style.css', array(), '1.0', 'all');
    wp_enqueue_script('flashcard-admin-script', FLASHCARD_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), '1.0', true);
}

// Initialize Shortcodes
// add_action('init', 'flashcard_register_shortcodes');
// function flashcard_register_shortcodes() {
//     Flashcard_Shortcode::register_shortcodes();
// }

// Load Text Domain for Translation
add_action('plugins_loaded', 'flashcard_load_textdomain');
function flashcard_load_textdomain()
{
    load_plugin_textdomain('flashcard-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
