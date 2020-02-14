<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goalore
 */

get_header();
?>
<section class="blog-detail-section inner-pages general-page">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header">
                    <h2>
                        <?php the_title();?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php if (have_posts()) : 
                	while (have_posts()) : the_post(); ?>
		                <div class="blog-detail-content">
		                    <?php the_content();?>
		                </div>
	            	<?php endwhile; 
	            endif; ?>
            </div>
        </div>
    </div>
</section>


<?php get_footer();
