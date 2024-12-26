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
                $front_text = json_decode($flashcard->front_text, true);
                $back_text = json_decode($flashcard->back_text, true);

                echo '<div class="flashcard is-flipped" onclick="toggleCard(this)">';
                echo '<div class="flashcard-front">';
                echo '<div class="flashcard-content">';

                // แสดงผลภาพหรือวิดีโอด้านหน้า
                if (!empty($flashcard->front_image)) {
                    echo '<img src="' . esc_url($flashcard->front_image) . '" alt="Front Image">';
                } elseif (!empty($flashcard->front_video)) {
                    self::render_video($flashcard->front_video);
                }

                echo '<h3>' . esc_html($front_text['line1']) . '</h3>';
                if (!empty($front_text['line2'])) {
                    echo '<p>' . esc_html($front_text['line2']) . '</p>';
                }
                echo '</div>';
                echo '</div>';

                echo '<div class="flashcard-back">';
                echo '<div class="flashcard-content">';

                // แสดงผลภาพหรือวิดีโอด้านหลัง
                if (!empty($flashcard->back_image)) {
                    echo '<img src="' . esc_url($flashcard->back_image) . '" alt="Back Image">';
                } elseif (!empty($flashcard->back_video)) {
                    self::render_video($flashcard->back_video);
                }

                echo '<h3>' . esc_html($back_text['line1']) . '</h3>';
                if (!empty($back_text['line2'])) {
                    echo '<p>' . esc_html($back_text['line2']) . '</p>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No flashcards found for this category.</p>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    private static function render_video($video_url)
    {
        // ตรวจสอบว่าเป็น YouTube URL
        if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
            // แปลงลิงก์ YouTube เป็นลิงก์ที่ฝังได้
            if (strpos($video_url, 'youtu.be') !== false) {
                $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
            } elseif (preg_match('/v=([^&]+)/', $video_url, $matches)) {
                $video_id = $matches[1];
            }

            if (!empty($video_id)) {
                echo '<iframe width="100%" height="250" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            } else {
                echo '<p>Invalid YouTube URL</p>';
            }
        } elseif (strpos($video_url, 'vimeo.com') !== false) {
            // แปลงลิงก์ Vimeo เป็นลิงก์ที่ฝังได้
            $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
            if (!empty($video_id)) {
                echo '<iframe width="100%" height="250" src="https://player.vimeo.com/video/' . esc_attr($video_id) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
            } else {
                echo '<p>Invalid Vimeo URL</p>';
            }
        } else {
            // แสดงวิดีโอที่อัปโหลด
            echo '<video width="100%" height="250" controls>
                    <source src="' . esc_url($video_url) . '" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>';
        }
    }
}
