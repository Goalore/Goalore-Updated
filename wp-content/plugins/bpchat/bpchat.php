<?php
/*		
	Plugin Name: bpchat
	Plugin URI: http://bpchat.wpapplab.com/
	Description: bpchat is a BuddyPress, WordPress social chat system. It enables users chat system within wordpress powered social networking, blog, forum, directory site that offer user registration. 
	Version: 2.0.2
	Author: Mircode Solution
	Author URI: http://codecanyon.net/user/mircode
	License: GPLv2
*/
	
if( !defined('BPC_PATH') )
	define( 'BPC_PATH', plugin_dir_path(__FILE__) );
if( !defined('BPC_URL') )
	define( 'BPC_URL', plugin_dir_url(__FILE__ ) );

//with trailing slash
require_once 'ajax/class-bpchat-ajax.php';
require_once 'admin/class-bpchat-database-manager.php';
require_once 'admin/class-bpchat-options.php';

class bpchat_Apps {
	
	 public function __construct() {
		 
    }
	
    public function initialize_controllers() {

        require_once 'controllers/class-bpchat-activation-controller.php';
        $activation_controller = new bpchat_Activation_Controller();
        $activation_controller->initialize_activation_hooks();
		
		require_once 'controllers/class-bpchat-schedule-controller.php';
        $schedule_controller = new bpchat_Schedule_Controller();
    }

    public function initialize_app_controllers() {

		require_once 'controllers/class-bpchat-script-controller.php';
        $script_controller = new bpchat_Script_Controller();
        $script_controller->enque_scripts();

        $ajax = new bpchat_Ajax();
        $ajax->initialize();

    }
	

}

$bpchat_app = new bpchat_Apps();
$bpchat_app->initialize_controllers();

function load_bpchat(){
	if(is_user_logged_in()){
		$bpchat_init = new bpchat_Apps();
		$bpchat_init->initialize_app_controllers();
		add_action( 'wp_footer', 'bpc_sound_function');
		
		//$test = new bpchat_Ajax();
		//var_dump ($test->bp_online_friends_function());
	}
}

add_action('init', 'load_bpchat');

function bpc_sound_function() {
	$sound = '';
	$sound .= '<audio id="bpchat_alert">';
	$sound .= '<source src="' . plugins_url() . '/bpchat/images/alert.ogg" type="audio/ogg">';
	$sound .= '<source src="' . plugins_url() . '/bpchat/images/alert.mp3" type="audio/mpeg">';
	$sound .= '</audio>';
	echo $sound;
}


?>