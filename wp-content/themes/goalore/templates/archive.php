<?php 
  /**
    *
    *Template Name: Archive
    *
    */

get_header(); 

$userID = get_current_user_id(); 

$myarchivedgoals = New WP_Query([
    'post_type'       => 'goals',
    'author'          => $userID, 
    'posts_per_page'  => -1,
    'meta_query'      => [
        'goal_status' => [ 'key' => 'goal_status' ],
        'target'      => [ 'key' => 'target' ],    
        [
            'key'     => 'archive',
            'value'   => '1',
            'compare' => '='
        ]
    ], 
    'orderby'         => [
        'goal_status' => 'DESC',
        'target'      => 'ASC',
    ]
]); $CountMyArchivedGoals = $myarchivedgoals->found_posts;

$myAdminAlliances = New WP_Query([
    'post_type'       => 'alliances',
    'posts_per_page'  => -1,
    'author'          => $userID, 
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

$myarchivealliance = New WP_Query([
    'post__in'       => $myAdminAlliancesIDs,
    'post_type'      => 'alliances',
    'author'         => $userID, 
    'posts_per_page' => -1,
    'meta_query'     => [
        'status' => [ 'key' => 'status' ],    
        [
            'key'     => 'archive',
            'compare' => 'EXISTS',
        ],[
            'key'     => 'archive',
            'value'   => '1',
            'compare' => '='
        ]
    ], 'orderby' => [ 'status' => 'DESC' ]
]); $CountMyArchivedAlliance = $myarchivealliance->found_posts;

$active_archive_goal = '';
$active_archive_alliance = '';
if(isset($_REQUEST['alliance'])){
    $active_archive_alliance = 'show active';
}else{
    $active_archive_goal = 'show active';
}

?>
<section class="goal-desktop-sec">
    <div class="container">
        <div class="row align-items-center goal-header">
            <div class="col-12 col-md-6 col-lg-4">
                <!-- <div class="create-goal">
                    <a href="javascript:;"  data-toggle="modal" data-target="#createGoal" class="btn btn-blue">Create New Goal</a>
                </div> -->
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <ul class="nav goal-catg" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link btn <?php echo $active_archive_goal; ?>" id="archivedgoal-tab" data-toggle="tab" href="#archivedgoal" role="tab" aria-controls="archivedgoal" aria-selected="false">
                            Archived Goals (<?php echo$CountMyArchivedGoals ?>)
                        </a>
                    </li>
                    <li class="nav-item alliances-tab-sec">
                        <a class="nav-link btn <?php echo $active_archive_alliance; ?> " id="archivedalliance-tab" data-toggle="tab" href="#archivedalliance" role="tab" aria-controls="archivedalliance" aria-selected="false">Archived Alliances (<?php echo$CountMyArchivedAlliance ?>)</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade <?php echo $active_archive_goal; ?>" id="archivedgoal" role="tabpanel" aria-labelledby="archivedgoal-tab">
                <div class="row">
                    <?php  
                  if($myarchivedgoals->have_posts()){
                    while($myarchivedgoals->have_posts()) { 
                      $myarchivedgoals->the_post();
                      get_template_part('template-parts/goal','content'); 
                    } wp_reset_query();
                  }else{ ?>
                    <div class="col-12 text-center ">
                        <div class="alert alert-warning">
                            No archived goals!
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane fade alliances-desktop-sec <?php echo $active_archive_alliance; ?>" id="archivedalliance" role="tabpanel" aria-labelledby="archivedalliance-tab">
                <div class="row">
                    <?php if($myarchivealliance->have_posts()) {
                        while($myarchivealliance->have_posts()) { 
                            $myarchivealliance->the_post(); 
                            get_template_part('template-parts/alliance','content'); 
                        } wp_reset_query();
                    }else{ ?>
                        <div class="col-12 text-center ">
                            <div class="alert alert-warning">
                                No archived alliances!
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>