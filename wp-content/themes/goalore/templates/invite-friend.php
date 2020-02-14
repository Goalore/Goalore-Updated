<?php 
    /**
      *
      *Invite Friend
      *
      */

get_header(); ?>

<section class="register-section inner-pages">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header text-center">
                    <h2>Invite a friend to Goalore!</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="blue-form register-form">
                    <form id="invite-friend-frm">
                        <div class="form-group">
                            <label>Recipient Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                            <small class="form-text-error"></small>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" id="message" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="<?php echo GCV2_Publick; ?>"></div>
                        </div>
                        <button type="submit" class="btn">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>