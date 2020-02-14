<?php 
  /**
    *
    *Profile page
    *
    */

get_header(); 


global $wpdb;
$currentUserID = get_current_user_id(); 
$userID = get_query_var( 'author' ) ;

$userID = (empty($userID))?$currentUserID:$userID;
$user = get_user_by('ID',$userID);

$mypp = false;
if($userID == $currentUserID) $mypp = true;

$US = get_user_meta($userID, 'user_Settings', true);
$public_profile = isset($US['public_profile'])?$US['public_profile']:'';
$personal_info = isset($US['personal_info'])?$US['personal_info']:'';
$show_full_name = isset($US['show_full_name'])?$US['show_full_name']:'';
$show_gender = isset($US['show_gender'])?$US['show_gender']:'';
$show_dob = isset($US['show_dob'])?$US['show_dob']:'';

$isDeactivated = get_user_meta($userID,'isDeactivated',true);
$isDeleted = get_user_meta($userID,'isDeleted',true);
$UNAVAILABLE = false;
if($isDeleted == 1 || $isDeactivated == 1)
  $UNAVAILABLE = true;

$full_name = get_user_meta($userID, 'full_name', true);
$username = $user->user_login;
$user_registered = date('m/d/Y',strtotime($user->user_registered));
$odob = get_user_meta($userID, 'dob', true);
$dob = date('m/d/Y',strtotime($odob));
$country = get_user_meta($userID, 'country', true);
$zip_code = get_user_meta($userID, 'zip_code', true);
$gender = get_user_meta($userID, 'gender', true);
$profile_picture = get_user_profile_picture($userID);

$table_gdp = $wpdb->prefix . "gdp";
$GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_gdp WHERE user_id = $userID"); 
if(empty($GDP)) $GDP = 0; 

/*$mygoals = New WP_Query([
  'posts_per_page' => -1,
  'post_type' => 'goals',
  'author'  => $userID,
  'meta_key'  => 'status',
  'meta_value'=> 'public'
]);*/

$mygoals = New WP_Query([
    'post_type'       => 'goals',
    'author'          => $userID, 
    'posts_per_page'  => -1,
    'meta_query'      => [
        'goal_status' => [ 'key' => 'goal_status' ],
        'target'      => [ 'key' => 'target' ],
        [ [
            'key'     => 'archive',
            'compare' => 'NOT EXISTS'
        ],
        'relation'    => 'OR',
        [
            'key'     => 'archive',
            'value'   => '1',
            'compare' => '!='
        ] ],
        [
            'key'     => 'status',
            'value'   => 'public',
        ]

    ], 
    'orderby'         => [
        'goal_status' => 'DESC',
        'target'      => 'ASC',
    ]
]);


/*$myalliance = New WP_Query([
    'post_type' => 'alliances',
    'author'    => $userID, 
    'meta_key'  => 'privacy_status',
    'meta_value'=> 'public'
]);*/



$myAdminAlliances = New WP_Query([
    'post_type'       => 'alliances',
    'posts_per_page'  => -1,
    'meta_key'        => 'status',
    'orderby'         => 'meta_value',
    'order'           => 'DESC',
    'meta_query'      => [
        [
            'key'     => 'admins',
            'value'   => '"' . $userID . '"',
            'compare' => 'LIKE',
        ],[
            'key'     => 'privacy_status',
            'value'   => 'private',
            'compare' => '!='
        ]
    ],
]);

