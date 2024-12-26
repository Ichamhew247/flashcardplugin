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

        // Fetch Flashcards related to Category ID
        $flashcards = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_flashcards WHERE category_id = %d", $category_id)
        );

        ob_start();

        echo '<h2>Category: ' . esc_html($category_name) . '</h2>';
        echo '<div class="flashcard-container">';

        if (!empty($flashcards)) {
            foreach ($flashcards as $flashcard) {
                // Decode JSON data for front and back text
                $front_text = json_decode($flashcard->front_text, true);
                $back_text = json_decode($flashcard->back_text, true);

                $front_audio = esc_url($flashcard->front_audio);
                $back_audio = esc_url($flashcard->back_audio);

                $front_video = esc_url($flashcard->front_video);
                $back_video = esc_url($flashcard->back_video);

                echo '<div class="flashcard is-flipped" onclick="toggleCard(this)">'; // Use onclick to flip the card
                echo '<div class="flashcard-front">';
                echo '<div class="flashcard-content">';

                // Display Front Image or Video
                if (!empty($flashcard->front_image)) {
                    echo '<img src="' . esc_url($flashcard->front_image) . '" alt="Front Image">';
                } elseif (!empty($front_video)) {
                    echo '<video controls>
                            <source src="' . $front_video . '" type="video/mp4">
                            Your browser does not support the video tag.
                          </video>';
                }

                echo '<h3>' . esc_html($front_text['line1']) . '</h3>';
                if (!empty($front_text['line2'])) {
                    echo '<p>' . esc_html($front_text['line2']) . '</p>';
                }

                // Front Audio Button
                if (!empty($front_audio)) {
                    echo '<button class="play-audio" data-audio="' . $front_audio . '">▶ Play Front</button>';
                }

                echo '</div>'; // .flashcard-content
                echo '</div>'; // .flashcard-front

                echo '<div class="flashcard-back">';
                echo '<div class="flashcard-content">';

                // Display Back Image or Video
                if (!empty($flashcard->back_image)) {
                    echo '<img src="' . esc_url($flashcard->back_image) . '" alt="Back Image">';
                } elseif (!empty($back_video)) {
                    echo '<video controls>
                            <source src="' . $back_video . '" type="video/mp4">
                            Your browser does not support the video tag.
                          </video>';
                }

                echo '<h3>' . esc_html($back_text['line1']) . '</h3>';
                if (!empty($back_text['line2'])) {
                    echo '<p>' . esc_html($back_text['line2']) . '</p>';
                }

                // Back Audio Button
                if (!empty($back_audio)) {
                    echo '<button class="play-audio" data-audio="' . $back_audio . '">▶ Play Back</button>';
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
