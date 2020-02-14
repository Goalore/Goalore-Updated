<?php 
    /**
      *
      *Mobile GDP Page
      *
      */

get_header(); 

$GDP_Summary = get_gdp_summary(); ?>

<section class="mobile-gd">
   <div class="container">
      <div class="row">
         <div class="col">
            <div class="mobile-good-deed-points">
               <a class="good-deed-item" href="#">
                  <div class="gdpn-summary">
                     <h6>Summary</h6>
                     <p>Good Deed Points: <strong><?php echo $GDP_Summary['total']; ?></strong></p>
                  </div>
                  <div class="gdpn-detail">
                     <h6>Details</h6>
                     <p>Opened Goalore Account: <strong><?php echo $GDP_Summary['registration']; ?></strong></p>
                     <p>Followers on Completed Goals: <strong><?php echo $GDP_Summary['goals']; ?></strong></p>
                     <p>Point of View Ratings: <strong><?php echo $GDP_Summary['pov']; ?></strong></p>
                     <p>Referral Points: <strong><?php echo $GDP_Summary['referral']; ?></strong></p>
                  </div>
               </a>
            </div>
         </div>
      </div>
   </div>
</section>

<?php get_footer(); ?>
