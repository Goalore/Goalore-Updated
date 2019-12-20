<?php 
    /**
      *
      *Template Name: User Dashboard
      *
      */

get_header(); 

$currentUserID = get_current_user_id();  ?>
<section class="goal-feed-desktop-sec">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10 col-md-4 col-lg-3 goal-alliances-card">
                <div class="goalorew-card ">
                    <div class="gcard-header gcard-header-all">
                        <h6>Goals</h6>
                        <a href="<?php the_permalink(165); ?>">All goals ></a>
                    </div>
                    <div class="gcard-body">
                        <ul class="b-body-nav">
                            <?php $goals = get_posts(['numberposts'=>3,'post_type'=>'goals','author'=>$currentUserID]);
                            if(!empty($goals)){
                                foreach($goals as $goal){ ?>
                                    <li><a href="<?php the_permalink($goal->ID); ?>">
                                        <?php echo $goal->post_title; ?>
                                    </a></li>
                                <?php }
                            }else{ ?>
                                <li>No Goals Created!</li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="gcard-header gcard-header-all">
                        <h6>Alliances</h6>
                        <a href="<?php the_permalink(167); ?>">All Alliances ></a>
                    </div>
                    <div class="gcard-body">
                        <ul class="b-body-nav">
                            <?php $goals = get_posts(['numberposts'=>3,'post_type'=>'alliances','author'=>$currentUserID]);
                            if(!empty($goals)){
                                foreach($goals as $goal){ ?>
                                    <li><a href="<?php the_permalink($goal->ID); ?>">
                                        <?php echo $goal->post_title; ?>
                                    </a></li>
                                <?php }
                            }else{ ?>
                                <li>No Alliances Created!</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8 col-lg-6 create-new-goal-card">
                <div class="goalorew-card">
                    <div class="gcard-form create-goal-form">
                        <form id="create-goal-frm" method="post">
                            <h6>Create a new goal!</h6>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" id="title" class="form-control" >
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label>Type of Goal</label>
                                    <select class="form-control" name="type" id="type">
                                        <option value="individual">Individual Goal</option>
                                        <option value="community">Community Goal</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Target Date of Completion</label>
                                    <input type="date" min="<?php echo date('Y-m-d'); ?>"  name="target" id="target" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label>Goal Category</label>
                                    <select name="category" class="form-control" id="category" >
                                        <option value="">Select</option>
                                        <?php $terms = get_terms([
                                              'taxonomy'   => 'goal_categories',
                                              'hide_empty' => false,
                                              'parent'   => 0,
                                              'meta_key' => 'active',
                                              'meta_value' => '1'
                                            ]); if(!empty($terms)){
                                                foreach($terms as $term){ ?>
                                                    <option value="<?php echo $term->term_id; ?>">
                                                        <?php echo $term->name ?>
                                                    </option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Goal Subcategory</label>
                                    <select class="form-control" name="subcategory" id="sub-category" >
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="form-group col-12 col-md-6">
                                    <label>Goal Status</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="private">Keep it private for now</option>
                                        <option value="public">Make this public to everyone</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <button type="submit" class="btn btn-blue">Create Goal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php 
            	$myconnectionsIDs = get_user_meta($currentUserID,'connections',true);
                if(!empty($myconnectionsIDs)) {
                	$table_notification = $wpdb->prefix . "notification";
	            	$SQL = ' SELECT * FROM '.$table_notification.' ORDER BY datetime DESC ' ;
	            	$notifications = $wpdb->get_results ( $SQL );
	            	if(!empty($notifications)){ $i = 0;
	            		foreach($notifications as $notif){ 
	            			$myuser = $notif->notifier_user_id;
	            			if(in_array($myuser, $myconnectionsIDs)){ $i++;

		            			$profile_picture = get_user_profile_picture($myuser);
		            			$full_name = get_user_meta($myuser, 'full_name', true);
		            			$profile_url =get_author_posts_url($myuser); 
		            			$table_name = $wpdb->prefix . "gdp";
								$GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $myuser"); 
								if(empty($GDP)) $GDP = 0; 

		            			$permalink = get_permalink($notif->permalink_id);
		            			$permalink = !empty($permalink) ? $permalink : 'javascript:;';
		            			 $message = $notif->message;
		            			
		            			if(strpos($message,'started a new goal')){
									$title = 'Started a new goal!';
		            			}else if(strpos($message,'created a new alliance')){
									$title = 'Created a new alliance!';
		            			}else if(strpos($message,'completed a milestone')){
									$title = 'Completed a milestone!';
		            			}else if(strpos($message,'posted a new milestone')){
									$title = 'Posted a new milestone!';
		            			}else if(strpos($message,'completed a challenge')){
									$title = 'Completed a challenge!';
		            			}else if(strpos($message,'posted a new challenge')){
									$title = 'Posted a new challenge!';
		            			}else if(strpos($message,'just Completed his')){
									$title = 'Gaol Completed!';
		            			}else if(strpos($message,'wrote a Point of View')){
									$title = 'Wrote a Point of View!';
		            			}else{
									$title = '';
		            			} ?>
				                <div class="goalorew-card user-goal">
				                        <a href="<?php echo $profile_url ?>">
				                    <div class="user-goal-header">
					                        <div class="guser-profile">
					                            <img src="<?php echo $profile_picture; ?>" class="img-fluid">
					                        </div>
					                        <div class="guser-detail">
					                            <p><?php echo $full_name; ?></p>
					                            <p>Good Deed Points: <?php echo $GDP; ?></p>
					                        </div>
				                    </div>
				                        </a>
				                    <div class="user-goal-body">
				                        <div class="goal-title">
				                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-icon-blue.svg" class="img-fluid">
					                        <a href="<?php echo $permalink ?>">
					                            <h6><?php echo $title; ?></h6>
					                        </a>
				                        </div>
				                        <p><?php echo $message ?></p>
				                    </div>
				                </div>
			            	<?php }
			            if($i==3) break;
			        	 }
			        } 
                } ?>

            </div>
            <div class="col-12 col-md-4 col-lg-3 new-blog-post-card">
                <div class="goalorew-card">
                    <div class="gcard-header gcard-header-all">
                        <h6>New Blog Posts</h6>
                        <a href="<?php the_permalink(11); ?>">All Articles ></a>
                    </div>
                    <div class="gcard-body">
                        <ul class="b-body-nav">
                            <?php 
                            $blogs = get_posts(['numberposts'=>3]);
                            if(!empty($blogs)){
                                foreach($blogs as $blog){ ?>
                                    <li><a href="<?php the_permalink($blog->ID); ?>">
                                        <?php echo $blog->post_title; ?>
                                    </a></li>
                                <?php }
                            } ?>
                        </ul>
                    </div>
                </div>
                <div class="invite-friend">
                    <a href="<?php echo home_url('/'.InviteFriend); ?>" class="btn btn-blue">Invite a Friend</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>