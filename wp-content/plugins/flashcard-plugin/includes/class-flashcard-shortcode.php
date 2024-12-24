<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


// Register shortcode for the flashcard form
add_shortcode('flashcard_form', 'render_flashcard_form');

function render_flashcard_form()
{
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flashcard_submit'])) {
        handle_flashcard_submission();
    }

    // Form HTML
    ob_start();
?>
    <form method="post" enctype="multipart/form-data">
        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="color">Color</option>
            <option value="number">Number</option>
            <option value="animal">Animal</option>
        </select><br>

        <label for="front_text_line1">Front Text Line 1:</label>
        <input type="text" name="front_text_line1" id="front_text_line1" required><br>

        <label for="front_text_line2">Front Text Line 2:</label>
        <input type="text" name="front_text_line2" id="front_text_line2"><br>

        <label for="back_text_line1">Back Text Line 1:</label>
        <input type="text" name="back_text_line1" id="back_text_line1" required><br>

        <label for="back_text_line2">Back Text Line 2:</label>
        <input type="text" name="back_text_line2" id="back_text_line2"><br>

        <label for="front_image">Front Image:</label>
        <input type="file" name="front_image" id="front_image" accept="image/*"><br>

        <label for="back_image">Back Image:</label>
        <input type="file" name="back_image" id="back_image" accept="image/*"><br>

        <label for="front_audio">Front Audio:</label>
        <input type="file" name="front_audio" id="front_audio" accept="audio/*"><br>

        <label for="back_audio">Back Audio:</label>
        <input type="file" name="back_audio" id="back_audio" accept="audio/*"><br>

        <button type="submit" name="flashcard_submit">Submit</button>
    </form>
<?php
    return ob_get_clean();
}
