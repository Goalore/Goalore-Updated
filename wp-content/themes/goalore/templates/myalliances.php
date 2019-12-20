<?php 
	/**
	  *
	  *Template Name: My Alliances
	  *
	  */

get_header(); 

$userID = get_current_user_id(); ?>

<section class="goal-desktop-sec alliances-desktop-sec">
    <div class="container">
        <div class="row align-items-center goal-header">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="create-goal">
                    <a href="javascript:;" data-toggle="modal" data-target="#exampleModal" class="btn btn-blue">Create new Alliance</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <ul class="nav goal-catg" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link btn active" id="adming-tab" data-toggle="tab" href="#adming" role="tab" aria-controls="adming" aria-selected="true">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn" id="memberg-tab" data-toggle="tab" href="#memberg" role="tab" aria-controls="memberg" aria-selected="false">Member</a>
                    </li>
                </ul>
                <!-- <div class="goal-catg">
				<a href="javascript:;" class="btn active">Admin</a>
				<a href="javascript:;" class="btn">Member</a>
				</div> -->
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="adming" role="tabpanel" aria-labelledby="adming-tab">
                <div class="row">
                    <?php $myalliance = New WP_Query([
                    'post_type' => 'alliances',
                    'author'    => $userID, 
                    ]); if($myalliance->have_posts()) {
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
            <div class="tab-pane fade" id="memberg" role="tabpanel" aria-labelledby="memberg-tab">
                <div class="row">
                    <?php $folloedAlliances = New WP_Query([
                        'post_type' => 'alliances',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                          array(
                            'key'     => 'members',
                            'value'   => '"' . $userID . '"',
                            // 'value'   => $userID,
                            'compare' => 'LIKE',
                          ),
                        ),
                    ]);  
                    if($folloedAlliances->have_posts()) {
                        while($folloedAlliances->have_posts()) { 
                            $folloedAlliances->the_post(); 
                            get_template_part('template-parts/alliance','content'); 
                        } wp_reset_query();
                    }else{ ?>
                        <div class="col-12 text-center ">
                            <div class="alert alert-warning">
                                You are currently not part of any alliance.
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
</section>
<section class="allience-invitation-sec">
    <div class="container">
        <div class="row">
            <div class="col">
                <h5>Pending Alliance Invitations</h5>
                <div class="allience-req-table">
                    <?php $alliance_invitation = get_user_meta($userID,'alliance_invitation',true); 
                    if(!empty($alliance_invitation)){ ?>
                        <table class="table">
                            <tbody>
                                <?php foreach($alliance_invitation as $AllianceID => $RequesterID){
                                    $ATitle = get_the_title($AllianceID);
                                    $RName  = get_user_meta($RequesterID,'full_name',true);
                                    if(empty($RName)){
                                        $Requester = get_userdata($RequesterID);
                                        $RName = $Requester->user_login;
                                    }
                                    $ALink  = get_permalink($AllianceID);
                                    $RLink  = get_author_posts_url($RequesterID); ?>
                                    <tr>
                                        <td><a href="<?php echo $ALink; ?>"><?php echo $ATitle; ?></a></td>
                                        <td class="hide-mob"><a href="<?php echo $RLink; ?>"><?php echo $RName; ?></a></td>
                                        <td class="hide-mob"><a href="<?php echo $ALink; ?>" class="btn btn-more">More details</a></td>
                                        <td>
                                            <div class="allience-req-opt">
                                                <a href="javascript:;" id="accept-ai" data-alliance_id="<?php echo $AllianceID; ?>" class="btn btn-blue">Accept</a>
                                                <a href="javascript:;" id="reject-ai" data-alliance_id="<?php echo $AllianceID; ?>" class="btn btn-white">Reject</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php }else{ ?>
                        <div class="col-12 text-center ">
                            <div class="alert alert-warning">
                                No Pending Alliance Invitations!
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal -->
<div class="modal fade " id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header tex">
                <h5 class="modal-title" id="exampleModalLabel">Create A new Alliance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  ">
            	<div class="container">
				  	<div class="row justify-content-md-center">
				    	<div class="col-lg-8 col-12">
		                	<div class="newmc-form-body">
								<div class="gcard-form create-goal-form">
									<form id="create-alliances-frm" class="alliances-frm">
										<div class="form-group">
											<label>Title</label>
											<input type="text" name="title" id="title" class="form-control">
										</div>
                                        <div class="form-group">
                                            <label>Objective</label>
                                            <textarea name="objective" id="objective" rows="4" class="form-control"></textarea>
                                        </div>
										<div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status" id="status"  >
                                              <option >Open</option>
                                              <option >Complete</option>
                                            </select>
                                          </div>
										<button type="submit" class="btn btn-blue" >Create Alliance</button>
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