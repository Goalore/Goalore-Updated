<?php 

add_action( 'wp_ajax_nopriv_login_frm',function(){
	check_ajax_referer( 'ajax-login-nonce', 'security' );
	$response = [
		'status' => 'failed',
		'msg' => 'Login Failed!'
	];
	$user_login = $_POST['user_login'];
	$user_pass = $_POST['user_pass'];
	/*$user = wp_authenticate($user_login,$user_pass);
	if ($user && !is_wp_error($user)){*/
		$creds = [
			'user_login'    => $user_login,
	        'user_password' => $user_pass,
	        'remember'      => false,
		];
		$user = wp_signon( $creds, false );

		if ( !empty( $user ) && is_wp_error( $user ) ) {
			$response['msg'] = 'Wrong username or password!';
	    }else{
	    	$isDeleted = get_user_meta($user->ID,'isDeleted',true);
	    	if($isDeleted == 1){
	    		wp_logout();
				$response['msg'] = 'Your Account is Deleted!';
	    	}else{
				$response['status'] = 'success';
				$response['msg'] = 'Login successful, redirecting...';
	    	}
	    }
	/*}else{
		$response['msg'] = 'Wrong username or password!';
		// $response['msg'] = $user->get_error_message();
	}*/
	echo json_encode($response);
	wp_die();
});


add_action( 'wp_ajax_verify_otp',function(){
	check_ajax_referer( 'ajax-otp-nonce', 'security' );
	$response = [
		'status' => 'failed',
		'msg' => 'OTP Verification Failed!'
	];
	
	$user_id = get_current_user_id();
	$key = '2faotp' . $user_id;
	$OTP = get_transient($key); 
	$otp = $_POST['otp'];
	if($otp == $OTP){
		delete_transient($key);
		$update = update_user_meta($user_id, '2FAV', 1);	
		if($update){
			$response['status'] = 'success';
			$response['msg'] = 'OTP Verified Successfully!';

			$categories = get_user_meta($user_id,'categories',true);
			if(empty($categories)){
				$response['redirect'] = get_permalink(138);
			}
		}

	}else $response['msg'] = 'Incorrect OTP!';

	echo json_encode($response);
	wp_die();
});

add_action( 'wp_ajax_register_frm',function(){
	$response = [
		'status' => 'failed',
		'msg' => 'You are already loggedin, logout and try again!'
	];
	echo json_encode($response);
	wp_die();
});

add_action( 'wp_ajax_validate_username',function(){
	$response = [
		'status' => 'failed',
		'msg' => 'Service is not available when loggedin.Logout and try again.'
	];
	echo json_encode($response);
	wp_die();
});	
add_action( 'wp_ajax_nopriv_validate_username',function(){
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$username = $_POST['username'];
	if(validate_username($username)){
		if(username_exists($username)){
			$response['msg'] = "The username '".$username."' is already in use";		
		}else{
			$response['status'] = 'success';
			$response['msg'] = "The username '".$username."' is available";
		}
	}else $response['msg'] = 'Invalid Username';
	echo json_encode($response);
	wp_die();
});

add_action( 'wp_ajax_validate_email','validate_email');	
add_action( 'wp_ajax_nopriv_validate_email','validate_email');
function validate_email(){
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$email = $_POST['email'];
	if(is_email($email)){
		if(email_exists($email)){
			// $response['msg'] = "The email '".$email."' is already in use";		
			$response['msg'] = "Email address is already in use";		
		}else{
			$response['status'] = 'success';
			// $response['msg'] = "The email '".$email."' is available";
			$response['msg'] = "Email address is valid";
		}
	}else $response['msg'] = 'Invalid Email!';
	echo json_encode($response);
	wp_die();
}

add_action( 'wp_ajax_validate_zip_code',function(){
	$response = [
		'status' => 'failed',
		'msg' => 'Service is not available when loggedin.Logout and try again.'
	];
	echo json_encode($response);
	wp_die();
});	

add_action( 'wp_ajax_nopriv_validate_zip_code',function(){
	global $wpdb;
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$zip_code = $_POST['zip_code'];
	$table_zc = $wpdb->prefix . "zip_code";
	$resultID = $wpdb->get_var("SELECT ID FROM $table_zc WHERE zip_code = '$zip_code' ");   
	if(!empty($resultID)){
		$response['status'] = 'success';
		$response['msg'] = "ZipCode is valid!";
	}else $response['msg'] = 'Not a valid Zipcode!';
	echo json_encode($response);
	wp_die();
});


