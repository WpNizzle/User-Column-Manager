<?php

/**
 * Register the plugin's settings page
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
 */
function user_column_manager_settings_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['user_column_manager_columns'] ) ) {
		$columns = sanitize_text_field( $_POST['user_column_manager_columns'] );
		update_option( 'user_column_manager_columns', $columns );
		echo '<div class="notice notice-success is-dismissible"><p>Custom columns have been added successfully.</p></div>';
	}
	$existing_columns          = get_option( 'user_column_manager_columns', '' );
	$registration_date_visible = get_option( 'user_column_manager_registration_date_visible', true );
	$last_login_date_visible = get_option( 'user_column_manager_last_login_date_visible', true );

	?>
	<div class="wrap">
		<h1>User Column Manager</h1>
		<form method="post">
			<label for="user_column_manager_columns">Enter column names separated by commas:</label>
			<input type="text" name="user_column_manager_columns" id="user_column_manager_columns"
				value="<?php echo esc_attr( $existing_columns ); ?>" class="regular-text"/>
			<?php submit_button( 'Save Columns', 'primary', 'user_column_manager_save_columns' ); ?>
		</form>       

		<?php
		if ( ! empty( $existing_columns ) ) {
			echo '<h2>Custom Columns</h2>';
			echo '<p>Drag and drop column to reorder</p>';
			echo '<div id="custom-columns-list">';
			$custom_column_labels = explode( ',', $existing_columns );
			foreach ( $custom_column_labels as $label ) {
				$column_key = sanitize_key( trim( $label ) );
				echo '<div class="custom-column-item" data-column-key="' . esc_attr( $column_key ) . '">' . esc_html( $label ) . '<span class="delete-column">Delete</span></div>';
			}
			echo '</div>';
		}

		?>

		<form method="post">
			<label for="user_column_manager_registration_date_visible" style="margin-right: 20px;">
				<input type="checkbox" name="user_column_manager_registration_date_visible" id="user_column_manager_registration_date_visible"
					value="1" <?php checked( $registration_date_visible ); ?> />
				Show Registration Date in Users List
			</label>
			<label for="user_column_manager_last_login_date_visible">
				<input type="checkbox" name="user_column_manager_last_login_date_visible" id="user_column_manager_last_login_date_visible"
					value="1" <?php checked( $last_login_date_visible ); ?> />
				Show Last Login Date in Users List
			</label>

			<?php submit_button( 'Save Settings', 'secondary', 'user_column_manager_save_settings' ); ?>
		</form>
	</div>
	<?php
}

if ( isset( $_POST['user_column_manager_save_settings'] ) ) {
    $registration_date_visible = isset( $_POST['user_column_manager_registration_date_visible'] ) ? true : false;
    update_option( 'user_column_manager_registration_date_visible', $registration_date_visible );

    $last_login_date_visible = isset( $_POST['user_column_manager_last_login_date_visible'] ) ? true : false;
    update_option( 'user_column_manager_last_login_date_visible', $last_login_date_visible );
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
	?>
	<table class="form-table">
		<tr>
			<th><label for="registration_date">Registration Date:</label></th>
			<td>
				<input type="text" name="registration_date" id="registration_date" value="" class="regular-text" disabled />
			</td>
		</tr>
	</table>
	<?php
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
	update_user_meta( $user_id, 'user_column_manager_additional_data_registration_date', current_time( 'mysql' ) );
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

/**
 * Add the registration date column to the user list.
 *
 * @param array $columns The existing columns in the user list.
 * @return array Modified list of columns including the registration date column if applicable.
 */
function user_column_manager_add_registration_date_column( $columns ) {
	$registration_date_visible = get_option( 'user_column_manager_registration_date_visible', true );

	if ( $registration_date_visible ) {
		$columns['registration_date'] = __( 'Registration Date', 'user-column-manager' );
	}

	return $columns;
}
add_filter( 'manage_users_columns', 'user_column_manager_add_registration_date_column' );

/**
 * Populate the registration date column with user registration dates.
 *
 * @param string $output      The default column output. Not used here.
 * @param string $column_name The name of the column being processed.
 * @param int    $user_id     The ID of the user being processed.
 * @return string Modified column output with the registration date if applicable.
 */
function user_column_manager_show_registration_date_data( $output, $column_name, $user_id ) {
	if ( 'registration_date' === $column_name ) {
		$user = get_userdata( $user_id );
		if ( $user ) {
			$registration_date = $user->user_registered;
			return date_i18n( get_option( 'date_format' ), strtotime( $registration_date ) );
		}
	}
	return $output;
}
add_filter( 'manage_users_custom_column', 'user_column_manager_show_registration_date_data', 10, 3 );

function user_column_manager_add_last_login_date_column( $columns ) {
    $last_login_date_visible = get_option( 'user_column_manager_last_login_date_visible', true );

    if ( $last_login_date_visible ) {
        $columns['last_login_date'] = __( 'Last Login Date', 'user-column-manager' );
    }

    return $columns;
}
add_filter( 'manage_users_columns', 'user_column_manager_add_last_login_date_column' );

function user_column_manager_show_last_login_date_data( $output, $column_name, $user_id ) {
    if ( 'last_login_date' === $column_name ) {
        $user = get_userdata( $user_id );
        if ( $user ) {
            // Get the last login timestamp from user meta
            $last_login_timestamp = get_user_meta( $user_id, 'user_column_manager_additional_data_last_login_date', true );

            if ( empty( $last_login_timestamp ) ) {
                return 'Never logged in';
            } else {
                // Convert timestamp to a human-readable format
                $last_login_date = date_i18n( get_option( 'date_format' ), $last_login_timestamp );
                return $last_login_date;
            }
        }
    }
    return $output;
}
add_filter( 'manage_users_custom_column', 'user_column_manager_show_last_login_date_data', 10, 3 );

function user_column_manager_update_last_login_date( $user_login, $user ) {
    // Update the last login timestamp for the user
    update_user_meta( $user->ID, 'user_column_manager_additional_data_last_login_date', time() );
}
add_action( 'wp_login', 'user_column_manager_update_last_login_date', 10, 2 );
