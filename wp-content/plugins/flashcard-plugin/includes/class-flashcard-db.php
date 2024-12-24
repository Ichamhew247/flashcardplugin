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

        // Character set and collation
        $charset_collate = $wpdb->get_charset_collate();

        // SQL for creating flashcards table
        $sql_flashcards = "CREATE TABLE IF NOT EXISTS $table_flashcards (
          id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    category ENUM('color', 'number', 'animal') DEFAULT NULL, -- หมวดหมู่ เช่น color, number, animal
    front_image VARCHAR(255) DEFAULT NULL,        -- URL ภาพด้านหน้า
    back_image VARCHAR(255) DEFAULT NULL,         -- URL ภาพด้านหลัง
    front_text JSON DEFAULT NULL,                 -- ข้อความด้านหน้า (หลายบรรทัดในรูป JSON)
    back_text JSON DEFAULT NULL,                  -- ข้อความด้านหลัง (หลายบรรทัดในรูป JSON)
    front_audio VARCHAR(255) DEFAULT NULL,        -- URL เสียงด้านหน้า
    back_audio VARCHAR(255) DEFAULT NULL,         -- URL เสียงด้านหลัง
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- เวลาสร้าง
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- เวลาปรับปรุง
    PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute SQL
        dbDelta($sql_flashcards);
    }

    /**
     * Insert mock data into the database.
     */
    public static function insert_mock_data()
    {
        global $wpdb;

        // Define table name
        $table_flashcards = $wpdb->prefix . 'flashcards';

        // Path to the mock data file
        $mock_data_path = FLASHCARD_PLUGIN_DIR_PATH . 'includes/mock-data.json';

        // Check if the file exists
        if (!file_exists($mock_data_path)) {
            return;
        }

        // Load and decode the mock data
        $mock_data = json_decode(file_get_contents($mock_data_path), true);

        if (empty($mock_data)) {
            return;
        }

        // Insert flashcards
        foreach ($mock_data as $flashcard) {
            $wpdb->insert(
                $table_flashcards,
                [
                    'category' => $flashcard['category'],
                    'front_image' => $flashcard['front_image'],
                    'back_image' => $flashcard['back_image'],
                    'front_text' => json_encode($flashcard['front_text']), // แปลง JSON เป็นสตริง
                    'back_text' => json_encode($flashcard['back_text']),   // แปลง JSON เป็นสตริง
                    'front_audio' => $flashcard['front_audio'],
                    'back_audio' => $flashcard['back_audio'],
                    'created_at' => current_time('mysql'),
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );
        }
    }




    /**
     * Drop database tables during uninstall.
     */
    public static function drop_tables()
    {
        global $wpdb;

        // Define table name
        $table_flashcards = $wpdb->prefix . 'flashcards';

        // SQL for dropping table
        $sql_flashcards = "DROP TABLE IF EXISTS $table_flashcards;";

        // Execute SQL
        $wpdb->query($sql_flashcards);
    }
}
