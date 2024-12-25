<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<!-- upload csv file -->
<h3>
    Form Data
</h3>
<p id="show_upload_message"></p>
<form method="post" enctype="multipart/form-data">
    <label for="csv_file">Upload CSV:</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv" required><br>
    <button type="submit" name="upload_csv">Upload CSV New</button>
</form>