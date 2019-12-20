<?php 
	/**
	  *
	  *
	  *Template Name: Register Step 2
	  *
	  *
	  */
	get_header(); 

	$user_category = get_transient('user_category'); ?>

<section class="register-section inner-pages suggest-cat-sec">
   <div class="container">
      <div class="row">
         <div class="col">
            <div class="section-header text-center">
               <?php while(have_posts()) : the_post(); the_content(); endwhile; ?>
            </div>
         </div>
      </div>
      <div class="row justify-content-center">
         <div class="col-12 col-md-8 col-lg-5">
            <div class="blue-form ">
               <form id="user-category-frm" >
                  <div class="row">
                     <?php 
                     $categories = get_user_meta(get_current_user_id(),'categories',true);
                     if(empty($categories)) $categories = [];
                     $terms = get_terms([
                        'taxonomy'   => 'goal_categories',
                        'hide_empty' => false,
                        'parent'   => 0
                      ]); if(!empty($terms)){ 
                        $size = ceil(count($terms) / 2); 
                        $termss = array_chunk($terms, $size); 
                        foreach($termss as $tts){ ?>
                          <div class="col-12 col-md-6">
                            <?php foreach($tts as $t){ ?>     
                              <div class="form-group">
                                 <label class="check-container"><?php echo $t->name; ?>
                                   <input type="checkbox" name="user_category[]" class="cats" <?php echo in_array($t->term_id, $categories)?'Checked':null;  ?> value="<?php echo $t->term_id; ?>" >
                                   <span class="checkmark"></span>
                                 </label>
                              </div>
                            <?php } ?>
                          </div>
                        <?php }  
                      } ?>
                  </div>
                  <div class="suggest-form-bottom">
                     <div class="skip-btn-group">
                        <input type="hidden" name="action" value="register_user_category">
                        <button type="submit" class="btn">Submit</button>
                        <a href="<?php the_permalink(98); ?>" class="skip-btn">Skip ></a>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<?php get_footer(); ?>