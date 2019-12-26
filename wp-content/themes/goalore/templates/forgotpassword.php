<?php 
  /**
    *
    *
    *Template Name: Forgot Password
    *
    *
    */

get_header();

?>
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
                    <form id="forgot-pwd-frm" >
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="user_login" id="user_login" />
                        </div>
                        <?php wp_nonce_field( 'ajax-forgot-pwd-nonce', 'security' ); ?>
                        <button type="submit" name="reset_pwd" class="btn">Send Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>