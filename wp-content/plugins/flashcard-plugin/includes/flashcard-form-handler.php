<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle form submission for adding flashcards.
 */
function handle_flashcard_submission()
{
    global $wpdb;

    $category = sanitize_text_field($_POST['category']);
    $front_text = json_encode([
        'line1' => sanitize_text_field($_POST['front_text_line1']),
        'line2' => sanitize_text_field($_POST['front_text_line2']),
    ]);
    $back_text = json_encode([
        'line1' => sanitize_text_field($_POST['back_text_line1']),
        'line2' => sanitize_text_field($_POST['back_text_line2']),
    ]);

    // Handle file uploads
    $front_image = handle_file_upload('front_image');
    $back_image = handle_file_upload('back_image');
    $front_audio = handle_file_upload('front_audio');
    $back_audio = handle_file_upload('back_audio');

    // Insert data into the database
    $table_name = $wpdb->prefix . 'flashcards';
    $wpdb->insert(
        $table_name,
        [
            'category' => $category,
            'front_image' => $front_image,
            'back_image' => $back_image,
            'front_text' => $front_text,
            'back_text' => $back_text,
            'front_audio' => $front_audio,
            'back_audio' => $back_audio,
            'created_at' => current_time('mysql'),
        ],
        ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
    );

    echo '<p>Flashcard successfully added!</p>';
}
