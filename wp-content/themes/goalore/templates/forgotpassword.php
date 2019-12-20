<?php 
	/**
	  *
	  *
	  *Template Name: Forgot Password
	  *
	  *
	  */

get_header();

$reset_pwd_error = get_transient('reset_pwd_error'); ?>
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
            <div class="gaolore-form">
              <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" >
                <div class="form-group">
                  <label>Email</label>
                  <input type="text" class="form-control" name="user_login" required />
                </div>
                <div class="form-group register-response">
                	<label><?php echo $reset_pwd_error; ?></label>
                </div>
                <input type="hidden" name="action" value="reset_pwd_frm">
                <button type="submit" name="reset_pwd" class="btn">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

<?php get_footer(); ?>