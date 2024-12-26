<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Flashcard_Shortcode
{
    public static function register_shortcodes()
    {
        // ลงทะเบียน Shortcode สำหรับแสดงแบบฟอร์มสร้าง Flashcard
        add_shortcode('flashcard_form', [__CLASS__, 'render_flashcard_form']);


        // ลงทะเบียน Shortcodes ไดนามิก
        Flashcard_Categories::register_category_shortcodes();
    }

    public static function render_flashcard_form()
    {
        global $wpdb;

        // Fetch categories from the database
        $table_categories = $wpdb->prefix . 'categories';
        $categories = $wpdb->get_results("SELECT id, name FROM $table_categories");

        // Check for status messages
        if (isset($_GET['flashcard_status'])) {
            if ($_GET['flashcard_status'] === 'success') {
                echo '<div class="notice success">Flashcard added successfully!</div>';
            } elseif ($_GET['flashcard_status'] === 'error') {
                echo '<div class="notice error">An error occurred while saving the flashcard. Please try again.</div>';
            }
        }

        // Include the CSV upload form
        $upload_csv_form_path = plugin_dir_path(__FILE__) . '../templates/upload-csv-form.php';
        if (file_exists($upload_csv_form_path)) {
            include $upload_csv_form_path;
        } else {
            echo '<p>Error: CSV upload form not found.</p>';
        }

        // Additional form rendering
?>
        <form method="post" enctype="multipart/form-data">
            <label for="category_id">Select Category:</label>
            <select name="category_id" id="category_id" required>
                <option value="">-- Select a Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo esc_attr($category->id); ?>">
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="front_text_line1">Front Text Line 1:</label>
            <input type="text" name="front_text_line1" id="front_text_line1" required><br>

            <label for="front_text_line2">Front Text Line 2:</label>
            <input type="text" name="front_text_line2"><br>

            <label for="back_text_line1">Back Text Line 1:</label>
            <input type="text" name="back_text_line1" id="back_text_line1" required><br>

            <label for="back_text_line2">Back Text Line 2:</label>
            <input type="text" name="back_text_line2"><br>

            <label for="front_image">Front Image:</label>
            <input type="file" name="front_image" id="front_image"><br>

            <label for="back_image">Back Image:</label>
            <input type="file" name="back_image" id="back_image"><br>

            <label for="front_audio">Front Audio:</label>
            <input type="file" name="front_audio" id="front_audio"><br>

            <label for="back_audio">Back Audio:</label>
            <input type="file" name="back_audio" id="back_audio"><br>

            <label for="front_video">Front Video:</label>
            <input type="file" name="front_video" id="front_video" accept="video/*"><br>

            <label for="back_video">Back Video:</label>
            <input type="file" name="back_video" id="back_video" accept="video/*"><br>

            <button type="submit" name="submit_flashcard">Submit</button>
        </form>

<?php
    }
}
