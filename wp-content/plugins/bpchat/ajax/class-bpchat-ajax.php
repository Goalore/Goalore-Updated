<?php

class bpchat_Ajax {

    public $ajax_actions;
	public $is_buddypress;
	public $is_bpfriend;
    /*
     * Configuring and intializing ajax files and actions
     *
     * @param  -
     * @return -
     */

    public function __construct() {
		
		if(session_id() == '') {
			session_start();
		}
		$this->check_buddypress();
		$data = get_option('bpchat_options');
		$this->is_bpfriend = $data['only_bp_friend'];
        //$this->configure_actions();
        //add_action('wp_enqueue_scripts', array($this, 'include_scripts'));
    }

    public function initialize() {
        $this->configure_actions();
    }

    /*
     * Confire the application specific AJAX actions array and
     * load the AJAX actions bases on supplied parameters
     *
     * @param  -
     * @return -
     */

    public function configure_actions() {

        $this->ajax_actions = array(
			"load_bpc_window" => array("action" => "load_bpc_window_action", "function" => "load_bpc_window_function"),
            "load_friends" => array("action" => "load_friends_action", "function" => "load_friends_function"),
			"refresh_friends" => array("action" => "refresh_friends_action", "function" => "refresh_friends_function"),
			"online_friends" => array("action" => "online_friends_action", "function" => "online_friends_function"),
			"bp_online_friends" => array("action" => "bp_online_friends_action", "function" => "bp_online_friends_function"),
			"bp_group_list" => array("action" => "bp_group_list_action", "function" => "bp_group_list_function"),
			"bp_group_friend_list" => array("action" => "bp_group_friend_list_action", "function" => "bp_group_friend_list_function"),
			"search_friends" => array("action" => "search_friends_action", "function" => "search_friends_function"),
			"load_chat" => array("action" => "load_chat_action", "function" => "load_chat_function"),
			"load_allchat" => array("action" => "load_allchat_action", "function" => "load_allchat_function"),
			"submit_message" => array("action" => "submit_message_action", "function" => "submit_message_function"),
			"set_active_chat" => array("action" => "set_active_chat_action", "function" => "set_active_chat_function"),
			"remove_active_chat" => array("action" => "remove_active_chat_action", "function" => "remove_active_chat_function"),
			"load_active_chat" => array("action" => "load_active_chat_action", "function" => "load_active_chat_function"),
        );

        /*
         * Add the AJAX actions into WordPress
         */
        foreach ($this->ajax_actions as $custom_key => $custom_action) {

            if (isset($custom_action["logged"]) && $custom_action["logged"]) {
                // Actions for users who are logged in
                add_action("wp_ajax_" . $custom_action['action'], array($this, $custom_action["function"]));
            } else if (isset($custom_action["logged"]) && !$custom_action["logged"]) {
                // Actions for users who are not logged in
                add_action("wp_ajax_nopriv_" . $custom_action['action'], array($this, $custom_action["function"]));
            } else {
                // Actions for users who are logged in and not logged in
                add_action("wp_ajax_nopriv_" . $custom_action['action'], array($this, $custom_action["function"]));
                add_action("wp_ajax_" . $custom_action['action'], array($this, $custom_action["function"]));
            }
        }
    }
	
	/*
     * chat_user_online functions for checking user online or offline
     *
     * @param  -
     * @return -
     */
	 
	public function chat_user_online($user_id, $time=5){			
			$user = get_user_by( 'ID', $user_id );
			$blogtime = strtotime(current_time( 'mysql' ));
			$meta = strtotime(get_user_meta($user->ID, 'bpc_login_time', true));
			$status = get_user_meta($user->ID, 'bpc_login_status', true);
			$interval  = abs($blogtime - $meta);
			$minutes   = round($interval / 60);
			if($minutes <= 5){
				return true;
			}else {
				return false;
			}
	}
	
	public function chat_user_status($user_id){			
			//$user = get_user_by( 'ID', $user_id );
			//$meta = get_user_meta($user_id, 'bpc_login_time', true);
			$status = get_user_meta($user_id, 'bpc_login_status', true);
			if($status == 'online'){
				return true;
			}else {
				return false;
			}
	}
	
	/*
     * is_buddypress functions for checking buddypress installation status
     *
     * @param  -
     * @return -
     */
	 
	public function check_buddypress(){
		global $wpdb;
		$isbp = $wpdb->get_results( "SELECT id FROM {$wpdb->base_prefix}bp_friends ORDER BY id LIMIT 1");
		if(!empty($isbp)){
			$this->is_buddypress = true;
		}else{
			$this->is_buddypress = false;
		}
	}
	
