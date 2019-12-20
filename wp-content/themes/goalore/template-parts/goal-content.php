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

$goal = get_post();

?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="goalorew-card goal-card-item">
        <div class="goal-item-header">
            <a href="<?php echo $goal->guid; ?>">
                <h5>
                    <?php the_title(); ?>
                </h5>
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
        </div>
    </div>
</div>