if($myAdminAlliances->have_posts()){
    $myAdminAlliancesIDs = wp_list_pluck( $myAdminAlliances->posts, 'ID' );
    //Inlcude Alliance ID with existing result with OR condidtion 
    function inlcude_post_id_with_OR_condidtion( $where, $query ) { 
        $where = str_replace("AND wp_posts.post_author IN", "OR wp_posts.post_author IN", $where);
        return $where;
    } add_filter( 'posts_where', 'inlcude_post_id_with_OR_condidtion', 10, 2 );
}else{
    $myAdminAlliancesIDs = [];
}
$myalliance = New WP_Query([
    'post__in'       => $myAdminAlliancesIDs,
    'post_type'      => 'alliances',
    'author'         => $userID, 
    'posts_per_page' => -1,
    'meta_query'     => [
        'status' => [ 'key' => 'status' ],
        [
            [
                'key'     => 'archive',
                'compare' => 'NOT EXISTS'
            ],
            'relation' => 'OR',
            [
                'key'     => 'archive',
                'value'   => '1',
                'compare' => '!='
            ]
        ],
        [
            'key'     => 'privacy_status',
            'value'   => 'private',
            'compare' => '!='
        ]
    ], 'orderby' => [ 'status' => 'DESC' ]
]); 

// filter after query is firied
remove_filter('posts_where','inlcude_post_id_with_OR_condidtion', 10, 2 );

$pcr = get_user_meta($userID,'pending_connection_request',true);
$mypcr = get_user_meta($currentUserID,'pending_connection_request',true);
$connections = get_user_meta($userID,'connections',true);
if(empty($connections)) $connections = [];
if(empty($pcr)) $pcr = [];
if(empty($mypcr)) $mypcr = [];

$totalConnections = count($connections);
$totalGoals = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts as Posts WHERE 1=1 AND Posts.post_author IN (".$userID.") AND Posts.post_type = 'goals' AND (Posts.post_status = 'publish' OR Posts.post_status = 'acf-disabled' OR Posts.post_status = 'private')");

$totalAlliances = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts as Posts WHERE 1=1 AND Posts.post_author IN (".$userID.") AND Posts.post_type = 'alliances' AND (Posts.post_status = 'publish' OR Posts.post_status = 'acf-disabled' OR Posts.post_status = 'private')");



 ?>
