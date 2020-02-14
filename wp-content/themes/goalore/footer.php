<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goalore
 */

?>

    <footer>
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="site-footer">
              <div class="site-footer-logo">
                <!-- <img src="<?php echo get_template_directory_uri(); ?>/images/footer-logo.svg"> -->
                <img src="<?php echo get_template_directory_uri(); ?>/images/logo.svg">
              </div>
              <div class="site-footer-navigation">
                <ul class="list-inline">
                  <li class="list-inline-item"><a href="<?php the_permalink(3); ?>">Privacy Policy</a></li>
                  <li class="list-inline-item"><a href="<?php the_permalink(9); ?>">Terms of Service</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>

<?php wp_footer(); ?>

</body>
</html>
