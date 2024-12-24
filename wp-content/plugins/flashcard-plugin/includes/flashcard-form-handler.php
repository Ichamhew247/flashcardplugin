<?php

/**
 * Handle file uploads and return the file URL or null.
 *
 * @param string $file_input_name The name attribute of the file input field in the form.
 * @return string|null The URL of the uploaded file, or null if no file was uploaded.
 */
function handle_flashcard_form_submission()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
        global $wpdb;

        $table_flashcards = $wpdb->prefix . 'flashcards';

        // ตรวจสอบและกรองข้อมูลจากฟอร์ม
        $category_id = intval($_POST['category_id']);
        $front_text = json_encode([
            'line1' => sanitize_text_field($_POST['front_text_line1']),
            'line2' => sanitize_text_field($_POST['front_text_line2']),
        ], JSON_UNESCAPED_UNICODE);

        $back_text = json_encode([
            'line1' => sanitize_text_field($_POST['back_text_line1']),
            'line2' => sanitize_text_field($_POST['back_text_line2']),
        ], JSON_UNESCAPED_UNICODE);

        $front_image = handle_file_upload('front_image');
        $back_image = handle_file_upload('back_image');
        $front_audio = handle_file_upload('front_audio');
        $back_audio = handle_file_upload('back_audio');

        // บันทึกข้อมูลลงฐานข้อมูล
        $result = $wpdb->insert(
            $table_flashcards,
            [
                'category_id' => $category_id,
                'front_text' => $front_text,
                'back_text' => $back_text,
                'front_image' => $front_image,
                'back_image' => $back_image,
                'front_audio' => $front_audio,
                'back_audio' => $back_audio,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            // ส่งพารามิเตอร์แจ้งเตือนข้อผิดพลาด
            wp_redirect(add_query_arg('flashcard_status', 'error', wp_get_referer()));
            exit;
        } else {
            // ส่งพารามิเตอร์แจ้งเตือนความสำเร็จ
            wp_redirect(add_query_arg('flashcard_status', 'success', wp_get_referer()));
            exit;
        }
    }
}
