<?php 
	/**
	  *
	  *My Connections Page
	  *
	  */

get_header(); 

global $wpdb;
$table_gdp = $wpdb->prefix . "gdp";
$userID = get_current_user_id();
$myconnectionsIDs = get_user_meta($userID,'connections',true);
$pcrIDs = get_user_meta($userID,'pending_connection_request',true);

?>

<section class="search-page-section">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="section-header">
                    <h2>My Connections</h2>
                </div>
            </div>
        </div>
        <div class="s-results">
            <div class="row">
                <div class="col">
                    <div class="user-search-header">
                        <h4>Pending Invitations</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6">
                	<?php if(!empty($pcrIDs)){
	                	$INIDS = implode(',', $pcrIDs);
                		$sortSQL = " SELECT user_id from $table_gdp WHERE user_id IN ($INIDS) GROUP BY user_id ORDER BY SUM(points) DESC";
                		$orderdIDs = $wpdb->get_col( $sortSQL );
                		foreach($orderdIDs as $orderdID){
                			$puser = get_user_by('ID', $orderdID);
                			set_query_var( 'userData', $puser ); ?>
                			<div class="connection-invitation-item">
		                        <?php get_template_part('template-parts/user','listing-content');  ?>
		                        <div class="allience-req-opt " >
		                            <button id="accept-request" data-user_id="<?php echo$puser->ID ?>" class="btn btn-blue">Accept</button>
		                            <button id="reject-request" data-user_id="<?php echo$puser->ID ?>" class="btn btn-white">Reject</button>
		                        </div>
		                    </div>
                		<?php }
	                }else{ ?>
	                	<div class="col-12 text-center ">
			            	<div class="alert alert-warning">
								No Pending Connections!
							</div>
						</div>
	                <?php } ?>
                </div>
            </div>
        </div>
        <div class="s-results">
            <div class="row">
                <div class="col">
                    <div class="user-search-header">
                        <h4>Connections</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6">
                	<?php if(!empty($myconnectionsIDs)){ 
                		$INIDS = implode(',', $myconnectionsIDs);
                		$sortSQL = " SELECT user_id from $table_gdp WHERE user_id IN ($INIDS) GROUP BY user_id ORDER BY SUM(points) DESC";
                		$orderdIDs = $wpdb->get_col( $sortSQL );
                		foreach($orderdIDs as $orderdID){
                			$cuser = get_user_by('ID', $orderdID);
                			set_query_var( 'userData', $cuser ); ?>
		                    <div class="connection-invitation-item">
		                        <?php get_template_part('template-parts/user','listing-content');  ?>
		                        <div class="allience-req-btn">
		                            <a href="" id="remove-this-connection" data-user_id="<?php echo$cuser->ID ?>" class="btn btn-remove-con"><img src="<?php echo get_template_directory_uri(); ?>/images/remove-icon.svg" class="img-fluid">Remove connection</a>
		                        </div>
		                    </div>
			            <?php } 
                	}else{ ?>
	                	<div class="col-12 text-center ">
			            	<div class="alert alert-warning">
								No Connections!
							</div>
						</div>
	                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>