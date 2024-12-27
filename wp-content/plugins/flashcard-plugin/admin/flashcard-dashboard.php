<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_flashcard_dashboard()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'flashcard-plugin'));
    }

    echo '<div class="wrap">';
    echo '<h1>Welcome to Flashcard_FLS Dashboard</h1>';
    echo '<p>This is the main page of the Flashcard_FLS plugin. You can manage features from the submenus.</p>';
    echo '</div>';
}