    /*
     * load_bpc_window function for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function load_bpc_window_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["bpc_window"] = '';
		global $wpdb;
		$fsql = $wpdb->get_results( "SELECT id FROM {$wpdb->base_prefix}bp_friends ORDER BY id LIMIT 1");
		$gsql = $wpdb->get_results( "SELECT id FROM {$wpdb->base_prefix}bp_groups ORDER BY id LIMIT 1");
		$UserId = get_current_user_id();
		
		$data = get_option('bpchat_options');
		
    	$member_list = !empty($data['bpc_lg_member_list'])?$data['bpc_lg_member_list']:'Member List';
		$members = !empty($data['bpc_lg_members'])?$data['bpc_lg_members']:'Members';
		$friends = !empty($data['bpc_lg_friends'])?$data['bpc_lg_friends']:'Friends';
		$search_friends = !empty($data['bpc_lg_search_friends'])?$data['bpc_lg_search_friends']:'Search friends';
		
		$refresh = !empty($data['bpc_lg_refresh'])?$data['bpc_lg_refresh']:'Refresh';
		$monline = !empty($data['bpc_lg_monline'])?$data['bpc_lg_monline']:'Members online';
		$fonline = !empty($data['bpc_lg_fonline'])?$data['bpc_lg_fonline']:'Friends online';
		$glist = !empty($data['bpc_lg_glist'])?$data['bpc_lg_glist']:'Group List';
		
		$morf = ($this->is_buddypress && $this->is_bpfriend)?$friends:$members;
			
		$a["bpc_window"] .= '<div id="bpchatChatsWindow">
							</div>
							<div id="bpchatFriendsWindow">
								<div class="bpchatFriendsHolder" data-window-state="0" data-identifier="-1" data-parameter-window-id="-1">
									<div class="bpchatFriendsHeader" data-event="close-friends-window" data-parameter-window-id="-1">
										<span class="chatMemberList">'.$member_list.'</span>
										<span class="bpchatFriendsEvents">
											<i data-location="bpchat-event-size--1" data-event="close-friends-window" data-parameter-window-id="-1" class="minus">&minus;</i>
										</span>
									</div>
									<div class="bpchatFriendsFilter">
										<span class="RefreshMembersList" title="'.$refresh.'" data-event="refresh_friends"><img src="'.BPC_URL.'images/refresh.png"/></span>
										<span class="LoadMembersOnline" title="'.$monline.'" data-event="online_friends"><img src="'.BPC_URL.'images/circle.png"/></span>';
										
										if(!empty($fsql) && !$this->is_bpfriend){
					$a["bpc_window"] .= '<span class="LoadOnlyFriendsOnline" title="'.$fonline.'" data-event="bp_online_friends"><img src="'.BPC_URL.'images/friends.png"/></span>';
										}
										if(!empty($gsql)){
					$a["bpc_window"] .= '<span class="LoadGroupList" title="'.$glist.'" data-event="bp_group_list"><img src="'.BPC_URL.'images/group.png"/></span>';
										}
			   $a["bpc_window"] .= '</div>
									<div class="bpchatFriendsBody">
										<center style="padding-top: 10px">
											<img id="loadingBar" />
										</center>
									</div>
									
									<div class="bpchatFriendsFooter">
										<input type="text" id="bpchatSearchFriends" placeholder="'.$search_friends.'" />
									</div>
								</div>
								<div class="bpchatFriendsTitle" data-event="open-friends-window">
									<span class="chatIcon"><img src="'.BPC_URL.'images/chatx16.png"/></span><span class="chat-member">'.$morf.' <b class="bpchatFriendsCount"></b></span><span class="listOpenIcon">&and;</span>
								</div>
							</div>';
				
		echo json_encode($chat);
		exit;
    }
	
    /*
     * load_friend functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function load_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		$a["friendsCount"] = -1;
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$no_friends = !empty($data['bpc_lg_no_friends'])?$data['bpc_lg_no_friends']:'There are no friends.';
		
		global $wpdb;
		$wpdb->show_errors = false;
				
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$meta_key = 'bpc_login_time';
		$time = 5;
		
		if($this->is_buddypress && $this->is_bpfriend){
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));
			
			$FriendsSQL1 = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) <= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));
		}else{
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $time, $blogtime));	
			$FriendsSQL1 = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) <= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $time, $blogtime));		
		}
				
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				$online = $this->chat_user_online($ID)?'chat_online':'chat_offline';
				
				$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus ".$online."_circle\"></i><img class=\"bpchatFriendsImage ".$online."\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
			}
		}
		if(count($FriendsSQL) < 50 && !empty($FriendsSQL1)){
			foreach($FriendsSQL1 as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				$online = 'chat_offline';
				
				$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus ".$online."_circle\"></i><img class=\"bpchatFriendsImage ".$online."\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
			}
		}
		
		if($this->is_buddypress && $this->is_bpfriend){
			$a["friendsCount"] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->base_prefix}bp_friends WHERE (initiator_user_id = %d AND is_confirmed = 1) OR (friend_user_id = %d AND is_confirmed = 1)", $UserId, $UserId));
		}else{
			$a["friendsCount"] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE ID NOT LIKE %d", $UserId, $UserId));		
		}
		if($a["friendsCount"] == 0)
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_friends."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		exit;
    }

    /*
     * refresh_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function refresh_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		$a["friendsCount"] = -1;
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$no_friends = !empty($data['bpc_lg_no_friends'])?$data['bpc_lg_no_friends']:'There are no friends.';
		
		global $wpdb;
		$wpdb->show_errors = true;
				
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$meta_key = 'bpc_login_time';
		
		$time = 5;
				
		if($this->is_buddypress && $this->is_bpfriend){
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));
			
			$FriendsSQL1 = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) <= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));
		}else{
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $time, $blogtime));	
			$FriendsSQL1 = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) <= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $time, $blogtime));		
		}
		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				$online = 'chat_online';
				
				$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus ".$online."_circle\"></i><img class=\"bpchatFriendsImage ".$online."\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
			}
		}
		
		if(count($FriendsSQL) < 50 && !empty($FriendsSQL1)){
			foreach($FriendsSQL1 as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				$online = 'chat_offline';
				
				$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus ".$online."_circle\"></i><img class=\"bpchatFriendsImage ".$online."\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
			}
		}
		
		
		$a["friendsCount"] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE ID NOT LIKE %d", $UserId, $UserId));
		
		if($a["friendsCount"] == 0)
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_friends."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		
		exit;
    }

    /*
     * online_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function online_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$nom_online = !empty($data['bpc_lg_nom_online'])?$data['bpc_lg_nom_online']:'No member online.';
		
		global $wpdb;
		$wpdb->show_errors = false;
		$time = 5;
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$meta_key = 'bpc_login_time';

		if($this->is_buddypress && $this->is_bpfriend){
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));
		}else{
			$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID AND um.meta_key = '%s' WHERE u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $time, $blogtime));	
		}	
				
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				//$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				if($this->chat_user_online($ID)){
					$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus chat_online_circle\"></i><img class=\"bpchatFriendsImage chat_online\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
				}
			}
		}
		if(empty($a["FriendsRow"]))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$nom_online."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		exit;
    }
	
    /*
     * bp_online_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function bp_online_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$nof_online = !empty($data['bpc_lg_nof_online'])?$data['bpc_lg_nof_online']:'No friends online';
		
		global $wpdb;
		$wpdb->show_errors = true;
				
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$meta_key = 'bpc_login_time';
		$time = 5;
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_friends bpf INNER JOIN $wpdb->usermeta um ON um.user_id = user_id AND um.meta_key = '%s' WHERE ((bpf.initiator_user_id = %d AND bpf.friend_user_id = u.ID AND bpf.is_confirmed = 1) OR (bpf.initiator_user_id = u.ID AND bpf.friend_user_id = %d AND bpf.is_confirmed = 1)) AND u.ID NOT LIKE %d AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= '%s' ORDER BY u.ID LIMIT 50", $meta_key, $UserId, $UserId, $UserId, $time, $blogtime));	
			
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($ID));
				
				//$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				if($this->chat_user_online($ID)){
					$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus chat_online_circle\"></i><img class=\"bpchatFriendsImage chat_online\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
				}
			}
		}
				
		if(empty($FriendsSQL)){
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		}else if(empty($a["FriendsRow"])){
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$nof_online."</center>";
		}
		echo json_encode($chat);
		exit;
    }

    /*
     * bp_group_list functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function bp_group_list_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$not_member = !empty($data['bpc_lg_not_member'])?$data['bpc_lg_not_member']:'You are not a member of any group.';
		
		global $wpdb;
		$wpdb->show_errors = false;
				
		$UserId = get_current_user_id();
	
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT g.id AS id, g.name AS name FROM {$wpdb->base_prefix}bp_groups g, {$wpdb->base_prefix}bp_groups_members gm WHERE g.id = gm.group_id AND gm.user_id = %d AND gm.is_confirmed = 1 ORDER BY g.id LIMIT 50", $UserId));		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				
				$string = stripslashes(htmlspecialchars($Row->name));
				$GroupName = mb_strlen($string) > 20 ? mb_substr($string,0,17).'...' : $string;
				//$GroupName = htmlspecialchars($Row->name);
								
				$a["FriendsRow"] .= "<div data-event=\"bp_group_friend_list\" data-parameter-group-name=\"".$GroupName."\" data-parameter-group-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><div class=\"bpchatFriendsName\">".$GroupName."</div></div>";
			}
		}
				
		if(empty($a["FriendsRow"]))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$not_member."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		exit;
    }

    /*
     * bp_group_friend_list functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function bp_group_friend_list_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$nom_online = !empty($data['bpc_lg_nom_online'])?$data['bpc_lg_nom_online']:'No members online.';
		
		global $wpdb;
		$wpdb->show_errors = false;
				
		$UserId = get_current_user_id();
		$GroupID = $_POST["GroupID"];
		
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT u.ID AS user_id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->base_prefix}bp_groups_members gm WHERE u.ID NOT LIKE %d AND gm.user_id = u.ID AND gm.group_id = %d ORDER BY u.ID LIMIT 50", $UserId, $GroupID));		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->user_id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->user_id));
				
				//$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				if($this->chat_user_online($ID)){
					$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus chat_online_circle\"></i><img class=\"bpchatFriendsImage chat_online\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
				}
			}
		}
				
		if(empty($a["FriendsRow"]))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$nom_online."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		exit;
    }
		
    /*
     * search_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function search_friends_function() {
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('bpchat_options');
		
    	$no_result = !empty($data['bpc_lg_no_result'])?$data['bpc_lg_no_result']:'No results.';
		$no_friends = !empty($data['bpc_lg_no_friends'])?$data['bpc_lg_no_friends']:'There are no friends';
		
		global $wpdb;
		$wpdb->show_errors = false;
		
		$searchData = $_POST["searchData"];
		
		$UserId = get_current_user_id();
	
		
		$FriendsSQL = $wpdb->get_results("SELECT id, display_name FROM $wpdb->users WHERE display_name LIKE '%".esc_sql($searchData)."%' AND id NOT LIKE '".$UserId."' ORDER BY RAND(id) LIMIT 50");
		if(!empty($FriendsSQL)){			
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = str_replace('&','&amp;',$this->get_avatar_src($Row->id));
				
				$online = $this->chat_user_online($ID)?'chat_online':'chat_offline';
				
				$a["FriendsRow"] .= "<div data-event=\"initialize-chat\" data-parameter-user-name=\"".$DisplayName."\" data-parameter-user-id=\"".$ID."\" class=\"bpchatFriendsRow memberalert\"><i class=\"chatStatus ".$online."_circle\"></i><img class=\"bpchatFriendsImage ".$online."\" src=\"".$src."\" /><div class=\"bpchatFriendsName\">".$DisplayName."</div></div>";
			}
		}
		if(empty($a["FriendsRow"]))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_friends."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		
		header("Content-Type: application/json");
		echo json_encode($chat);
		exit;
    }

    /*
     * load_chat functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

   function load_chat_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["bpc_senderinfo"] = array();		
		$a["bpc_chatinfo"] = array();
        $chatAray = array();
	
		global $wpdb;
		$wpdb->show_errors = false;
		
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$time = 5;
		$Read = 0;
	
		$senderQuery = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT user_sender FROM {$wpdb->base_prefix}bpchat_message WHERE user_receiver = %d AND chat_read = %d AND DATE_ADD( chat_time, INTERVAL 5 MINUTE ) >= '%s' ORDER BY id DESC LIMIT 15", $UserId, $Read, $blogtime));
		if(count($senderQuery) > 0) {
			foreach($senderQuery as $senderId) {
				$mFriendId = $senderId->user_sender;
				$src1 = str_replace('&','&amp;',$this->get_avatar_src($senderId->user_sender));
					
				$nameQuery = $wpdb->get_results($wpdb->prepare("SELECT display_name FROM $wpdb->users WHERE ID = %d ", $mFriendId));
				foreach($nameQuery as $senderName) {
					$string = stripslashes(htmlspecialchars($senderName->display_name));
					$mFriendName = (mb_strlen($string) > 16) ? mb_substr($string,0,13).'...' : $string;
				}
				$a["bpc_senderinfo"][$mFriendId] = array("SenderID" => $mFriendId, 
														"SenderName" => $mFriendName, 
														"avatar"=> $src1
														);
																					
				$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}bpchat_message WHERE user_receiver = %d AND user_sender = %d AND chat_read = %d AND DATE_ADD( chat_time, INTERVAL 5 MINUTE ) >= '%s' ORDER BY id ASC LIMIT 15", $UserId, $mFriendId, $Read, $blogtime));
				foreach($MessageSQL as $Row) {
					$chatID = $Row->id;
					$chatAray[] = $Row->id;
					$senderID = $Row->user_sender;
					$receiverID = $Row->user_receiver;
					$chat_time = $Row->chat_time;
					$message = stripslashes($Row->message);
										
					$src = str_replace('&','&amp;',$this->get_avatar_src($senderID));
					
					$a["bpc_chatinfo"][$chatID] = array("chatid" => $chatID, 
														"senderid" => $senderID, 
														"receiverid" => $receiverID, 
														"message"=> $message,
														"chat_time"=> $chat_time,
														"avatar"=> $src,
														);
					
				}
			}
		}
	
		if(count($chatAray) > 0) {
			foreach($chatAray as $key=>$id){
				$wpdb->update( 
					$wpdb->base_prefix.'bpchat_message',
					array( 'chat_read' => 1),
					array( 'id' => $id ),
					array( '%d'),
					array( '%d')
				);
			}
		}
		
		//date_default_timezone_set('asia/dhaka');
		/*
		$date = date('Y-m-d H:i:s');
		$wpdb->update( 
			$wpdb->users,
			array( 'bpchat_last_activity' => $date),
			array( 'ID' => $UserId )
		);
		*/
		$blogtime = current_time( 'mysql' );
		update_user_meta( $UserId, 'bpc_login_time', $blogtime );
		update_user_meta( $UserId, 'bpc_login_status', 'online' );
		
