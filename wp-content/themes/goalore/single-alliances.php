<?php 
    /**
      *
      *Alliance Details Page
      *
      */

get_header(); 

get_fields()?extract(get_fields()):null; 

$AllianceID = get_the_ID();
$AAID = get_post_field( 'post_author', $AllianceID );
$currentUserID = get_current_user_id(); 

$members = !isset($members) ? get_post_meta($AllianceID,'members',true) : $members;

if(empty($members)){
	$members = [];
	$membersCount = 0;
}else $membersCount = count($members);

$ADMINS = get_post_meta($AllianceID,'admins',true);
if(empty($ADMINS)) $ADMINS = [];


$linkedGoalsID = !isset($goals) ? get_post_meta($AllianceID,'goals',true) : $goals;
if(empty($linkedGoalsID)) $linkedGoalsID = [];


$isMember = false;
$isAdmin = false;
$isMemberAdmin = false;
if(in_array($currentUserID, $members)) {
	$isMember = true;
}
if(in_array($currentUserID, $ADMINS)) {
	$isMemberAdmin = true;
}
if($AAID == $currentUserID) {
	$isAdmin = true;
}


$linkedGoals = New WP_Query( [ 
	'posts_per_page' => 0,
	'post_type' 	 => 'goals',
	'post__in' 		 => $linkedGoalsID,
]);

$mygoals = New WP_Query([
	'posts_per_page' => -1,
	'post_type' => 'goals',
	'author'	=> $currentUserID,
	'meta_key' 	=> 'status',
	'meta_value'=> 'public',
	'post__not_in' => $linkedGoalsID
]);

$myconnectionsIDs = get_user_meta($currentUserID,'connections',true);


?>

