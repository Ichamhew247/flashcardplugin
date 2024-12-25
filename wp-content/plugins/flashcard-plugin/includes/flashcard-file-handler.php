<?php

function handle_file_upload($file_input_name)
{
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $upload = wp_handle_upload($_FILES[$file_input_name], ['test_form' => false]);

    if (isset($upload['error'])) {
        error_log('File upload error: ' . $upload['error']);
        return null;
    }

    return $upload['url'];
}
