<?php
  /**
    *
    *
    *Template Name: Reset Password
    *
    *
    */

get_header();

$error = '';
$disabled = 'disabled';
$key ='';
$login ='';

if(isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['login']) && !empty($_GET['login']) ){
    $key = $_GET['key'];
    $login = $_GET['login'];

    $user = check_password_reset_key($key,$login);
    if(!is_wp_error($user)){
        $error = $disabled = '';

    }else {
        $error = $user->get_error_message();
    }

}

?>
<section class="register-section inner-pages suggest-cat-sec">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header text-center">
                    <?php if(!empty($disabled)){ ?>
                        <?php while(have_posts()) : the_post(); the_content(); endwhile; ?>
                    <?php }else{ ?>
                        <h2>Reset Your Password</h2>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="gaolore-form">
                    <?php if(!empty($disabled)){ ?>
                         <form id="forgot-pwd-frm" >
                            <div class="form-group">
                                <label>Username or Email</label>
                                <input type="text" class="form-control" name="user_login" id="user_login" />
                            </div>
                            <div class="register-response">
                                <label><?php //echo $error;  ?></label>
                            </div>
                            <?php wp_nonce_field( 'ajax-forgot-pwd-nonce', 'security' ); ?>
                            <button type="submit" name="reset_pwd" class="btn">Send Email</button>
                        </form>
                    <?php }else{ ?>    
                        <form id="reset-pwd-frm" >
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" <?php echo $disabled; ?> class="form-control" name="new_password" id="new_password" />
                            </div>
                            <div class="form-group">
                                <label>Verify Password</label>
                                <input type="password" <?php echo $disabled; ?> class="form-control" name="verify_password" id="verify_password" />
                            </div>
                            <?php wp_nonce_field( 'ajax-reset-pwd-nonce', 'security' ); ?>
                            <input type="hidden" name="key" id="key" value="<?php echo $key; ?>">
                            <input type="hidden" name="login" id="login" value="<?php echo $login; ?>">
                            <button <?php echo $disabled; ?> type="submit" name="reset_pwd" class="btn">Submit</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>