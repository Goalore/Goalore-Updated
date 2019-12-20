<?php

class bpchat_Script_Controller{

    public function enque_scripts(){
        add_action('wp_enqueue_scripts', array($this, 'include_scripts_styles'));
		add_action('wp_head', array($this, 'include_custom_styles'));
        add_action('admin_enqueue_scripts', array($this, 'include_admin_scripts_styles'));
    }

    /*
     * Include AJAX plugin specific scripts and pass the neccessary data.
     *
     * @param  -
     * @return -
     */

    public function include_scripts_styles(){
        global $post;
		
		$data = get_option('bpchat_options');
    	$chatRate = $data['chat_refresh_rate'];
		$start_chat = !empty($data['bpc_lg_start_chat'])?$data['bpc_lg_start_chat']:'Start Chat';
		
		$friendListRate = $data['friend_list_refresh_rate'];
		$full_height = $data['mobile_full_height'];
		$memberCount = $data['show_bpc_friend_count']?1:0;
		
        wp_register_script('bpchat_ajax', plugins_url('js/bpchat-ajax.js', dirname(__FILE__)), array("jquery"));
        wp_enqueue_script('bpchat_ajax');

        $nonce = wp_create_nonce("unique_key");
		
        $ajax = new bpchat_AJAX();
        $ajax->initialize();
		
		$avatar_url = get_avatar(get_current_user_id());
		$regex = '/(^.*src="|" w.*$)/';		
		$doc = new DOMDocument();
		$doc->loadHTML($avatar_url);
		$xpath = new DOMXPath($doc);
		$raw_src = $xpath->evaluate("string(//img/@src)");
		$src = str_replace('&','&amp;',preg_replace($regex, '', $raw_src));
				
        $config_array = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'ajaxActions' => $ajax->ajax_actions,
            'ajaxNonce' => $nonce,
            'siteURL' => site_url(),
			'pluginsURL' => plugins_url(),
			'templateURL' => plugins_url('template/', dirname(__FILE__)),
			'chatRate' => $chatRate,
			'friendListRate' => $friendListRate,
			'avatar' => $src,
			'fullHeight' => $full_height,
			'start_chat' => $start_chat,
			'memberCount' => $memberCount
        );

        wp_localize_script('bpchat_ajax', 'bpchat_conf', $config_array);

        wp_register_style('user_styles', plugins_url('css/bpchat-style.css', dirname(__FILE__)));
        wp_enqueue_style('user_styles');

