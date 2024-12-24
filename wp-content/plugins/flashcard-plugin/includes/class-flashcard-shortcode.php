<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class Flashcard_Shortcode
{
    public static function register_shortcodes()
    {
        add_shortcode('flashcard_form', [__CLASS__, 'render_flashcard_form']);
    }

    public static function render_flashcard_form()
    {
        global $wpdb;

        // ดึงข้อมูล categories จากฐานข้อมูล
        $table_categories = $wpdb->prefix . 'categories';
        $categories = $wpdb->get_results("SELECT id, name FROM $table_categories");

        // แสดงฟอร์ม
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

            <button type="submit">Submit</button>
        </form>
<?php
    }
}
