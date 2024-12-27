<?php

/**
 * Class Flashcard_DB
 * Handles database operations for the Flashcard plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Flashcard_DB
{

    /**
     * Create necessary database tables.
     */
    public static function create_tables()
    {
        global $wpdb;

        // Define table name
        $table_flashcards = $wpdb->prefix . 'flashcards';
        $table_categories = $wpdb->prefix . 'categories';

        // Character set and collation
        $charset_collate = $wpdb->get_charset_collate();

        // SQL for creating categories table
        $sql_categories = "CREATE TABLE IF NOT EXISTS $table_categories (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // SQL for creating flashcards table
        $sql_flashcards = "CREATE TABLE IF NOT EXISTS $table_flashcards (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            category_id BIGINT(20) UNSIGNED DEFAULT NULL,
            front_text JSON DEFAULT NULL,
            back_text JSON DEFAULT NULL,
            front_image VARCHAR(255) DEFAULT NULL,
            back_image VARCHAR(255) DEFAULT NULL,
            front_audio VARCHAR(255) DEFAULT NULL,
            back_audio VARCHAR(255) DEFAULT NULL,
            front_video VARCHAR(255) DEFAULT NULL,
            back_video VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute SQL for categories
        dbDelta($sql_categories);

        // Execute SQL for flashcards
        dbDelta($sql_flashcards);
    }

    /**
     * Insert mock data into the database.
     */
    public static function insert_mock_data()
    {
        global $wpdb;

        // Define table names
        $table_categories = $wpdb->prefix . 'categories';
        $table_flashcards = $wpdb->prefix . 'flashcards';

        // Path to the mock data file
        $mock_data_path = FLASHCARD_PLUGIN_DIR_PATH . 'includes/mock-data.json';

        // Check if the file exists
        if (!file_exists($mock_data_path)) {
            error_log("Mock data file not found.");
            return;
        }

        // Load and decode the mock data
        $mock_data = json_decode(file_get_contents($mock_data_path), true);

        if (empty($mock_data)) {
            error_log("Mock data is empty.");
            return;
        }

        // Insert flashcards
        foreach ($mock_data['flashcards'] as $flashcard) {
            $category_id = intval($flashcard['category_id']); // ดึง Category ID
            $front_text = json_encode($flashcard['front_text'], JSON_UNESCAPED_UNICODE);
            $back_text = json_encode($flashcard['back_text'], JSON_UNESCAPED_UNICODE);

            // ตรวจสอบว่ามี flashcard นี้อยู่แล้วหรือไม่
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_flashcards WHERE category_id = %d AND front_text = %s AND back_text = %s",
                $category_id,
                $front_text,
                $back_text
            ));

            if ($exists) {
                error_log("Flashcard already exists for Category ID $category_id. Skipping...");
                continue; // ข้ามการแทรก flashcard ที่ซ้ำ
            }

            // แทรกข้อมูลใหม่
            $result = $wpdb->insert(
                $table_flashcards,
                [
                    'category_id' => $category_id,
                    'front_image' => sanitize_text_field($flashcard['front_image']),
                    'back_image' => sanitize_text_field($flashcard['back_image']),
                    'front_text' => $front_text,
                    'back_text' => $back_text,
                    'front_audio' => sanitize_text_field($flashcard['front_audio']),
                    'back_audio' => sanitize_text_field($flashcard['back_audio']),
                    'front_video' => sanitize_text_field($flashcard['front_video']),
                    'back_video' => sanitize_text_field($flashcard['back_video']),
                    'created_at' => current_time('mysql'),
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );

            if ($result === false) {
                error_log("Failed to insert flashcard: " . $wpdb->last_error);
            }
        }


        // Insert flashcards
        foreach ($mock_data['flashcards'] as $flashcard) {
            $front_text = json_encode($flashcard['front_text'], JSON_UNESCAPED_UNICODE);
            $back_text = json_encode($flashcard['back_text'], JSON_UNESCAPED_UNICODE);

            $result = $wpdb->insert(
                $table_flashcards,
                [
                    'category_id' => $flashcard['category_id'],
                    'front_image' => sanitize_text_field($flashcard['front_image']),
                    'back_image' => sanitize_text_field($flashcard['back_image']),
                    'front_text' => $front_text,
                    'back_text' => $back_text,
                    'front_audio' => sanitize_text_field($flashcard['front_audio']),
                    'back_audio' => sanitize_text_field($flashcard['back_audio']),
                    'front_video' => sanitize_text_field($flashcard['front_video']), // เพิ่มวิดีโอ
                    'back_video' => sanitize_text_field($flashcard['back_video']), // เพิ่มวิดีโอ
                    'created_at' => current_time('mysql'),
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );

            if ($result === false) {
                error_log("Failed to insert flashcard: " . $wpdb->last_error);
            }
        }
    }

    /**
     * Drop database tables during uninstall.
     */
    public static function drop_tables()
    {
        global $wpdb;

        // Define table names
        $table_flashcards = $wpdb->prefix . 'flashcards';
        $table_categories = $wpdb->prefix . 'categories';

        // SQL for dropping tables
        $sql_flashcards = "DROP TABLE IF EXISTS $table_flashcards;";
        $sql_categories = "DROP TABLE IF EXISTS $table_categories;";

        // Execute SQL
        $wpdb->query($sql_flashcards);
        $wpdb->query($sql_categories);
    }
}
