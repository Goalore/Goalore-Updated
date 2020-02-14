<?php 
  /**
    *
    *OTP verification
    *
    */

get_header();


$sent = false;
$user_id = get_current_user_id();
$key = '2faotp' . $user_id;
$expirationSeconds = get_option('_transient_timeout_' . $key);
$canSetDateTime  = strtotime('-45 minutes', $expirationSeconds); 
$currentDateTime = time();
if($canSetDateTime < $currentDateTime){
    
}else{
    $sent = true;

}

if(isset($_GET['resend']) ){
    $sent = true;
  
    if($canSetDateTime < $currentDateTime){
        delete_transient($key);
    }    
}

send_two_factor_authentication_mail();

?>
<section class="register-section inner-pages suggest-cat-sec">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header text-center">
                    <h2>Two Factor Authentication</h2>
                    <h5>We have sent you a one time password(OTP) to the registered email address. OTP is only valid for 60 minutes.</h5>
                    <?php if($sent){ ?>
                        OTP sent(Can resend after 15 minutes).
                    <?php }else{ ?>
                        <a href="?resend" >Resend OTP?</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="gaolore-form">
                    <form  method="post" id="verify-otp-frm">
                        <div class="form-group">
                            <label>Enter OTP</label>
                            <input type="text" class="form-control" name="otp" id="otp" />
                        </div>
                        <?php wp_nonce_field( 'ajax-otp-nonce', 'security' ); ?>
                        <button type="submit" name="verify_otp" class="btn">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>