<?php
    global $wpdb;
    $table_name = $wpdb->prefix . "gdp";
    $AllianceID = get_the_ID();
    $AAID = get_post_field( 'post_author', $AllianceID );
    $goalCount = 0;
    $goals = !isset($goals) ? get_post_meta($AllianceID,'goals',true) : $goals;
    if(empty($goals)){
        $goals = [];
        $goalsCount = 0;
    }else $goalsCount = count($goals);

    $actionCount = get_comments_number();

    $GDP = 0;
    $members = !isset($members) ? get_post_meta($AllianceID,'members',true) : $members;
    if(!empty($members)){
        array_push($members, $AAID);
        $members = implode("' ,'", $members);
        $GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id IN ('".$members."')"); 
        if(empty($GDP)) $GDP = 0; 
    }else {
        $GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $AAID"); 
        if(empty($GDP)) $GDP = 0; 
    }

    $alliance = get_post();

?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="goalorew-card goal-card-item">
        <div class="goal-item-header">
            <a href="<?php echo $alliance->guid; ?>"> <h5><?php the_title(); ?></h5></a>
        </div>
        <div class="goal-item-content">
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-goal.svg" class="img-fluid">
                    <p>Goals</p>
                </div>
                <h6><?php echo$goalsCount; ?></h6>
            </div>
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-actions.svg" class="img-fluid">
                    <p>Actions</p>
                </div>
                <h6><?php echo$actionCount; ?></h6>
            </div>
            <div class="goal-item goal-itemlg">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-gdp.svg" class="img-fluid">
                    <p>Total Good Deed Points</p>
                </div>
                <h6><?php echo$GDP; ?></h6>
            </div>
        </div>
    </div>
</div>