<?php 

	function get_user_profile_picture($userID = ''){

		$userID = empty($userID)?get_current_user_id():$userID;

		$profile_picture_id = get_user_meta($userID,'profile_picture',true);

		if(!empty($profile_picture_id)){
			$profile_picture = wp_get_attachment_url($profile_picture_id);
		}else $profile_picture = get_template_directory_uri() .'/images/profile-placeholder.png'; 

		return $profile_picture;

	}

	function load_template_part($template_name, $part_name=null) {

		ob_start();

		get_template_part($template_name, $part_name);

		$var = ob_get_contents();

		ob_end_clean();

		return $var;

	}

	function send_notification( $notifier = '' , $notification_receiver  , $permalink_id, $message ){
		
		global $wpdb;	

		$table_notification = $wpdb->prefix . "notification";

		// if(empty($notifier)) $notifier = get_current_user_id();

		if(!empty($notification_receiver)) {

			// foreach ($notification_receiver as $user_id) {

				$wpdb->insert($table_notification, array(

					'user_ids'	   	   => maybe_serialize($notification_receiver), 

					'notifier_user_id' => $notifier,

					'permalink_id'     => $permalink_id,

					'message' 		   => maybe_serialize($message),

				) );

			// }

		} 

	}