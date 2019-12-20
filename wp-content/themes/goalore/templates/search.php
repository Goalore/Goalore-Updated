<?php 
	/**
	  *
	  *Search Result page
	  *
	  */

get_header(); 

$key = isset($_REQUEST['key'])?$_REQUEST['key']:'';
$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';


$goalArgs = [
	'post_type'    => 'goals',
	's' 		   => $key,
	'meta_key'     => 'status',
    'meta_value'   => 'private',
	'meta_compare' => '!='
	
];

if($type == 'goal'){
	$goalArgs['posts_per_page'] = -1;
}else{
	$goalArgs['posts_per_page'] = 9;
}

$goals = New WP_Query($goalArgs);


global $wpdb;
$pageUrl = site_url().'/'.SearchResult;

//Seach Member SQL Query
$MemebrSQL = "
SELECT U.ID FROM {$wpdb->prefix}users AS U
	INNER JOIN {$wpdb->prefix}usermeta AS UM1 ON (U.ID = UM1.user_id) AND (UM1.meta_key = 'full_name' AND UM1.meta_value LIKE '%". $key."%')
	OR (U.user_login LIKE '%". $key."%' OR U.user_nicename LIKE '%". $key."%' OR U.user_email LIKE '%". $key."%' ) GROUP BY ID
";
if($type != 'member'){
	$MemebrSQL .= ' LIMIT 9';
}
$results =  $wpdb->get_results( $MemebrSQL, ARRAY_A );

//get Member IDs from SQL result
$memberIDs = []; 
if(!empty($results)){
	$objTmp = (object) array('aFlat' => array());
	array_walk_recursive($results, function(&$v, $k, &$t){ $t->aFlat[] = $v; }, $objTmp);
	$memberIDs = $objTmp->aFlat;
}

// get public profile Member IDs
$PMIDs = []; 
if(!empty($memberIDs)){
	$members = new WP_user_query([
		'include' => $memberIDs,
	]); if(!empty($members->get_results())){
		foreach($members->get_results() as $member) {
			$isDeleted = get_user_meta($member->ID, 'isDeactivated', true);
			$isDeactivated = get_user_meta($member->ID, 'isDeleted', true);
			$US = get_user_meta($member->ID, 'user_Settings', true);
			$public_profile = isset($US['public_profile'])?$US['public_profile']:'';
			if($public_profile != '0' && $isDeactivated != '1' && $isDeleted != '1'){
				$PMIDs[] = $member->ID;
			}
		}
	}
}

