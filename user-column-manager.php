<?php
/**
 * Plugin Name: User Column Manager
 * Plugin URI: https://github.com/WpNizzle/User-Column-Manager
 * Description: Allows users to add additional columns to the user list.
 * Version: 0.1.0
 * Author: WpNizzle
 * Author URI: https://wpnizzle.com/
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package User Column manager
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Enqueue CSS and JavaScript files.
function user_column_manager_enqueue_scripts() {
    wp_enqueue_style( 'custom-columns-style', plugin_dir_url( __FILE__ ) . 'assets/css/usc-main.css', array(), '0.1.0' );
    wp_enqueue_script( 'custom-columns-script', plugin_dir_url( __FILE__ ) . 'assets/js/usc-main.js', array( 'jquery' ), '0.1.0', true );
}
add_action( 'admin_enqueue_scripts', 'user_column_manager_enqueue_scripts' );

require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';


/**
 * Settings page content
 */
function user_column_manager_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save custom columns on form submission.
    if (isset($_POST['user_column_manager_columns'])) {
        $columns = sanitize_text_field($_POST['user_column_manager_columns']);
        update_option('user_column_manager_columns', $columns);
        echo '<div class="notice notice-success is-dismissible"><p>Custom columns have been added successfully.</p></div>';
    }

    // Get existing custom columns.
    $existing_columns = get_option('user_column_manager_columns', '');

    ?>
    <div class="wrap">
        <h1>User Column Manager</h1>
        <form method="post">
            <label for="user_column_manager_columns">Enter column names separated by commas:</label>
            <input type="text" name="user_column_manager_columns" id="user_column_manager_columns"
                   value="<?php echo esc_attr($existing_columns); ?>" class="regular-text"/>
            <?php submit_button('Save Columns', 'primary', 'user_column_manager_save_columns'); ?>
        </form>
        <?php
        if (!empty($existing_columns)) {
            echo '<h2>Custom Columns</h2>';
            echo '<p>Drag and drop column to reorder</p>';
            echo '<div id="custom-columns-list">';
            $custom_column_labels = explode(',', $existing_columns);
            foreach ($custom_column_labels as $label) {
                $column_key = sanitize_key(trim($label));
                echo '<div class="custom-column-item" data-column-key="' . esc_attr($column_key) . '">' . esc_html($label) . '<span class="delete-column">Delete</span></div>';
            }
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

