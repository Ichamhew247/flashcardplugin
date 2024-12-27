<!-- class-flashcard-categories.php -->

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_manage_categories_page()
{
    global $wpdb;

    // Table name
    $table_categories = $wpdb->prefix . 'categories';
    // จัดการ CRUD Categories (ตัวอย่างโค้ดในโพสต์ก่อนหน้า)
    echo '<div class="wrap">';
    echo '<h1>Manage Categories</h1>';
    echo '<p>Here you can add, edit, or delete categories for your flashcards.</p>';
    // Handle form submission for Add/Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = sanitize_text_field($_POST['action']);
        $category_name = sanitize_text_field($_POST['category_name']);

        // Check for duplicate name
        $existing_category = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $table_categories WHERE name = %s", $category_name)
        );

        if ($existing_category && $action === 'add') {
            echo '<div class="error"><p>Category name already exists!</p></div>';
        } elseif ($action === 'add') {
            // Add new category
            $wpdb->insert($table_categories, ['name' => $category_name], ['%s']);
            echo '<div class="updated"><p>Category added successfully!</p></div>';
        } elseif ($action === 'update' && isset($_POST['category_id'])) {
            // Update existing category
            $category_id = intval($_POST['category_id']);
            $wpdb->update(
                $table_categories,
                ['name' => $category_name],
                ['id' => $category_id],
                ['%s'],
                ['%d']
            );
            echo '<div class="updated"><p>Category updated successfully!</p></div>';
        }
    }

    // Handle Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $category_id = intval($_POST['category_id']);
        $wpdb->delete($table_categories, ['id' => $category_id], ['%d']);
        echo '<div class="updated"><p>Category deleted successfully!</p></div>';
    }

    // Get all categories
    $categories = $wpdb->get_results("SELECT * FROM $table_categories");

?>
    <div class="wrap">
        <h1>Manage Categories</h1>

        <!-- Form to Add/Update Category -->
        <form method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('manage_categories_nonce'); ?>">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" id="category_name" required>
            <button type="submit" class="button button-primary">Add Category</button>
        </form>

        <h2>Existing Categories</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <td><?php echo esc_html($category->id); ?></td>
                        <td><?php echo esc_html($category->name); ?></td>
                        <td>
                            <!-- Edit Form -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="category_id" value="<?php echo esc_attr($category->id); ?>">
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('manage_categories_nonce'); ?>">
                                <input type="text" name="category_name" value="<?php echo esc_attr($category->name); ?>" required>
                                <button type="submit" class="button button-secondary">Update</button>
                            </form>

                            <!-- Delete Form -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_id" value="<?php echo esc_attr($category->id); ?>">
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('manage_categories_nonce'); ?>">
                                <button type="submit" class="button button-danger" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
}
