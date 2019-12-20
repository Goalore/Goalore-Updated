  <?php 
/**
  *
  *Template Name:Blog Listing Page
  *
  */
  get_header(); 

  get_fields()?extract(get_fields()):null; ?>

	<section class="blog-page-section inner-pages">
	    <div class="container">

	        <div class="row">
	          <div class="col">
	            <div class="section-header">
	              <h2>Recent Articles</h2>
	            </div>
	          </div>
	        </div>

			<?php $my_posts = new WP_Query(['posts_per_page' => -1, ]); ?>
	        <div class="row">
	         <?php
	         	$counter = 0;
	          	while ($my_posts->have_posts()) : $my_posts->the_post(); $counter++;
	         	if(	$counter == 1):
	          	echo '<div class="col-12 col-md-12 col-lg-7">';
				elseif(	$counter == 2):
	          	echo '<div class="col-12 col-md-6 col-lg-5">';
	          	else:
	          	echo '<div class="col-12 col-md-6 col-lg-4">';
  				endif;
  				?>
	            <div class="blog-article-item">
	              <a href="<?php echo get_permalink(); ?>"><div class="blog-article-image" style="background-image: url('<?php the_post_thumbnail_url(); ?>');"></div></a>
	              	<h5><?php the_title();?></h5>
	              	<p>
					<?php $content = strip_tags(get_the_content());
                      if(strlen($content) > 125){
                        echo $content = substr($content, 0, 130) . '...';
                        // echo apply_filters('the_content',$content);
                      }else echo $content; ?>
	            	</p>
	            </div>
	          </div>
	        <?php endwhile; wp_reset_postdata(); ?>
	        </div>
		</div>
	</section>

<?php get_footer(); ?>