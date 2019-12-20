<?php 
	/**
	  *
	  *Template Name: Register
	  *
	  */

get_header();


$email = isset($_GET['email']) ? $_GET['email'] : ''; ?>
 <section class="register-section inner-pages">
      <div class="container">

        <div class="row">
          <div class="col">
            <div class="section-header text-center">
              <h2><?php the_title(); ?></h2>
            </div>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-12 col-lg-6">

            <div class="blue-form register-form">
              <form name="register-frm" id="register-frm"  method="post" >

                <div class="form-group">
                  <label>Member Type</label>
                  <select class="form-control" id="type" name="type" >
                  	<option value="" ></option>
                  	<option value="individual" >Individual</option>
                  	<option value="private_company" >Private Company</option>
                  	<option value="non_profit" >Non Profit</option>
                  	<option value="goverment" >Goverment</option>
                  	<option value="community_group" >Community Group</option>
                  </select>
                </div>

                <div class="row">
                  <div class="form-group col-10 col-md-6">
                    <label>Full Name</label>
                    <input type="text" class="form-control"  name="full_name" id="full_name" />
                  </div>
                  <div class="form-group col-10 col-md-6">
                    <label>Date of Birth</label>
                    <div class="input-ttip">
                    	<?php $mindob = strtotime(date('Y-m-d').' -18 year'); ?>
                    <input type="date" class="form-control"  name="dob" id="dob"  min="1920-01-01" max="<?php echo date('Y-12-31', $mindob); ?>" />
                    <small class="form-text">*For age verification purposes</small>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label>Username </label>
                  <input type="text" class="form-control"  name="username" id="username" />
                </div>

                <div class="form-group">
                  <label>Email</label>
                  <input type="text" class="form-control"  name="email" value="<?php echo $email; ?>" id="email" />
                </div>

                <div class="row">
                  <div class="form-group col-10 col-md-6">
                    <label>Password</label>
                    <input type="password" class="form-control"  name="password" id="password" />
                  </div>
                  <div class="form-group col-10 col-md-6">
                    <label>Verify Password</label>
                    <input type="password" class="form-control"  name="verify_password" id="verify_password" />
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-10 col-md-6">
                    <label>Country</label>
                    <input type="text" class="form-control"  name="country" id="country" />
                  </div>
                  <div class="form-group col-10 col-md-6">
                    <label>Zip Code</label>
                    <input type="text" class="form-control"  name="zip_code" id="zip_code" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="check-container">Accept Terms and Conditions
                    <input type="checkbox" name="TermsConditions" id="terms-condition" >
                    <span class="checkmark"></span>
                  </label>
                </div>

                <div class="form-group">
                  <label class="check-container">Accept Privacy Policy
                    <input type="checkbox" name="PrivacyPolicy" id="privacy-policy" >
                    <span class="checkmark"></span>
                  </label>
                </div>
                <?php wp_nonce_field( 'ajax-register-nonce', 'security' ); ?>
                <input type="hidden" name="referral_code" id="referral_code" value="<?php echo isset($_GET['referral_code']) ? $_GET['referral_code'] : '';; ?>">
                <button type="submit" class="btn" name="register" value="Register" >Submit</button>

              </form>
            </div>

          </div>
        </div>

      </div>
    </section>


<?php get_footer(); ?>