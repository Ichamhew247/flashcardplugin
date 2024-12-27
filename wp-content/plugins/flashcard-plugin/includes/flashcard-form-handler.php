<?php

function handle_flashcard_form_submission()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flashcard_action'])) {
        // ตรวจสอบ nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'flashcard_form_nonce')) {
            wp_die('Security check failed');
        }
        global $wpdb;

        $table_flashcards = $wpdb->prefix . 'flashcards';

        $category_id = intval($_POST['category_id']);

        function get_post_value($key, $default = '')
        {
            return isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $default;
        }

        $front_text = json_encode([
            'line1' => isset($_POST['front_text_line1']) ? sanitize_text_field($_POST['front_text_line1']) : '',
            'line2' => isset($_POST['front_text_line2']) ? sanitize_text_field($_POST['front_text_line2']) : '',
        ], JSON_UNESCAPED_UNICODE);

        $back_text = json_encode([
            'line1' => isset($_POST['back_text_line1']) ? sanitize_text_field($_POST['back_text_line1']) : '',
            'line2' => isset($_POST['back_text_line2']) ? sanitize_text_field($_POST['back_text_line2']) : '',
        ], JSON_UNESCAPED_UNICODE);


        // ตรวจสอบว่าเป็น URL หรืออัปโหลดไฟล์วิดีโอ
        $front_video_url = !empty($_POST['front_video_url']) ? esc_url_raw($_POST['front_video_url']) : null;
        $back_video_url = !empty($_POST['back_video_url']) ? esc_url_raw($_POST['back_video_url']) : null;

        // Ensure handle_file_upload function is defined or included
        if (!function_exists('handle_file_upload')) {
            require_once FLASHCARD_PLUGIN_DIR_PATH . 'includes/flashcard-file-handler.php';
        }

        $front_video = $front_video_url ?: handle_file_upload('front_video', 5 * 1024 * 1024, 10);
        $back_video = $back_video_url ?: handle_file_upload('back_video', 5 * 1024 * 1024, 10);

        $front_image = isset($_FILES['front_image']) ? handle_file_upload('front_image') : null;
        $back_image = isset($_FILES['back_image']) ? handle_file_upload('back_image') : null;

        $front_audio = handle_file_upload('front_audio');
        $back_audio = handle_file_upload('back_audio');

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
                'front_video' => $front_video,
                'back_video' => $back_video,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            wp_redirect(add_query_arg('flashcard_status', 'error', wp_get_referer()));
            exit;
        } else {
            wp_redirect(add_query_arg('flashcard_status', 'success', wp_get_referer()));
            exit;
        }
    }
}
add_action('init', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flashcard_action'])) {
        handle_flashcard_form_submission();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_csv'])) {
        handle_csv_upload();
    }
});


function handle_csv_upload()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_csv']) && isset($_FILES['csv_file'])) {
        global $wpdb;

        // ตรวจสอบสิทธิ์ผู้ใช้งาน
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'flashcard-plugin'));
        }

        $file = $_FILES['csv_file'];

        // ตรวจสอบชนิดไฟล์
        $allowed_mime_types = ['text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($file['type'], $allowed_mime_types)) {
            wp_redirect(add_query_arg('flashcard_status', 'error_invalid_file_type', wp_get_referer()));
            exit;
        }

        // ตรวจสอบขนาดไฟล์ (5MB)
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            wp_redirect(add_query_arg('flashcard_status', 'error_file_too_large', wp_get_referer()));
            exit;
        }

        // ดำเนินการอ่านไฟล์ CSV
        $file_path = $file['tmp_name'];

        if (($handle = fopen($file_path, 'r')) !== false) {
            $is_header = true; // ใช้ตัวแปรนี้เพื่อข้าม header
            $table_flashcards = $wpdb->prefix . 'flashcards';

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // ข้าม header (แถวแรก)
                if ($is_header) {
                    $is_header = false;
                    continue;
                }

                // ตรวจสอบว่าแต่ละแถวมีข้อมูลเพียงพอ
                if (count($data) >= 8) {
                    $category_id = intval($data[0]);
                    $front_image = sanitize_text_field($data[1]);
                    $back_image = sanitize_text_field($data[2]);
                    $front_text = json_encode(['line1' => sanitize_text_field($data[3]), 'line2' => ''], JSON_UNESCAPED_UNICODE);
                    $back_text = json_encode(['line1' => sanitize_text_field($data[4]), 'line2' => ''], JSON_UNESCAPED_UNICODE);
                    $front_audio = sanitize_text_field($data[5]);
                    $back_audio = sanitize_text_field($data[6]);

                    // ตรวจสอบว่ามีข้อมูลซ้ำในฐานข้อมูลหรือไม่
                    $exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $table_flashcards WHERE category_id = %d AND front_text = %s AND back_text = %s",
                        $category_id,
                        $front_text,
                        $back_text
                    ));

                    if ($exists) {
                        error_log('Duplicate entry skipped for category ID: ' . $category_id);
                        continue; // ข้ามข้อมูลซ้ำ
                    }

                    // บันทึกข้อมูลลงฐานข้อมูล
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

                    // Debug: ตรวจสอบผลการบันทึก
                    if ($result === false) {
                        error_log('Insert Error: ' . $wpdb->last_error);
                    }
                }
            }
            fclose($handle);

            // Redirect พร้อมสถานะสำเร็จ
            wp_redirect(add_query_arg('flashcard_status', 'success', wp_get_referer()));
            exit;
        } else {
            // Redirect พร้อมสถานะข้อผิดพลาด
            wp_redirect(add_query_arg('flashcard_status', 'error_open_file', wp_get_referer()));
            exit;
        }
    }
}
