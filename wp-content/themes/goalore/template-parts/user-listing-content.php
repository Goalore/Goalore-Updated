<?php 
global $wpdb;
$userData = get_query_var('userData');
// $full_name = get_user_meta($userData->ID, 'full_name', true);
$username = $userData->user_login;
$user_registered = date('m/d/Y',strtotime($userData->user_registered));
$profile_picture = get_user_profile_picture($userData->ID); 
$profile_url =get_author_posts_url($userData->ID); 
$GDP = get_gdp($userData->ID);

?>
<a href="<?php echo $profile_url; ?>">
    <div class="user-card-box">
        <div class="user-profile-pic">
            <img src="<?php echo $profile_picture; ?>" class="img-fluid">
        </div>
        <div class="user-profile-info">
            <p><?php echo$username; ?></p>
            <p>Good Deed Points: <?php echo $GDP; ?></p>
            <p>Member Since: <?php echo$user_registered; ?></p>
        </div>
    </div>
</a>