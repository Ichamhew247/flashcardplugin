<?php

/**
 * Plugin Name: Flashcard Plugin Mew3
 * Description: A custom plugin to manage flashcards and categories mew.
 * Version: 1.0
 * Author: Free Language School mew3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define Plugin Paths
define('FLASHCARD_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('FLASHCARD_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include Necessary Files
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-db.php';
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-categories.php';
include_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-display.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-form-handler.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-file-handler.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'admin/flashcard-admin-categories.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'admin/flashcard-dashboard.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-video-handler.php';
require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-shortcode.php';

// Activation Hook
register_activation_hook(__FILE__, 'flashcard_plugin_activate');
function flashcard_plugin_activate()
{
    global $wpdb;
    require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/class-flashcard-db.php';
    Flashcard_DB::create_tables();

    // ตรวจสอบก่อนแทรก mock data
    $categories_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}categories");
    if ($categories_count == 0) {
        Flashcard_DB::insert_mock_data();
    }
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
    Flashcard_DB::drop_tables();
}


// Admin Menu Setup
add_action('admin_menu', 'flashcard_menu_setup');
function flashcard_menu_setup()
{
    add_menu_page(
        'Flashcard FLS',
        'Flashcard_FLS',
        'manage_options',
        'flashcard-fls',
        'render_flashcard_dashboard',
        'dashicons-welcome-learn-more',
        20
    );

    add_submenu_page(
        'flashcard-fls',
        'Manage Categories',
        'Manage Categories',
        'manage_options',
        'manage-categories',
        'render_manage_categories_page'
    );
}

// Register Shortcodes
if (class_exists('Flashcard_Shortcode')) {
    add_action('init', ['Flashcard_Shortcode', 'register_shortcodes']);
} else {
    // error_log('Flashcard_Shortcode class not found');
}


// Enqueue Styles and Scripts
add_action('wp_enqueue_scripts', 'flashcard_enqueue_assets');
function flashcard_enqueue_assets()
{
    if (is_page() && has_shortcode(get_post()->post_content, 'flashcard_shortcode')) {
        wp_enqueue_style(
            'flashcard-style',
            plugins_url('assets/css/flashcard-style.css', __FILE__)
        );

        wp_enqueue_script(
            'flashcard-toggle-js',
            plugins_url('assets/js/flashcard-toggle.js', __FILE__),
            [],
            '1.0',
            true
        );
    }
}


// Handle Form Submission
add_action('init', 'handle_flashcard_form_submission');
