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

/*add_action( 'admin_post_login_frm', function(){
	set_transient('login_errors', 'Already loggedIn!', MINUTE_IN_SECONDS );
	wp_redirect(site_url());
	exit(); 
} );*/

/*add_action( 'admin_post_register_frm', function(){
	set_transient('register_errors', 'No Registration when loggedIn!', MINUTE_IN_SECONDS );
	wp_redirect(get_permalink(96));
	exit(); 
});*/

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
			$creds = [
				'user_login'    => $username,
		        'user_password' => $password,
		        'remember'      => false,
			];
			$user = wp_signon( $creds, false );
			$response['status'] = 'success';
			$response['msg'] = 'Registration Successfully!';
			$response['redirect'] = get_permalink(138);

			$table_gdp = $wpdb->prefix . "gdp";
			$wpdb->insert($table_gdp, array(
				'user_id'	  => $user_id, 
				'points' 	  => '50', 
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

				$notification = 'Received 10 Good Deed Points for "Referral Joining" of "'.$email.'"';
	    		send_notification(0, [ (string) $resultID->referrer_id], '', $notification);
			}


		}else{
			$response['msg'] = $user_id->get_error_message();
		}
	}else{
		$response['msg'] = 'Invalid Zip Code!';
	}

	echo json_encode($response);
	wp_die();
});


add_action('wp_ajax_user_category_update', function() {
	$categories = $_POST['catIDs'];
	$response = [
		'status' => 'failed',
		'msg' => 'Registration Failed!'
	];
	if(!empty($categories)){
		$update = update_user_meta(get_current_user_id(),'categories',$categories);
		if($update){
			$response['status'] = 'success';
			$response['msg'] = 'Categories updated!';
		}
	}
	echo json_encode($response);
	wp_die();
});

/*add_action('admin_post_nopriv_register_user_category', function(){
	wp_redirect(get_permalink(96));
	exit(); 
});*/

/*add_action('admin_post_reset_pwd_frm', function(){
	set_transient('reset_pwd_error', 'No resting password when loggedIn!', MINUTE_IN_SECONDS );
	wp_redirect(get_permalink(100));
	exit(); 
});*/

add_action('admin_post_nopriv_reset_pwd_frm', function(){
	$pageId = 100;
	$user_login = $_POST['user_login'];
	$user = get_user_by('email', $user_login);
	if(!$user){
		$user = get_user_by('login', $user_login);
	}
	if($user){
		$new_password = wp_generate_password();
		wp_set_password($new_password, $user->user_id);
		$message = 'Dear '.$user->first_name;
		$message .= '<br>
			Your new password is '. $password;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($user->user_email, 'Password Reset', $message, $headers );
		set_transient('reset_pwd_error', 'Password Reset Email Sent!', MINUTE_IN_SECONDS );

	}else{
		set_transient('reset_pwd_error', 'User not found!', MINUTE_IN_SECONDS );
	}
	wp_redirect(get_permalink($pageId));
	exit(); 
});



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
	$goalID = wp_insert_post([
		'post_title'   => $title,
	    'post_status'  => 'publish',
	    'post_type'    => 'goals',
	    'post_author'  => $currentUserID,
	    // 'tax_input'    => ['goal_categories'=>[$category,$subcategory]],
	    'meta_input'   => [
	    	'type' 	   => $type,
	    	'target'   => str_replace('-', '', $target),
	    	'status'   => $status,
	    ]
	]);
	if(!is_wp_error($goalID)){
		$term_taxonomy_ids = wp_set_post_terms($goalID, [$category,$subcategory], 'goal_categories');
		$response['status'] = 'success';
		$response['msg'] = 'Goal Created Successfully!';

		/*
		 *Fire Notification on:
		 *
		 *@Connection start a new goal
		 *
		 */
		$full_name = get_user_meta($currentUserID, 'full_name', true);
		$notification = $full_name.' just started a new goal "'.$title.'"';
		$connections = get_user_meta($currentUserID,'connections',true);
		send_notification($currentUserID, $connections, $goalID, $notification);


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
	$AlliancesID = wp_insert_post([
		'post_title'   => $title,
	    'post_status'  => 'publish',
	    'post_type'    => 'alliances',
	    'post_author'  => $currentUserID,
	    'meta_input'   => [
	    	'objective'=> $objective,
	    	'status'   => $status,
	    ]
	]);
	if(!is_wp_error($AlliancesID)){
		$response['status'] = 'success';
		$response['msg'] = 'Alliance Created Successfully!';

		/*
		 *Fire Notification on:
		 *
		 *@Connection creates new goal
		 *
		 */

		$full_name = get_user_meta($currentUserID, 'full_name', true);
		$notification = $full_name.' just created a new alliance "'.$title.'"';
		$connections = get_user_meta($currentUserID,'connections',true);
		send_notification($currentUserID, $connections, $AlliancesID, $notification);

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
	}
	echo json_encode($response);
	wp_die();
});

