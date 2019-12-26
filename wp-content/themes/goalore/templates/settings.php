<?php 
    /**
      *
      *User Settings Page
      *
      */

get_header(); 

$userID = get_current_user_id();

$US = get_user_meta($userID, 'user_Settings', true);

$public_profile = isset($US['public_profile'])?$US['public_profile']:'';
$personal_info = isset($US['personal_info'])?$US['personal_info']:'';
$show_full_name = isset($US['show_full_name'])?$US['show_full_name']:'';
$show_gender = isset($US['show_gender'])?$US['show_gender']:'';
$show_dob = isset($US['show_dob'])?$US['show_dob']:'';

$isDeactivated = get_user_meta($userID,'isDeactivated',true);

?>

<section class="user-setting-section inner-pages">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header text-center">
                    <h2>User Settings</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-5">
                <div class="setting-form">
                    <form id="user-Settings-frm">
                        <h6>Privacy Options</h6>
                        <?php if($isDeactivated != '1'){ ?>
                        <div class="form-check-grp row">
                            <label class="col-12 col-md-6">Public Profile</label>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">Yes
                                        <input type="radio" name="public_profile" value="1" 
                                        <?php echo $public_profile != '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">No
                                        <input type="radio" name="public_profile" value="0"
                                        <?php echo $public_profile == '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-check-grp row">
                            <label class="col-12 col-md-6">Show Personal Info</label>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">Yes
                                        <input type="radio" name="personal_info" value="1" 
                                        <?php echo $personal_info != '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">No
                                        <input type="radio" name="personal_info" value="0" 
                                        <?php echo $personal_info == '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-check-grp row">
                            <label class="col-12 col-md-6">Show Full Name</label>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">Yes
                                        <input type="radio" name="show_full_name" value="1" 
                                        <?php echo $show_full_name != '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">No
                                        <input type="radio" name="show_full_name" value="0" 
                                        <?php echo $show_full_name == '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-check-grp row">
                            <label class="col-12 col-md-6">Show Gender</label>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">Yes
                                        <input type="radio" name="show_gender" value="1" 
                                        <?php echo $show_gender != '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">No
                                        <input type="radio" name="show_gender" value="0" 
                                        <?php echo $show_gender == '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-check-grp row">
                            <label class="col-12 col-md-6">Show DOB</label>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">Yes
                                        <input type="radio" name="show_dob" value="1" 
                                        <?php echo $show_dob != '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="custom-radiog">No
                                        <input type="radio" name="show_dob" value="0" 
                                        <?php echo $show_dob == '0'?'checked':''; ?> >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="<?php the_permalink(138); ?>">Edit Goal Categories ></a>
                        </div>
                        <div class="form-group">
                            <a href="<?php the_permalink(381) ?>">Reset Password ></a>
                        </div>
                        <div class="form-group">
                            <a href="javascript:;" id="deactivate-account">Deactivate Account ></a>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <a href="javascript:;" id="delete-account" >Delete Account ></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>