        //wp_register_style('bpchat_theme', plugins_url('css/bpchat-theme.css',dirname(__FILE__)));
        //wp_enqueue_style('bpchat_theme');

    }

    public function colourchanger($hex, $percent) {
		// Work out if hash given
		$hash = '';
		if (stristr($hex,'#')) {
			$hex = str_replace('#','',$hex);
			$hash = '#';
		}
		/// HEX TO RGB
		$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
		for ($i=0; $i<3; $i++) {
			// See if brighter or darker
			if ($percent > 0) {
				// Lighter
				$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
			} else {
				// Darker
				$positivePercent = $percent - ($percent*2);
				$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
			}
			// In case rounding up causes us to go to 256
			if ($rgb[$i] > 255) {
				$rgb[$i] = 255;
			}
		}
		//// RBG to Hex
		$hex = '';
		for($i=0; $i < 3; $i++) {
			// Convert the decimal digit to hex
			$hexDigit = dechex($rgb[$i]);
			// Add a leading zero if necessary
			if(strlen($hexDigit) == 1) {
			$hexDigit = "0" . $hexDigit;
			}
			// Append to the hex string
			$hex .= $hexDigit;
		}
		return $hash.$hex;
	}
	//$colour = '#ae64fe';
	//$brightness = 0.5; // lighter
	//$brightness = 0.3; // more lighter
	//$brightness = 0.1; // close to white
	//$newColour = colourchanger($colour,$brightness);
	//$colour = '#ae64fe';
	//$brightness = -0.5; // 50% darker
	//$brightness = -0.3; // more darker
	//$brightness = -0.1; // more darker close to black
	//$newColour = colourchanger($colour,$brightness);
	public function hex2rgba($hex,$opc) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = 'rgba('.$r.','.$g.','.$b.','.$opc.')';
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
	
	public function include_custom_styles(){
		
		$data = get_option('bpchat_options');
    	$friendHeaderBg = !empty($data['bpc_friend_title_bg_color']) && $data['bpc_friend_title_bg_color'] != '#'?$data['bpc_friend_title_bg_color']:'#b0becf';
		$friendTitleHover = !empty($data['bpc_friend_title_bg_color']) && $data['bpc_friend_title_bg_color']!= '#'?$this->colourchanger($data['bpc_friend_title_bg_color'], -.9):$this->colourchanger('#b0becf', -.9);
		$friendHeaderText = !empty($data['bpc_friend_title_color']) && $data['bpc_friend_title_color']!='#'?$data['bpc_friend_title_color']:'#555555';
		$friendFilterBg = !empty($data['bpc_friend_filter_bg_color']) && $data['bpc_friend_filter_bg_color']!='#'?$data['bpc_friend_filter_bg_color']:'#cfd8dc';
		$friendBodybgc = !empty($data['bpc_friend_bg_color']) && $data['bpc_friend_bg_color']!='#'?'background:'.$data['bpc_friend_bg_color']:'background:#e9eaed';
		$friendBodybgp = isset($data['bpc_friend_bg_pattern']) && $data['bpc_friend_bg_pattern']!='none'? 'background: url('.BPC_URL . 'images/bg/'.$data['bpc_friend_bg_pattern'].'.png) repeat':'';
		$friendBodybgi = !empty($data['bpc_friend_bg_image'])? 'background: url('.$data['bpc_friend_bg_image']['src'].') no-repeat center center;':'';
		if($friendBodybgi){
			$friendBodybg = $friendBodybgi;
		}else if($friendBodybgp){
			$friendBodybg = $friendBodybgp; 
		}else{
			$friendBodybg = $friendBodybgc;
		}
		
		$bpchatHeaderbgc = !empty($data['bpc_chat_title_bg_color']) && $data['bpc_chat_title_bg_color']!='#'?$data['bpc_chat_title_bg_color']:'#b0becf';
		$bpchatHeaderbdr = !empty($data['bpc_chat_title_bg_color']) && $data['bpc_chat_title_bg_color']!='#'?$this->colourchanger($data['bpc_chat_title_bg_color'], -.6):$this->colourchanger('#b0becf', -.6);
		$bpchatHeaderText = !empty($data['bpc_chat_title_text_color']) && $data['bpc_chat_title_text_color']!='#'?$data['bpc_chat_title_text_color']:'#555555';
		$bpchatBodybgc = !empty($data['bpc_chat_bg_color']) && $data['bpc_chat_bg_color']!='#'?'background:'.$data['bpc_chat_bg_color']:'background:#eaeaea;';
		$bpchatBodybgp = isset($data['bpc_chat_bg_pattern']) && $data['bpc_chat_bg_pattern']!='none'? 'background: url('.BPC_URL . 'images/bg/'.$data['bpc_chat_bg_pattern'].'.png) repeat;':'';
		$bpchatBodybgi = !empty($data['bpc_chat_bg_image'])? 'background: url('.$data['bpc_chat_bg_image']['src'].') no-repeat center center;':'';
		
		if($bpchatBodybgi){
			$bpchatBodybg = $bpchatBodybgi;
		}else if($bpchatBodybgp){
			$bpchatBodybg = $bpchatBodybgp; 
		}else{
			$bpchatBodybg = $bpchatBodybgc;
		}
		$bpchatStartChatBdr = !empty($data['bpc_start_chat_bdr_color']) && $data['bpc_start_chat_bdr_color'] != '#'?'border-top-color:' .$data['bpc_start_chat_bdr_color'].';':'';
		$bpchatStartChatBg = !empty($data['bpc_start_chat_bg_color']) && $data['bpc_start_chat_bg_color'] != '#'?'background-color:' .$data['bpc_start_chat_bg_color'].';':'';
		$bpc_placeholder_text = !empty($data['bpc_placeholder_text_color']) && $data['bpc_placeholder_text_color'] != '#'?'
		.bpchatFooter input::-webkit-input-placeholder{
		   color: '.$data['bpc_placeholder_text_color'].';
		}
		.bpchatFooter input:-moz-placeholder{
		   color: '.$data['bpc_placeholder_text_color'].';  
		}
		.bpchatFooter input::-moz-placeholder{
		   color: '.$data['bpc_placeholder_text_color'].';  
		}
		.bpchatFooter input:-ms-input-placeholder{  
		   color: '.$data['bpc_placeholder_text_color'].';  
		}
		':'';
		$bpchatColor = !empty($data['bpc_chat_text_color']) && $data['bpc_chat_text_color']!='#'?$data['bpc_chat_text_color']:'#555555';

		$bpchatBdrBottom = !empty($data['bpc_chatbox_bg_color']) && $data['bpc_chatbox_bg_color']!='#'?'border-bottom-color:' .$this->colourchanger($data['bpc_chatbox_bg_color'], -.6).';':'';
		
		$bpchatBgc = 'background-color:"#4185ca"';
		$bpchatTip  = '';
		if($data['enable_bpc_chatbox_bg_opacity'] && !empty($data['bpc_chatbox_bg_color']) && $data['bpc_chatbox_bg_color'] != '#'){
			
			$bpchatBgc = 'background-color:' .$this->hex2rgba($data['bpc_chatbox_bg_color'], $data['bpc_chatbox_bg_opacity']).';';
			
			$bpchatTip ='
			.leftMessage:after{border-color:rgba(255, 255, 255,0);border-right-color:'.$this->hex2rgba($data['bpc_chatbox_bg_color'],$data['bpc_chatbox_bg_opacity']-.2).';}
			.leftMessage:before{border-color:rgba(218, 222, 225,0);border-right-color:'.$this->hex2rgba($data['bpc_chatbox_bg_color'],$data['bpc_chatbox_bg_opacity']-.2).';}
			.rightMessage:after{border-color:rgba(255, 255, 255,0);border-left-color:'.$this->hex2rgba($data['bpc_chatbox_bg_color'],$data['bpc_chatbox_bg_opacity']-.2).';}
			.rightMessage:before{border-color:rgba(218, 222, 225,0);border-left-color:'.$this->hex2rgba($data['bpc_chatbox_bg_color'],$data['bpc_chatbox_bg_opacity']-.2).';}
			';
		}else if(!empty($data['bpc_chat_bg_color']) && $data['bpc_chat_bg_color'] != '#'){
			$bpchatBgc = 'background-color:' .$data['bpc_chatbox_bg_color'].';';
			
			$bpchatTip = '
			.leftMessage:after{border-color: rgba(255, 255, 255, 0);border-right-color: '.$data['bpc_chatbox_bg_color'].';}
			.leftMessage:before{border-color: rgba(218, 222, 225, 0);border-right-color: '.$data['bpc_chatbox_bg_color'].';}
			.rightMessage:after{border-color: rgba(255, 255, 255, 0);border-left-color: '.$data['bpc_chatbox_bg_color'].';}
			.rightMessage:before{border-color: rgba(218, 222, 225, 0);border-left-color: '.$data['bpc_chatbox_bg_color'].';}
			';
		}
		
		$fullwidth = $data['mobile_full_width']?'
		@media only screen 
		  and (min-device-width: 320px) 
		  and (max-device-width: 480px)
		  and (-webkit-min-device-pixel-ratio: 2),
		  only screen 
		  and (min-device-width: 320px) 
		  and (max-device-width: 568px)
		  and (-webkit-min-device-pixel-ratio: 2),
		  only screen 
		  and (min-device-width: 375px) 
		  and (max-device-width: 667px) 
		  and (-webkit-min-device-pixel-ratio: 2),
		  only screen 
		  and (min-device-width: 414px) 
		  and (max-device-width: 736px) 
		  and (-webkit-min-device-pixel-ratio: 3),
		  only screen
		  and (max-width: 767px){
			.bpchatWindow {width: 100%;}
			#bpchatFriendsWindow{bottom:64px;}
			#bpchatChatsWindow{right:0;width: 100%;}
		}':'';
		$custom_css = !empty($data['bpc_custom_style'])?$data['bpc_custom_style']:'';
		echo "<style type=\"text/css\">
		.bpchatFriendsHeader, 
		.bpchatFriendsTitle { background-color: ".$friendHeaderBg."; color: ".$friendHeaderText." } 
		.bpchatFriendsFilter{ background-color: ".$friendFilterBg."; } 
		.bpchatFriendsHeader:hover, 
		.bpchatFriendsTitle:hover{ background-color:".$friendTitleHover.";} 
		.bpchatFriendsFilter span.RefreshMembersList:hover, 
		.bpchatFriendsFilter span.LoadMembersOnline:hover, 
		.bpchatFriendsFilter span.LoadOnlyFriendsOnline:hover, 
		.bpchatFriendsFilter span.LoadGroupList:hover, 
		.bpchatFriendsFilter span:hover{background-color: ".$friendHeaderBg.";} 
		.bpchatFriendsHolder {".$friendBodybg." } 
		.bpchatHeader{background-color: ".$bpchatHeaderbgc."; color: ".$bpchatHeaderText."; border-bottom: 1px solid ".$bpchatHeaderbdr." } 
		.bpchatWindow{".$bpchatBodybg." } 
		.bpchatMessage{".$bpchatBgc.$bpchatBdrBottom."}
		.bpchatFooter input[type=\"text\"]{".$bpchatStartChatBg.$bpchatStartChatBdr."} 
		.bpchatContent{color: ".$bpchatColor."}".$fullwidth.$bpc_placeholder_text.$bpchatTip.$custom_css."</style>"; 
    }
	
    public function include_admin_scripts_styles(){
        wp_register_style( 'bpchat_admin_css', plugins_url('css/bpchat-admin.css', dirname(__FILE__)));
        wp_enqueue_style( 'bpchat_admin_css' );
    }


}

?>