?>

	<section class="search-page-section">
	    <div class="container">
	        <div class="row">
	            <div class="col">
	                <div class="section-header">
	                    <h2>Search Results</h2>
	                </div>
	            </div>
	        </div>
	        <?php if($type != 'goal'){ ?>
	        <div class="s-results">
	            <div class="row">
	                <div class="col">
	                    <div class="user-search-header">
	                        <h4>Users</h4>
	                        <?php if(!empty($PMIDs) && count($PMIDs) > 9 && $type == ''){ 
	                        	$seeAll = add_query_arg([
	                        		'key' => $key,
	                        		'type' => 'member',
	                        	], $pageUrl);  ?>
	                        	<a href="<?php echo $seeAll; ?>">View all user results ></a>
	                    	<?php } ?>
	                    </div>
	                </div>
	            </div>
	            <div class="row">
	            	<?php if(!empty($PMIDs)){
	            		$members = new WP_user_query([
							'include' => $PMIDs,
						]); if(!empty($members->get_results())) {
                		foreach($members->get_results() as $member) {
                			set_query_var( 'userData', $member ); ?>
			                <div class="col-12 col-md-6 col-lg-4">
			                	<?php get_template_part('template-parts/user','listing-content');  ?>
			                </div>
			        	<?php } }
			    	}else{ ?>
		        		<div class="col-12 text-center ">
		                    <div class="alert alert-warning">
		                	   	No members found!
		                    </div>
		                 </div>
			       	<?php } ?>
	            </div>
	        </div>
	    	<?php } ?>
	        <?php if($type != 'member'){ ?>
		        <div class="s-results">
		            <div class="row">
		                <div class="col">
		                    <div class="user-search-header">
		                        <h4>Goals</h4>
		                        <?php if($goals->have_posts() && $goals->found_posts > 9 && $type == ''){ 
		                        	$seeAll = add_query_arg([
		                        		'key' => $key,
		                        		'type' => 'goal',
		                        	], $pageUrl);  ?>
			                        <a href="<?php echo $seeAll; ?>">View all goal results ></a>
			                    <?php } ?>
		                    </div>
		                </div>
		            </div>
		            <div class="row">
		            	<?php if($goals->have_posts()) { 
		            		while($goals->have_posts()) { 
		            			$goals->the_post();
		            			$GoalID = get_the_ID();
								$userID = get_post_field( 'post_author', $GoalID );
								$user = get_user_by('ID',$userID);

								$userProfile = get_author_posts_url($userID);
								$full_name = get_user_meta($userID, 'full_name', true);
								$user_registered = date('Y/m/d',strtotime($user->user_registered));
								$profile_picture = get_user_profile_picture($userID); 

								$table_name = $wpdb->prefix . "gdp";
								$GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $userID"); 
								if(empty($GDP)) $GDP = 0; 

								$povCount = get_comments_number();
								$milestones = get_field('milestones');
								$milestonesCount = 0;
								if(!empty($milestones)) $milestonesCount = count($milestones);
								$challenges = get_field('challenges');
								$challengesCount = 0;
								if(!empty($challenges)) $challengesCount = count($challenges); 

								$attachments = get_field('attachments');
								$attachmentsCount = 0;
								if(!empty($attachments)) $attachmentsCount = count($attachments); 

		            		 ?>
				                <div class="col-12 col-md-6">
				                    <div class="search-goal-item">
				                        <div class="search-goal-header">
				                            <a href="<?php the_permalink(); ?>"><h6><?php the_title(); ?></h6></a>
				                        </div>
				                        <div class="search-goal-body">
				                            <div class="row align-items-end">
				                                <div class="col-21 col-md-12 col-lg-6">
				                                	<a href="<?php echo $userProfile; ?>">
					                                    <div class="search-goal-user-info">
					                                        <div class="cgu-img">
					                                            <img src="<?php echo $profile_picture; ?>" class="img-fluid">
					                                        </div>
					                                        <p><?php echo $full_name; ?></p>
					                                        <p>Good Deed Points: <?php echo $GDP; ?></p>
					                                        <p>Member Since: <?php echo $user_registered; ?></p>
					                                    </div>
				                                    </a>
				                                </div>
				                                <div class="col-21 col-md-12 col-lg-6">
				                                    <div class="goal-info-bar">
				                                        <div class="gib-item">
				                                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-milestones.svg" class="img-fluid">
				                                            <h6><?php echo $milestonesCount; ?></h6>
				                                        </div>
				                                        <div class="gib-item">
				                                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-pov.svg" class="img-fluid">
				                                            <h6><?php echo $challengesCount; ?></h6>
				                                        </div>
				                                        <div class="gib-item">
				                                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-challenges.svg" class="img-fluid">
				                                            <h6><?php echo $povCount; ?></h6>
				                                        </div>
				                                        <div class="gib-item">
				                                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-attachments.svg" class="img-fluid">
				                                            <h6><?php echo $attachmentsCount; ?></h6>
				                                        </div>
				                                    </div>
				                                </div>
				                            </div>
				                        </div>
				                    </div>
				                </div>
		                	<?php }
		                }else{ ?>
			        		<div class="col-12 text-center ">
			                    <div class="alert alert-warning">
			                	   	No public goals found!
			                    </div>
			                 </div>
				       	<?php } ?>
		            </div>
		        </div>
		    <?php } ?>
	    </div>
	</section>
<?php get_footer(); ?>