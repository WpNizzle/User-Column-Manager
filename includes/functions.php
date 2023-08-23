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
        'user_column_manager_settings',
        'user_column_manager_settings_page'
    );

    // Submenu: Customize Custom Columns
    // add_submenu_page(
    //     'user-column-manager',
    //     'Customize Custom Columns',
    //     'Customize Columns',
    //     'manage_options',
    //     'user_column_manager_customize_columns',
    //     'user_column_manager_customize_columns_page'
    // );
	
    // Submenu: import/export data
    // add_submenu_page(
    //     'user-column-manager',
    //     'Import/Export',
    //     'Import/Export',
    //     'manage_options',
    //     'user_column_manager_import_export',
    //     'user_column_manager_import_export_page'
    // );

	// Submenu: Sorting and Filtering / User Grouping
	// add_submenu_page(
    //     'user-column-manager',
    //     'Sorting and Grouping',
    //     'Sorting & Grouping',
    //     'manage_options',
    //     'user_column_manager_sorting_grouping',
    //     'user_column_manager_sorting_grouping_page'
    // );

    // Submenu: premium
    // add_submenu_page(
    //     'user-column-manager',
    //     'Upgrade To Premium',
    //     'Upgrade To Premium',
    //     'manage_options',
    //     'user_column_manager_premium',
    //     'user_column_manager_premium_page'
    // );

    // Submenu: support
    // add_submenu_page(
    //     'user-column-manager',
    //     'Help and support',
    //     'Help and support',
    //     'manage_options',
    //     'user_column_manager_support',
    //     'user_column_manager_support_page'
    // );

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
                        <a href="<?php echo admin_url('admin.php?page=user_column_manager_settings'); ?>">
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

/**
 * Add custom columns to the user list
 *
 * @param array $columns The existing columns in the user list.
 * @return array The updated columns with custom ones.
 */
function user_column_manager_add_custom_columns( $columns ) {
	$custom_columns = get_option( 'user_column_manager_columns', '' );
	if ( ! empty( $custom_columns ) ) {
		$custom_column_labels = explode( ',', $custom_columns );
		$new_columns = array();
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			$new_columns[ $column_key ] = $label;
		}
		$posts_index = array_search( 'posts', array_keys( $columns ) );
		$before_posts = array_slice( $columns, 0, $posts_index, true );
		$after_posts = array_slice( $columns, $posts_index, null, true );
		$columns = array_merge( $before_posts, $new_columns, $after_posts );
	}
	return $columns;
}
add_filter( 'manage_users_columns', 'user_column_manager_add_custom_columns' );


/**
 * Display data in custom column
 *
 * @param string $value The default value to display.
 * @param string $column_name The name of the column.
 * @param int    $user_id The ID of the user.
 * @return string The data to display in the custom column.
 */
function user_column_manager_show_user_column_data( $value, $column_name, $user_id ) {
	$custom_columns = get_option( 'user_column_manager_columns', '' );
	if ( ! empty( $custom_columns ) ) {
		$custom_column_labels = explode( ',', $custom_columns );
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			if ( $column_key === $column_name ) {
				$additional_data = get_user_meta( $user_id, 'user_column_manager_additional_data_' . $column_key, true );

				// Check if the value is empty, and display "-" if it is.
				if ( empty( $additional_data ) ) {
					return '-';
				}

				return $additional_data;
			}
		}
	}

	return $value;
}
add_filter( 'manage_users_custom_column', 'user_column_manager_show_user_column_data', 10, 3 );


/**
 * Add additional column fields to "Add New User" page
 */
function user_column_manager_add_new_user_fields() {
	$custom_columns = get_option( 'user_column_manager_columns', '' );
	if ( ! empty( $custom_columns ) ) {
		$custom_column_labels = explode( ',', $custom_columns );
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			?>
			<table class="form-table">
				<tr>
					<th><label for="<?php echo esc_attr( $column_key ); ?>"><?php echo esc_html( $label ); ?>:</label></th>
					<td>
						<input type="text" name="<?php echo esc_attr( $column_key ); ?>" id="<?php echo esc_attr( $column_key ); ?>" value="" class="regular-text" />
					</td>
				</tr>
			</table>
			<?php
		}
	}
}

add_action( 'user_new_form', 'user_column_manager_add_new_user_fields' );
add_action( 'edit_user_profile', 'user_column_manager_add_new_user_fields' );
add_action( 'show_user_profile', 'user_column_manager_add_new_user_fields' );

/**
 * Save additional data when a new user is registered
 *
 * @param int $user_id The ID of the newly registered user.
 */
function user_column_manager_save_additional_data_on_registration( $user_id ) {
	$custom_columns = get_option( 'user_column_manager_columns', '' );
	if ( ! empty( $custom_columns ) ) {
		$custom_column_labels = explode( ',', $custom_columns );
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			if ( isset( $_POST[ $column_key ] ) ) {
				$additional_data = sanitize_text_field( $_POST[ $column_key ] );
				update_user_meta( $user_id, 'user_column_manager_additional_data_' . $column_key, $additional_data );
			}
		}
	}
}
add_action( 'user_register', 'user_column_manager_save_additional_data_on_registration' );


/**
 * Save additional data when editing a user profile
 *
 * @param int $user_id The ID of the user being updated.
 */
function user_column_manager_save_additional_data_on_profile_update( $user_id ) {
	$custom_columns = get_option( 'user_column_manager_columns', '' );
	if ( ! empty( $custom_columns ) ) {
		$custom_column_labels = explode( ',', $custom_columns );
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			if ( isset( $_POST[ $column_key ] ) ) {
				$additional_data = sanitize_text_field( $_POST[ $column_key ] );
				update_user_meta( $user_id, 'user_column_manager_additional_data_' . $column_key, $additional_data );
			}
		}
	}
}

add_action( 'personal_options_update', 'user_column_manager_save_additional_data_on_profile_update' );
add_action( 'edit_user_profile_update', 'user_column_manager_save_additional_data_on_profile_update' );

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