<?php if($UNAVAILABLE){ ?>
<section class="profile-unavailable">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center ">
                <div class="alert alert-danger unavailable-msg">
                    <?php if($isDeleted == '1'){ ?>
                    Member account is deleted!
                    <?php }else if($isDeactivated == '1'){ ?>
                    Member account is currently deactivated!
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php } ?>
<section class="profile-section ">
    <div class="container">
        <div class="row">
            <div class="col-12 <?php echo $UNAVAILABLE ? 'profile-unavailable-sec' : ''; ?>">
                <div class="user-profile-header">
                    <div class="user-card-box">
                        <div class="user-profile-pic">
                            <img src="<?php echo $profile_picture; ?>" id="pp-preview" class="member-profile-img">
                            <div id="add-pp-btn" class="pp-add-img" >
                                <img src="<?php echo get_template_directory_uri(); ?>/images/green-plus-icon.svg" >
                            </div>
                        </div>
                        <div class="user-profile-info">
                            <?php if($mypp || $show_full_name && $public_profile != '0'){ ?>
                                <p> <?php echo $full_name ?> </p>
                            <?php }else{ ?>
                                <p> <?php echo $username; ?> </p>
                            <?php } ?>
                            <p>Good Deed Points: <?php echo $GDP ?> </p>
                            <?php if($public_profile != '0' || $mypp){ ?>
                                <p>Member Since: <?php echo $user_registered; ?> </p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="user-statusbar">
                        <div class="statusbar-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid">
                            <p>Connections: </p>
                             <span> <?php echo $totalConnections; ?></span>
                        </div>
                        <div class="statusbar-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-icon-blue.svg" class="img-fluid">
                            <p>Goals: </p>
                            <span> <?php echo $totalGoals; ?></span>
                        </div>
                        <div class="statusbar-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-icon.svg" class="img-fluid">
                            <p>Alliances: </p>
                            <span><?php echo $totalAlliances; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="profile-detail-section <?php echo $UNAVAILABLE ? 'profile-unavailable-sec' : ''; ?> ">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="profile-setting-sec-btn">
                    <?php if($userID == $currentUserID){ ?>
                        <button id="edit-profile-btn" class="btn btn-blue ">Edit my profile</button>
                        <a href="<?php echo home_url('/'.PROFILE.'/'.SETTINGS); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/gearIcon.svg">
                        </a>
                    <?php }elseif (in_array($currentUserID, $connections)){ ?>
                        <button id="remove-connection" data-user_id="<?php echo$userID; ?>" class="btn border border-primary ">
                        Remove Connection</button>
                    <?php }elseif (in_array($userID, $mypcr)){ ?>
                        
                            <button id="accept-request" data-user_id="<?php echo$userID; ?>" class="btn btn-blue">
                            Accept</button>
                            <button id="reject-request" data-user_id="<?php echo$userID; ?>" class="btn border border-primary">
                            Reject</button>
                        
                    <?php }elseif (in_array($currentUserID, $pcr)){ ?>
                        <button id="remove-connection-request" data-user_id="<?php echo$userID; ?>" class="btn border border-primary">
                        Remove Connection Request</button>
                    <?php }else{ ?>
                        <button id="connection-request" data-user_id="<?php echo$userID; ?>" class="btn btn-blue ">
                        Request Connection</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if($mypp || $public_profile != '0' && ($show_gender != '0' || $personal_info != '0' || $show_dob != '0')){ ?>
        <div class="profiled-row">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="profile-row-header">
                        <h5>Personal Information</h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="personal-info-crd">
                        <div id="personal-info-crd" >
                            <?php if($mypp || $show_gender != '0'){ ?>
                                <p>Gender: <?php echo ucfirst($gender); ?></p>
                            <?php } if($mypp || $personal_info != '0'){ ?>
                                <p>Country: <?php echo $country; ?></p>
                                <p>Zip Code: <?php echo $zip_code; ?></p>
                            <?php } if($mypp || $show_dob != '0'){ ?>
                                <p>Date of Birth: <?php echo $dob; ?></p>
                            <?php } ?>
                        </div>
                        <form id="edit-profile-frm" style="display: none;">
                            <p>Gender: <select name="gender" id="gender">
                                <option value="" ></option>
                                <option <?php echo $gender=='male'?'selected':'';; ?> value="male">Male</option>
                                <option <?php echo $gender=='female'?'selected':'';; ?> value="female">Female</option>
                                <option <?php echo $gender=='other'?'selected':'';; ?> value="other">Other</option>
                            </select> </p>    
                            <p>Country:
                                <select name="country" id="country" >
                                    <option>USA</option>
                                </select>
                            </p>
                            <p>Date of Birth:
                                <?php $mindob = strtotime(date('Y-m-d').' -18 year'); ?>
                                <input type="date" placeholder="yyyy-mm-dd" value="<?php echo $odob; ?>" name="dob" id="dob" min="1920-01-01" max="<?php echo date('Y-12-31', $mindob); ?>" />
                            </p>
                            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/png, image/jpeg, image/jpg, image/gif">
                            <?php wp_nonce_field( 'ajax-update-profile-nonce', 'security' . $userID ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="profiled-row">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="profile-row-header">
                        <h5>Public Goals</h5>
                    </div>
                </div>
            </div>
            <div class="row">
            <?php if($mygoals->have_posts()){
            while($mygoals->have_posts()) { 
                  $mygoals->the_post();
                  get_template_part('template-parts/goal','content'); 
                } wp_reset_query();
            }else{ ?>
                <div class="col-12 text-center ">
                    <div class="alert alert-warning">
                        No Goals found!
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
        <div class="profiled-row">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="profile-row-header">
                        <h5>Public Alliances</h5>
                    </div>
                </div>
            </div>
            <div class="row alliances-desktop-sec">
                <?php if($myalliance->have_posts()) {
                while($myalliance->have_posts()) { 
                    $myalliance->the_post(); 
                        get_template_part('template-parts/alliance','content'); 
                    } wp_reset_query();
                }else{ ?>
                    <div class="col-12 text-center ">
                        <div class="alert alert-warning">
                            No Alliances found!
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>