		echo json_encode($chat);
		exit;
    }
		
 	/*
     * load_allchat functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    function load_allchat_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;		
		$a["allmessages"] = array();
		
		global $wpdb;
		$wpdb->show_errors = false;
		
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$senderID = $_POST["senderID"];
				
		$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}bpchat_message WHERE (user_receiver = %d AND user_sender = %d) or (user_receiver = %d AND user_sender = %d) ORDER BY id DESC LIMIT 15", $UserId, $senderID, $senderID, $UserId));
		
		$Cached = array();
		
		foreach($MessageSQL as $Row) {
			$userMessage = $Row->user_sender == $UserId;
			if($Row->user_sender == $senderID){
				$chatID = $Row->id;
			}
			
			$src = str_replace('&','&amp;',$this->get_avatar_src($userMessage ? $UserId : $senderID));
			
			if($userMessage){
				array_push($Cached, "<div class=\"bpchatMessageRow bpc_clear\"><div class=\"bpchatMessageUserImage rightImage\"><img src=\"".$src."\" /></div><div class=\"bpchatMessage rightMessage\"><div data-parameter=\"".$Row->id."\" class=\"bpchatContent bpchatMessageLocation-".$senderID."\">".stripslashes($Row->message)."</div></div></div>");
			}else{
				array_push($Cached, "<div class=\"bpchatMessageRow bpc_clear\"><div class=\"bpchatMessageUserImage leftImage\"><img src=\"".$src."\" /></div><div class=\"bpchatMessage leftMessage\"><div data-parameter=\"".$Row->id."\" class=\"bpchatContent bpchatMessageLocation-".$senderID."\">".stripslashes($Row->message)."</div></div></div>");
			}
				
			$wpdb->update( $wpdb->base_prefix.'bpchat_message', array( 'chat_read' => 1),array( 'id' => $chatID ));
			
			
		}
		
		for($i = count($Cached); $i > -1; $i--){
			$a["allmessages"][$senderID] .= $Cached[$i];
		}
		
		echo json_encode($chat);
		exit;
    }	
	
		
	
	 /*
     * submit_message functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    function submit_message_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_errors = false;
		
		$message = $_POST["messageContent"];
		$ReceiverUserId = $_POST["receiverUserId"];
		$UserId = get_current_user_id();
		$blogtime = current_time( 'mysql' );
		$Read = 0;
			
		$bp_msg_id = $wpdb->insert( 
			$wpdb->base_prefix.'bpchat_message', 
			array( 
				'user_sender' => $UserId, 
				'user_receiver' => $ReceiverUserId,
				'message' => $message,
				'chat_read' => $Read,
				'chat_time' => $blogtime,
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%d',
				'%s',
			) 
		);
		
		if(function_exists('bp_is_active') && !$this->chat_user_status($ReceiverUserId)){
			if(bp_is_active('messages')){
				$is_bp_msg = $this->send_bp_privte_messge($UserId, $ReceiverUserId, $message);
			}
		}
		
		
		
		echo json_encode($chat);
        exit;
    }

    /*
     * set_active_chat function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    function set_active_chat_function() {
		
        header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		
		$ChatId = $_POST["windowId"];
		$UserName = $_POST["WindowUserName"];
		$UserImage = $_POST["UserImage"];
		$State = $_POST["windowState"];
		
		if(!isset($_SESSION["ChatStored"]))
			$_SESSION["ChatStored"] = array();
		
		$_SESSION["ChatStored"][$ChatId] = array("WINDOWID" => $ChatId, "USERNAME" => $UserName, "STATE" => $State, "USERIMAGE" => $UserImage);
		
		echo json_encode($chat);
        exit;
    }

    /*
     * remove_active_chat function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    function remove_active_chat_function() {
		
		header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		$ChatId = $_POST["windowId"];
		
		if(!isset($_SESSION["ChatStored"]))
			$_SESSION["ChatStored"] = array();
		
		if(array_key_exists($ChatId, $_SESSION["ChatStored"]))
			unset($_SESSION["ChatStored"][$ChatId]);
		
		$a["cacheData"] =$_SESSION["ChatStored"];
		
		echo json_encode($chat);
        
        exit;
    }

    /*
     * load_active_chat function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    function load_active_chat_function() {
		
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
				
		if(!isset($_SESSION["ChatStored"]))
			$_SESSION["ChatStored"] = array();
		
		$a["ChatStored"] = $_SESSION["ChatStored"];
		
		echo json_encode($chat);
        
        exit;
    }
	
	public function get_avatar_src($user_id){
		$avatar_url = get_avatar($user_id);
		$doc = new DOMDocument();
		$doc->loadHTML($avatar_url);
		$xpath = new DOMXPath($doc);
		$src = $xpath->evaluate("string(//img/@src)");
		return $src;
	}
	
	public function send_bp_privte_messge($sender_id, $receiver_id, $messge){
		global $wpdb;
		$thread_id = (int) $wpdb->get_var( "SELECT MAX(thread_id) FROM {$wpdb->base_prefix}bp_messages_messages" ) + 1;
		$subject = 'Message sent via chat';
		$blogtime = current_time( 'mysql' );
		$messge_id = $wpdb->insert( 
			$wpdb->base_prefix.'bp_messages_messages', 
			array( 
				'thread_id' => $UserId, 
				'sender_id' => $sender_id,
				'subject' => $subject,
				'message' => $messge,
				'date_sent' => $blogtime,
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%s',
				'%s',
			) 
		);
		$res_id = $wpdb->insert( 
			$wpdb->base_prefix.'bp_messages_recipients', 
			array( 
				'user_id' => $receiver_id, 
				'thread_id' => $thread_id,
				'unread_count' => 1,
			), 
			array( 
				'%d', 
				'%d',
				'%d',
			) 
		);
		$sen_id = $wpdb->insert( 
			$wpdb->base_prefix.'bp_messages_recipients', 
			array( 
				'user_id' => $sender_id, 
				'thread_id' => $thread_id,
				'sender_only' => 1,
			), 
			array( 
				'%d', 
				'%d',
				'%d',
			) 
		);
		$noti_id = $wpdb->insert( 
			$wpdb->base_prefix.'bp_notifications', 
			array( 
				'user_id' => $receiver_id, 
				'item_id' => $thread_id,
				'secondary_item_id' => $sender_id,
				'component_name' => 'messages',
				'component_action' => 'new_message',
				'date_notified' => $blogtime,
				'is_new' => 1,
			), 
			array( 
				'%d', 
				'%d',
				'%d',
				'%s', 
				'%s',
				'%s',
				'%d',
			) 
		);
		if($messge_id){
			return true;
		}else{
			return false;
		}
		
	}
	
}

?>
