<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goalore
 */
global $wpdb; 
$SearchpageUrl = site_url().'/'.SearchResult;
$HOME = is_user_logged_in() ? get_permalink(98) : site_url(); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/fav-icon.png" type="image/png" >

		<?php wp_head(); ?>
	</head>
	<body>		
		<header id="site-header">
		  <!-- Navbar Desktop -->
		  <nav class="navbar navbar-expand-lg navbar-dark bg-white navbar-goalore">
		      <a class="navbar-brand" href="<?php echo $HOME; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.svg" class="img-fluid"></a>
		      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		          <span class="navbar-toggler-icon"></span>
		      </button>
		      	<?php if(is_user_logged_in()){ ?>
			      	<div class="collapse navbar-collapse signed-in" id="navbarSupportedContent">
					    <form class="form-inline search-form" action="<?php echo $SearchpageUrl ?>">
					        <div class="input-group">
					            <input class="form-control border-0" value="<?php echo isset($_REQUEST['key'])?$_REQUEST['key']:''; ?>" type="text" name="key" placeholder="Search goals and people">
					            <div class="input-group-append">
					                <button class="btn btn-outline-secondary my-2 my-sm-0 border-0" type="submit"><img src="<?php echo get_template_directory_uri(); ?>/images/search-icon.svg"> </button>
					            </div>
					        </div>
					    </form>
					    <ul class="navbar-nav ml-auto">
					    	<li class="nav-item active">
					            <a class="nav-link" href="<?php the_permalink(98); ?>">Dashboard</a>
					        </li>
					    	<?php $CurrentUserID = get_current_user_id();  
					    	$user_meta=get_userdata($CurrentUserID);
					    	$user_roles=$user_meta->roles;
					    	if(in_array("administrator", $user_roles)){ ?>
								<li class="nav-item active">
						            <a class="nav-link" href="<?php the_permalink(174); ?>">Tickets</a>
						        </li>					    		
					    	<?php } ?>
					        <li class="nav-item active">
					            <a class="nav-link" href="<?php the_permalink(165); ?>">Goals</a>
					        </li>
					        <li class="nav-item">
					            <a class="nav-link" href="<?php the_permalink(167); ?>">Alliances</a>
					        </li>
					        <li class="nav-item">
					            <a class="nav-link" href="<?php the_permalink(11); ?>">Blog</a>
					        </li>
					        <li class="nav-item dropdown notification-dropdown">
					            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                <div class="notification-icon active-notification">
					                    <img src="<?php echo get_template_directory_uri(); ?>/images/notification-icon.svg">
					                    <span class="notification-count" >0</span>
					                </div>
					            </a>
					            <div class="dropdown-menu mCustomScrollbar gib-large" aria-labelledby="navbarDropdown">
					            	<?php get_template_part('template-parts/notification','content'); ?>
					            </div>
					        </li>
					        <li class="nav-item dropdown profile-dropdown">
					            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                <img class="member-profile-img" style="border: 1px solid #fff;" src="<?php echo get_user_profile_picture(); ?>">
					            </a>
					            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE); ?>">My Profile</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE.'/'.MY_CONNECTIONS); ?>">My Connections</a>
					                <?php if(in_array("administrator", $user_roles)){ ?>
										<a class="dropdown-item" href="<?php the_permalink(170); ?>">Admin Overview</a>
							    	<?php } ?>
					                <a class="dropdown-item" href="<?php the_permalink(6); ?>">FAQ</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE.'/'.SETTINGS); ?>">Settings</a>
							    	<a class="dropdown-item" href="<?php the_permalink(579); ?>">Archive</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.CONTACT); ?>">Contact Us</a>
					                <a class="dropdown-item" href="<?php echo wp_logout_url(site_url()) ?>">Logout</a>
					            </div>
					        </li>
					        <li class="nav-item dropdown good-deed-dropdown">
					        <?php $GDP_Summary = get_gdp_summary(); ?>
					            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                Good deed points: <?php echo $GDP_Summary['total']; ?>
					            </a>
					            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					                <a class="dropdown-item" href="#">
					                    <div class="gdpn-summary">
					                        <h6>Summary</h6>
					                        <p>Good Deed Points: <strong><?php echo $GDP_Summary['total']; ?></strong></p>
					                    </div>
					                    <div class="gdpn-detail">
					                        <h6>Details</h6>
					                        <p>Opened Goalore Account: <strong><?php echo $GDP_Summary['registration']; ?></strong></p>
					                        <p>Followers on Completed Goals: <strong><?php echo $GDP_Summary['goals']; ?></strong></p>
					                        <p>Point of View Ratings: <strong><?php echo $GDP_Summary['pov']; ?></strong></p>
					                        <p>Referral Points: <strong><?php echo $GDP_Summary['referral']; ?></strong></p>
					                    </div>
					                </a>
					            </div>
					        </li>
					    </ul>
					</div>
		  		<?php }else{ ?>
					<div class="collapse navbar-collapse not-signed-in" id="navbarSupportedContent">
						<?php wp_nav_menu([
			              'menu'       => 'Header-Menu-not-loggedin',
			              'menu_class' => 'navbar-nav ml-auto',
			              'container'  => ''
			            ]); ?>
					    <form class="form-inline nav-login-form my-2 my-lg-0 login-frm" id="login-frm">
					        <input class="form-control mr-sm-2" type="text" placeholder="Username or Email" name="user_login" id="user_login">
					        <div class="password-group">
					            <input class="form-control mr-sm-2" type="password" placeholder="Password" name="user_pass" id="user_pass">
					            <a href="<?php the_permalink(100); ?>" class="f-pass">Forgot Password?</a>
					        </div>
					        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
					        <button class="btn my-2 my-sm-0 login-btn"  type="submit">Login</button>
					    </form>
					</div>
		  		<?php } ?>
		  </nav>
		  <!-- Navbar mobile -->
		  <nav class="navbar-goalore-mobile">
		      <div class="mobile-menu">
		          <div class="burger-menu">
		              <a href="javascript:void(0)" onclick="openNav()"><img src="<?php echo get_template_directory_uri(); ?>/images/burger-menu.svg" class="img-fluid"></a>
		          </div>
		          <div class="mobile-navbar-nav ml-auto text-right">
		          	<?php if(!is_user_logged_in()){ ?>
		            	<a href="<?php the_permalink(96); ?>" class="btn btn-primary">Sign Up</a>
		            <?php }if(is_user_logged_in()){ ?>
						<div class="mobile-search-bar">
						    <form class="form-inline search-form" action="<?php echo $SearchpageUrl; ?>">
						        <div class="input-group">
						            <input class="form-control border-0" value="<?php echo isset($_REQUEST['key'])?$_REQUEST['key']:''; ?>"  type="text" name="key" placeholder="Search goals and people">
						            <div class="input-group-append">
						                <button class="btn btn-outline-secondary my-2 my-sm-0 border-0" type="submit"><img src="<?php echo get_template_directory_uri(); ?>/images/search-icon.svg"> </button>
						            </div>
						        </div>
						    </form>
						</div>
		  			<?php } ?>
		          </div>
		      </div>
		    <?php if(!is_user_logged_in()){ ?>
		      <div class="mobile-login-form">
		          <form  class="login-frm">
		              <div class="row">
		                  <div class="form-group col-6">
		                      <label>Username or Email</label>
		                      <input type="text" class="form-control" name="user_login" id="user_login">
		                  </div>
		                  <div class="form-group col-6">
		                      <label>Password</label>
		                      <input type="password" class="form-control"  name="user_pass" id="user_pass" >
		                      <small class="form-text text-muted"><a href="<?php the_permalink(100); ?>">Forgot Password?</a></small>
		                  </div>
		              </div>
		              <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
		              <button type="submit" class="login-btn btn btn-primary float-right">Login</button>
		          </form>
		      </div>
		  	<?php } ?>
		      <!-- overlay menu -->
		      <div id="myNav" class="overlay">
		          <div class="monav-top">
		              <div class="ovm-logo">
		                  <a href="<?php echo $HOME; ?>">
		                      <img src="<?php echo get_template_directory_uri(); ?>/images/logo.svg" class="img-fluid">
		                  </a>
		              </div>
		              <div class="ovm-clsbtn ml-auto">
		                  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">
		                      <img src="<?php echo get_template_directory_uri(); ?>/images/cls-btn.svg" class="img-fluid">
		                  </a>
		              </div>
		          </div>
		          <div class="overlay-content">
		              <ul>
		                  <?php if(is_user_logged_in()){ 
		                  	$CurrentUserID = get_current_user_id();  
					    	$user_meta=get_userdata($CurrentUserID);
					    	$user_roles=$user_meta->roles; ?>
					    		<li><a href="<?php the_permalink(98); ?>">Dashboard</a></li>
						    	<?php if(in_array("administrator", $user_roles)){ ?>
									<li><a href="<?php the_permalink(174); ?>">Tickets</a></li>					    		
						    	<?php } ?>
				              	<li><a href="<?php the_permalink(165); ?>">Goals</a></li>
				              	<li><a href="<?php the_permalink(167); ?>">Alliances</a></li>
				                <li><a href="<?php echo home_url('gdp'); ?>">Good Deed Points</a></li>
				              	<li><a href="<?php the_permalink(11); ?>">Blog</a></li>
				                <li><a href="<?php echo home_url('notification'); ?>">Notification (<span class="notification-count" >0</span>)</a></li>
				              	<li><a href="<?php echo home_url('/'.PROFILE); ?>">My Profile</a></li>
				                <li><a href="<?php echo home_url('/'.PROFILE.'/'.MY_CONNECTIONS); ?>">My Connections</a></li>
				                <li><a href="<?php echo home_url('/'.PROFILE.'/'.SETTINGS); ?>">Settings</a></li>
				                <?php if(in_array("administrator", $user_roles)){ ?>
									<li><a href="<?php the_permalink(170); ?>">Admin Overview</a></li>					    		
						    	<?php } ?>
				                <li><a href="<?php the_permalink(6); ?>">FAQ</a></li>
						    	<li><a href="<?php the_permalink(579); ?>">Archive</a></li>
				                <li><a href="<?php echo home_url('/'.CONTACT); ?>">Contact Us</a></li>
				                <li><a href="<?php echo wp_logout_url(site_url()) ?>">Logout</a></li>
				            <?php }else{ ?>
				           		<li><a href="<?php the_permalink(11); ?>">Blog</a></li>
			                	<li><a href="<?php the_permalink(6); ?>">FAQ</a></li>
			                	<li><a href="<?php the_permalink(96); ?>">Sign Up</a></li>
			                	<li><a href="<?php the_permalink(100); ?>">Forgot Password</a></li>
		                  <?php } ?>
		              </ul>
		          </div>
		      </div>
		  </nav>
		</header>
