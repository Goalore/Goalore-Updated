<?php 
    /**
      *
      *Gaol Details Page
      *
      */

get_header(); 

get_fields()?extract(get_fields()):null; 

$GoalID = get_the_ID();
$goalAuthorID = get_post_field( 'post_author', $GoalID );
$currentUserID = get_current_user_id(); 
$backlink = get_permalink(165);

$followers = !isset($followers) ? get_post_meta($GoalID,'followers',true) : $followers;
$allFollowers = $followers;
if(empty($followers)){
	$allFollowers = $followers = [];
	$followersCount = 1;
}else $followersCount = count($followers) + 1;

$isFollower = false;
$isAdmin = false;
if(in_array($currentUserID, $followers)) {
	$isFollower = true;
    $backlink = $backlink . '?follower';
}
if($goalAuthorID == $currentUserID) {
	$isAdmin = true;
}

$Privacystatus = get_post_meta($GoalID,'status',true);

$GoalArchive = get_post_meta($GoalID, 'archive', true);
$GoalStatus = get_post_meta($GoalID,'goal_status',true);
$canManage = false;
$canManageSub = false;
if($GoalStatus != 'complete' && $isAdmin == true){
	$canManage = true;
}
if($GoalStatus != 'complete' && $isFollower == true){
	$canManageSub = true;
}

if($GoalArchive){
    $canManage = $canManageSub = false;   
    $backlink = get_permalink(579);
}

if(isset($attachments)){
	if(!empty($attachments)){
		$attachmentsCount = count($attachments);
	}else $attachmentsCount = 0;
}else $attachmentsCount = 0;

$GoalTitle = get_the_title(); 
$target = get_post_meta($GoalID, 'target', true);

$goal_categories = get_the_terms( $GoalID , 'goal_categories' );
$cat_parent_id = $cat_sub_id = 0;
if(isset($goal_categories[0]->term_id)){
    if($goal_categories[0]->parent == 0){
        $cat_parent_id = $goal_categories[0]->term_id;
        $cat_sub_id    = $goal_categories[1]->term_id;
    }else{
        $cat_sub_id    = $goal_categories[0]->term_id;
        $cat_parent_id = $goal_categories[1]->term_id;
    }
}

$show_more_link = '<a href="javascript:;" class="show-more-text" >... <b>show more</b></a>';

?>

