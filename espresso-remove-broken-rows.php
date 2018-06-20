<?php
/*
 * Plugin Name: EE3 Remove broken rows
 * Description: Removes broken rows create by using older version of EE3 with the latest version of WP (activate once and then delete this plugin).
 * Version: 1.0.0
 * Author: Tony Warwick
 */

function ee_remove_broken_rows() {

	global $wpdb;

	$deleted_rows = array();
	//Remove price rows where event_id == 0
	$deleted_rows['prices_rows'] = $wpdb->delete( EVENTS_PRICES_TABLE, array( 'event_id' => '0' ), array( '%d' ) );
	//Remove start/end time rows where event_id == 0
	$deleted_rows['times_rows'] = $wpdb->delete( EVENTS_START_END_TABLE, array( 'event_id' => '0' ), array( '%d' ) );
	//Remove category rows where event_id == 0
	$deleted_rows['category_rows'] = $wpdb->delete( EVENTS_CATEGORY_REL_TABLE, array( 'event_id' => '0' ), array( '%d' ) );
	//Add a notice if rows were effected.
	if( $deleted_rows['prices_rows'] || $deleted_rows['times_rows'] || $deleted_rows['category_rows'] ) {
		set_transient( 'ee_remove_broken_rows_success', $deleted_rows, MINUTE_IN_SECONDS );
	}
}

add_action( 'admin_notices', 'ee_remove_broken_rows_success_message');

function ee_remove_broken_rows_success_message() {

	$deleted_rows = get_transient( 'ee_remove_broken_rows_success' );

	if( $deleted_rows['prices_rows'] || $deleted_rows['times_rows'] || $deleted_rows['category_rows'] ) {
	
		//if( $deleted_rows['prices_rows'] && $deleted_rows['times_rows'] ) {
		$message = 'The "EE3 remove broken rows" plugin removed:'; 
		if ( $deleted_rows['prices_rows'] ) {
			$message .= '<br>' . $deleted_rows['prices_rows'] . ' rows from the ' . EVENTS_PRICES_TABLE . ' table';
		} 
		if ($deleted_rows['times_rows'] ) {
			$message .= '<br>' . $deleted_rows['times_rows'] . ' rows from the ' . EVENTS_START_END_TABLE . ' table';
		} 
		if ($deleted_rows['category_rows'] ) {
			$message .= '<br>' . $deleted_rows['category_rows'] . ' rows from the ' . EVENTS_CATEGORY_REL_TABLE . ' table';
		} 

		?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo $message; ?></p>
			</div>
		<?php
	}
	delete_transient( 'ee_remove_broken_rows_success' );
	deactivate_plugins( plugin_basename( __FILE__ ) );
}
register_activation_hook( __FILE__, 'ee_remove_broken_rows' );