add_action( 'wp_ajax_nopriv_register_frm',function(){
	check_ajax_referer( 'ajax-register-nonce', 'security' );
	global $wpdb;
	$response = [
		'status' => 'failed',
		'msg' => 'Registration Failed!'
	];
	$full_name = $_POST['full_name'];
	$name = explode(' ', $full_name);
	$first_name = $name[0];
	$last_name = isset($name[1])?$name[1]:'';
	$type 	  = $_POST['type'];
	$dob 	  = $_POST['dob'];
	$username = $_POST['username'];
	$email 	  = $_POST['email'];
	$password = $_POST['password'];
	$country  = $_POST['country'];
	$zip_code = $_POST['zip_code'];
	$referral_code = $_POST['referral_code'];
	$secret = GCV2_Private;
	$captcha_response = $_REQUEST["captcha"];

	$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captcha_response}");
	$captcha_success=json_decode($verify);
	
	if ($captcha_success->success==true) {

		$table_zc = $wpdb->prefix . "zip_code";
		$resultID = $wpdb->get_var("SELECT ID FROM $table_zc WHERE zip_code = '$zip_code' ");   
		if(!empty($resultID)){
			$user_id = wp_create_user( $username, $password, $email );
			if(!is_wp_error($user_id)) {
				update_user_meta($user_id, 'type', $type);
				update_user_meta($user_id, 'first_name', $first_name);
				update_user_meta($user_id, 'last_name', $last_name);
				update_user_meta($user_id, 'full_name', $full_name);
				update_user_meta($user_id, 'dob', $dob);
				update_user_meta($user_id, 'country', $country);
				update_user_meta($user_id, 'zip_code', $zip_code);	
				//2 Factor Authentication verify
				update_user_meta($user_id, '2FAV', 0);	

				$creds = [
					'user_login'    => $username,
			        'user_password' => $password,
			        'remember'      => false,
				];
				$user = wp_signon( $creds, false );
				$response['status'] = 'success';
				$response['msg'] = 'Registered Successfully!';
				$response['redirect'] = get_permalink(138);

				$table_gdp = $wpdb->prefix . "gdp";
				$wpdb->insert($table_gdp, array(
					'user_id'	  => $user_id, 
					'points' 	  => '50', 
					'meta_key'    => 'registration',
					'date' 		  => current_time( 'mysql' ),
					'description' => 'Registration',
				) );

				$notification = 'Received 50 Good Deed Points for "New Registration"';
		    	send_notification(0, [ (string) $user_id], '', $notification);
				
				$table_rf = $wpdb->prefix . "referral_friend";
				$SQL = "SELECT ID,referrer_id FROM $table_rf WHERE referral_code = '$referral_code' AND referral_email = '$email'";
				$resultID = $wpdb->get_row($SQL);
				if(!empty($resultID)) {
					$wpdb->update($table_rf, array(
						'is_joined' => 1,
					), ['ID' => $resultID->ID ] );

					$wpdb->insert($table_gdp, array(
						'user_id'	  => $resultID->referrer_id, 
						'points' 	  => 10, 
						'date' 		  => current_time( 'mysql' ),
						'meta_key'    => 'referral_user_id',
						'meta_value'  => $user_id,
						'description' => 'Referred Friend Joined',
					) );

					$notification = 'Received 10 Good Deed Points as "Referral Points" for "'.$email.'"';
		    		send_notification(0, [ (string) $resultID->referrer_id], '', $notification);
				}
			}else $response['msg'] = $user_id->get_error_message();
		}else $response['msg'] = 'Invalid Zip Code!';
	}else $response['msg'] = 'Recaptcha verification failed!';
		

	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_user_category_update', function() {
	$categories = $_POST['catIDs'];
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	if(!empty($categories)){
		$update = update_user_meta(get_current_user_id(),'categories',$categories);
		// if($update){
			$response['status'] = 'success';
			$response['msg'] = 'Categories updated!';
		// }
	}
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_reset_pwd', 'reset_member_password');
add_action('wp_ajax_nopriv_reset_pwd', 'reset_member_password');

function reset_member_password() {
	check_ajax_referer( 'ajax-reset-pwd-nonce', 'security' );
	$response = [
		'status' => 'failed',
		'msg' => 'Registration Failed!'
	];
	$key = $_POST['key'];
	$login = $_POST['login'];
	$new_password = $_POST['new_password'];

	 $user = check_password_reset_key($key,$login);
	if(!is_wp_error($user)){

		wp_set_password($new_password,$user->ID);

        $response['status'] = 'success';
		$response['msg'] = 'Password Updated Successfully!';
		$redirect = is_user_logged_in() ? get_permalink(98) : site_url();
		$response['redirect'] = $redirect;
		

    }else $response['msg'] = $user->get_error_message();

	echo json_encode($response);
	wp_die();
}

add_action('wp_ajax_forgot_pwd', 'forgot_pwd');
add_action('wp_ajax_nopriv_forgot_pwd', 'forgot_pwd');
function forgot_pwd(){

	check_ajax_referer( 'ajax-forgot-pwd-nonce', 'security' );
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$user_login_r = $_POST['user_login'];
	$user = get_user_by('email', $user_login_r);
	if(!$user){
		$user = get_user_by('login', $user_login_r);
	}
	if($user){
		$adt_rp_key = get_password_reset_key( $user );
		$user_login = $user->user_login;

		$rp_link = add_query_arg( array(
	    	'key' => $adt_rp_key,
		    'login' => $user_login,
		), get_permalink(381) );

		$message = 'Dear '.$user->first_name.',';
		$message .= '<br><br>We have received a reset password request.<br>';
		$message .= '<br>If this was a mistake, just ignore this email and nothing will happen.<br>';
		$message .= '<br> To reset your password, visit the following address: <br><br>'.$rp_link;

		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($user->user_email, 'Password Reset', $message, $headers );
		
		$response['status'] = 'success';
		$response['msg'] = 'You should receive an email to reset your password. Please check all your email folders to locate this email.';
	}else{
		$response['msg'] = 'Member not found!';
	}

	echo json_encode($response);
	wp_die();
};



add_action('wp_ajax_get_goal_subcats',function(){
	$catID = $_REQUEST['catID'];
	
	$terms = get_terms([
      'taxonomy'   => 'goal_categories',
      'hide_empty' => false,
      'parent'   => $catID
    ]);
    echo json_encode($terms);

	wp_die();
});

add_action('wp_ajax_create_goal',function(){
	$GoalData = array();
	$currentUserID = get_current_user_id();
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	parse_str($_REQUEST['GoalData'], $GoalData);
	extract($GoalData);

	$meta_input  = [
    	'target' => str_replace('-', '', $target),
    	'goal_status'=> 'open',
    ];
	if(isset($type) && !empty($type)) $meta_input['type'] = $type;
	if(isset($status) && !empty($status)) $meta_input['status'] = $status;

	$goal_data = [
		'ID' 		  => isset($goal_id) ? $goal_id : 0,
		'post_title'  => $title,
		'post_name'   => substr($title, 0, 75),
	    'post_status' => 'publish',
	    'post_type'   => 'goals',
	    'post_author' => $currentUserID,
	    'meta_input'  => $meta_input,
	];

	$goalID = wp_insert_post( $goal_data );
	if(!is_wp_error($goalID)){
		$term_taxonomy_ids = wp_set_post_terms($goalID, [$category,$subcategory], 'goal_categories');
		$response['status'] = 'success';

		if($goal_id > 0){
			$response['msg'] = 'Goal Updated Successfully!';
		}else{
			$response['msg'] = 'Goal Created Successfully!';

			if($status != 'private'){
			/*
			 *Fire Notification on:
			 *
			 *@Connection start a new goal
			 *
			 */
				$user 		  = get_user_by('ID', $currentUserID);
				$username 	  = $user->user_login;
				$subtitle     = get_limited_string($title);
				$notification = $username.' just started a new goal "'.$subtitle.'"';
				$connections  = get_user_meta($currentUserID,'connections',true);
				send_notification($currentUserID, $connections, $goalID, $notification);
			}
		}


	}else{
		$response['msg'] = $goalID->get_error_message();
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_create_alliance',function(){
	$AlliancesData = array();
	$currentUserID = get_current_user_id();
	parse_str($_REQUEST['AlliancesData'], $AlliancesData);
	extract($AlliancesData);
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];

	$meta_input = [
    	'objective' => $objective,
    	'status'    => 'open',
    ];

    if(isset($status) && !empty($status)) $meta_input['status'] = $status;
    if(isset($privacy_status) && !empty($privacy_status)) $meta_input['privacy_status'] = $privacy_status;

 
	$alliance_data = [
		'ID' 		  => $alliance_id > 0 ? $alliance_id : 0,
		'post_title'  => $title,
		'post_name'   => substr($title, 0, 75),
	    'post_status' => 'publish',
	    'post_type'   => 'alliances',
	    'post_author' => $currentUserID,
	    'meta_input'  => $meta_input
	];
	$AlliancesID = wp_insert_post( $alliance_data );
	if(!is_wp_error($AlliancesID)){
		$response['status'] = 'success';
		if($alliance_id > 0){
			$response['msg'] = 'Alliance Updated Successfully!';
		}else{
			$response['msg'] = 'Alliance Created Successfully!';

			if($privacy_status != 'private'){
				/*
				 *Fire Notification on:
				 *
				 *@Connection creates new goal
				 *
				 */

				$user 		  = get_user_by('ID', $currentUserID);
				$username 	  = $user->user_login;
				$subtitle     = get_limited_string($title);
				$notification = $username.' just created a new alliance "'.$subtitle.'"';
				$connections  = get_user_meta($currentUserID,'connections',true);
				send_notification($currentUserID, $connections, $AlliancesID, $notification);
			}
		}

	}else{
		$response = $AlliancesID->get_error_message();
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_manage_goal_catgory',function(){
	$GoalCatData = array();
	parse_str($_REQUEST['GoalCatData'], $GoalCatData);
	$parent_id = 0;
	extract($GoalCatData);
	// $response = 'failed';
	if($term_id){
		$updateData = array();
		if($parent_id){
			$updateData['parent'] = $parent_id;
		}
		if($name){
			$updateData['name'] = $name;
			$updateData['slug'] = wp_unique_term_slug($name,get_term($term_id));
		}
		$update = wp_update_term( $term_id, 'goal_categories', $updateData );
		 
		if ( ! is_wp_error( $update ) ) {
		    $response = 'updated';
		    update_term_meta($update['term_id'],'active',$active);
		}else{
			$response = $update->get_error_message();
		}
	}else{
		$insert = wp_insert_term(
		    $name,   
		    'goal_categories', 
		    array(
		        'parent' => $parent_id
		    )
		); 
		if ( ! is_wp_error( $insert ) ) {
		    $response = 'inserted';
			update_term_meta($insert['term_id'],'active',$active);
		}else{
			$response = $insert->get_error_message();
		}
	}
	echo($response);
	wp_die();
});

add_action('wp_ajax_manage_ticket',function(){
	$resolution = $_REQUEST['resolution'];
	$status = $_REQUEST['status'];
	$id = $_REQUEST['id'];

	update_post_meta($id,'status',$status);
	update_post_meta($id,'resolution',$resolution);

	if($status == 'closed'){
		$user_id = get_post_field( 'post_author', $id );
		$user 	 = get_user_by('ID', $user_id);

		$reportedon = get_the_time('d-m-Y', $id);
		$message = 'Dear '.$user->first_name.',';
		$message .= '<br><br>You contacted us on '.$reportedon.'.  Please see the update on your request.<br>';
		$message .= '<br><br>'.$resolution.'<br>';
		$message .= '<br><br>Regards';
		$message .= '<br><b>Goalore Admin</b>';

		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($user->user_email, 'Goalore Ticket Update', $message, $headers );
	}

	$response = 'Ticket updated successfully!';
	echo($response);
	wp_die();
});

add_action('wp_ajax_submit_ticket_user',function(){
	$type = $_REQUEST['type'];
	$description = $_REQUEST['description'];
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$ticketID = wp_insert_post([
		'post_title'   => wp_strip_all_tags( $type ),
		'post_content' => $description,
		'post_author'  => $currentUserID,
		'post_status'  => 'publish',
		'post_type'    => 'tickets',
		'meta_input'   => [
	    	'status'   => 'open',
	    ]
	]);
	if(!is_wp_error($ticketID)){
		$response['status'] = 'success';
		$response['msg'] = 'Ticket submitted successfully!';
	}else $response['msg'] = $ticketID;

	echo json_encode($response);
	wp_die();

});

add_action('wp_ajax_connection_request',function(){
	$userID = $_REQUEST['userID'];
	$currentUserID = get_current_user_id(); 
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$pcr = get_user_meta($userID,'pending_connection_request',true);
	if(empty($pcr))
		$pcr = [$currentUserID];
	else if (in_array($currentUserID, $pcr)){
		$response['msg'] = 'Connection Request Already Sent!';
		echo json_encode($response);
		wp_die();
	}
	else array_push($pcr, $currentUserID);
	$update = update_user_meta($userID,'pending_connection_request',$pcr);
	if($update){
		$response['status'] = 'success';
		$response['msg'] = 'Connection Request Sent!';

		$user 		  = get_user_by('ID', $currentUserID);
		$username 	  = $user->user_login;
		$notification = 'New connection request received from '.$username ;
		$connections_page = home_url('/'.PROFILE.'/'.MY_CONNECTIONS);
		send_notification($currentUserID, [(string) $userID], $connections_page, $notification);
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_remove_connection_request',function(){
	global $wpdb;
	$userID = $_REQUEST['userID'];
	$currentUserID = get_current_user_id(); 
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$pcr = get_user_meta($userID,'pending_connection_request',true);
	if(!empty($pcr)){
		$IDkey = array_search($currentUserID, $pcr);
		if($IDkey !== false){
			unset($pcr[$IDkey]);
			$update = update_user_meta($userID,'pending_connection_request',$pcr);
			if($update){

				$user 	  = get_user_by('ID', $currentUserID);
				$username = $user->user_login;
				$message  = 'New connection request received from '.$username ;
				$ntable   = $wpdb->prefix . "notification";
				$uids 	  = '\'%"'. $userID . '"%\'';
				$SQL = "DELETE FROM $ntable WHERE notifier_user_id = $currentUserID AND message = '$message' AND user_ids LIKE $uids";
				$wpdb->query($SQL);

				$response['status'] = 'success';
				$response['msg'] = 'Connection Request Removed!';
			}
		}
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_remove_connection',function(){
	$userID = $_REQUEST['userID'];
	$currentUserID = get_current_user_id(); 
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$connections = get_user_meta($userID,'connections',true);
	$myconnections = get_user_meta($currentUserID,'connections',true);
	if(!empty($connections) && !empty($myconnections)) {
		$IDkey = array_search($currentUserID, $connections);
		$myIDkey = array_search($userID, $myconnections);
		// if($IDkey !== false && $myIDkey !== false){
			unset($connections[$IDkey]);
			unset($myconnections[$myIDkey]);
			$update = update_user_meta($userID,'connections',$connections);	
			$myupdate = update_user_meta($currentUserID,'connections',$myconnections);	
			// if($update && $myupdate){
				$response['status'] = 'success';
				$response['msg'] = 'Connection Request Removed!';
			// }
		// }
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_reject_request',function(){
	$userID = $_REQUEST['userID'];
	$currentUserID = get_current_user_id(); 
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$pcr = get_user_meta($currentUserID,'pending_connection_request',true);
	if(!empty($pcr)){
		$IDkey = array_search($userID, $pcr);
		if($IDkey !== false){
			unset($pcr[$IDkey]);
			$update = update_user_meta($currentUserID,'pending_connection_request',$pcr);
			if($update){
				$response['status'] = 'success';
				$response['msg'] = 'Connection Request Rejected!';
			}
		}
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_accept_request',function(){
	$userID = (string) $_REQUEST['userID'];
	$currentUserID = (string) get_current_user_id(); 
	$response = [
		'status' => 'failed',
		'msg' => 'An error occurred'
	];
	$pcr = get_user_meta($currentUserID,'pending_connection_request',true);
	if(!empty($pcr)){
		$IDkey = array_search($userID, $pcr);
		if($IDkey !== false){
			unset($pcr[$IDkey]);
			$update = update_user_meta($currentUserID,'pending_connection_request',$pcr);
			if($update){
				$myconnections = get_user_meta($currentUserID,'connections',true);
				if(empty($myconnections)) $myconnections=[]; 
				array_push($myconnections, $userID);
				$connections = get_user_meta($userID,'connections',true);
				if(empty($connections)) $connections=[]; 
				array_push($connections, $currentUserID);

				$myupdate = update_user_meta($currentUserID,'connections',$myconnections);
				$update = update_user_meta($userID,'connections',$connections);

				
				if($update && $myupdate){
					$response['status'] = 'success';
					$response['msg'] = 'Connection Request Accepted!';
				}
			}
		}
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_add_goal_mc',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$title 		 = $_REQUEST['title'];
	$target  	 = date('Ymd',strtotime($_REQUEST['target']));
	$status 	 = $_REQUEST['status'];
	$selector 	 = $_REQUEST['selector'];
	$row_id 	 = $_REQUEST['row_id'];

	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$data = [
		'title' => $title,
		'target' => $target,
		'status' => $status,
	];

	if(!empty($row_id) && $row_id != '0'){
		$row = update_row($selector,$row_id,$data,$goal_id);
		$stat = 'updated';
	}else{
		$row = add_row($selector,$data,$goal_id);
		$stat = 'created';
	} 

	

	if($row){
		$response['status'] = 'success';
		$msg = ucfirst(trim($selector,'s')) .' '. $stat.' successfully';
		$response['msg'] = $msg;

		/*
		 *Fire Notification on:
		 *
		 *@Posted a new milestone
		 *@Posted a new challenge
		 *@Completed a milestone
		 *@Completed a challenge
		 *
		 */
		$goalAuthorID = get_post_field( 'post_author', $goal_id );
		$followers 	  = get_post_meta($goal_id,'followers',true);
		$full_name 	  = get_user_meta($goalAuthorID, 'full_name', true);
		$user 		  = get_user_by('ID', $goalAuthorID);
		$username 	  = $user->user_login;
		$notification  = '';
		
		$subtitle      = get_limited_string($title);
		if(!empty($row_id) && $row_id != '0'){
			if($status == 'Completed') {
				$notification = $username.' has completed a '. trim($selector,'s').' in goal "'.$subtitle.'"';
			}
		}else{
			$notification = $username.' has posted a new '. trim($selector,'s').' in goal "'.$subtitle.'"';
		}

		if(!empty($notification)){
			send_notification($goalAuthorID, $followers, $goal_id, $notification);
		}
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_delete_goal_mc',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$row_id 	 = $_REQUEST['row_id'];
	$selector 	 = $_REQUEST['selector'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];

	$row = delete_row($selector,$row_id,$goal_id);

	if($row){
		$response['status'] = 'success';
		$response['msg'] = 'Deleted Successfully!';
	}
	
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_update_goal_status',function(){
	global $wpdb;
	$goal_id 	 = $_REQUEST['goal_id'];
	$row_id 	 = $_REQUEST['row_id'];
	$selector 	 = $_REQUEST['selector'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred 123'
	];

	/*$isStatus = metadata_exists('post', $goal_id, 'goal_status');
	if($isStatus){*/
		$status = get_post_meta($goal_id,'goal_status',true);
		if($status == 'open') 
			$status = 'complete';
		else $status = 'open';
	// }else $status = 'complete';
	
	$canUpdate = true;
	if($status == 'complete'){
		$milestones = get_field('milestones',$goal_id);
		$challenges = get_field('challenges',$goal_id);
		if(!empty($milestones)){
			foreach($milestones as $m){
				if($m['status']['value'] == 'Open'){
					$canUpdate = false;
					$response['msg'] = 'Please complete all milestones!';
					break;
				}
			}
		} 
		if(!empty($challenges)){
			foreach($challenges as $c){
				if($c['status']['value'] == 'Open'){
					$canUpdate = false;
					$response['msg'] = 'Please complete all challenges!';
					break;
				}
			}
		}
	}

	if($canUpdate){
		$update = update_post_meta($goal_id,'goal_status',$status);
		if($update){
			$table_name = $wpdb->prefix . "gdp";
			$goalAuthorID = get_post_field( 'post_author', $goal_id );
			if($status == 'complete'){
				$followers = get_post_meta($goal_id,'followers',true);
				if(!empty($followers)){
					$totalPoints = count($followers);
					$description = 'Goal Completed';
					$wpdb->insert($table_name, array(
						'user_id'	  => $goalAuthorID, 
						'points' 	  => $totalPoints, 
						'date' 		  => current_time( 'mysql' ),
						'meta_key'    => 'goals',
						'meta_value'  => $goal_id,
						'description' => $description,
					) );

					/*
					 *Fire Notification on
					 *
					 *@Goal Completed
					 *@GDP Credited
					 *
					 */
					$followers 	  = get_post_meta($goal_id,'followers',true);
					$user 		  = get_user_by('ID', $goalAuthorID);
					$username 	  = $user->user_login;
					$title        = get_the_title($goal_id);
					$subtitle     = get_limited_string($title);
					$notification = $username.' just Completed "'.$subtitle.'" goal!';
					send_notification($goalAuthorID, $followers, $goal_id, $notification);

					$notification = 'Received '.$totalPoints.' Good Deed Points for completing goal "'.$subtitle.'"';
	    			send_notification(0, [(string) $goalAuthorID], $goal_id, $notification);
	    			
				}

			}else{
				$SQL = "SELECT ID FROM $table_name WHERE user_id = $goalAuthorID AND meta_key = 'goals' AND meta_value = $goal_id";
				$resultID = $wpdb->get_var($SQL);
				if(!empty($resultID)){
					$delete = $wpdb->delete( $table_name, array( 'ID' => $resultID ) );
				}
			}
			$response['status'] = 'success';
			$response['msg'] = 'Goal Status Updated Successfully!';
		}	
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_archive_ag',function(){
	$post_id 	   = $_REQUEST['post_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$archive = get_post_meta($post_id,'archive',true);

	if($archive != "1"){
		$udpate = update_post_meta($post_id,"archive","1");
		$archive = "Archived";
	} else {
		$udpate = update_post_meta($post_id,"archive","0");
		$archive = "Unarchived";
	}

	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = $archive. ' successfully!';;
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_add_goal_pov',function(){
	$current_user  = wp_get_current_user();
	$goal_id 	   = $_REQUEST['goal_id'];
	$description   = $_REQUEST['description'];
	$pov_parent_id = isset($_REQUEST['pov_parent_id'])?$_REQUEST['pov_parent_id']:0;;
    $time 	       = current_time('mysql');
    $response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
					
    $status = wp_get_comment_status( $pov_parent_id );
		if ( 'approved' === $status || $pov_parent_id == 0 ) {
	    $povID = wp_insert_comment([
	    	'comment_post_ID' 	   => $goal_id,
	        'comment_parent' 	   => $pov_parent_id,
	        'comment_author' 	   => $current_user->user_login,
	        'comment_author_email' => $current_user->user_email,
	        'comment_content' 	   => $description,
	        'user_id' 			   => $current_user->ID,
	        'comment_date' 		   => $time,
	        'comment_approved' 	   => 1,
	    ]);


		if($povID){
			$response['status'] = 'success';
			if($pov_parent_id > 0) $response['msg'] = 'Response Added Successfully!';
			else $response['msg'] = 'POV Added Successfully!';

			/*
			 *Fire Notification on
			 *
			 *@POV added
			 *
			 */
			$currentUserID = $current_user->ID;
			$goalAuthorID = (string) get_post_field( 'post_author', $goal_id );
			$followers 	  = get_post_meta($goal_id,'followers',true);	
			$IDkey = array_search($currentUserID, $followers);
			unset($followers[$IDkey]);
			array_push($followers, $goalAuthorID);
			$user 		  = get_user_by('ID', $currentUserID);
			$username 	  = $user->user_login;
			$title        = get_the_title($goal_id);
			$subtitle     = get_limited_string($title);
			$notification = $username.' wrote a Point of View (POV) in "'.$subtitle.'" goal!';
			send_notification($currentUserID, $followers, $goal_id, $notification);

		}
	}else $response['msg'] = 'POV Is Blocked!';

	echo json_encode($response);
	wp_die();
});
add_action('wp_ajax_unblock_pov_rating',function(){
	global $wpdb;
	$pov_id = $_REQUEST['pov_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];	

	$status = wp_set_comment_status($pov_id,'approve');

	if($status){
		$response['status'] = 'success';
		$response['msg'] = 'POV unbloacked successfully!';
	}else $response['msg'] = 'Something went wrong while unbloacking POV!';

	echo json_encode($response);
	wp_die();

});

add_action('wp_ajax_update_pov_rating',function(){
	global $wpdb;
	$pov_id = $_REQUEST['pov_id'];
	$rating = $_REQUEST['rating'];
    $response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];											  
	$status = wp_get_comment_status( $pov_id );
	if ( 'approved' === $status ) {
		$update = update_comment_meta($pov_id,'rating',$rating);  
		if($update || get_comment_meta($pov_id,'rating',true) == $rating){

			$response['status'] = 'success';
			$response['msg'] = 'POV Rated Successfully!';

			$table_name = $wpdb->prefix . "gdp";
			$POV = get_comment($pov_id);
			$POVAuthorID = $POV->user_id;
			if($rating == 0){

				$SQL = "SELECT ID FROM $table_name WHERE user_id = $POVAuthorID AND meta_key = 'POV' AND meta_value = $pov_id";
				$resultID = $wpdb->get_var($SQL);
				if(!empty($resultID)){
					$wpdb->delete( $table_name, array( 'ID' => $resultID ) );
				}
				// $pov_block = wp_delete_comment($pov_id,true);
				$pov_block = wp_update_comment(['comment_ID'=> $pov_id,'comment_approved' => 0]);

				if($pov_block){
					$response['msg'] = 'POV Blocked Successfully!';
				}

			}else{
				if($update){
					$points = '';
					switch ($rating) {
						case '4':
							$points = 8;
						break;
						case '3':
							$points = 6;
						break;
						case '2':
							$points = 4;
						break;
						case '1':
							$points = 2;
						break;
					}
					if(!empty($points)){

						$post_id = $POV->comment_post_ID;
						$notifier_user_id = get_post_field( 'post_author', $post_id );
						$title   = get_the_title($POV->comment_post_ID);
						$subtitle     = get_limited_string($title);
						$notification = 'Received '.$points.' Good Deed Points for POV for goal "'.$subtitle.'"';

						$SQL = "SELECT ID,date,points FROM $table_name WHERE user_id = $POVAuthorID AND meta_key = 'POV' AND meta_value = $pov_id";
						$resultID = $wpdb->get_row($SQL);
						if(!empty($resultID->ID)){
							$wpdb->update($table_name, array(
								'points' 	  => $points, 
								'date' 		  => current_time( 'mysql' ),
							), ['ID' => $resultID->ID ] );

						
						$ntable = $wpdb->prefix . "notification";
						$message = 'Received '.$resultID->points.' Good Deed Points for POV for goal "'.$subtitle.'"';
						$notification = 'Received '.$points.' Good Deed Points for POV for goal "'.$subtitle.'"';
						$uids = '\'%"'. $POVAuthorID . '"%\'';
						$UpdateSQL = "UPDATE $ntable SET message = '$notification',datetime ='".current_time( 'mysql' )."'  WHERE datetime = '$resultID->date' AND permalink_id = $post_id AND notifier_user_id = $notifier_user_id AND message = '$message' AND user_ids LIKE $uids ";
						$wpdb->query($UpdateSQL);

						}else{ $description = 'POV Rated';
							$wpdb->insert($table_name, array(
								'user_id'	  => $POVAuthorID, 
								'points' 	  => $points, 
								'date' 		  => current_time( 'mysql' ),
								'meta_key'    => 'POV',
								'meta_value'  => $pov_id,
								'description' => $description,
							) );

						//Fire receive GDP new notification
		    			send_notification($notifier_user_id, [(string) $POVAuthorID], $post_id, $notification);

						}

					}
				}
			}
		}  
	}else $response['msg'] = 'POV Is Blocked!';
	
	echo json_encode($response);
	wp_die();

});


add_action('wp_ajax_add_goal_attachment',function(){
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$UserID 	 = get_current_user_id(); 
	$goal_id 	 = $_REQUEST['goal_id'];
	$description = $_REQUEST['description'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$attach_id = media_handle_upload('attachment', $goal_id);
	if($attach_id && !is_wp_error($attach_id)){
		$row = add_row('attachments',[
			'file'		  => $attach_id,
			'user' 		  => $UserID,
			'description' => $description,
		],$goal_id);
		if($row){
			$response['status'] = 'success';
			$response['msg'] = 'Attachment Added Successfully!';
		}else{
			$response['msg'] = 'Attachment Uploaded But Something Went Wrong While Updateing Goal Data!';
		}
	}else{
		$response['msg'] = $attach_id->get_error_message();
	}

	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_remove_goal_attachment',function(){
	$row_id 	 = $_REQUEST['row_id'];
	$goal_id 	 = $_REQUEST['goal_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$attachments = get_field('attachments',$goal_id);
	if(!empty($attachments[$row_id]['file']['ID']))
		wp_delete_attachment($attachments[$row_id]['file']['ID'],true);	

	$row = delete_row('attachments',$row_id,$goal_id);
	if($row){
		$response['status'] = 'success';
		$response['msg'] = 'Attachment Removed Successfully!';
	}
	echo json_encode($response);
	wp_die();

});

add_action('wp_ajax_follow_goal',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = (string) get_current_user_id(); 
	$followers = get_post_meta($goal_id,'followers',true);
	if(empty($followers)) $followers=[]; 
	array_push($followers, $currentUserID);
	$udpate = update_post_meta($goal_id,'followers',$followers);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Goal Followed!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_nopriv_follow_goal',function(){
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'Login to follow goal!'
	];
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_unfollow_goal',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$followers = get_post_meta($goal_id,'followers',true);
	if(empty($followers)) $followers=[]; 
	$IDkey = array_search($currentUserID, $followers);
	unset($followers[$IDkey]);
	$udpate = update_post_meta($goal_id,'followers',$followers);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Goal Unfollowed!';
	}
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_update_user_settings',function(){
	$name 	 = $_REQUEST['name'];
	$value 	 = $_REQUEST['value'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id();
	/*if($name == 'public_profile'){
		$udpate = update_user_meta($currentUserID,'public_profile',$value);
	}else{*/	
		$US = get_user_meta($currentUserID, 'user_Settings', true);
		if(empty($US)) $US = [];
		$US[$name] = $value;
		$udpate = update_user_meta($currentUserID,'user_Settings',$US);
	// } 
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Setting Updated Successfully!';
	}
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_link_goal',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$alliance_id = $_REQUEST['alliance_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$goals = get_post_meta($alliance_id,'goals',true);
	if(empty($goals)) $goals=[]; 
	array_push($goals, $goal_id);
	$udpate = update_post_meta($alliance_id,'goals',$goals);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Goal Linked Successfully!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_unlink_goal',function(){
	$goal_id 	 = $_REQUEST['goal_id'];
	$alliance_id = $_REQUEST['alliance_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$goals = get_post_meta($alliance_id,'goals',true);
	if(empty($goals)) $goals=[]; 
	$IDkey = array_search($goal_id, $goals);
	unset($goals[$IDkey]);


	$udpate = update_post_meta($alliance_id,'goals',$goals);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Goal Unlinked Successfully!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_add_action_log',function(){
	$current_user  = wp_get_current_user();
	$alliance_id   = $_REQUEST['alliance_id'];
	$description   = $_REQUEST['description'];
	$al_parent_id  = isset($_REQUEST['al_parent_id'])?$_REQUEST['al_parent_id']:0;;
    $time 	       = current_time('mysql');

    $response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];

    $povID = wp_insert_comment([
    	'comment_post_ID' 	   => $alliance_id,
        'comment_parent' 	   => $al_parent_id,
        'comment_author' 	   => $current_user->user_login,
        'comment_author_email' => $current_user->user_email,
        'comment_content' 	   => $description,
        'user_id' 			   => $current_user->ID,
        'comment_date' 		   => $time,
        'comment_approved' 	   => 1,
    ]);


	if($povID){
		$response['status'] = 'success';
		if($al_parent_id > 0){
			$response['msg'] = 'Response Added Successfully!';
		}else $response['msg'] = 'Action Added Successfully!';
	}
	
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_join_alliance',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];


	$currentUserID = (string) get_current_user_id();
	$members = get_post_meta($alliance_id,'members',true);
	if(empty($members)) $members=[];
	$IDkey = array_search($currentUserID, $members);
	if(!$IDkey){
		array_push($members, $currentUserID);
		$udpate = update_post_meta($alliance_id,'members',$members);
		if($udpate){
			
			$alliance_invitation = get_user_meta($currentUserID,'alliance_invitation',true);
			if(isset($alliance_invitation[$alliance_id])){
				unset($alliance_invitation[$alliance_id]);
				update_user_meta($currentUserID,'alliance_invitation',$alliance_invitation);
			}

			$response['status'] = 'success';
			$response['msg'] = 'Alliance Joined Successfully!';

		}
	}else{
		$response['msg'] = 'Already member of this alliance!';
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_leave_alliance',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$members = get_post_meta($alliance_id,'members',true);
	if(empty($members)) $members=[]; 
	$IDkey = array_search($currentUserID, $members);
	unset($members[$IDkey]);
	$udpate = update_post_meta($alliance_id,'members',$members);
	if($udpate){

		$admins = get_post_meta($alliance_id,'admins',true);
		if(empty($admins)) $admins=[]; 
		$IDkey = array_search($user_id, $admins);
		unset($admins[$IDkey]);
		update_post_meta($alliance_id,'admins',$admins);

		$response['status'] = 'success';
		$response['msg'] = 'Alliance Left Successfully!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_send_alliance_invitation',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$user_id 	 = $_REQUEST['user_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 

	$alliance_invitation = get_user_meta($user_id,'alliance_invitation',true);
	if(empty($alliance_invitation)) $alliance_invitation=[]; 

	if($alliance_invitation[$alliance_id] == $currentUserID){
		$response['msg'] = 'Alliance Invitation Already Sent!';
	}else{
		$alliance_invitation[$alliance_id] = $currentUserID;
		$udpate =  update_user_meta($user_id,'alliance_invitation',$alliance_invitation);
		if($udpate){

			$response['status'] = 'success';
			$response['msg'] = 'Alliance Invitation Sent!';

			$title 		  = get_the_title($alliance_id);
			$subtitle     = get_limited_string($title);
			$user 		  = get_user_by('ID', $currentUserID);
			$username 	  = $user->user_login;
			$notification = $username . ' sent you alliance invitation "'.$subtitle.'"' ;
			send_notification($currentUserID, [(string) $user_id], $alliance_id, $notification);
		}
	}
	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_reject_ai',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 

	$alliance_invitation = get_user_meta($currentUserID,'alliance_invitation',true);
	unset($alliance_invitation[$alliance_id]);
	$udpate = update_user_meta($currentUserID,'alliance_invitation',$alliance_invitation);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Alliance Invitation Rejected!';
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_ai_make_admin',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$user_id 	 = (string) $_REQUEST['user_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$admins = get_post_meta($alliance_id,'admins',true);
	if(empty($admins)) $admins=[]; 
	array_push($admins, $user_id);
	$udpate = update_post_meta($alliance_id,'admins',$admins);
	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Admin Rights Assigned Successfully!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_ai_remove_admin',function(){
	$alliance_id = $_REQUEST['alliance_id'];
	$user_id 	 = $_REQUEST['user_id'];
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];

	$admins = get_post_meta($alliance_id,'admins',true);
	if(empty($admins)) $admins=[]; 
	$IDkey = array_search($user_id, $admins);
	unset($admins[$IDkey]);
	$udpate = update_post_meta($alliance_id,'admins',$admins);

	if($udpate){
		$response['status'] = 'success';
		$response['msg'] = 'Admin Rights Removed Successfully!';
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_search_member_ai',function(){
	global $wpdb;
	$key = $_REQUEST['key'];
	$alliance_id = $_REQUEST['alliance_id'];
	$membersData = '';
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred',
		'membersData' => '',
	];
	$currentUserID = get_current_user_id(); 
	$AAID = get_post_field( 'post_author', $alliance_id );
	$myconnectionsIDs = get_user_meta($currentUserID,'connections',true);
	$members = get_post_meta($alliance_id,'members',true);
	if(empty($members))  $members = [];
	if(!empty($myconnectionsIDs)){
		//Exlude Members 
		$onlyInclude = array_diff($myconnectionsIDs, $members);
		//Exlude Alliance Admin 
		$onlyInclude = array_diff($onlyInclude, [$AAID]);
		$cUsers = New WP_User_Query([
    		'include' => $onlyInclude,
    		'search'  => '*'.esc_attr( $key ).'*',
    		 'meta_query' => array(
		        'relation' => 'OR',
		        array(
		            'key'     => 'full_name',
		            'value'   => $key,
		            'compare' => 'LIKE'
		        ),
		    )
    	]); if(!empty($cUsers->get_results())){
    		$response['status'] = 'success';
			$response['msg'] = 'Members found!';
			
			foreach($cUsers->get_results() as $cuser){ 
				$full_name 			 = get_user_meta($cuser->ID, 'full_name', true);
    			$user_registered 	 = date('Y/m/d',strtotime($cuser->user_registered)); 
    			$profile_picture 	 = get_user_profile_picture($cuser->ID);
    			$profile_url 	 	 = get_author_posts_url($cuser->ID);
    			$alliance_invitation = get_user_meta($cuser->ID,'alliance_invitation',true);
				if(empty($alliance_invitation)) $alliance_invitation=[]; 
    			if(array_key_exists($alliance_id,$alliance_invitation)){
    				$IDD = 'invitation-sent';
    				$LABEL = 'Invitation Sent';
    			}else{
    				$IDD = 'invite-member';
    				$LABEL = '<img src="'. get_template_directory_uri() .'/images/link-add-icon.svg" class="img-fluid">Invite to Alliance';
    			}
				set_query_var( 'userData', $cuser );
				$membersData .= '<div class="connection-invitation-item">';
				$membersData .= load_template_part('template-parts/user','listing-content');
				$membersData .= '    <div class="allience-req-btn">
				    <a href="javascript:;" id="'. $IDD . '" data-alliance_id="'. $alliance_id .'" data-user_id="'. $cuser->ID .'" class="btn btn-remove-con" > 
				    	'.$LABEL.'
                    </a>
                    </div>
				</div>';
			}
    	}else{
			$response['msg'] = 'No members found!';
			$response['request'] = $cUsers->request;
		}
	}else{
		$response['msg'] = 'No members found!';
	}

	$response['membersData'] = $membersData;
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_deactivate_account',function(){
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$user = get_user_by('ID',$currentUserID);

	// $isDeactivated = get_user_meta($currentUserID,'isDeactivated',true);
	$isDeactivated = 1;
	$udpate = update_user_meta($currentUserID,'isDeactivated',$isDeactivated);
	if($udpate){

		$to = $user->user_email;
		$name =  $user->first_name . ' ' . $user->last_name;
		$subject = 'Goalore Account Deactivated';
		$body = 'Dear ' . $name;
		$body .= '<br><br>Your Goalore account has been deactivated. To reactivate, please contact Goalore administrator using the Contact Us feature.';
		$body .= '<br><br>Regards';
		$body .= '<br><b>Goalore</b>';
		$headers = array('Content-Type: text/html; charset=UTF-8'); 
		wp_mail( $to, $subject, $body, $headers );

		$response['status'] = 'success';
		$response['msg'] = 'Account Deactivated Successfully!';
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_delete_account',function(){
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$currentUserID = get_current_user_id(); 
	$user = get_user_by('ID',$currentUserID);

	$isDeleted = 1;
	$udpate = update_user_meta($currentUserID,'isDeleted',$isDeleted);
	if($udpate){

		$to = $user->user_email;
		$subject = 'Goalore Account Deleted';

		$name =  $user->first_name . ' ' . $user->last_name;
		$body = 'Dear ' . $name;
		$body .= '<br><br>Your Goalore account has been deleted as per your request.';
		$body .= '<br><br>Regards';
		$body .= '<br><b>Goalore</b>';
		$headers = array('Content-Type: text/html; charset=UTF-8'); 
		wp_mail( $to, $subject, $body, $headers );

		$body = 'Dear Admin';
		$body .= '<br><br>Member with email '.$to.' account was deleted pemanently.';
		$body .= '<br><br>Regards';
		$body .= '<br><b>Goalore</b>';
		$adminemail = get_option('admin_email');
		wp_mail( $adminemail, $subject, $body, $headers );



		$response['status'] = 'success';
		$response['msg'] = 'Account Deleted Successfully!';
		wp_logout();
	}
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_invite_friend',function(){
	global $wpdb;
	$email = $_REQUEST['email'];
	$subject = $_REQUEST['subject'];
	$friendmessage = $_REQUEST['message'];
	$friendmessage = str_replace(PHP_EOL,"<br>",$friendmessage);
	$secret = GCV2_Private;
	$captcha_response = $_REQUEST["captcha"];

	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];

	$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captcha_response}");
	$captcha_success=json_decode($verify);

	if ($captcha_success->success==true) {
		$friend = get_user_by('email',$email);
		if(!$friend){
			$currentUserID = get_current_user_id(); 
			$referral_code = wp_generate_password(20,false);
			$referral_link = add_query_arg( ['referral_code' => $referral_code, 'email' => $email ] , get_permalink(96) );
			$full_name = get_user_meta($currentUserID, 'full_name', true);
			
			$message = 'Dear User,<br><br>
				You have been invited by '.$full_name.' to join Goalore.<br><br>
				Please use below link to register: <br><br>
				'.$referral_link.'<br><br>
				Here is a message from your friend:<br><br><br>
				'.$friendmessage.'<br><br><br>
				Regards,<br>
				<b>Goalore</b> ';

			$headers = array('Content-Type: text/html; charset=UTF-8'); 
			$mail = wp_mail( $email, $subject, $message, $headers );
			$response['mail'] = $mail;
			if($mail){
			
				$table_name = $wpdb->prefix . "referral_friend";
				$SQL = "SELECT ID FROM $table_name WHERE referrer_id = $currentUserID AND referral_email = '$email'";
				$resultID = $wpdb->get_var($SQL);
				if(!empty($resultID)){
					$wpdb->update($table_name, array(
						'referral_code' => $referral_code,
					), ['ID' => $resultID ] );
				}else{
					$wpdb->insert($table_name, array(
						'referrer_id'	 => $currentUserID, 
						'referral_email' => $email, 
						'referral_code'  => $referral_code,
					) );
				}

				$response['status'] = 'success';
				$response['msg'] = 'Invitation sent successfully to '. $email;
				$response['SQL'] =  $SQL;
			}
		}else $response['msg'] = 'Recipient email already exists!';
	}else $response['msg'] = 'Recaptcha verification failed!';
	
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_update_profile',function(){

	$userID 	 = get_current_user_id(); 
	check_ajax_referer( 'ajax-update-profile-nonce', 'security' .$userID );

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];

	$dob 	 = $_REQUEST['dob'];
	$gender  = $_REQUEST['gender'];
	$country = $_REQUEST['country'];

	update_user_meta($userID,'gender',$gender);
	update_user_meta($userID,'country',$country);
	update_user_meta($userID,'dob',$dob);

	$attach_id = media_handle_upload('profile_picture', 0);
	if($attach_id && !is_wp_error($attach_id)){
		update_user_meta($userID,'profile_picture',$attach_id);	
	}
	/*else{
		$response['msg'] = $attach_id->get_error_message();
	}*/
		$response['status'] = 'success';
		$response['msg'] = 'Profile Updated Successfully!';

	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_read_notification',function(){
	global $wpdb;
	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$id 	= $_REQUEST['id'];
	$userID = (string) get_current_user_id(); 

	$table_name = $wpdb->prefix . "notification";
	$SQL = "SELECT readby_user_ids FROM $table_name WHERE ID = $id ";
	$readby_user_ids = $wpdb->get_var($SQL);

	if(!empty($readby_user_ids)){
		$readby_user_ids = unserialize($readby_user_ids);
		if(!in_array($userID, $readby_user_ids)){
			array_push($readby_user_ids, $userID);
			$readby_user_ids = maybe_serialize($readby_user_ids);
			$wpdb->update($table_name, array(
				'readby_user_ids' => $readby_user_ids,
			), ['ID' => $id ] );
			$response['status'] = 'success';
			$response['msg'] = '';
		}	
	}else{
		$readby_user_ids = [];
		array_push($readby_user_ids, $userID);
		$readby_user_ids = maybe_serialize($readby_user_ids);
		$wpdb->update($table_name, array(
			'readby_user_ids' => $readby_user_ids,
		), ['ID' => $id ] );
		$response['status'] = 'success';
		$response['msg'] = '';
	}
	

	echo json_encode($response);
	wp_die();

});