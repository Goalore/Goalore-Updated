<?php
/**
 * goalore functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package goalore
 */

if ( ! function_exists( 'goalore_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function goalore_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on goalore, use a find and replace
		 * to change 'goalore' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'goalore', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'goalore' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'goalore_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'goalore_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function goalore_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'goalore_content_width', 640 );
}
add_action( 'after_setup_theme', 'goalore_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function goalore_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'goalore' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'goalore' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'goalore_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function goalore_scripts() {
	wp_enqueue_style( 'goalore-alertify-css', get_template_directory_uri() . '/css/alertify.min.css' );
	// wp_enqueue_style( 'goalore-default-css', get_template_directory_uri() . '/css/default.min.css' );
	wp_enqueue_style( 'goalore-bootstrap-css', get_template_directory_uri() . '/css/bootstrap.min.css' );
	wp_enqueue_style( 'goalore-font-awesome-min-css', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'goalore-mCustomScrollbar.css', get_template_directory_uri() . '/css/jquery.mCustomScrollbar.css' );
	wp_enqueue_style( 'goalore-custom-css', get_template_directory_uri() . '/css/custom.css' );
	wp_enqueue_style( 'goalore-style', get_stylesheet_uri() );

	// wp_enqueue_script( 'goalore-jquery-slim', get_template_directory_uri() . '/js/jquery-3.3.1.slim.min.js', array(), '20151215', true );
	wp_enqueue_script( 'goalore-jquery-min', 'https://code.jquery.com/jquery-3.2.1.min.js', array(), '20151215', true );
	wp_enqueue_script( 'goalore-bootstrap-min-js', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '20151215', true );
	wp_enqueue_script( 'goalore-alertify-min-js', get_template_directory_uri() . '/js/alertify.min.js', array(), '20151215', true );
	wp_enqueue_script( 'goalore-jquery-mCustomScrollbar-js',get_template_directory_uri().'/js/jquery.mCustomScrollbar.js', array(), '20151215', true );
	wp_enqueue_script( 'goalore-jquery-mousewheel-3.0.6-js',get_template_directory_uri().'/js/jquery.mousewheel-3.0.6.js', array(), '20151215', true );
	
	wp_enqueue_script( 'goalore-frontend-ajax',  get_template_directory_uri() . '/js/frontend-ajax.js', array('jquery'), null, true );
    wp_localize_script( 'goalore-frontend-ajax', 'frontendJSobject',
        array(  'ajaxURL' => admin_url( 'admin-ajax.php' ),'userDashbord' => get_permalink(98) )
    );

	wp_enqueue_script( 'goalore-custom-js', get_template_directory_uri() . '/js/custom.js', array(), '20151215', true );
	
}
add_action( 'wp_enqueue_scripts', 'goalore_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load Custom Routes
 */
require get_template_directory() . '/includes/routes.php';

/**
 * Load Custom Functions
 */
require get_template_directory() . '/includes/custom_functions.php';

/**
 * Form Request Hander
 */
require get_template_directory() . '/includes/form_request_handler.php';



function setup_post_type() {
    register_post_type( 'faqs', array(
        'public'    => true,
        'menu_icon' => 'dashicons-list-view',
        'label'     => __( 'FAQs', 'goaloretheme' ),
        'supports'  => array( 'title', 'editor' ),
    ) );
     
    register_taxonomy( 'categories', 'faqs', array(
        'label'        => __( 'Category', 'goaloretheme' ),
        'hierarchical' => true,
        'show_ui'      => true,
        'show_admin_column' => true,
    ) );

    register_post_type( 'goals', array(
        'public'    => true,
        'menu_icon' => 'dashicons-admin-site',
        'label'     => __( 'Goals', 'goaloretheme' ),
        'supports'  => array( 'title','author','comments' ),
    ) );

    register_taxonomy( 'goal_categories', 'goals', array(
        'label'        => __( 'Categories', 'goaloretheme' ),
        'hierarchical' => true,
        'show_ui'      => true,
        'show_admin_column' => true,
    ) );

    register_post_type( 'alliances', array(
        'public'    => true,
        'menu_icon' => 'dashicons-groups',
        'label'     => __( 'Alliances', 'goaloretheme' ),
        'supports'  => array( 'title','author', 'comments' ),
    ) );

    register_post_type( 'tickets', array(
        'public'    => true,
        'menu_icon' => 'dashicons-editor-help',
        'label'     => __( 'Tickets', 'goaloretheme' ),
        'supports'  => array( 'title','author', 'editor' ),
    ) );


   

}
add_action( 'init', 'setup_post_type' );

/*Add Class in Header Menu anchor tag*/
function wpse156165_menu_add_class( $atts, $item, $args ) {
	if($args->menu == 'Header-Menu-not-loggedin'){
		if($item->title == 'Contact' || $item->object_id == 13 ) $class = 'nav-link contact-btn'; 
		else $class = 'nav-link'; 
	    $atts['class'] = $class;
	}
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'wpse156165_menu_add_class', 10, 3 );


add_action('wp_footer',function(){ ?>
	<script type="text/javascript">	
		$('#user-category-frm').submit(function(e){	
			var errMsg = $('#user-category-frm .register-response label');
			if($('.cats:checkbox:checked').length == 0){
				errMsg.text('At least select one category!');
				return false;
			}else{
				errMsg.text('');
			}
		});	
	</script>			
<?php }, 999);


add_filter('show_admin_bar', '__return_false');



add_action( 'add_meta_boxes_comment', function () {
    add_meta_box( 'pmg-comment-title', __( 'Ratings' ), 'pmg_comment_tut_meta_box_cb', 'comment', 'normal', 'high' );
});

function pmg_comment_tut_meta_box_cb( $comment )
{
    $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
     wp_nonce_field( 'pmg_comment_update', 'pmg_comment_update', false );
    ?>
    <p>
        <label for="pmg_comment_title"><?php _e( 'POV Rating' ); ?></label>;
        <input type="number" min="0" max="4" name="rating" value="<?php echo esc_attr( $rating ); ?>" class="widefat" />
    </p>
    <?php
}

add_action( 'edit_comment', 'pmg_comment_tut_edit_comment' );
function pmg_comment_tut_edit_comment( $comment_id )
{
    if( ! isset( $_POST['pmg_comment_update'] ) || ! wp_verify_nonce( $_POST['pmg_comment_update'], 'pmg_comment_update' ) )
        return;
    if( isset( $_POST['rating'] ) )
        update_comment_meta( $comment_id, 'rating', esc_attr( $_POST['rating'] ) );
}


add_action('template_redirect',function() {
	$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

	if(is_user_logged_in()){
		$currentUserID = get_current_user_id(); 
		$isDeactivated = get_user_meta($currentUserID,'isDeactivated',true);
    	if($isDeactivated == '1'){

    		if(	is_page([13,6,100,2,94,3,11,96,9]) || is_singular('post') ){

    		}else{
			    wp_redirect( home_url(PROFILE.'/'.SETTINGS) );
	        	die;
    		}
    	}

	}else{
		if(	
			/*Static page */
			!is_page([13,6,100,2,94,3,11,96,9]) || 

			/*Blog Details Page */
			is_singular('post') ||

			/*Member Profile related pages*/
			is_author()
		) {
		    wp_redirect( home_url() );
        	die;
		}
	}


});

function add_custom_column_name($columns) {
    $columns['isDeactivated'] = 'isDeactivated';
    $columns['isDeleted'] = 'isDeleted';
    return $columns;
}
function show_custom_column_values($value, $column_name, $user_id) {
    if ( 'isDeactivated' == $column_name )
        return (get_user_meta( $user_id, 'isDeactivated', true ) == 1) ? 'Yes':'No';
    if ( 'isDeleted' == $column_name )
        return (get_user_meta( $user_id, 'isDeleted', true ) == 1) ? 'Yes':'No';
    return $value;
}

add_filter('manage_users_columns', 'add_custom_column_name');
add_action('manage_users_custom_column', 'show_custom_column_values', 10, 3);

function custom_user_profile_fields($user){
  ?>
    <h3>profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="company">Is Deactivated</label></th>
            <td>
            	<?php $isDeactivated = get_user_meta( $user->ID, 'isDeactivated', true );  ?>
            	<input type="radio" <?php echo $isDeactivated==1?'checked':''; ?> name="isDeactivated" value="1">Yes
            	<input type="radio" <?php echo $isDeactivated!=1?'checked':''; ?> name="isDeactivated" value="0">No
            </td>
        </tr>
        <tr>
            <th><label for="company">Is Deleted</label></th>
            <td>
            	<?php $isDeleted = get_user_meta( $user->ID, 'isDeleted', true );  ?>
            	<input type="radio" <?php echo $isDeleted==1?'checked':''; ?> name="isDeleted" value="1">Yes
            	<input type="radio" <?php echo $isDeleted!=1?'checked':''; ?> name="isDeleted" value="0">No
            </td>
        </tr>
    </table>
  <?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );


function save_custom_user_profile_fields($user_id){
    # again do this only if you can
    if(!current_user_can('manage_options'))
        return false;

    $user = get_user_by('ID',$user_id);
    $to = $user->user_email;
	$name =  $user->first_name . ' ' . $user->last_name;
	$headers = array('Content-Type: text/html; charset=UTF-8'); 

    # save my custom field
    $isDeactivated = get_user_meta($user_id,'isDeactivated',true);
    $isDeactivatedKey = metadata_exists('user', $user_id, 'isDeactivated');
    if($isDeactivated != $_POST['isDeactivated']){
    	$isDeactivatedUpdated = update_user_meta($user_id, 'isDeactivated', $_POST['isDeactivated']);

    	if($isDeactivatedUpdated && $isDeactivatedKey){
		    if($_POST['isDeactivated'] == '0'){
				$subject = 'Goalore Account Reactivated';
		    	$body = 'Dear ' . $name;
				$body .= '<br><br>You Goalore account was reactivated by administrator.';
				$body .= '<br><br>Regards';
				$body .= '<br><b>Goalore</b>';
				wp_mail( $to, $subject, $body, $headers );
		    }else{
				$subject = 'Goalore Account Deactivated';
				$body = 'Dear ' . $name;
				$body .= '<br><br>You Goalore account was deactivated to reactivate your account contact administrator.';
				$body .= '<br><br>Regards';
				$body .= '<br><b>Goalore</b>';
				wp_mail( $to, $subject, $body, $headers );
		    }
		}
    }
    update_user_meta($user_id, 'isDeleted', $_POST['isDeleted']);


	
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');

/*
function on_post_publish( $new_status, $old_status, $post ) {
  if (get_post_type($post) !== 'post')
        return;    //Don't touch anything that's not a post (i.e. ignore links and attachments and whatnot )

    //If some variety of a draft is being published, dispatch an email
    if( ( 'draft' === $old_status || 'auto-draft' === $old_status ) && $new_status === 'publish' ) {
        		$allMembers = [];
		$allUsers = get_users();
		if(!empty($allUsers)){
		 	foreach ( $allUsers as $user ) {
		 		$allMembers[] = (string) $user->ID;
		 	}
		}
		
		$notification = 'A new blog has just been published "'.$post['title'].'"';
    	// send_notification(1, $allMembers, $post['ID'], $notification);
    }
}
add_action('transition_post_status', 'on_post_publish');*/


add_action( 'transition_post_status', 'a_new_post', 10, 3 );

function a_new_post( $new_status, $old_status, $post )
{
    if ( 'publish' !== $new_status or 'publish' === $old_status )
        return;

    if ( 'post' !== $post->post_type )
	    return; 

   $allMembers = [];
	$allUsers = get_users();
	if(!empty($allUsers)){
	 	foreach ( $allUsers as $user ) {
	 		$allMembers[] = (string) $user->ID;
	 	}
	}
	
	$notification = 'A new blog has just been published "'.$post->post_title.'"';
	send_notification(0, $allMembers, $post->ID, $notification);



    // do something awesome
}