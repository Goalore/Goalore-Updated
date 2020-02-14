  <?php 

  get_header(); 

  get_fields()?extract(get_fields()):null; ?>

  <section class="blog-detail-section inner-pages">
      <div class="container">

        <div class="row">
          <div class="col">
            <div class="section-header">
              <h2><?php the_title();?></h2>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="blog-detail-content">
              <img src="<?php the_post_thumbnail_url(); ?>" class="img-fluid">
              <?php the_content();?>


             <?php  
             	if($image){
             	echo ' <img src="'.$image.'" class="img-fluid">';
           		}
           		echo $extra_content;
             	?>
            </div>
      <?php endwhile; ?>
            <?php endif; ?>
          </div>
        </div>
        <?php echo do_shortcode('[addtoany]'); ?>
      </div>
    </section>

<?php get_footer(); ?>