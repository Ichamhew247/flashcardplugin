<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Flashcard_Categories
{
    public static function register_category_shortcodes()
    {
        global $wpdb;
        $table_categories = $wpdb->prefix . 'categories';

        // ดึงชื่อ Categories จากฐานข้อมูล
        $categories = $wpdb->get_results("SELECT id, name FROM $table_categories");

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $shortcode_name = sanitize_title($category->name); // ชื่อ Shortcode

                // ลงทะเบียน Shortcode สำหรับแต่ละ Category
                add_shortcode($shortcode_name, function () use ($category) {
                    return Flashcard_Display::render_flashcards_by_category($category->id, $category->name);
                });
            }
        }
    }
}