add_action('wp_ajax_remove_connection_request',function(){
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

	if(!empty($row_id) && $row_id != '0')
		$row = update_row($selector,$row_id,$data,$goal_id);
	else $row = add_row($selector,$data,$goal_id);

	

	if($row){
		$response['status'] = 'success';
		$response['msg'] = 'Successfully!';

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
		$followers 	   = get_post_meta($goal_id,'followers',true);
		$full_name 	   = get_user_meta($goalAuthorID, 'full_name', true);
		$notification  = '';
		
		if(!empty($row_id) && $row_id != '0'){
			if($status == 'Completed') {
				$notification = $full_name.' has completed a '. trim($selector,'s').' in goal "'.$title.'"';
			}
		}else{
			$notification = $full_name.' has posted a new '. trim($selector,'s').' in goal "'.$title.'"';
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

	$isStatus = metadata_exists('post', $goal_id, 'goal_status');
	if($isStatus){
		$status = get_post_meta($goal_id,'goal_status',true);
		if($status == 'incomplete') 
			$status = 'complete';
		else $status = 'incomplete';
	}else $status = 'complete';
	
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
				}

				/*
				 *Fire Notification on
				 *
				 *@Goal Completed
				 *@GDP Credited
				 *
				 */
				$followers 	  = get_post_meta($goal_id,'followers',true);
				$full_name 	  = get_user_meta($goalAuthorID, 'full_name', true);
				$title        = get_the_title($goal_id);
				$notification = $full_name.' just Completed his "'.$title.'" goal!';
				send_notification($goalAuthorID, $followers, $goal_id, $notification);

				$notification = 'Received '.$totalPoints.' Good Deed Points for completing goal "'.$title.'"';
    			send_notification(0, [(string) $goalAuthorID], $goal_id, $notification);

			}else{
				$SQL = "SELECT ID FROM $table_name WHERE user_id = $goalAuthorID AND meta_key = 'goals' AND meta_value = $goal_id";
				$resultID = $wpdb->get_var($SQL);
				if(!empty($resultID)){
					$delete = $wpdb->delete( $table_name, array( 'ID' => $resultID ) );
				}
			}
			$response['status'] = 'success';
			$response['msg'] = 'Gaol Status Updated Successfully!';
		}	
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
		$response['msg'] = 'POV Added Successfully!';

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
		$full_name 	  = get_user_meta($currentUserID, 'full_name', true);
		$title        = get_the_title($goal_id);
		$notification = $full_name.' wrote a Point of View (POV) in "'.$title.'" goal!';
		send_notification($currentUserID, $followers, $goal_id, $notification);

	}
	
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
	$update = update_comment_meta($pov_id,'rating',$rating);
	if($update){

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
			$pov_block = wp_delete_comment($pov_id,true);
			if($pov_block){
				$response['msg'] = 'POV Blocked Successfully!';
			}

		}else{
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
				$SQL = "SELECT ID FROM $table_name WHERE user_id = $POVAuthorID AND meta_key = 'POV' AND meta_value = $pov_id";
				$resultID = $wpdb->get_var($SQL);
				if(!empty($resultID)){
					$wpdb->update($table_name, array(
						'points' 	  => $points, 
						'date' 		  => current_time( 'mysql' ),
					), ['ID' => $resultID ] );
				}else{ $description = 'POV Rated';
					$wpdb->insert($table_name, array(
						'user_id'	  => $POVAuthorID, 
						'points' 	  => $points, 
						'date' 		  => current_time( 'mysql' ),
						'meta_key'    => 'POV',
						'meta_value'  => $pov_id,
						'description' => $description,
					) );
				}

				$post_id 	  = $POV->comment_post_ID;
				$post_title   = get_the_title($POV->comment_post_ID);
				$notification = 'Received '.$points.' Good Deed Points for POV rated in goal "'.$post_title.'"';
    			send_notification(0, [(string) $POVAuthorID], $post_id, $notification);

			}
		}
	}
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
		$response['msg'] = 'Goal Unfollowed!';
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
		$response['msg'] = 'Action Log Added Successfully!';
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
	array_push($members, $currentUserID);
	$udpate = update_post_meta($alliance_id,'members',$members);
	if($udpate){
		
		$alliance_invitation = get_user_meta($currentUserID,'alliance_invitation',true);
		unset($alliance_invitation[$alliance_id]);
		update_user_meta($currentUserID,'alliance_invitation',$alliance_invitation);

		$response['status'] = 'success';
		$response['msg'] = 'Alliance Joined Successfully!';

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
		$response['msg'] = 'Alliance Leaved Successfully!';
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
		$body .= '<br><br>You Goalore account was deactivated to reactivate your account contact administrator.';
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
		$body = 'Dear Admin';
		$body .= '<br><br>You Goalore account was deleted pemanently.';
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

	$response 	 = [
		'status' => 'failed',
		'msg' 	 => 'An error occurred'
	];
	$friend = get_user_by('email',$email);

	if(!$friend){
		$currentUserID = get_current_user_id(); 
		$user = get_user_by('ID',$currentUserID);
		$referral_code = wp_generate_password(20,false);
		$referral_link = add_query_arg( ['referral_code' => $referral_code, 'email' => $email ] , get_permalink(96) );
		$from = $user->user_email;
		
		$message = 'Dear User,<br><br>

			You have been invited by '.$from.' to join Goalore.<br><br>

			Please use below link to register: <br><br>

			'.$referral_link.'<br><br>

			Also please check message from inviter:<br><br><br>


			'.$friendmessage.'<br><br><br>


			Regards,<br>
			<b>Goalore</b> ';
		
		$headers = array('Content-Type: text/html; charset=UTF-8'); 
		$headers[] = 'From: Goalore Member <'.$from.'>';

		$mail = wp_mail( $email, $subject, $message, $headers );
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
		$response['msg'] = 'Prodile Updated Successfully!';

	echo json_encode($response);
	wp_die();
});