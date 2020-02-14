	<?php 
	/**
	 *
	 *Notification Content
	 *
	 */

	global $wpdb; 
	$CurrentUserID = get_current_user_id(); 
	$table_notification = $wpdb->prefix . "notification";
	$SQL = ' SELECT * FROM '.$table_notification.' WHERE user_ids LIKE \'%"'. $CurrentUserID . '"%\' ORDER BY datetime DESC ' ;
	$notifications = $wpdb->get_results ( $SQL );
		if(!empty($notifications)){
			foreach($notifications as $notif){
				$readByuser = $notif->readby_user_ids;
				$readByuser = unserialize($readByuser);
				if(empty($readByuser)) $readByuser = [];
				$profile_picture = get_user_profile_picture($notif->notifier_user_id);
				if (wp_http_validate_url( $notif->permalink_id )) {
					$permalink = $notif->permalink_id;
				}else  $permalink = get_permalink($notif->permalink_id);
				$permalink = !empty($permalink) ? $permalink : 'javascript:;';
				$message = $notif->message; ?>
				<a class="dropdown-item" href="<?php echo$permalink; ?>" data-id="<?php echo$notif->ID; ?>" id="<?php echo !in_array($CurrentUserID,$readByuser)?'read-notification':''; ?>" >
		            <div class="notification-dropdown-item <?php  echo in_array($CurrentUserID, $readByuser) ? 'read-notification' : ''; ?> ">
		                <div class="ndi-img">
		                    <img class="" src="<?php echo $profile_picture; ?>">
		                </div>
		                <div class="ndi-msg">
		                    <p><?php echo $message; ?></p>
		                </div>
		            </div>
		        </a>
			<?php }
		}else{ ?>
			<a class="dropdown-item" href="javascript:;">
		        <div class="notification-dropdown-item">
		            <div class="ndi-msg">
		                <p>No Notifications</p>
		            </div>
		        </div>
		    </a>
	<?php } ?>