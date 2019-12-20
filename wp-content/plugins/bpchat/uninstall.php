<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();
	
	global $wpdb;
	
	$table = $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}bpchat_message");
	
	if($table){
		$column = $wpdb->query("ALTER TABLE $wpdb->users 
			DROP bpchat_status, 
			DROP bpchat_last_activity");

	}
?>