  <?php 
    /**
      *
      *Template Name:Home Page
      *
      */
      get_header(); 

      get_fields()?extract(get_fields()):null; ?>
    
    <section class="home-main-banner" style="background-image: url('<?php echo $image_1; ?>');">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="home-banner-content">
              <h2><?php echo $title_1; ?></h2>
              <?php if($button_1['show']){ ?>
                <a href="<?php echo $button_1['url']; ?>"><?php echo $button_1['label']; ?></a>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="how-it-works-sec">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section-header text-center">
              <h2><?php echo $title_2; ?></h2>
            </div>
          </div>
        </div>
        <div class="row">
          <?php if(!empty($how_it_works)){ 
            foreach($how_it_works as $hit){ ?>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="how-it-works-item">
                  <div class="hiw-item-image" style="background-image: url('<?php echo $hit['image']; ?>'); ">
                    <!-- <img src="<?php echo $hit['image']; ?>" class="img-fluid"> -->
                  </div>
                  <p><?php echo $hit['short_text']; ?></p>
                </div>
              </div>
            <?php } 
          } ?>
        </div>
      </div>
    </section>

    <section class="join-allience-section">
      <div class="join-allience-cw">
      <img src="<?php echo get_template_directory_uri(); ?>/images/crown-icon.svg" class="img-fluid">
      <h2><?php echo $alliance_title; ?></h2>
      <img src="<?php echo get_template_directory_uri(); ?>/images/crown-icon.svg" class="img-fluid">
      </div>
    </section>

    <section class="our-blog-sec">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section-header text-center">
              <h2><?php echo $title_3; ?></h2>
            </div>
          </div>
        </div>
        <div class="row">
          <?php $latest_posts = get_posts( ['numberposts'=>3] );
            if(!empty($latest_posts)){
              foreach($latest_posts as $lp){ ?>
                <div class="col-12 col-sm-6 col-md-4">
                  <div class="our-blog-item">
                    <div class="our-blog-image">
                      <a href="<?php the_permalink($lp->ID); ?>">
                        <!-- <img src="<?php echo get_the_post_thumbnail_url($lp); ?>" class="img-fluid"> -->
                        <div class="blog-article-image" style="background-image: url('<?php echo get_the_post_thumbnail_url($lp); ?>');"></div>
                      </a>
                    </div>
                    <h5><?php echo $lp->post_title; ?></h5>
                    <p>
                    <?php $content = strip_tags($lp->post_content);
                      if(strlen($content) > 140){
                        echo $content = substr($content, 0, 135) . '...';
                        // echo apply_filters('the_content',$content);
                      }else echo $content; ?>
                    </p>
                  </div>
                </div>
              <?php }
            } ?>
        </div>
      </div>
    </section>

    <?php if($show_stories_section){ ?>
      <section class="sucess-stories-sec">
        <div class="container">
          <div class="row">
            <div class="col">
              <div class="section-header text-center">
                <h2><?php echo $title_4; ?></h2>
              </div>
            </div>
          </div>
          <div class="row">
            <?php if(!empty($stories)){ 
              foreach($stories as $s){ ?>
                <div class="col-12 col-sm-6 col-md-4 ss-col">
                  <div class="sucess-story-item">
                    <h6><?php echo $s['title']; ?></h6>
                    <p><?php echo $s['short_text']; ?> </p>
                  </div>
                </div>
              <?php } 
            } ?>
          </div>
        </div>
      </section>
    <?php } ?>

    <section class="join-today-section">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section-header text-center">
              <h2><?php echo $joint_title; ?></h2>
            </div>
            <div class="join-today-navigate text-center">
              <?php if($button_4['show']){ ?>
                <a href="<?php echo $button_4['url']; ?>"><?php echo $button_4['label']; ?></a>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    
<?php get_footer(); ?>