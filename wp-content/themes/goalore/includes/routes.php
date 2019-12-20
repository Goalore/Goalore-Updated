  <?php   

    /*
     *CUSTOM ROUTES SLUGS ARE DEFINED IN wp-config.php 
     */
    


  add_action('init', function() {

    /*
     *Define Profile Page URL
     */ 
    global $wp_rewrite;
    $author_slug = PROFILE;
    $wp_rewrite->author_base = $author_slug;


    /*
     *Define custom routes 
     */ 
    $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

    if(is_user_logged_in()){
      
      /*Check if member account is not deactivated*/
      $currentUserID = get_current_user_id(); 
      $isDeactivated = get_user_meta($currentUserID,'isDeactivated',true);
      $UNAVAILABLE = false;
      if($isDeactivated == 1) $UNAVAILABLE = true;

      if(!$UNAVAILABLE){
        //By default load profile page
        if ( $url_path === PROFILE) {
           $load = locate_template('author.php', true);
           if ($load) {
              exit(); 
           }
        } 
        //Load my connection page
        if ( $url_path === PROFILE.'/'.MY_CONNECTIONS ) {
           $load = locate_template('templates/my-connections.php', true);
           if ($load) {
            exit(); 
           }
        }
        
        //Load profile settings page
        if ( $url_path === SearchResult ) {
           $load = locate_template('templates/search.php', true);
           if ($load) {
            exit(); 
           }
        }
        //Load invite friend page
        if ( $url_path === InviteFriend ) {
           $load = locate_template('templates/invite-friend.php', true);
           if ($load) {
            exit(); 
           }
        }
        //Load invite friend page
        if ( $url_path === CONTACT ) {
           $load = locate_template('templates/contact.php', true);
           if ($load) {
            exit(); 
           }
        }
      }

      //Load profile settings page
        if ( $url_path === PROFILE.'/'.SETTINGS ) {
           $load = locate_template('templates/settings.php', true);
           if ($load) {
            exit(); 
           }
        }

    } 
    

});