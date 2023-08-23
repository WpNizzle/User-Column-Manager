<?php 


/**
 * Register the plugin's settings page and submenus
 */
function user_column_manager_register_menus() {
    // Main Menu: User Column Manager
    add_menu_page(
        'User col Manager',
        'User col Manager',
        'manage_options',
        'user-column-manager',
        'user_column_manager_dashboard_page',
        'dashicons-admin-users',
        20
    );

    // Submenu: Manage Custom Columns
    add_submenu_page(
        'user-column-manager',
        'Manage Custom Columns',
        'Manage Columns',
        'manage_options',
        'user-column-manager-manage-columns',
        'user_column_manager_manage_columns_page'
    );

    // Submenu: Customize Custom Columns
    add_submenu_page(
        'user-column-manager',
        'Customize Custom Columns',
        'Customize Columns',
        'manage_options',
        'user_column_manager_customize_columns',
        'user_column_manager_customize_columns_page'
    );
	
    // Submenu: import/export data
    add_submenu_page(
        'user-column-manager',
        'Import/Export',
        'Import/Export',
        'manage_options',
        'user_column_manager_import_export',
        'user_column_manager_import_export_page'
    );

	// Submenu: Sorting and Filtering / User Grouping
	add_submenu_page(
        'user-column-manager',
        'Sorting and Grouping',
        'Sorting & Grouping',
        'manage_options',
        'user_column_manager_sorting_grouping',
        'user_column_manager_sorting_grouping_page'
    );

    // Submenu: premium
    add_submenu_page(
        'user-column-manager',
        'Upgrade To Premium',
        'Upgrade To Premium',
        'manage_options',
        'user_column_manager_premium',
        'user_column_manager_premium_page'
    );

    // Submenu: support
    add_submenu_page(
        'user-column-manager',
        'Help and support',
        'Help and support',
        'manage_options',
        'user_column_manager_support',
        'user_column_manager_support_page'
    );

    add_action('admin_print_styles', 'user_column_manager_add_submenu_icons');
}
add_action('admin_menu', 'user_column_manager_register_menus');

/**
 * Add Dashicons to submenu items
 */
function user_column_manager_add_submenu_icons() {
    $submenu_icons = array(
        'user-column-manager-manage-columns' => 'dashicons-admin-settings',
    );

    foreach ($submenu_icons as $submenu_slug => $icon) {
        echo '<style>';
        echo '#' . $submenu_slug . ' .wp-menu-image::before { content: "\f111"; }';
        echo '</style>';
    }
}

/**
 * Dashboard Page Callback
 */
function user_column_manager_dashboard_page() {
    ?>
    <div class="wrap">
        <h1>User Column Manager Dashboard</h1>
        <p>Welcome to the User Column Manager dashboard. Here, you can access important actions and get an overview of your custom user columns.</p>

        <div class="dashboard-widgets">
            <div class="dashboard-widget">
                <h2>Quick Actions</h2>
                <ul class="quick-actions">
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=user-column-manager-manage-columns'); ?>">
                            <span class="dashicons dashicons-admin-settings"></span>
                            Manage Custom Columns
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('http://127.0.0.1/wordpress/wp-admin/admin.php?page=user_column_manager_sorting_grouping'); ?>">
                            <span class="dashicons dashicons-admin-generic"></span>
                            Sorting & Grouping
                        </a>
                    </li>
                    <!-- Add more quick actions as needed -->
                </ul>
            </div>

            <div class="dashboard-widget">
                <h2>Column Overview</h2>
                <p>Get a quick overview of your custom columns here.</p>
                <!-- Add relevant overview content here -->
            </div>
        </div>
    </div>
    <?php
}


function user_column_manager_manage_columns_page() {
    // Create, edit, and delete custom columns
    // Reorder custom columns using drag-and-drop
}
function user_column_manager_customize_columns_page() {
    // Customize the styling and formatting of custom columns
	// Apply fonts, colors, alignment, and other visual settings
}
function user_column_manager_support_page() {
    // Customize the styling and formatting of custom columns
	// Apply fonts, colors, alignment, and other visual settings
}
function user_column_manager_premium_page() {
    // Customize the styling and formatting of custom columns
	// Apply fonts, colors, alignment, and other visual settings
}
function user_column_manager_import_export_page() {
    // Customize the styling and formatting of custom columns
	// Apply fonts, colors, alignment, and other visual settings
}
function user_column_manager_sorting_grouping_page() {
    // Content of the Sorting & Grouping page
}