<?php 
    /**
      *
      *Invite Friend
      *
      */

get_header(); ?>

<section class="register-section inner-pages suggest-cat-sec">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header text-center">
                    <h2>Contact Us</h2>
                    <h5>Please make user to check out the FAQ before submitting a request</h5>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="contact-form-main">
                    <form id="contact-frm">
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" name="type" id="type" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-blue">Send Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>