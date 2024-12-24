<?php

/**
 * Handle file uploads and return the file URL or null.
 *
 * @param string $file_input_name The name attribute of the file input field in the form.
 * @return string|null The URL of the uploaded file, or null if no file was uploaded.
 */
function handle_file_upload($file_input_name)
{
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploaded_file = $_FILES[$file_input_name];
    $upload_overrides = ['test_form' => false];
    $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        return $movefile['url'];
    } else {
        return null;
    }
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle file uploads and return the file URL or null.
 *
 * @param string $file_input_name The name attribute of the file input field in the form.
 * @return string|null The URL of the uploaded file, or null if no file was uploaded.
 */
function handle_flashcard_submission()
{
    global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ตรวจสอบ category_id
        if (!isset($_POST['category_id']) || empty($_POST['category_id'])) {
            echo '<p>Error: Please select a category.</p>';
            return;
        }

        $category_id = intval($_POST['category_id']); // รับค่า category_id

        // จัดการข้อความด้านหน้าและด้านหลัง
        $front_text = json_encode([
            'line1' => sanitize_text_field($_POST['front_text_line1']),
            'line2' => sanitize_text_field($_POST['front_text_line2']),
        ], JSON_UNESCAPED_UNICODE);

        $back_text = json_encode([
            'line1' => sanitize_text_field($_POST['back_text_line1']),
            'line2' => sanitize_text_field($_POST['back_text_line2']),
        ], JSON_UNESCAPED_UNICODE);

        // จัดการการอัปโหลดไฟล์
        $front_image = handle_file_upload('front_image');
        $back_image = handle_file_upload('back_image');
        $front_audio = handle_file_upload('front_audio');
        $back_audio = handle_file_upload('back_audio');

        // บันทึกข้อมูลลงฐานข้อมูล
        $table_flashcards = $wpdb->prefix . 'flashcards';
        $result = $wpdb->insert(
            $table_flashcards,
            [
                'category_id' => $category_id,
                'front_image' => $front_image,
                'back_image' => $back_image,
                'front_text' => $front_text,
                'back_text' => $back_text,
                'front_audio' => $front_audio,
                'back_audio' => $back_audio,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            echo '<p>Error: Unable to save flashcard. ' . esc_html($wpdb->last_error) . '</p>';
        } else {
            echo '<p>Flashcard successfully saved!</p>';
        }
    }
}