<section class="goal-detail-sec">
    <div class="container">
    	<div class="row goal-header-nty">
            <div class="col-12 col-md-2  ">
                <a href="<?php  echo $backlink; ?>" class="btn bck-btn"> 
                    <img src="https://staginggoalore.wpengine.com/wp-content/themes/goalore/images/back-icon.svg" class="img-fluid back-button-img"> Back 
                </a>
            </div>
            <div class="col-12 col-md-10 text-center ">
                <?php if($GoalStatus == 'complete'){ ?>
	            <div class="alert alert-success">
	                This goal is marked as completed!
	            </div>
                <?php } if($GoalArchive){ ?>
                    <div class="alert alert-dark">
                        This goal has been archived!
                    </div>
                <?php } ?>
	        </div>
        </div>
        <div class="goalorew-card goal-detail-card">
            <div class="row">
                <div class="col">
                	<?php $subtitle = false; if(strlen($GoalTitle) > 20){ $subtitle=true; ?>
                		<h4 class="full-text" style="display: none;">
                			<?php the_title(); ?>
                			<a href="javascript:;" class="show-less-text" > <b>show less</b></a>
                		</h4>
                		<h4 class="less-text">
                			<?php echo get_limited_string($GoalTitle, 180, $show_more_link); ?>
	                	</h4>
                	<?php } ?>
                    <div class="goal-detail-header">
                        	<?php if(!$subtitle){ ?>
		                        <h4> <?php the_title(); ?> </h4>
		                	<?php } ?>
                        <?php if(!$isAdmin ) { ?>
                        	<?php if($isFollower) { ?>
		                        <button id="unfollow-goal" data-goal_id="<?php echo$GoalID; ?>" class="btn border border-primary mr-3 ml-2">Unfollow</button>
		                    <?php }else if($GoalStatus != 'complete' ){ ?>
		                        <button id="follow-goal" data-goal_id="<?php echo$GoalID; ?>" class="btn btn-blue  mr-3 ml-2">Follow</button>
		                    <?php } ?>
	                    <?php } ?>
                        <img id="show-all-followers" style="cursor: pointer;" src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid">
                        <h5><?php echo $followersCount; ?></h5>
                        <?php if($canManage){ ?>
                            <img id="edit-goal" data-toggle="modal" data-target="#updateGoal" style="cursor: pointer;" src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg" class="img-fluid">
                        <?php } if($Privacystatus == "public" ){ ?>
                            <img title="Public" src="<?php echo get_template_directory_uri(); ?>/images/unlock.svg" class="img-p" >
                        <?php }else{ ?>
                            <img title="Private" src="<?php echo get_template_directory_uri(); ?>/images/lock.svg" class="img-p" >
                        <?php } ?>
                        <p>Target: <?php echo date('m/d/Y', strtotime($target)); ?></p>
                        <div class="gdh-btn-group">
                            <?php if($isAdmin && !$GoalArchive) { ?>
                                <a title="Archive Goal" data-ag_id="<?php echo $GoalID ?>" id="archive-goal" class="btn btn-archive" >Archive Goal</a>
                        	<?php } if($isAdmin && !$GoalArchive){ ?>
                            	<a href="javascript:;" id="update-goal-status" data-goal_id="<?php echo $GoalID ?>" class="btn btn-green">
                            		<?php echo$GoalStatus == 'complete'? 'Reopen' : 'Complete';  ?> Goal</a>
                        	<?php } if($canManageSub){  ?>
	                            <a href="javascript:;" id="add-pov-btn" class="btn btn-purple">Add POV</a>
	                        <?php } ?>
                            <a href="javascript:;" id="add-attachments-btn" class="btn btn-blue">Attachments (<?php echo $attachmentsCount; ?>)</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="goal-ctn">
                <div class="col-12 col-lg-4">
                    <div class="milestone-challenges-sec" id="mc-listing">
                        <div class="goald-item-card <?php //echo (isset($milestones) && count($milestones)<=1 && count($challenges)>1)?'swmc':''; ?>">
                            <div class="goald-item-header">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-milestones.svg" id="type-img" class="img-fluid header-labl">
                                <h5>Milestones</h5>
                                <?php if($canManage){ ?>
	                                <div class="gih-newbtn-group">
	                                    <label>New Milestone</label>
	                                    <a href="javascript:;" id="add-milestone-btn" >
	                                    	<img src="<?php echo get_template_directory_uri(); ?>/images/green-plus-icon.svg">
	                                    </a>
	                                </div>
	                            <?php } ?>
                            </div>
                            <div class="goal-item-body listing-in-goal mCustomScrollbar gib-scroll" id="milestones" data-mcs-theme="dark">
                            	<?php if(!empty($milestones)){ $i=1;
                            		foreach($milestones as $m){ $Title = $m['title']; ?>
		                                <div class="goal-item-child" >
		                                	<?php if(strlen($Title) > 65){ ?>	
		                                    	<h6 id="title" class="full-text" style="display: none;">
		                                    		<span><?php echo $Title ?></span>
		                                    		<a href="javascript:;" class="show-less-text" > <b> show less</b></a>
		                                    	</h6>
			                                <?php } ?>	
	                                    	<h6 id="<?php echo strlen($Title) <= 65 ? 'title' : '' ; ?>" class="less-text">  
                                                <span><?php echo get_limited_string($Title, 65, $show_more_link); ?></span>
											</h6>
		                                    <p id="target">Target Date of Completion: <span><?php echo date('m/d/Y', strtotime($m['target'])); ?></span></p>
		                                    <p id="status" >Status: <span><?php echo $m['status']['label']; ?></span></p>
		                                    <?php if($canManage){ ?>
			                                    <div class="modification-links" >
				                                    <a href="javascript:;" class="edit-link" id="edit-ms-btn" data-row_id="<?php echo$i; ?>" title="Edit Milestones" >
														<img src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg">
													</a>
													<a href="javascript:;" id="remove-milestone" class="remove" data-row_id="<?php echo$i; ?>" data-goal_id="<?php echo $GoalID ?>" title="Delete Milestones" >
								                    	<img src="<?php echo get_template_directory_uri(); ?>/images/close-icon.svg">
								                    </a>
				                                </div>
                            				<?php } ?>
		                                </div>
                            		<?php $i++; }
                            	}else{ ?>
                            		<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Milestones!
										</div>
									</div>
                            	<?php } ?>
                            </div>
                        </div>
                        <div class="goald-item-card <?php //echo (isset($challenges) && count($challenges)<=1 && count($milestones)>1)?'swmc':''; ?>">
                            <div class="goald-item-header">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-challenges.svg" id="type-img" class="img-fluid header-labl">
                                <h5>Challenges</h5>
                                <?php if($canManage){ ?>
	                                <div class="gih-newbtn-group">
	                                    <label>New Challenge</label>
	                                    <a href="javascript:;" id="add-challenge-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/green-plus-icon.svg"></a>
	                                </div>
	                            <?php } ?>
                            </div>
                            <div class="goal-item-body listing-in-goal mCustomScrollbar gib-scroll" id="challenges" data-mcs-theme="dark">
                            	<?php if(!empty($challenges)){ $i=1;
	                            	foreach($challenges as $c){ $Title = $c['title']; ?>
		                                <div class="goal-item-child">
		                                    <?php if(strlen($Title) > 65){ ?>	
		                                    	<h6 id="title" class="full-text" style="display: none;">
		                                    		<span><?php echo $Title ?></span> 
		                                    		<a href="javascript:;" class="show-less-text" > <b>show less</b></a>
		                                    	</h6>
			                                <?php } ?>
	                                    	<h6 id="<?php echo strlen($Title) <= 65 ? 'title' : '' ; ?>" class="less-text"> 
	                                    		<span><?php echo get_limited_string($Title, 65, $show_more_link); ?></span>
											</h6>
		                                    <p id="target" >Target Date of Completion:  <span ><?php echo date('m/d/Y', strtotime($c['target'])); ?></span></p>
		                                    <p id="status" >Status: <span ><?php echo $c['status']['label']; ?></span></p>
		                                    <?php if($canManage){ ?>
				                                <div class="modification-links" >
				                                    <a href="javascript:;" class="edit-link" id="edit-challenge-btn" data-row_id="<?php echo$i; ?>" title="Edit Challenge" >
														<img src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg">
													</a>
													<a href="javascript:;" id="remove-challenge" class="remove" data-row_id="<?php echo$i; ?>" data-goal_id="<?php echo $GoalID ?>" title="Delete Challenge" >
								                    	<img src="<?php echo get_template_directory_uri(); ?>/images/close-icon.svg">
								                    </a>
				                                </div>
                            				<?php } ?>
			                            </div>
                            		<?php $i++; }    
	                            }else{ ?>
                            		<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Challenges!
										</div>
									</div>
                            	<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="newmc-form-sec" id="add-goal-data">
                        <div class="goald-item-header newmc-form-header">
                            <a href="javascript:;" id="back-mc-btn" class="back-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg"></a>
                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-milestones.svg" id="mc-type-img" class="img-fluid header-labl">
                            <h5 id="label" >Milestones</h5>
                        </div>
                        <div class="newmc-form-body">
                            <div class="gcard-form create-goal-form">
                                <form id="goal-mc-frm">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" id="title" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Target Date</label>
                                        <input type="date" placeholder="yyyy-mm-dd" name="target" id="target" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d',strtotime($target)); ?>" class="form-control">
                                        <small class="form-text-error"></small> 
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" id="status" class="form-control">
                                        	<option>Open</option>
                                        	<option>Completed</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="row_id" value="0" id="row_id">
                                    <input type="hidden" name="selector" value="milestones" id="selector">
                                    <input type="hidden" name="goal_id" value="<?php echo $GoalID ?>" id="goal_id">
                                    <button type="submit" class="btn btn-blue">Create</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="pov-sec" id="pov-listing">
                        <div class="goald-item-card">
                            <div class="goald-item-header">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/goal-pov.svg" class="img-fluid header-labl">
                                <h5>Points of View</h5>
                            </div>
                            <div class="goal-item-body gib-large mCustomScrollbar" data-mcs-theme="dark">
                            	<?php $POV = get_comments( [
                            		'post_id' => $GoalID,
                            		'parent'  => 0,
                            		'status'  => $canManage ? 'all' : 'approve'
                            	]); 
                            	if(!empty($POV)){
                            		foreach($POV as $pov){	
                            			$povID = $pov->comment_ID;
                            			$CommentContent = $pov->comment_content;
                            			$blockClass = $pov->comment_approved != 1 ? 'blockedPOV' : '';
                            			$SubPOV = get_comments( [
		                            		'post_id' => $GoalID,
		                            		'parent'  => $povID,
		                            	]); ?>
                            			<div class="goal-item-child pov-item-child ">
		                                    <div class="pov-gitem-header">
		                                        <a href="<?php echo get_author_posts_url($pov->user_id); ?>">
		                                        	<h6><?php echo $pov->comment_author; ?></h6>
		                                        </a>
		                                        <small><?php echo date('m/d/Y',strtotime($pov->comment_date)); ?></small>
		                                    </div>
		                                    <?php if(strlen($CommentContent) > 200){ ?>	
		                                    	<p id="title" class="full-text" style="display: none;">
		                                    		<?php echo $CommentContent ?>
		                                    		<a href="javascript:;" class="show-less-text" > <b> show less</b></a>
		                                    	</p>
			                                <?php } ?>	
		                                    <p class="less-text" >
											 	<?php echo get_limited_string($CommentContent, 200, $show_more_link); ?> 
											</p>
		                                    <div class="pov-gitem-footer pov-parent-rating  " >
		                                    	<?php //if($pov->user_id != $currentUserID && $canManage){
                                                if($pov->user_id == $currentUserID || $canManage){
		                                    		$rating = get_comment_meta($povID,'rating',true); ?>
			                                        <small>Rate this POV</small>
			                                        <ul class="list-inline <?php echo $blockClass; ?>" data-pov_id="<?php echo $povID; ?>"   >
                                                        <?php if($canManage){ ?>
    			                                            <li class="list-inline-item">
    			                                                <a href="javascript:;" id="unrate-pov" ><img src="<?php echo get_template_directory_uri(); ?>/images/block-icon.svg" class="img-fluid block-pov-img"></a>
    			                                            </li>
                                                        <?php } for($i=1;$i<5;$i++){ ?>
    			                                            <li class="list-inline-item">
    			                                                <label class="custom-radiog">
                                                                    <?php if($canManage){ ?>
        			                                                    <input type="radio" value="<?php echo $i; ?>" <?php echo $rating==$i?'checked':''; ?> name="rating-<?php echo $povID; ?>">
                                                                    <?php } ?>
    			                                                    <span class="checkmark">
                                                                        <img data-img_name="rating-<?php echo $povID; ?>" data-value="<?php echo $i; ?>" src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $i<=$rating?'bulb-on.svg':'bulb-off.svg' ;  ?>" class="img-fluid">
                                                                    </span>
    			                                                </label>
    			                                            </li>
                                                        <?php } ?>
			                                        </ul>
				                                <?php } ?>
		                                        <?php if($blockClass){ ?>
                                                    <a href="javascript:;" id="unblock-pov" data-pov_id="<?php echo $povID; ?>" class="btn btn-blue pov-respond-btn">Unblock POV</a>
                                                <?php }else if($pov->user_id != $currentUserID && $canManage ){ ?>
			                                        <a href="javascript:;" id="pov-respond-btn"  data-pov_id="<?php echo $povID; ?>" class="btn btn-blue pov-respond-btn">Respond</a>
			                                    <?php } ?>
		                                    </div>
		                                    <?php if(!empty($SubPOV)){
			                            		foreach($SubPOV as $spov){ 
			                            			$SpovID = $spov->comment_ID;
			                            			$SCommentContent = $spov->comment_content ?>
				                                    <hr> <div class="goal-item-child pov-item-child pov-sub-child">
													    <div class="pov-gitem-header">
													        <h6><?php echo $spov->comment_author; ?></h6>
													        <small><?php echo date('m/d/Y',strtotime($spov->comment_date)); ?></small>
													    </div>
													    <?php if(strlen($SCommentContent) > 190){ ?>	
					                                    	<p id="title" class="full-text" style="display: none;">
					                                    		<?php echo $SCommentContent ?>
					                                    		<a href="javascript:;" class="show-less-text" > <b> show less</b></a>
					                                    	</p>
						                                <?php } ?>	
					                                    <p class="less-text" >
														 	<?php echo get_limited_string($SCommentContent, 190, $show_more_link); ?> 
														</p>
													    <?php /* if($spov->user_id != $currentUserID && $canManage){ 
													    	$rating = get_comment_meta($SpovID,'rating',true); ?>
													    	<div class="pov-gitem-footer">
						                                        <small>Rate this POV</small>
						                                        <ul class="list-inline" data-pov_id="<?php echo $SpovID; ?>">
						                                            <li class="list-inline-item">
						                                                <a href="javascript:;"  id="unrate-pov" ><img src="<?php echo get_template_directory_uri(); ?>/images/block-icon.svg" class="img-fluid block-pov-img"></a>
						                                            </li>
						                                            <li class="list-inline-item">
						                                                <label class="custom-radiog">1
						                                                    <input type="radio" value="1" <?php echo $rating==1?'checked':''; ?> name="rating-<?php echo $SpovID; ?>">
						                                                    <span class="checkmark"></span>
						                                                </label>
						                                            </li>
						                                            <li class="list-inline-item">
						                                                <label class="custom-radiog">2
						                                                    <input type="radio" value="2" <?php echo $rating==2?'checked':''; ?> name="rating-<?php echo $SpovID; ?>">
						                                                    <span class="checkmark"></span>
						                                                </label>
						                                            </li>
						                                            <li class="list-inline-item">
						                                                <label class="custom-radiog">3
						                                                    <input type="radio" value="3" <?php echo $rating==3?'checked':''; ?> name="rating-<?php echo $SpovID; ?>">
						                                                    <span class="checkmark"></span>
						                                                </label>
						                                            </li>
						                                            <li class="list-inline-item">
						                                                <label class="custom-radiog">4
						                                                    <input type="radio" value="4" <?php echo $rating==4?'checked':''; ?> name="rating-<?php echo $SpovID; ?>">
						                                                    <span class="checkmark"></span>
						                                                </label>
						                                            </li>
						                                        </ul>
						                                    </div>
						                                <?php } */ ?>
													</div>
												<?php }
											} ?>
		                                </div>
                            		<?php }
                            	}else{ ?>
                            		<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Points of View!
										</div>
									</div>
                            	<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="newmc-form-sec" id="pov-main-ctn" >
                    	<div class="row justify-content-md-center">
                    		<div class="col-lg-10 ">
		                        <div class="goald-item-header newmc-form-header">
		                            <a href="javascript:;" id="back-pov-btn" class="back-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg"></a>
		                            <img src="<?php echo get_template_directory_uri(); ?>/images/goal-pov.svg"  class="img-fluid header-labl">
		                            <h5 id="label" >Points of View</h5>
		                        </div>
		                        <div class="newmc-form-body">
		                            <div class=" create-goal-form">
		                                <form id="goal-pov-frm">
		                                    <div class="form-group">
		                                        <label>Description</label>
		                                        <textarea name="description" id="description" rows="6" class="form-control"></textarea>
		                                    </div>
		                                    <input type="hidden" name="goal_id" value="<?php echo $GoalID ?>" id="goal_id">
		                                    <input type="hidden" name="pov_parent_id" value="0" id="pov_parent_id">
		                                    <button type="submit" class="btn pov-submit btn-blue">Submit</button>
		                                </form>
		                            </div>
		                        </div>
		                    </div>
		                </div>
                    </div>
                    <div class="newmc-form-sec" id="attachments-ctn" >
                    	<div class="row justify-content-md-center">
                    		<div class="col-lg-10 ">
		                        <div class="goald-item-header newmc-form-header">
		                            <a href="javascript:;" id="back-pov-btn" class="back-btn"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg"></a>
		                            <img src="<?php echo get_template_directory_uri(); ?>/images/attachment-icon.svg"  class="img-fluid header-labl">
		                            <h5 id="label" >Attachments</h5>
		                        </div>
                				<?php if($canManage){  ?>
		                        <div class="newmc-form-body">
		                            <div class=" create-goal-form">
		                                <form id="goal-attachments-frm">
		                                	<div class="upload-btn-wrapper">
							                    <button class="btn btn-blue">Upload</button>
							                    <input type="file" name="attachment" id="attachment" accept=".mp4,.mov,.jpeg,.jpg, .png ,.svg , .doc,.docx,.pdf,.pages,.xls,.xlsx "  />
                                                <label></label>
                                                <p>Supported formats are mp4, mov, jpeg, png, svg, doc, docx, pdf, pages, xls & xlsx.</p>
						                    </div>
		                                    <div class="form-group">
		                                        <label>Description</label>
		                                        <textarea name="description" id="description" rows="6" class="form-control"></textarea>
		                                    </div>
		                                    <input type="hidden" name="goal_id" value="<?php echo $GoalID ?>" id="goal_id">
		                                    <button type="submit" class="btn pov-submit btn-blue">Submit</button>
		                                </form>
		                            </div>
		                        </div>
		                		<?php } ?>
		                    </div>
		                    <div class="col-12 col-lg-10 mt-4 mCustomScrollbar gib-scroll">
		                    	<?php if(!empty($attachments)){ ?>
                                    <div class="table-responsive">
    							        <table class="table admin-ovtable" id="attch-listing">
    							            <thead>
    							                <tr>
    							                    <th><b>Attachment</b></th>
    							                    <th><b>Desciption</b></th>
    								                <th><?php if($canManage){ ?>
    								                   	<b>Remove</b>
    								                <?php } ?></th>
    							                </tr>
    							            </thead>
    							            <tbody>	
    						            		<?php $i=1; foreach($attachments as $a){ ?>
    								                <tr class="attachR">
    								                    <td><a href="<?php echo$a['file']['url']; ?>"data-toggle="modal"data-target="#attachmentsPreview" id="preview-attachment-btn">
    								                    	<?php echo $a['file']['filename']; ?>		
    								                    </a></td>
    								                    <td><?php echo htmlspecialchars($a['description']); ?></td>
    									                <td><?php if($canManage){ ?>
    									                    <a href="javascript:;" id="remove-attachment" data-row_id="<?php echo$i; ?>">
    									                    	<img src="<?php echo get_template_directory_uri(); ?>/images/close-icon.svg">
    									                    </a>
    									                <?php } ?></td>
    								                </tr>
    								            <?php $i++; } ?>
    							            </tbody>
    							        </table>
                                    </div>
								<?php }else{ ?>
									<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Attachments Added!
										</div>
									</div>
								<?php } ?>
						    </div>
		                </div>
                    </div>
                </div>
            </div>
            <div class="row" id="goal-followers-ctn" style="display: none;" >
                <div class="col-12 col-lg-12">
                    <div class="pov-sec">
                        <div class="goald-item-card">
                            <div class="goald-item-header">
                                <a href="javascript:;" id="back-goal"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg" class="img-fluid back-button"></a>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid header-labl">
                                <h5>Goal Followers</h5>
                            </div>
                            <div class="goal-item-body gib-large mCustomScrollbar" data-mcs-theme="dark">
                                <div class="row">
                            	<?php //if(!empty($followers)){
                                    array_push($allFollowers, $goalAuthorID);
				                	$FUsers = New WP_User_Query([
				                		'include' => $allFollowers,
				                	]); 
				                	if(!empty($FUsers->get_results())){
				                	$hfn = floor(count($FUsers->get_results()) / 3);
			                		$hfn = $hfn > 1 ? $hfn  : 1;
			                		$FUserss = array_chunk($FUsers->get_results(), $hfn);
			                			foreach($FUserss as $fUsers){
							                echo '<div class="col-12 col-lg-4">';
						                		foreach($fUsers as $fuser){
							                		set_query_var( 'userData', $fuser); ?> 
		                                            <div class="connection-invitation-item">
		                                               <?php get_template_part('template-parts/user','listing-content'); 
                                                       if($fuser->ID == $goalAuthorID){ ?> 
                                                        <a href="javascript:;" class="btn">
                                                            <label>Creator</label>
                                                        </a><?php } ?>
		                                            </div>
			                                	<?php }
			                                echo '</div>';
		                            	}
		                            }
                           		/* }else{ ?>
                           			<div class="col-12 text-center ">
						            	<div class="alert alert-warning">
											No Followers!
										</div>
									</div>
                           		<?php } */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade " id="attachmentsPreview" tabindex="-1" role="dialog" aria-labelledby="attachments_preview" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header tex">
                <h5 class="modal-title" id="attachments_preview">Attachments Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  ">
                <div class="container">
                    <div class="row justify-content-md-center">
                        <div class="col-12 text-center">
                            <!-- <iframe class="doc" src="https://docs.google.com/gview?url=http://writing.engr.psu.edu/workbooks/formal_report_template.doc&embedded=true"></iframe> -->
                            <!-- <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://goalore01.blob.core.windows.net/goalore-wp/2019/11/user2.jpg" style="width:100%; height: 100%" frameborder="0"> </iframe> -->
                            <img id="img-preview" src="">
                            <video controls >
                                <source src="" id="video-preview" type="">
                            </video>
                            <a href="javascript:;" class="btn btn-primary btn-lg m-5 " target="_blank" id="attachment-download" download>
                                <i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="updateGoal" tabindex="-1" role="dialog" aria-labelledby="createGoalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header tex">
                <h5 class="modal-title" id="createGoalLabel">Update Goal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  ">
                <div class="container">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-10 col-12">
                            <div class="newmc-form-body">
                                <div class="gcard-form create-goal-form">
                                    <form id="create-goal-frm" method="post">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" id="title" value="<?php echo $GoalTitle; ?>" class="form-control" >
                                        </div>
                                        <div class="form-group ">
                                            <label>Target Date of Completion</label>
                                            <!-- <input type="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d',strtotime($target)); ?>"  name="target" id="target" class="form-control"> -->
                                            <input type="date" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d',strtotime($target)); ?>"  name="target" id="target" class="form-control">
                                        </div>
                                        <div class="form-group">
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
                                                        foreach($terms as $term){ $term_id = $term->term_id; ?>
                                                            <option <?php echo $cat_parent_id == $term_id ? 'selected' : ''; ?> value="<?php echo $term_id; ?>">
                                                                <?php echo $term->name ?>
                                                            </option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Goal Subcategory</label>
                                            <select class="form-control" name="subcategory" id="sub-category" >
                                                <?php $terms = get_terms([
                                                      'taxonomy'   => 'goal_categories',
                                                      'hide_empty' => false,
                                                      'parent'   => $cat_parent_id,
                                                      'meta_key' => 'active',
                                                      'meta_value' => '1'
                                                    ]); if(!empty($terms)){
                                                        foreach($terms as $term){ $term_id = $term->term_id; ?>
                                                            <option <?php echo $cat_sub_id == $term_id ? 'selected' : ''; ?> value="<?php echo $term_id; ?>">
                                                                <?php echo $term->name ?>
                                                            </option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <?php if($Privacystatus != 'public') { ?>
                                            <div class="form-group">
                                                <label>Privacy Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option <?php echo $Privacystatus == 'private' ? 'selected' : ''; ?> value="private">Private</option>
                                                    <option <?php echo $Privacystatus == 'public' ? 'selected' : ''; ?> value="public">Public</option>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <input type="hidden" name="goal_id" value="<?php echo $GoalID; ?>">
                                            <button type="submit" class="btn btn-blue">Update Goal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
	
<?php get_footer(); ?>