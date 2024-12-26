<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function handle_video_upload($file_input_name, $max_file_size = 5 * 1024 * 1024, $max_duration = 10)
{
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    // ตรวจสอบขนาดไฟล์
    if ($_FILES[$file_input_name]['size'] > $max_file_size) {
        error_log('File upload error: File size exceeds limit.');
        return null;
    }

    // ตรวจสอบความยาววิดีโอ
    $file_path = $_FILES[$file_input_name]['tmp_name'];
    $duration = get_video_duration($file_path);

    if ($duration > $max_duration) {
        error_log('File upload error: Video duration exceeds limit.');
        return null;
    }

    // อัปโหลดไฟล์
    $upload = wp_handle_upload($_FILES[$file_input_name], ['test_form' => false]);

    if (isset($upload['error'])) {
        error_log('File upload error: ' . $upload['error']);
        return null;
    }

    return $upload['url'];
}

// ฟังก์ชันสำหรับตรวจสอบความยาววิดีโอโดยใช้ FFmpeg
function get_video_duration($file_path)
{
    $command = "ffmpeg -i " . escapeshellarg($file_path) . " 2>&1";
    $output = shell_exec($command);

    if (preg_match('/Duration: (\d{2}):(\d{2}):(\d{2})\.(\d+)/', $output, $matches)) {
        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $seconds = (int)$matches[3];
        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    return 0; // คืนค่าความยาวเป็น 0 หากไม่สามารถตรวจสอบได้
}
