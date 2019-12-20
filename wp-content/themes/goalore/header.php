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
$HOME = is_user_logged_in() ? get_permalink(98) : site_url();
$CurrentUserID = get_current_user_id(); 
?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
					                </div>
					            </a>
					            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					            	<?php 
					            	$table_notification = $wpdb->prefix . "notification";
					            	$SQL = ' SELECT * FROM '.$table_notification.' WHERE user_ids LIKE \'%"'. $CurrentUserID . '"%\' ORDER BY datetime DESC ' ;
					            	$notifications = $wpdb->get_results ( $SQL );
					            	if(!empty($notifications)){
					            		foreach($notifications as $notif){
					            			$profile_picture = get_user_profile_picture($notif->notifier_user_id);
					            			$permalink = get_permalink($notif->permalink_id);
					            			$permalink = !empty($permalink) ? $permalink : 'javascript:;';
					            			$message = $notif->message; ?>
					            			<a class="dropdown-item" href="<?php echo $permalink; ?>">
							                    <div class="notification-dropdown-item">
							                        <div class="ndi-img">
							                            <img class="" src="<?php echo $profile_picture; ?>">
							                        </div>
							                        <div class="ndi-msg">
							                            <p><?php echo $message; ?></p>
							                        </div>
							                    </div>
							                </a>
					            		<?php }
					            	}else{ ?>
					            		<a class="dropdown-item" href="javascript:;">
						                    <div class="notification-dropdown-item">
						                        <div class="ndi-msg">
						                            <p>No Notifications</p>
						                        </div>
						                    </div>
						                </a>
					            	<?php } ?>
					            </div>
					        </li>
					        <li class="nav-item dropdown profile-dropdown">
					            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                <img class="user-profile-icon" src="<?php echo get_user_profile_picture(); ?>">
					            </a>
					            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE); ?>">My Profile</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE.'/'.MY_CONNECTIONS); ?>">My Connections</a>
					                <a class="dropdown-item" href="<?php the_permalink(6); ?>">FAQ</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.PROFILE.'/'.SETTINGS); ?>">Settings</a>
					                <a class="dropdown-item" href="<?php echo home_url('/'.CONTACT); ?>">Contact Us</a>
					                <a class="dropdown-item" href="<?php echo wp_logout_url(site_url()) ?>">Logout</a>
					            </div>
					        </li>
					        <li class="nav-item dropdown good-deed-dropdown">
					        <?php $table_name = $wpdb->prefix . "gdp";
							$GDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $CurrentUserID"); 
							if(empty($GDP)) $GDP = 0; 

							$goalsGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $CurrentUserID AND meta_key = 'goals'"); 
							if(empty($goalsGDP)) $goalsGDP = 0; 

							$POVGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $CurrentUserID AND meta_key = 'POV'"); 
							if(empty($POVGDP)) $POVGDP = 0; 
							
							$ReferralGDP = $wpdb->get_var("SELECT SUM(points) FROM $table_name WHERE user_id = $CurrentUserID AND meta_key = 'referral_user_id'"); 
							if(empty($ReferralGDP)) $ReferralGDP = 0; 
							?>
					            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					                Good deed points: <?php echo$GDP; ?>
					            </a>
					            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					                <a class="dropdown-item" href="#">
					                    <div class="gdpn-summary">
					                        <h6>Summary</h6>
					                        <p>Good Deed Points: <strong><?php echo$GDP; ?></strong></p>
					                    </div>
					                    <div class="gdpn-detail">
					                        <h6>Details</h6>
					                        <p>Opened Goalore Account: <strong>50</strong></p>
					                        <p>Followers on Completed Goals: <strong><?php echo$goalsGDP; ?></strong></p>
					                        <p>Point of View Ratings: <strong><?php echo$POVGDP; ?></strong></p>
					                        <p>Referral Points: <strong><?php echo$ReferralGDP; ?></strong></p>
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
					        <input class="form-control mr-sm-2" type="text" placeholder="Username" name="user_login" id="user_login">
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
						            <input class="form-control border-0" value="<?php echo isset($_REQUEST['key'])?$_REQUEST['key']:''; ?>" type="search" placeholder="Search goals and people">
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
		                      <label>Username</label>
		                      <input type="text" class="form-control" name="user_login" id="user_login">
		                  </div>
		                  <div class="form-group col-6">
		                      <label>Password</label>
		                      <input type="password" class="form-control"  name="user_pass" id="user_pass" >
		                      <small class="form-text text-muted"><a href="">Forgot Password?</a></small>
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
		                  <?php if(is_user_logged_in()){ ?>
				              	<li><a href="<?php echo home_url('/'.PROFILE); ?>">My Profile</a></li>
				                <li><a href="<?php echo home_url('/'.PROFILE.'/'.MY_CONNECTIONS); ?>">My Connections</a></li>
				                <li><a href="<?php the_permalink(6); ?>">FAQ</a></li>
				                <li><a href="<?php echo home_url('/'.PROFILE.'/'.SETTINGS); ?>">Settings</a></li>
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
