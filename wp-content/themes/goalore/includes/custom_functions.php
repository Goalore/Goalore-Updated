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

	function send_two_factor_authentication_mail(){
		
		$user  = wp_get_current_user();
		$user_id = $user->ID;

		$verifed = get_user_meta($user_id, '2FAV', true);

		if($verifed != 1){
			$key = '2faotp' . $user_id;
			$OTP = get_transient($key);
			if(empty($OTP)){
				$full_name = get_user_meta($user_id, 'full_name', true);
				$OTP = wp_rand(100000,999999);
				
				set_transient($key, $OTP, HOUR_IN_SECONDS );

				$message = 'Hello ' . $full_name;
				$message .= '<br><br>Your OTP is: ' . $OTP;
				$message .= '<br><br>OTP is only valid for next 60 minutes';
				$message .= '<br><br><br>Regards,<br><b>Goalore</b>';

				$subject = 'Goalore OTP';
				$to = $user->user_email;
				$headers = array('Content-Type: text/html; charset=UTF-8');
				wp_mail($to, $subject, $message, $headers );
			}

		}else{
			return true;
		}

	}

	function get_limited_string($str ='',$length = 110,$end =''){
		if(strlen($str) > $length){
			$subtitle = substr($str, 0, $length - 10);
			if(!empty($end)) $subtitle.= $end;
			else $subtitle.= ' [...]';
		} 
		else $subtitle = $str;

		return $subtitle;
	}


	function get_gdp($userID=''){
		global $wpdb;	
		$userID = empty($userID)?get_current_user_id():$userID;
		$table_name = $wpdb->prefix . "gdp";
		$SQL = "SELECT SUM(points) FROM $table_name WHERE user_id ";
		if(is_array($userID)){
			$userIDs = implode("' ,'", $userID);
			$SQL .= " IN ('".$userIDs."')";
		} else $SQL .= " = $userID";
		$GDP = $wpdb->get_var($SQL); 
		if(empty($GDP)) $GDP = 0; 

		return $GDP;
	}

	function get_gdp_summary($userID=''){
		global $wpdb;	
		$GDP_Summary = [];
		$userID = empty($userID)?get_current_user_id():$userID;
		$table_name = $wpdb->prefix . "gdp";
 
		$GDP_Summary['total'] = get_gdp();

		$registrationGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $userID AND meta_key = 'registration'"); 
		if(empty($registrationGDP)) $registrationGDP = 0; 
		$GDP_Summary['registration'] = $registrationGDP;

		$goalsGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $userID AND meta_key = 'goals'"); 
		if(empty($goalsGDP)) $goalsGDP = 0; 
		$GDP_Summary['goals'] = $goalsGDP;

		$POVGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $userID AND meta_key = 'POV'"); 
		if(empty($POVGDP)) $POVGDP = 0; 
		$GDP_Summary['pov'] = $POVGDP;
		
		$ReferralGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $userID AND meta_key = 'referral_user_id'"); 
		if(empty($ReferralGDP)) $ReferralGDP = 0; 
		$GDP_Summary['referral'] = $ReferralGDP;

		return $GDP_Summary;
	}

