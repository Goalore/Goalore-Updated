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

$followers = !isset($followers) ? get_post_meta($GoalID,'followers',true) : $followers;

if(empty($followers)){
	$followers = [];
	$followersCount = 0;
}else $followersCount = count($followers);

$isFollower = false;
$isAdmin = false;
if(in_array($currentUserID, $followers)) {
	$isFollower = true;
}
if($goalAuthorID == $currentUserID) {
	$isAdmin = true;
}

$GoalStatus = get_post_meta($GoalID,'goal_status',true);
$canManage = false;
$canManageSub = false;
if($GoalStatus != 'complete' && $isAdmin == true){
	$canManage = true;
}
if($GoalStatus != 'complete' && $isFollower == true){
	$canManageSub = true;
}

if(isset($attachments)){
	if(!empty($attachments)){
		$attachmentsCount = count($attachments);
	}else $attachmentsCount = 0;
}else $attachmentsCount = 0;


?>

<section class="goal-detail-sec">
    <div class="container">
    	<?php if($GoalStatus == 'complete'){ ?>
	    	<div class="row">
		    	<div class="col-12 text-center ">
		            <div class="alert alert-success">
		                This goals is marked completed!
		            </div>
		        </div>
	        </div>
	    <?php } ?>
        <div class="goalorew-card goal-detail-card">
            <div class="row">
                <div class="col">
                    <div class="goal-detail-header">
                        <h4><?php the_title(); ?></h4>
                        <?php if(!$isAdmin && $GoalStatus != 'complete') { ?>
                        	<?php if($isFollower) { ?>
		                        <button id="unfollow-goal" data-goal_id="<?php echo$GoalID; ?>" class="btn border border-primary mr-3 ml-2">Unfollow</button>
		                    <?php }else{ ?>
		                        <button id="follow-goal" data-goal_id="<?php echo$GoalID; ?>" class="btn btn-blue  mr-3 ml-2">Follow</button>
		                    <?php } ?>
	                    <?php } ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/images/people-goal.svg" class="img-fluid">
                        <h5><?php echo $followersCount; ?></h5>
                        <div class="gdh-btn-group">
                        	<?php if($isAdmin){ ?>
                            	<a href="javascript:;" id="update-goal-status" data-goal_id="<?php echo $GoalID ?>" class="btn btn-green">
                            		<?php echo$GoalStatus == 'complete'? 'Incomplete' : 'Complete';  ?> Goal</a>
                        	<?php } 	
                        	if($canManageSub){  ?>
	                            <a href="javascript:;" id="add-pov-btn" class="btn btn-purple">Add POV</a>
	                        <?php } ?>
                            <a href="javascript:;" id="add-attachments-btn" class="btn btn-blue">Attachments (<?php echo $attachmentsCount; ?>)</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="milestone-challenges-sec" id="mc-listing">
                        <div class="goald-item-card">
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
                            		foreach($milestones as $m){ ?>
		                                <div class="goal-item-child">
		                                    <h6 id="title"><?php echo $m['title']; ?></h6>
		                                    <p id="target">Target Date of Completion: <span><?php echo $m['target']; ?></span></p>
		                                    <p id="status" >Status: <span><?php echo $m['status']['label']; ?></span></p>
		                                    <?php if($canManage){ ?>
			                                    <div class="modification-links" >
				                                    <a href="#" class="edit-link" id="edit-ms-btn" data-row_id="<?php echo$i; ?>" title="Edit Milestones" >
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
                        <div class="goald-item-card">
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
	                            	foreach($challenges as $c){ ?>
		                                <div class="goal-item-child">
		                                    <h6 id="title" ><?php echo $c['title']; ?></h6>
		                                    <p id="target" >Target Date of Completion:  <span ><?php echo $c['target']; ?></span></p>
		                                    <p id="status" >Status: <span ><?php echo $c['status']['label']; ?></span></p>
		                                    <?php if($canManage){ ?>
				                                <div class="modification-links" >
				                                    <a href="#" class="edit-link" id="edit-challenge-btn" data-row_id="<?php echo$i; ?>" title="Edit Challenge" >
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
                                        <input type="date" name="target" id="target" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d',strtotime($target)); ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Milestone Status</label>
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
                            		'parent'     => 0,
                            	]); 
                            	if(!empty($POV)){
                            		foreach($POV as $pov){ 
                            			$povID = $pov->comment_ID;
                            			$SubPOV = get_comments( [
		                            		'post_id' => $GoalID,
		                            		'parent'  => $povID,
		                            	]); ?>
                            			<div class="goal-item-child pov-item-child">
		                                    <div class="pov-gitem-header">
		                                        <a href="<?php echo get_author_posts_url($pov->user_id); ?>">
		                                        	<h6><?php echo $pov->comment_author; ?></h6>
		                                        </a>
		                                        <small><?php echo date('d/m/Y',strtotime($pov->comment_date)); ?></small>
		                                    </div>
		                                    <p><?php echo $pov->comment_content; ?></p>
		                                    <div class="pov-gitem-footer">
		                                    	<?php if($pov->user_id != $currentUserID && $canManage){
		                                    		$rating = get_comment_meta($povID,'rating',true); ?>
			                                        <small>Rate this POV</small>
			                                        <ul class="list-inline" data-pov_id="<?php echo $povID; ?>"  >
			                                            <li class="list-inline-item">
			                                                <a href="javascript:;" id="unrate-pov" ><img src="<?php echo get_template_directory_uri(); ?>/images/block-icon.svg" class="img-fluid block-pov-img"></a>
			                                            </li>
			                                            <li class="list-inline-item">
			                                                <label class="custom-radiog">1
			                                                    <input type="radio" value="1" <?php echo $rating==1?'checked':''; ?> name="rating-<?php echo $povID; ?>">
			                                                    <span class="checkmark"></span>
			                                                </label>
			                                            </li>
			                                            <li class="list-inline-item">
			                                                <label class="custom-radiog">2
			                                                    <input type="radio" value="2" <?php echo $rating==2?'checked':''; ?> name="rating-<?php echo $povID; ?>">
			                                                    <span class="checkmark"></span>
			                                                </label>
			                                            </li>
			                                            <li class="list-inline-item">
			                                                <label class="custom-radiog">3
			                                                    <input type="radio" value="3" <?php echo $rating==3?'checked':''; ?> name="rating-<?php echo $povID; ?>">
			                                                    <span class="checkmark"></span>
			                                                </label>
			                                            </li>
			                                            <li class="list-inline-item">
			                                                <label class="custom-radiog">4
			                                                    <input type="radio" value="4" <?php echo $rating==4?'checked':''; ?> name="rating-<?php echo $povID; ?>">
			                                                    <span class="checkmark"></span>
			                                                </label>
			                                            </li>
			                                        </ul>
				                                <?php } ?>
		                                        <?php if($pov->user_id != $currentUserID && $canManage ){ ?>
			                                        <a href="javascript:;" id="pov-respond-btn"  data-pov_id="<?php echo $povID; ?>" class="btn btn-blue pov-respond-btn">Respond</a>
			                                    <?php } ?>
		                                    </div>
		                                    <?php if(!empty($SubPOV)){
			                            		foreach($SubPOV as $spov){ 
			                            			$SpovID = $spov->comment_ID; ?>
				                                    <hr> <div class="goal-item-child pov-item-child pov-sub-child">
													    <div class="pov-gitem-header">
													        <h6><?php echo $spov->comment_author; ?></h6>
													        <small><?php echo date('d/m/Y',strtotime($spov->comment_date)); ?></small>
													    </div>
													    <p><?php echo $spov->comment_content; ?></p>
													    <?php if($spov->user_id != $currentUserID && $canManage){ 
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
						                                <?php } ?>
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
							                    <input type="file" name="attachment" id="attachment" />
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
								                    <!-- <td><a href="<?php echo $a['file']['url']; ?>" data-target="#attachmentsPreview" download target="_blank" > -->
								                    <td><a href="<?php echo $a['file']['url']; ?>" data-toggle="modal" data-target="#attachmentsPreview" id="preview-attachment-btn" >
								                    	<?php echo $a['file']['filename']; ?>		
								                    </a></td>
								                    <td><?php echo $a['description']; ?></td>
									                <td><?php if($canManage){ ?>
									                    <a href="javascript:;" id="remove-attachment" data-row_id="<?php echo$i; ?>">
									                    	<img src="<?php echo get_template_directory_uri(); ?>/images/close-icon.svg">
									                    </a>
									                <?php } ?></td>
								                </tr>
								            <?php $i++; } ?>
							            </tbody>
							        </table>
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
                        <div class="col-lg-10 col-12">
                            <!-- <iframe class="doc" src="https://docs.google.com/gview?url=http://writing.engr.psu.edu/workbooks/formal_report_template.doc&embedded=true"></iframe> -->
                            <!-- <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://goalore01.blob.core.windows.net/goalore-wp/2019/11/user2.jpg" style="width:100%; height: 100%" frameborder="0"> </iframe> -->
                            <img id="img-preview" src="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
	
<?php get_footer(); ?>