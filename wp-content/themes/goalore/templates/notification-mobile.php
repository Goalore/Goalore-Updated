<?php 
    /**
      *
      *Mobile Notification View Page
      *
      */

get_header(); ?>

<section class="mobile-notification">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="mobile-notification-list">
                	<?php get_template_part('template-parts/notification','content'); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
