<?php

/**
 * Handle file uploads and return the file URL or null.
 *
 * @param string $file_input_name The name attribute of the file input field in the form.
 * @return string|null The URL of the uploaded file, or null if no file was uploaded.
 */
function handle_file_upload($file_input_name)
{
    // ตรวจสอบไฟล์ที่อัปโหลด
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // โหลดฟังก์ชันการจัดการไฟล์ของ WordPress
    require_once ABSPATH . 'wp-admin/includes/file.php';

    // จัดการการอัปโหลด
    $upload = wp_handle_upload($_FILES[$file_input_name], ['test_form' => false]);

    if (isset($upload['error'])) {
        error_log('File upload error: ' . $upload['error']);
        return null;
    }

    return $upload['url'];
}
