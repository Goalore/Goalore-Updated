  <?php 
/**
  *
  *Template Name:FAQs Page
  *
  */
  get_header(); 

  get_fields()?extract(get_fields()):null; ?>

	
	<section class="faq-section inner-pages">
      	<div class="container">
	        <div class="row">
	          	<div class="col">
	            	<div class="section-header">
	              		<h2><?php the_title(); ?></h2>
	            	</div>
	          	</div>
	        </div>
	        <div class="row">
	          	<div class="col-12 col-lg-8">
	          		<?php $terms = get_terms(['taxonomy'=>'categories']); 
	          		if(!empty($terms)){
	          			foreach($terms as $term){ ?>
							<div class="faq-item">
				             	<h4><?php echo $term->name; ?></h4>
				              	<div class="accordion" id="<?php echo $term->slug; ?>">
					              	<?php $faqs = New WP_Query([
					              		'post_type'=>'faqs',
					              		'posts_per_page' => -1,
					              		'tax_query' => [
					              			[
					              			'taxonomy' => 'categories',
					              			'field' => 'term_id',
					              			'terms' => $term->term_id,
					              			]
					              		]
					              	]); if($faqs->have_posts()){
					              		while($faqs->have_posts()){ $faqs->the_post(); ?>
					              			<div class="card">
							                  	<div class="card-header" id="heading-<?php the_ID(); ?>" data-toggle="collapse" data-target="#collapse-<?php the_ID(); ?>" aria-expanded="false" aria-controls="collapse-<?php the_ID(); ?>">
								                    <img src="<?php echo get_template_directory_uri(); ?>/images/faq-plus-icon.svg" class="faq-plus">
								                    <img src="<?php echo get_template_directory_uri(); ?>/images/faq-minus-icon.svg" class="faq-minus">
								                    <h5><?php the_title(); ?></h5>
								                 </div>
								                <div id="collapse-<?php the_ID(); ?>" class="collapse" aria-labelledby="heading-<?php the_ID(); ?>" data-parent="#<?php echo $term->slug; ?>">
								                    <div class="card-body faqs_content">
								                      <?php the_content(); ?>
								                    </div>
								                </div>
							                </div>		
					              		<?php }
					              	} ?>
				              	</div>
				            </div>          				
	          			<?php }
	          		} ?>
	         	</div>
	        </div>
     	</div>
    </section>

<?php get_footer(); ?>