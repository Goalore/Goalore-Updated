<?php 
	/**
	  *
	  *Template Name: Manage Categories
	  *
	  */

get_header(); 

$userID = get_current_user_id(); ?>

    <section class="admin-overview-section" id="admin-overview">
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
			        <li class="active" ><a href="javascript:;">Manage Goal Categories</a></li>
			        <li ><a href="<?php the_permalink(172); ?>">Manage Goal Subcategories</a></li>
			        <li ><a href="<?php the_permalink(174); ?>">Manage Tickets</a></li>
			      </ul>
			    </div>
			  </div>
			  <div class="col-12 col-lg-6">
			    <div class="admin-side-content">
			      <div class="gcard-form goal-cat-form">
			        <form id="add-goal-cat-frm">
			          	<div class="form-group">
				            <label>Category</label>
				            <input type="text" name="name" id="term_name" class="form-control" required>
			          	</div>
			          	<div class="form-group">
				            <label>Active Flag</label>
				            <div class="form-check">
				              <label class="custom-radiog">Yes
				                <input type="radio" name="active" class="term_active" checked="true" value="1">
				                <span class="checkmark"></span>
				              </label>
				            </div>
				            <div class="form-check">
				              <label class="custom-radiog">No
				                <input type="radio" name="active" class="term_active" value="0">
				                <span class="checkmark"></span>
				              </label>
				            </div>
			          	</div>
			          	<div class="col-12 text-center " style="display: none;">
			            	<div class="alert " id="response">
							</div>
						</div>
						<input type="hidden" name="term_id" id="term_id" value="">
			          <button type="submit" id="submit" class="btn btn-blue">Submit</button>
			        </form>
			      </div>
			    </div>
			  </div>
			</div>

			<?php  $order = ( isset($_REQUEST['order']) && $_REQUEST['order'] == 'desc') ? 'DESC' : 'ASC' ; ?>
			<div class="row">
			  <div class="col-12 col-lg-7">
			    <table class="table admin-ovtable">
			      <thead>
			        <tr>
			          <th><a href="?order=<?php echo ($order=='DESC')?'asc':'desc'; ?>">Goal_Category <img src="<?php echo get_template_directory_uri(); ?>/images/table-order-icon.svg" class="img-fluid"></a></th>
			          <th>Active?</th>
			          <th>Edit</th>
			        </tr>
			      </thead>
			      <tbody id="editCategory" >
			      	<?php $terms = get_terms([
                      'taxonomy'   => 'goal_categories',
                      'hide_empty' => false,
                      'parent'   => 0,
                      'order' => $order,
                    ]); if(!empty($terms)){
                    	foreach($terms as $term){ ?>
                    		<tr>
								<td class="term_name" ><?php echo $term->name; ?></td>
								<td class="term_active" ><?php echo (get_field('active',$term))?'Yes':'No'; ?></td>
								<td><a href="javascript:;" class="edit-link" data-term_id="<?php echo $term->term_id; ?>" ><img src="<?php echo get_template_directory_uri(); ?>/images/edit-icon.svg"></a></td>
					        </tr>
                    	<?php }
                    }  ?>
			      </tbody>
			    </table>
			  </div>
			</div>
		</div>
    </section> 

<?php get_footer(); ?>