	<?php 
		/**
		  *
		  *Template Name: Manage Tickets
		  *
		  */

	get_header(); 

	$userID = get_current_user_id(); ?>

	  <section class="admin-overview-section">
	      <div class="container">

	        <div class="row">
	          <div class="col">
	            <div class="section-header">
	              <h2>Admin Overview</h2>
	            </div>
	          </div>
	        </div>

	        <div class="row justify-content-between admin-mdl-row">
	          <div class="col-12 col-lg-4">
	            <div class="admin-side-navbar">
	              <ul class="sidebar-nav">
	                <li ><a href="<?php the_permalink(170); ?>">Manage Goal Categories</a></li>
				    <li ><a href="<?php the_permalink(172); ?>" >Manage Goal Subcategories</a></li>
	                <li class="active"><a  href="javascript:;" >Manage Tickets</a></li>
	              </ul>
	            </div>
	          </div>
	          <div class="col-12 col-lg-6">
	            <div class="admin-side-content" id="tickets-ctn">
					<ul class="nav goal-catg admin-ticket-tab" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link btn active" id="open-t-tab" data-toggle="tab" href="#open-t" role="tab" aria-controls="open-t" aria-selected="true">Open Tickets</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn" id="closed-t-tab" data-toggle="tab" href="#closed-t" role="tab" aria-controls="closed-t" aria-selected="false">Closed Tickets</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="open-t" role="tabpanel" aria-labelledby="open-t-tab">
							<?php $openTickets = New WP_Query([
						    	'post_type' => 'tickets',
						    	'posts_per_page' => -1,
						    	'meta_key' => 'status',
						    	'meta_value' => 'open',
						    ]); if($openTickets->have_posts()){ ?>
								<table class="table admin-ovtable">
									<thead>
									  <tr>
									    <th><b>User</b></th>
									    <th><b>Type</b></th>
									    <th><b>Report Date</b></th>
									    <th><b>Edit</b></th>
									  </tr></thead>
									<tbody>
										<?php while($openTickets->have_posts()) : 
											$openTickets->the_post();
											$post_author_id = get_post_field( 'post_author', get_the_ID() ); 
											$user = get_user_by('ID',$post_author_id); ?>
											<tr>
											    <td><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->user_login ?></a></td>
											    <td><a href="javascript:;"><?php the_title() ?></a></td>
											    <td><?php echo get_the_date('d-m-Y'); ?></td>
											    <td>
											    	<a href="javascript:;" class="edit-ticket" data-id="<?php the_ID() ?>" data-status="open" ><img src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg"></a>
											    	<div class="description d-none" >
											    		<?php the_content(); ?>
											    	</div>
											    	<div class="resolution d-none" >
											    		<?php the_field('resolution'); ?>
											    	</div>
											    </td>
											</tr>
										<?php endwhile; wp_reset_query(); ?>
									</tbody>
								</table>
								<?php }else{ ?>
						  		<div class="col-12 text-center ">
						        	<div class="alert alert-warning " >
						        		No open tickets!
									</div>
								</div>
						  	<?php } ?>
						</div>
						<div class="tab-pane fade" id="closed-t" role="tabpanel" aria-labelledby="closed-t-tab">
						  <?php $closedTickets = New WP_Query([
						    	'post_type' => 'tickets',
						    	'posts_per_page' => -1,
						    	'meta_key' => 'status',
						    	'meta_value' => 'closed',
						    ]); if($closedTickets->have_posts()){ ?>
								<table class="table admin-ovtable">
									<thead>
									  <tr>
									    <th><b>User</b></th>
									    <th><b>Report Date</b></th>
									    <th><b>Edit</b></th>
									  </tr></thead>
									<tbody>
										<?php while($closedTickets->have_posts()) : 
											$closedTickets->the_post();
											$post_author_id = get_post_field( 'post_author', get_the_ID() ); 
											$user = get_user_by('ID',$post_author_id); ?>
											<tr>
											    <td><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->user_login ?></a></td>
											    <td><a href="javascript:;"><?php the_title() ?></a></td>
											    <td><?php echo get_the_date('d-m-Y'); ?></td>
											    <td>
											    	<a href="javascript:;" class="edit-ticket" data-id="<?php the_ID() ?>" data-status="closed" ><img src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg"></a>
											    	<div class="description d-none" >
											    		<?php the_content(); ?>
											    	</div>
											    	<div class="resolution d-none" >
											    		<?php the_field('resolution'); ?>
											    	</div>
											    </td>
											</tr>
										<?php endwhile; ?>
									</tbody>
								</table>
								<?php }else{ ?>
						  		<div class="col-12 text-center ">
						        	<div class="alert alert-warning " >
						        		No closed tickets!
									</div>
								</div>
						  	<?php } ?>
						</div>
					</div>
	            </div>
				<div class="gcard-form goal-cat-form form-with-bbtn" id="ticket-frm-main" style="display: none;">
					<div class="frm-bck-btn">
						<a href="javascript:;"><img src="<?php echo get_template_directory_uri(); ?>/images/back-icon.svg" id="bck-ticket-list" class="img-fluid"></a>
					</div>
					<form id="submit-ticket">
						<div class="form-group ">
							<label>Description</label>
							<textarea disabled class="form-control ticket_description" id="exampleFormControlTextarea1" style="opacity: 0.6" rows="4"></textarea>
						</div>
						<div class="form-group">
							<label>Resolution</label>
							<textarea class="form-control ticket_resolution" required  id="exampleFormControlTextarea1" rows="4"></textarea>
						</div>
						<div class="form-group">
							<label>Status</label>
							<select class="form-control" name="status" id="status">
								<option value="open" >Open</option>
								<option value="closed" >Closed</option>
							</select>
						</div>
						<div class="col-12 text-center alert-success" style="display: none;">
			            	<div class="alert" id="response" >
							</div>
						</div>
						<input type="hidden" name="ticket_id" id="ticket_id" >
						<button type="Submit" id="submit" class="btn btn-blue">Submit</button>
					</form>
				</div>
	          </div>
	        </div>

	      </div>
	    </section> 

	<?php get_footer(); ?>