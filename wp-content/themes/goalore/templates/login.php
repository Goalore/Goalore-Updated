<?php 
	/**
	  *
	  *
	  *Template Name: Login
	  *
	  *
	  */

get_header();

$errors = get_transient('login_errors'); ?>

<div class="container login-container">
	<div class="row">
	    <div class="col-md-6 login-form-1">
	        <h3>Login for Form 1</h3>
	        <p><?php print_r($errors); ?></p>
	        <form name="login-frm" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
	            <div class="form-group">
	                <input type="text" class="form-control" name="username" placeholder="Your Email *" value="" />
	            </div>
	            <div class="form-group">
	                <input type="password" class="form-control" name="password" placeholder="Your Password *" value="" />
	            </div>
	            <div class="form-group">
	            	<input type="hidden" name="action" value="login_frm">
	                <input type="submit" name="login" class="btnSubmit" value="Login" />
	            </div>
	            <div class="form-group">
	                <a href="#" class="ForgetPwd">Forget Password?</a>
	            </div>
	        </form>
	    </div>
	</div>
</div>

<?php get_footer(); ?>