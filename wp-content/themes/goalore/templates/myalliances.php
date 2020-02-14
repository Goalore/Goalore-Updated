<?php 
	/**
	  *
	  *Template Name: My Alliances
	  *
	  */

get_header(); 

$userID = get_current_user_id(); 


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
        ],
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
        ]
    ], 'orderby' => [ 'status' => 'DESC' ]
]); $CountMyAlliance = $myalliance->found_posts;

// filter after query is firied
remove_filter('posts_where','inlcude_post_id_with_OR_condidtion', 10, 2 );

$folloedAlliances = New WP_Query([
    'post__not_in'    => $myAdminAlliancesIDs,
    'post_type'       => 'alliances',
    'posts_per_page'  => -1,
    'meta_key'        => 'status',
    'orderby'         => 'meta_value',
    'order'           => 'DESC',
    'meta_query'      => [
        [
            'key'     => 'members',
            'value'   => '"' . $userID . '"',
            'compare' => 'LIKE',
        ],
    ],

]); $CountFolloedAlliance = $folloedAlliances->found_posts;


$active_alliance_admin = '';
$active_alliance_member = '';
if(isset($_REQUEST['member'])){
    $active_alliance_member = 'show active';
}else{
    $active_alliance_admin = 'show active';
}

?>

<section class="goal-desktop-sec alliances-desktop-sec">
    <div class="container">
        <div class="row align-items-center goal-header">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="create-goal">
                    <a href="javascript:;" data-toggle="modal" data-target="#exampleModal" class="btn btn-blue">Create New Alliance</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <ul class="nav goal-catg" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link btn <?php echo $active_alliance_admin; ?>" id="adminalliance-tab" data-toggle="tab" href="#adminalliance" role="tab" aria-controls="adminalliance" aria-selected="true">Admin (<?php echo$CountMyAlliance ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn <?php echo $active_alliance_member; ?> " id="memberalliance-tab" data-toggle="tab" href="#memberalliance" role="tab" aria-controls="memberalliance" aria-selected="false">Member (<?php echo$CountFolloedAlliance ?>)</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade <?php echo $active_alliance_admin; ?>" id="adminalliance" role="tabpanel" aria-labelledby="adminalliance-tab">
                <div class="row">
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
            <div class="tab-pane fade <?php echo $active_alliance_member; ?> " id="memberalliance" role="tabpanel" aria-labelledby="memberalliance-tab">
                <div class="row">
                    <?php if($folloedAlliances->have_posts()) {
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
                                    // $RName  = get_user_meta($RequesterID,'full_name',true);
                                    // if(empty($RName)){
                                        $Requester = get_userdata($RequesterID);
                                        $RName = $Requester->user_login;
                                    // }
                                    $ALink  = get_permalink($AllianceID);
                                    $RLink  = get_author_posts_url($RequesterID); ?>
                                    <tr>
                                        <td><a href="<?php echo $ALink; ?>"><?php echo $ATitle; ?></a></td>
                                        <td ><a href="<?php echo $RLink; ?>"><?php echo $RName; ?></a></td>
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
                <h5 class="modal-title" id="exampleModalLabel">Create A New Alliance</h5>
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
                                            <label>Alliance Status</label>
                                            <select class="form-control" name="status" id="status"  >
                                                <option >Open</option>
                                                <option >Complete</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Privacy Status</label>
                                            <select class="form-control" name="privacy_status" id="privacy_status">
                                                <option value="private">Keep it private for now</option>
                                                <option value="public">Make this public to everyone</option>
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