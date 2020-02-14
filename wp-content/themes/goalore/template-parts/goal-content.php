<?php 
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

$target = get_field('target');
$Privacystatus = get_field('status');
$goal_status = get_field('goal_status');

$goal = get_post(get_the_ID());

?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="goalorew-card goal-card-item">
        <div class="goal-item-header">
            <?php $title = get_the_title();
                $subtitle = get_limited_string($title,80);  ?>
            <a href="<?php echo $goal->guid; ?>" title="<?php echo$title; ?>" >
                <h5><?php echo $subtitle; ?></h5>
                <p>Target Date: <?php echo date('m/d/Y', strtotime($target)); ?> 
                    <?php if($Privacystatus == "public" ){ ?>
                        <img title="Public" src="<?php echo get_template_directory_uri(); ?>/images/unlock.svg" class="img-p" >
                    <?php }else{ ?>
                        <img title="Private" src="<?php echo get_template_directory_uri(); ?>/images/lock.svg" class="img-p" >
                    <?php } ?>
                </p>
            </a>
        </div>
        <div class="goal-item-content">
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/goal-milestones.svg" class="img-fluid">
                    <p>Milestones</p>
                </div>
                <h6>
                    <?php echo $milestonesCount; ?>
                </h6>
            </div>
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/goal-challenges.svg" class="img-fluid">
                    <p>Challenges</p>
                </div>
                <h6>
                    <?php echo $challengesCount; ?>
                </h6>
            </div>
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/goal-pov.svg" class="img-fluid">
                    <p>Points of View</p>
                </div>
                <h6>
                    <?php echo $povCount;  ?>
                </h6>
            </div>
            <div class="goal-item">
                <div class="goal-item-label">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/goal-attachments.svg" class="img-fluid">
                    <p>Attachments</p>
                </div>
                <h6>
                    <?php echo $attachmentsCount; ?>
                </h6>
            </div>
            <div class="goal-status">
                <?php if($goal_status == 'complete'){ ?>
                    <i title="Completed" class="fa fa-check" aria-hidden="true"></i>
                <?php }else{ ?>
                    <i title="Open" class="fa fa-minus" aria-hidden="true"></i>
                <?php } ?>
            </div>
        </div>
    </div>
</div>