<section class="goal-detail-sec" >
    <div class="container">
        <div class="goalorew-card goal-detail-card">
            <div class="row">
                <div class="col">
                    <div class="goal-detail-header">
                        <h4><?php the_title(); ?></h4>
                        <?php if(!$isAdmin) { ?>
                        	<?php if($isMember) { ?>
		                        <button id="leave-alliance" data-alliance_id="<?php echo$AllianceID; ?>" class="btn border border-primary mr-3 ml-2">Leave</button>
		                    <?php }else{ ?>
		                        <button id="join-alliance" data-alliance_id="<?php echo$AllianceID; ?>" class="btn btn-blue  mr-3 ml-2">Join</button>
		                    <?php } ?>
	                    <?php } ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid">
                        <h5><?php echo $membersCount ?></h5>
                        <?php if($isAdmin || $isMemberAdmin){ ?>
	                        <div class="gdh-btn-group">
	                            <!-- <a href="" class="btn btn-blue">Attachments (2)</a> -->
	                            <a href="javascript:;" id="show-all-members" class="btn btn-green">All Members</a>
	                            <label>Invite a Member</label>
	                            <a href="javascript:;" class="open-form" id="invite-member-btn" >
	                            	<img src="<?php echo get_template_directory_uri(); ?>/images/green-plus-icon.svg">
	                            </a>
	                        </div>
	                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row" id="alliance-ctn">
                <div class="col-12 col-lg-4">
                    <div class="milestone-challenges-sec">
                        <div class="goald-item-card" id="linked-goals-list">
                            <div class="goald-item-header">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-icon.svg" class="img-fluid header-labl">
                                <h5>Alliance Goals</h5>
                                <?php if($isAdmin || $isMemberAdmin){ ?>
	                                <div class="gih-newbtn-group">
	                                    <label>Link My Goal</label>
	                                    <a href="javascript:;" class="open-form" id="link-goal" >
	                                    	<img src="<?php echo get_template_directory_uri(); ?>/images/green-plus-icon.svg">
	                                    </a>
	                                </div>
	                            <?php } ?>
                            </div>
                            <div class="goal-item-body mCustomScrollbar gib-large" data-mcs-theme="dark">
                            	<?php $LGD = [];
                            	if(!empty($linkedGoalsID)) : 
                            		while($linkedGoals->have_posts()) : $linkedGoals->the_post();  
                            			$LGD[] = [ 'id' => get_the_ID(),
                            				'title' => get_the_title() ];
                            			$ALCount = get_comments_number();
										$milestones = get_field('milestones');
										$milestonesCount = 0;
										if(!empty($milestones)) $milestonesCount = count($milestones);
										$challenges = get_field('challenges');
										$challengesCount = 0;
										if(!empty($challenges)) $challengesCount = count($challenges); 
										$attachments = get_field('attachments');
										$attachmentsCount = 0;
										if(!empty($attachments)) $attachmentsCount = count($attachments); ?>
		                                <div class="goalorew-card goal-alliance-item">
		                                    <div class="goal-item-header">
		                                        <a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
		                                    </div>
		                                    <div class="goal-item-content">
		                                        <div class="goal-item">
		                                            <div class="goal-item-label">
		                                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-milestones.svg" class="img-fluid">
		                                                <p>Milestones: <span><?php echo $milestonesCount; ?></span></p>
		                                            </div>
		                                        </div>
		                                        <div class="goal-item">
		                                            <div class="goal-item-label">
		                                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-challenges.svg" class="img-fluid">
		                                                <p>Challenges: <span><?php echo $challengesCount; ?></span></p>
		                                            </div>
		                                        </div>
		                                        <div class="goal-item">
		                                            <div class="goal-item-label">
		                                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-pov.svg" class="img-fluid">
		                                                <p>Points of View: <span><?php echo $ALCount; ?></span></p>
		                                            </div>
		                                        </div>
		                                        <div class="goal-item">
		                                            <div class="goal-item-label">
		                                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-attachments.svg" class="img-fluid">
		                                                <p>Attachments: <span><?php echo $attachmentsCount; ?></span></p>
		                                            </div>
		                                        </div>
		                                    </div>
		                                </div>
		                            <?php endwhile; wp_reset_query();
		                        else :  ?>
		                        	<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Goals linked!
										</div>
									</div>
								<?php endif; ?>
								<?php if($linkedGoals->found_posts == 1 && !empty($linkedGoalsID) && ($isAdmin || $isMemberAdmin ) ) { ?>
	                                <a href="javascript:;" id="unlinked-goal-btn" class="red-link">Remove a linked goal</a>
	                            <?php } ?>
                            </div>
						<?php if($linkedGoals->found_posts > 1 && !empty($linkedGoalsID) && ($isAdmin || $isMemberAdmin ) ) { ?>
                        	<a href="javascript:;" id="unlinked-goal-btn" class="red-link">Remove a linked goal</a>
	                    <?php } ?>
                        </div>
                        <div class="link-my-goal-card" id="linked-goal" style="display: none;">
                            <div class="goald-item-header">
                            	<a href="javascript:;" id="back-linked-goals-list-btn" class="back-btn">
                            		<img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg">
                            	</a>
                                <h5>Link my goal</h5>
                            </div>
                            <?php if($mygoals->have_posts()) : 
                            while($mygoals->have_posts()) : $mygoals->the_post(); ?>
	                            <div class="goald-item-item">
	                                <p><?php the_title(); ?></p>
	                                <a href="javascript:;" data-goal_id="<?php the_ID(); ?>" data-alliance_id ="<?php echo $AllianceID; ?>" id="link-goal-btn" class="link-goal">
	                                	<img src="<?php echo get_template_directory_uri(); ?>/images/link-add-icon.svg"> Link
	                                </a>
	                            </div>
	                        <?php endwhile; wp_reset_query();
	                        else :  ?>
	                        	<div class="goald-item-item">
	                        		<div class=" col-12 alert alert-warning text-center">
										No Goals linked!
									</div>
	                            </div>
	                    <?php endif; ?>
                        </div>
                        <div class="link-my-goal-card"  id="unlink-goal" style="display: none;">
                            <div class="goald-item-header">
                            	<a href="javascript:;" id="back-linked-goals-list-btn" class="back-btn">
                            		<img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg">
                            	</a>
                                <h5>Unlink Goal</h5>
                            </div>
                            <?php if(!empty($LGD)) { 
                        		foreach($LGD as $LG){ ?>
		                            <div class="goald-item-item">
		                                <p><?php echo $LG['title']; ?></p>
		                                <a href="javascript:;" data-goal_id="<?php echo $LG['id']; ?>" data-alliance_id ="<?php echo $AllianceID; ?>" id="unlink-goal-btn" class="link-goal">
		                                	<img src="<?php echo get_template_directory_uri(); ?>/images/unlink-icon.svg"> Unlink
		                                </a>
		                            </div>
                           		<?php }
                           	} ?>
                        </div>
               	    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="pov-sec" id="al-listing">
                        <div class="goald-item-card">
                            <div class="goald-item-header">
                                <!-- <a href=""><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg" class="img-fluid back-button"></a> -->
                                <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-actions.svg" class="img-fluid header-labl">
                                <h5>Action Log</h5>
                                <?php if($isAdmin || $isMemberAdmin){ ?>
	                                <a href="javascript:;" id="add-al-btn" class="btn btn-blue headr-right">Create an action</a>
	                            <?php } ?>
                            </div>
                            <div class="goal-item-body gib-large mCustomScrollbar" data-mcs-theme="dark">
                                <?php $ActionLogs = get_comments( [
                            		'post_id' => $AllianceID,
                            		'parent'     => 0,
                            	]); 
                                if(!empty($ActionLogs)){
                            		foreach($ActionLogs as $AL){ 
                            			$ALID = $AL->comment_ID;
                            			$SubActionLogs = get_comments( [
		                            		'post_id' => $AllianceID,
		                            		'parent'  => $ALID,
		                            	]); ?>
                            			<div class="goal-item-child pov-item-child">
		                                    <div class="pov-gitem-header">
		                                        <a href="<?php echo get_author_posts_url($AL->user_id); ?>">
		                                        	<h6><?php echo $AL->comment_author; ?></h6>
		                                        </a>
		                                        <small><?php echo date('d/m/Y',strtotime($AL->comment_date)); ?></small>
		                                    </div>
		                                    <p><?php echo $AL->comment_content; ?></p>
		                                    <div class="pov-gitem-footer">
		                                        <?php if($AL->user_id != $currentUserID && ($isMemberAdmin || $isAdmin) ){ ?>
			                                        <a href="javascript:;" id="al-respond-btn"  data-al_id="<?php echo $ALID; ?>" class="btn btn-blue pov-respond-btn">Respond</a>
			                                    <?php } ?>
		                                    </div>
		                                    <?php if(!empty($SubActionLogs)){
			                            		foreach($SubActionLogs as $SAL){ 
			                            			$SpovID = $SAL->comment_ID; ?>
				                                    <hr> <div class="goal-item-child pov-item-child pov-sub-child">
													    <div class="pov-gitem-header">
													    	<a href="<?php echo get_author_posts_url($SAL->user_id); ?>">
													        	<h6><?php echo $SAL->comment_author; ?></h6>
													        </a>
													        <small><?php echo date('d/m/Y',strtotime($SAL->comment_date)); ?></small>
													    </div>
													    <p><?php echo $SAL->comment_content; ?></p>
													</div>
												<?php }
											} ?>
		                                </div>
                            		<?php }
                            	}else{ ?>
                            		<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Action Log!
										</div>
									</div>
                            	<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="newmc-form-sec" id="add-al-ctn" >
                    	<div class="row justify-content-md-center">
                    		<div class="col-lg-10 ">
		                        <div class="goald-item-header newmc-form-header">
		                            <a href="javascript:;" id="back-al-btn" class="back-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg"></a>
		                            <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-actions.svg"  class="img-fluid header-labl">
		                            <h5 id="label" >Action Log</h5>
		                        </div>
		                        <div class="newmc-form-body">
		                            <div class=" create-goal-form">
		                                <form id="action-log-frm">
		                                    <div class="form-group">
		                                        <label id="label">Action</label>
		                                        <textarea name="description" id="description" rows="6" class="form-control"></textarea>
		                                    </div>
		                                    <input type="hidden" name="alliance_id" value="<?php echo $AllianceID ?>" id="alliance_id">
		                                    <input type="hidden" name="al_parent_id" value="0" id="al_parent_id">
		                                    <button type="submit" class="btn pov-submit btn-blue">Submit</button>
		                                </form>
		                            </div>
		                        </div>
		                    </div>
		                </div>
                    </div>
			        <div class="newmc-form-sec " id="invite-member-ctn" >
			        	<div class="row justify-content-md-center">
                    		<div class="col-12 col-lg-11 ">
                    			<div class="goald-item-header newmc-form-header">
		                            <a href="javascript:;" id="back-al-btn" class="back-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg"></a>
		                            <h5 id="label" >Invite Member</h5>
		                        </div>
		                    	<div class="goal-item-child goalc-item-search">
		                            <form class="form-inline search-form" id="search-member-ai-frm">
		                                <div class="input-group">
		                                    <input class="form-control border-0" id="search-member-ai" type="text" data-alliance_id="<?php echo $AllianceID ?>" placeholder="Search Goalore">
		                                    <div class="input-group-append">
		                                        <button class="btn btn-outline-secondary my-2 my-sm-0 border-0" type="submit"><img src="<?php echo get_template_directory_uri(); ?>/images/search-icon.svg"> </button>
		                                    </div>
		                                </div>
		                            </form>
		                        </div>
		                        <div class="doal-con-subheader">
		                            <h6>Your Connections</h6>
		                        </div>
		                        <div class="pov-item-child gib-large mCustomScrollbar">
		                        	<div id="my-connections-ai">
			                        	<div class="col-12 text-center ">
							                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
							            </div>
							            <div id="my-connections-listing-ai">
				                        	<?php if(!empty($myconnectionsIDs)){
				                        		//Exlude Members 
				                        		$onlyInclude = array_diff($myconnectionsIDs, $members);
				                        		//Exlude Alliance Admin 
				                        		$onlyInclude = array_diff($onlyInclude, [$AAID]);
							                	$cUsers = New WP_User_Query([
							                		'include' => $onlyInclude,
							                	]); if(!empty($cUsers->get_results())){
							                		foreach($cUsers->get_results() as $cuser){ $SENT=false;
							                			set_query_var( 'userData', $cuser ); 
							                			$alliance_invitation = get_user_meta($cuser->ID,'alliance_invitation',true);
							                			if(empty($alliance_invitation)) $alliance_invitation=[]; 
							                			if(array_key_exists($AllianceID,$alliance_invitation)){
							                				$SENT =  true;
							                			} ?>
							                            <div class="connection-invitation-item">
							                            	<?php get_template_part('template-parts/user','listing-content');  ?>
							                                <div class="allience-req-btn">
							                                    <a href="javascript:;" id="<?php echo $SENT?'invitation-sent':'invite-member'; ?>" data-alliance_id="<?php echo$AllianceID; ?>" data-user_id="<?php echo$cuser->ID; ?>"  class="btn btn-remove-con" >
							                                    	<?php if($SENT){
							                                    		echo 'Invitation Sent';
							                                    	}else{ ?>
							                                    		<img src="<?php echo get_template_directory_uri(); ?>/images/link-add-icon.svg" class="img-fluid">
							                                    		Invite to Alliance
							                                    	<?php } ?>
							                                    </a>
							                                </div>
							                            </div>
							                    	<?php }
							                    }
							                }else{ ?>
							                	<div class="col-12 text-center ">
									            	<div class="alert alert-warning">
														No Connections!
													</div>
												</div>
							                <?php } ?> 
							            </div>
					            	</div>
		                        </div>
		                    </div>
		                </div>
                    </div>
                </div>
            </div>
            <div class="row" id="alliance-member-ctn" style="display: none;" >
                <div class="col-12 col-lg-12">
                    <div class="pov-sec">
                        <div class="goald-item-card">
                            <div class="goald-item-header">
                                <a href="javascript:;" id="back-alliance"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg" class="img-fluid back-button"></a>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid header-labl">
                                <h5>Alliance Members</h5>
                            </div>
                            <div class="goal-item-body gib-large mCustomScrollbar" data-mcs-theme="dark">
                                <div class="row">
                                	<?php 
                                	if(!empty($members)){
					                	$MUsers = New WP_User_Query([
					                		'include' => $members,
					                	]); 
					                	if(!empty($MUsers->get_results())){
					                		$hfn = floor(count($MUsers->get_results()) / 2);
					                		$hfn = $hfn > 1 ? $hfn  : 1;
					                		$MUserss = array_chunk($MUsers->get_results(), $hfn);
					                		foreach($MUserss as $musers){
						                		echo '<div class="col-12 col-lg-6">';
							                		foreach($musers as $muser){
								                		set_query_var( 'userData', $muser );
							                			$MemberAdmin = false;
							                			if(in_array($muser->ID, $ADMINS)) {
															$MemberAdmin = true;
														}?> 
				                                        <div class="pov-item-child">
				                                            <div class="connection-invitation-item">
				                                                <?php get_template_part('template-parts/user','listing-content');  ?>
				                                                <div class="allience-req-btn">
				                                                	<?php if($MemberAdmin){ ?>
				                                                		<a href="javascript:;" id="ai-remove-admin" data-alliance_id="<?php echo$AllianceID; ?>" data-user_id="<?php echo$muser->ID; ?>" class="btn btn-remove-con">
					                                                    	<img src="<?php echo get_template_directory_uri(); ?>/images/unlink-icon.svg" class="img-fluid">
					                                                    	<label>Remove Admin</label>
					                                                    </a>
					                                                <?php }else{ ?>
					                                                	<a href="javascript:;" id="ai-make-admin" data-alliance_id="<?php echo$AllianceID; ?>" data-user_id="<?php echo$muser->ID; ?>" class="btn btn-remove-con">
					                                                    	<img src="<?php echo get_template_directory_uri(); ?>/images/link-add-icon.svg" class="img-fluid">
					                                                    	<label>Make Admin</label>
					                                                    </a>
					                                                <?php } ?>
				                                                </div>
				                                            </div>
				                                        </div>
				                                	<?php }
			                                	echo '</div>';
		                                	}
			                            }
	                           		}else{ ?>
	                           			<div class="col-12 text-center ">
							            	<div class="alert alert-warning">
												No Members!
											</div>
										</div>
	                           		<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>
