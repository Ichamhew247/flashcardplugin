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
require_once plugin_dir_path(__FILE__) . 'includes/flashcard-form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/flashcard-file-handler.php';

require_once plugin_dir_path(__FILE__) . 'admin/flashcard-admin-categories.php';
require_once plugin_dir_path(__FILE__) . 'admin/flashcard-dashboard.php';

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



// Load Text Domain for Translation
add_action('plugins_loaded', 'flashcard_load_textdomain');
function flashcard_load_textdomain()
{
    load_plugin_textdomain('flashcard-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('admin_menu', 'flashcard_menu_setup');
function flashcard_menu_setup()
{
    // สร้างเมนูหลัก Flashcard_FLS
    add_menu_page(
        'Flashcard FLS',      // Page title
        'Flashcard_FLS',      // Menu title
        'manage_options',     // Capability
        'flashcard-fls',      // Menu slug
        'render_flashcard_dashboard', // Callback function for the main page
        'dashicons-welcome-learn-more', // Icon
        20                    // Position
    );

    // เพิ่มเมนูย่อย Manage Categories
    add_submenu_page(
        'flashcard-fls',        // Parent slug (เมนูหลัก)
        'Manage Categories',    // Page title
        'Manage Categories',    // Submenu title
        'manage_options',       // Capability
        'manage-categories',    // Submenu slug
        'render_manage_categories_page' // Callback function for Manage Categories
    );
}

add_action('init', ['Flashcard_Shortcode', 'register_shortcodes']);


add_action('wp_enqueue_scripts', 'enqueue_flashcard_styles');
function enqueue_flashcard_styles()
{
    wp_enqueue_style(
        'flashcard-form-style', // ชื่อของสไตล์
        plugin_dir_url(__FILE__) . 'assets/css/flashcard-form.css', // URL ของไฟล์ CSS
        [],
        '1.0' // เวอร์ชัน
    );
}

add_action('init', 'handle_flashcard_form_submission');
