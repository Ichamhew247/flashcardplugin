<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Flashcard_Display
{
    public static function render_flashcards_by_category($category_id, $category_name)
    {
        global $wpdb;
        $table_flashcards = $wpdb->prefix . 'flashcards';

        // ดึง Flashcards ที่เกี่ยวข้องกับ Category ID
        $flashcards = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_flashcards WHERE category_id = %d", $category_id)
        );

        ob_start();

        echo '<h2>Category: ' . esc_html($category_name) . '</h2>';
        echo '<div class="flashcard-container">';

        if (!empty($flashcards)) {
            foreach ($flashcards as $flashcard) {
                // Decode JSON ข้อมูลด้านหน้าและด้านหลัง
                $front_text = json_decode($flashcard->front_text, true);
                $back_text = json_decode($flashcard->back_text, true);

                echo '<div class="flashcard" onclick="toggleCard(this)">'; // ใช้ onclick เพื่อคลิกพลิกการ์ด
                echo '<div class="flashcard-front">';
                echo '<div class="flashcard-content">';

                if (!empty($flashcard->front_image)) {
                    echo '<img src="' . esc_url($flashcard->front_image) . '" alt="Front Image">';
                }

                echo '<h3>' . esc_html($front_text['line1']) . '</h3>';
                if (!empty($front_text['line2'])) {
                    echo '<p>' . esc_html($front_text['line2']) . '</p>';
                }
                echo '</div>'; // .flashcard-content
                echo '</div>'; // .flashcard-front

                echo '<div class="flashcard-back">';
                echo '<div class="flashcard-content">';

                if (!empty($flashcard->back_image)) {
                    echo '<img src="' . esc_url($flashcard->back_image) . '" alt="Back Image">';
                }

                echo '<h3>' . esc_html($back_text['line1']) . '</h3>';
                if (!empty($back_text['line2'])) {
                    echo '<p>' . esc_html($back_text['line2']) . '</p>';
                }
                echo '</div>'; // .flashcard-content
                echo '</div>'; // .flashcard-back
                echo '</div>'; // .flashcard

            }
        } else {
            echo '<p>No flashcards found for this category.</p>';
        }

        echo '</div>'; // .flashcard-container

        return ob_get_clean();
    }
}
