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
    $privacy_status = get_field('privacy_status');
    $status = get_field('status');
    $guid = get_the_guid();
    
    $members = !isset($members) ? get_post_meta($AllianceID,'members',true) : $members;
    if(!empty($members)){
        array_push($members, $AAID);
        $AllGDP = get_gdp($members);
    }else $AllGDP = get_gdp($AAID);

?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="goalorew-card goal-card-item">
        <div class="goal-item-header">
            <?php $title = get_the_title();
                $subtitle = get_limited_string($title,75);  ?>
            <a href="<?php echo $guid; ?>" title="<?php echo$title; ?>" > 
                <h5><?php echo $subtitle; ?>
                    <?php if($privacy_status == "public" ){ ?>
                        <img title="Public" src="<?php echo get_template_directory_uri(); ?>/images/unlock.svg" class="img-p" >
                    <?php }else{ ?>
                        <img title="Private" src="<?php echo get_template_directory_uri(); ?>/images/lock.svg" class="img-p" >
                    <?php } ?>
                </h5>
            </a>
        </div>
        <div class="goal-item-content">
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-goal.png" class="img-fluid">
                    <p>Goals</p>
                </div>
                <h6><?php echo$goalsCount; ?></h6>
            </div>
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-actions.png" class="img-fluid">
                    <p>Actions</p>
                </div>
                <h6><?php echo$actionCount; ?></h6>
            </div>
            <div class="goal-item goal-itemlg">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/alliance-gdp.svg" class="img-fluid">
                    <p>Total Good Deed Points</p>
                </div>
                <h6><?php echo$AllGDP; ?></h6>
            </div>
            <div class="goal-status">
                <?php if($status == 'complete'){ ?>
                    <i title="Completed" class="fa fa-check" aria-hidden="true"></i>
                <?php }else{ ?>
                    <i title="Open" class="fa fa-minus" aria-hidden="true"></i>
                <?php } ?>
            </div>
        </div>
    </div>
</div>