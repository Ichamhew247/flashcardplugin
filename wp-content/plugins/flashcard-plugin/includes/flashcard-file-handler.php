<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle file uploads and return the file URL or null.
 *
 * @param string $file_input_name The name attribute of the file input field in the form.
 * @return string|null The URL of the uploaded file, or null if no file was uploaded.
 */
function handle_file_upload($file_input_name)
{
    // Load WordPress file handling functions
    require_once ABSPATH . 'wp-admin/includes/file.php';

    // Check if file is uploaded and no errors occurred
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null; // No file uploaded or there was an error
    }

    // Use wp_handle_upload to upload the file
    $upload = wp_handle_upload($_FILES[$file_input_name], ['test_form' => false]);

    // Check if the upload was successful
    if (isset($upload['error'])) {
        return null; // Return null if there was an error
    }

    return $upload['url']; // Return the URL of the uploaded file
}
