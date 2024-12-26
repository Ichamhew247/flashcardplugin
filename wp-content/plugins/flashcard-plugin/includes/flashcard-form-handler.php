<?php

function handle_flashcard_form_submission()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
        global $wpdb;

        $table_flashcards = $wpdb->prefix . 'flashcards';

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

        $front_video = handle_video_upload('front_video', 5 * 1024 * 1024, 10); // จำกัดขนาด 5MB และความยาว 10 วินาที
        $back_video = handle_video_upload('back_video', 5 * 1024 * 1024, 10);

        // เพิ่มในฐานข้อมูล
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
add_action('init', 'handle_csv_upload');

function handle_csv_upload()
{
    if (isset($_POST['upload_csv']) && isset($_FILES['csv_file'])) {
        global $wpdb;

        $table_flashcards = $wpdb->prefix . 'flashcards';
        $file = $_FILES['csv_file']['tmp_name'];

        // Debug: ตรวจสอบว่ามีการส่งไฟล์หรือไม่
        if (empty($file)) {
            error_log('CSV Upload Error: No file uploaded.');
            wp_redirect(add_query_arg('flashcard_status', 'error_no_file', wp_get_referer()));
            exit;
        }

        if (($handle = fopen($file, 'r')) !== false) {
            $is_header = true; // ใช้ตัวแปรนี้เพื่อข้าม header
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Debug: ตรวจสอบข้อมูลแต่ละบรรทัด
                error_log('CSV Row: ' . print_r($data, true));

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

                    // Debug: ตรวจสอบค่าที่จะบันทึก
                    error_log('Inserting Row: ' . print_r([
                        'category_id' => $category_id,
                        'front_image' => $front_image,
                        'back_image' => $back_image,
                        'front_text' => $front_text,
                        'back_text' => $back_text,
                        'front_audio' => $front_audio,
                        'back_audio' => $back_audio,
                    ], true));

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
