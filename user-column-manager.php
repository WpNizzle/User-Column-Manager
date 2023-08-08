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

/**
 * Register the plugin's settings page
 *
 * @return void
 */
function user_column_manager_register_settings_page() {
	add_users_page(
		'User Column Manager',
		'User Column Manager',
		'manage_options',
		'user-column-manager',
		'user_column_manager_settings_page'
	);
}
add_action( 'admin_menu', 'user_column_manager_register_settings_page' );

/**
 * Settings page content
 *
 * @return void
 */
function user_column_manager_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Save custom columns on form submission.
	if ( isset( $_POST['user_column_manager_columns'] ) ) {
		$columns = sanitize_text_field( $_POST['user_column_manager_columns'] );
		update_option( 'user_column_manager_columns', $columns );
		echo '<div class="notice notice-success is-dismissible"><p>Custom columns have been added successfully.</p></div>';
	}

	// Get existing custom columns.
	$existing_columns = get_option( 'user_column_manager_columns', '' );

	?>
	<div class="wrap">
		<h1>User Column Manager</h1>
		<form method="post">
			<label for="user_column_manager_columns">Enter column names separated by commas:</label>
			<input type="text" name="user_column_manager_columns" id="user_column_manager_columns" value="<?php echo esc_attr( $existing_columns ); ?>" class="regular-text" />
			<?php submit_button( 'Save Columns', 'primary', 'user_column_manager_save_columns' ); ?>
		</form>
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
		foreach ( $custom_column_labels as $label ) {
			$column_key = sanitize_key( trim( $label ) );
			$columns[ $column_key ] = $label;
		}
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
			if ( isset( $_POST[$column_key] ) ) {
				$additional_data = sanitize_text_field( $_POST[$column_key] );
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
			if ( isset( $_POST[$column_key] ) ) {
				$additional_data = sanitize_text_field( $_POST[$column_key] );
				update_user_meta( $user_id, 'user_column_manager_additional_data_' . $column_key, $additional_data );
			}
		}
	}
}

add_action( 'personal_options_update', 'user_column_manager_save_additional_data_on_profile_update' );
add_action( 'edit_user_profile_update', 'user_column_manager_save_additional_data_on_profile_update' );
